<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Affiche la liste des départements
     */
    public function index(Request $request)
    {
        try {
            $query = Department::with(['teachers.user', 'subjects'])
                ->withCount(['teachers', 'subjects']);

            // Gestion des filtres
            if ($request->filled('status')) {
                $query->where('is_active', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $departments = $query->orderBy('name')->get();

            // Statistiques globales
            $totalDepartments = Department::count();
            $activeDepartments = Department::where('is_active', true)->count();
            $totalTeachers = Teacher::count();
            $totalSubjects = Subject::count();

            $stats = [
                'total_departments' => $totalDepartments,
                'active_departments' => $activeDepartments,
                'inactive_departments' => $totalDepartments - $activeDepartments,
                'total_teachers' => $totalTeachers,
                'total_subjects' => $totalSubjects,
                'departments_without_teachers' => Department::doesntHave('teachers')->count(),
                'departments_without_subjects' => Department::doesntHave('subjects')->count(),
            ];

            return view('admin.departments.index', compact('departments', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage des départements: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement des départements.');
        }
    }

    /**
     * Affiche le formulaire de création d'un département
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Enregistre un nouveau département
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:departments,name'
                ],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'alpha_num',
                    'unique:departments,code'
                ],
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'Le nom du département est obligatoire.',
                'name.unique' => 'Ce nom de département existe déjà.',
                'code.required' => 'Le code du département est obligatoire.',
                'code.unique' => 'Ce code de département existe déjà.',
                'code.alpha_num' => 'Le code ne peut contenir que des lettres et des chiffres.',
                'code.max' => 'Le code ne peut pas dépasser 10 caractères.',
                'description.max' => 'La description ne peut pas dépasser 1000 caractères.'
            ]);

            // Assurer que le code est en majuscules
            $validated['code'] = strtoupper($validated['code']);
            
            // Définir is_active par défaut à true si non fourni
            $validated['is_active'] = $request->has('is_active') ? true : ($validated['is_active'] ?? true);

            $department = Department::create($validated);

            Log::info('Département créé avec succès', [
                'department_id' => $department->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.departments.index')
                ->with('success', "Le département '{$department->name}' a été créé avec succès.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création du département.')
                        ->withInput();
        }
    }

    /**
     * Affiche les détails d'un département
     */
    public function show(Department $department)
    {
        try {
            $department->load([
                'teachers.user',
                'subjects' => function($query) {
                    $query->with('teacher.user')->orderBy('name');
                }
            ]);

            // Statistiques détaillées du département
            $stats = [
                'total_teachers' => $department->teachers->count(),
                'active_teachers' => $department->teachers->where('is_active', true)->where('status', 'active')->count(),
                'inactive_teachers' => $department->teachers->where('is_active', false)->count(),
                'total_subjects' => $department->subjects->count(),
                'active_subjects' => $department->subjects->where('is_active', true)->count(),
                'inactive_subjects' => $department->subjects->where('is_active', false)->count(),
                'total_credits' => $department->subjects->sum('credits'),
                'subjects_with_teacher' => $department->subjects->whereNotNull('teacher_id')->count(),
                'subjects_without_teacher' => $department->subjects->whereNull('teacher_id')->count(),
                'average_teacher_experience' => $department->teachers->avg('experience_years') ?? 0,
                'newest_teacher' => $department->teachers->sortBy('hire_date')->last(),
                'most_experienced_teacher' => $department->teachers->sortByDesc('experience_years')->first(),
            ];

            // Données pour les graphiques
            $chartData = [
                'subjects_by_status' => [
                    'active' => $stats['active_subjects'],
                    'inactive' => $stats['inactive_subjects']
                ],
                'teachers_by_status' => [
                    'active' => $stats['active_teachers'],
                    'inactive' => $stats['inactive_teachers']
                ],
                'subjects_assignment' => [
                    'assigned' => $stats['subjects_with_teacher'],
                    'unassigned' => $stats['subjects_without_teacher']
                ]
            ];

            return view('admin.departments.show', compact('department', 'stats', 'chartData'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement du département.');
        }
    }

    /**
     * Affiche le formulaire d'édition d'un département
     */
    public function edit(Department $department)
    {
        $department->load(['teachers.user', 'subjects']);
        
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Met à jour un département
     */
    public function update(Request $request, Department $department)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments')->ignore($department->id)
                ],
                'code' => [
                    'required',
                    'string',
                    'max:10',
                    'alpha_num',
                    Rule::unique('departments')->ignore($department->id)
                ],
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'Le nom du département est obligatoire.',
                'name.unique' => 'Ce nom de département existe déjà.',
                'code.required' => 'Le code du département est obligatoire.',
                'code.unique' => 'Ce code de département existe déjà.',
                'code.alpha_num' => 'Le code ne peut contenir que des lettres et des chiffres.',
                'code.max' => 'Le code ne peut pas dépasser 10 caractères.',
                'description.max' => 'La description ne peut pas dépasser 1000 caractères.'
            ]);

            // Assurer que le code est en majuscules
            $validated['code'] = strtoupper($validated['code']);
            
            // Gérer le checkbox is_active
            $validated['is_active'] = $request->has('is_active');

            $oldData = $department->toArray();
            $department->update($validated);

            Log::info('Département mis à jour', [
                'department_id' => $department->id,
                'changes' => array_diff_assoc($validated, $oldData),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.departments.show', $department)
                ->with('success', "Le département '{$department->name}' a été mis à jour avec succès.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du département.')
                        ->withInput();
        }
    }

    /**
     * Supprime un département
     */
    public function destroy(Department $department)
    {
        try {
            // Vérifications de sécurité
            $teachersCount = $department->teachers()->count();
            $subjectsCount = $department->subjects()->count();

            if ($teachersCount > 0 || $subjectsCount > 0) {
                return back()->with('error', 
                    "Impossible de supprimer le département '{$department->name}'. " .
                    "Il contient {$teachersCount} enseignant(s) et {$subjectsCount} matière(s). " .
                    "Veuillez d'abord réassigner ou supprimer ces éléments."
                );
            }

            $departmentName = $department->name;
            
            DB::transaction(function () use ($department) {
                $department->delete();
            });

            Log::info('Département supprimé', [
                'department_name' => $departmentName,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.departments.index')
                ->with('success', "Le département '{$departmentName}' a été supprimé avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression du département.');
        }
    }

    /**
     * Active/Désactive un département
     */
    public function toggleStatus(Department $department)
    {
        try {
            $oldStatus = $department->is_active;
            
            $department->update([
                'is_active' => !$department->is_active
            ]);

            $status = $department->is_active ? 'activé' : 'désactivé';
            
            Log::info('Statut du département modifié', [
                'department_id' => $department->id,
                'old_status' => $oldStatus,
                'new_status' => $department->is_active,
                'user_id' => auth()->id()
            ]);

            return back()->with('success', "Le département '{$department->name}' a été {$status} avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du changement de statut.');
        }
    }

    /**
     * Exporte la liste des départements
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'excel');
            
            // Pour l'instant, on retourne une notification
            // TODO: Implémenter l'export avec Laravel Excel ou autre
            
            Log::info('Export des départements demandé', [
                'format' => $format,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('info', 
                "L'export au format {$format} est en cours de développement. " .
                "Vous serez notifié lorsque cette fonctionnalité sera disponible."
            );

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'export des départements: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'export.');
        }
    }

    /**
     * Recherche de départements
     */
    public function search(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Restaure un département supprimé (si utilisation de soft deletes)
     */
    public function restore($id)
    {
        try {
            // Cette méthode nécessiterait l'utilisation de SoftDeletes
            // sur le modèle Department
            
            return back()->with('info', 'La restauration des départements n\'est pas encore implémentée.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration du département: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration.');
        }
    }

    /**
     * Obtient les statistiques pour l'API (AJAX)
     */
    public function apiStats()
    {
        try {
            $stats = [
                'total' => Department::count(),
                'active' => Department::where('is_active', true)->count(),
                'with_teachers' => Department::has('teachers')->count(),
                'with_subjects' => Department::has('subjects')->count(),
                'recent' => Department::where('created_at', '>=', now()->subMonth())->count(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des statistiques'], 500);
        }
    }

    /**
     * Valide la disponibilité d'un code de département (AJAX)
     */
    public function checkCodeAvailability(Request $request)
    {
        try {
            $code = strtoupper($request->get('code'));
            $departmentId = $request->get('department_id');
            
            $query = Department::where('code', $code);
            
            if ($departmentId) {
                $query->where('id', '!=', $departmentId);
            }
            
            $exists = $query->exists();
            
            return response()->json([
                'available' => !$exists,
                'message' => $exists ? 'Ce code est déjà utilisé' : 'Code disponible'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du code: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la vérification'], 500);
        }
    }

    /**
     * Obtient les départements pour les sélecteurs (API)
     */
    public function apiList(Request $request)
    {
        try {
            $query = Department::select(['id', 'name', 'code', 'is_active']);
            
            if ($request->get('active_only')) {
                $query->where('is_active', true);
            }
            
            $departments = $query->orderBy('name')->get();
            
            return response()->json($departments);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la liste des départements: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement'], 500);
        }
    }
}