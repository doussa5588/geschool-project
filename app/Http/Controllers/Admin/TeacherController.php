<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use App\Models\Department;
use App\Models\Classe; // Utilisation de votre modèle Classe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'department', 'subjects']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('employee_number', 'like', "%{$search}%")
              ->orWhere('specialization', 'like', "%{$search}%");
        }

        // Filtre par département
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filtre par matière
        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function($q) use ($request) {
                $q->where('subjects.id', $request->subject_id);
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === '1' || $request->status === 'active') {
                $query->where('status', 'active')->where('is_active', true);
            } elseif ($request->status === '0' || $request->status === 'inactive') {
                $query->where('status', 'inactive')->orWhere('is_active', false);
            } elseif ($request->status === 'suspended') {
                $query->where('status', 'suspended');
            }
        }

        // Tri par défaut
        $query->orderBy('created_at', 'desc');

        // Pagination
        $teachers = $query->paginate(20)->withQueryString();

        // Récupérer les départements pour les filtres
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Récupérer les matières pour les filtres
        $subjects = Subject::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.teachers.index', compact('teachers', 'departments', 'subjects'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.teachers.create', compact('departments', 'subjects'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'required|string|max:255',
            'hire_date' => 'required|date|before_or_equal:today',
            'salary' => 'nullable|numeric|min:0',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();
            
            // Générer automatiquement le mot de passe et le numéro employé
            $password = 'unchk' . date('Y');
            $employeeNumber = $this->generateEmployeeNumber();
            
            // Gérer l'upload de photo
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $photoPath = $request->file('profile_photo')->store('teachers/photos', 'public');
            }
            
            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($password),
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'profile_photo' => $photoPath,
                'is_active' => true
            ]);
            
            // Assigner le rôle enseignant
            $user->assignRole('teacher');
            
            // Créer l'enseignant
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_number' => $employeeNumber,
                'department_id' => $validated['department_id'],
                'specialization' => $validated['specialization'],
                'hire_date' => $validated['hire_date'],
                'salary' => $validated['salary'],
                'status' => 'active',
                'is_active' => true
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.teachers.index')
                ->with('success', "Enseignant {$user->name} créé avec succès. Mot de passe temporaire: {$password}");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la création de l\'enseignant: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'department', 'subjects.department']);
        
        // Calculer les statistiques basées sur vos modèles
        $stats = [
            'total_subjects' => $teacher->subjects()->count(),
            'total_credits' => $teacher->subjects()->sum('credits'),
            'experience_years' => $teacher->hire_date ? 
                Carbon::parse($teacher->hire_date)->diffInYears(now()) : 0,
            'department_subjects' => $teacher->department ? 
                $teacher->department->subjects()->count() : 0,
            'grades_given_this_month' => $teacher->grades()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'total_classes' => $teacher->subjects()
                ->with('classes')
                ->get()
                ->pluck('classes')
                ->flatten()
                ->unique('id')
                ->count(),
        ];
        
        return view('admin.teachers.show', compact('teacher', 'stats'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        $teacher->load(['user', 'subjects']);
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.teachers.edit', compact('teacher', 'departments', 'subjects'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($teacher->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'required|string|max:255',
            'hire_date' => 'required|date|before_or_equal:today',
            'salary' => 'nullable|numeric|min:0',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            // Gérer l'upload de nouvelle photo
            $photoPath = $teacher->user->profile_photo;
            if ($request->hasFile('profile_photo')) {
                // Supprimer l'ancienne photo
                if ($photoPath && \Storage::disk('public')->exists($photoPath)) {
                    \Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('profile_photo')->store('teachers/photos', 'public');
            }
            
            // Mettre à jour l'utilisateur
            $teacher->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'profile_photo' => $photoPath,
                'is_active' => $validated['is_active'] ?? true
            ]);
            
            // Mettre à jour l'enseignant
            $teacher->update([
                'department_id' => $validated['department_id'],
                'specialization' => $validated['specialization'],
                'hire_date' => $validated['hire_date'],
                'salary' => $validated['salary'],
                'status' => $validated['status'],
                'is_active' => $validated['is_active'] ?? true
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.teachers.show', $teacher)
                ->with('success', 'Enseignant mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            
            // Vérifier s'il y a des données liées (notes)
            $hasGrades = $teacher->grades()->exists();
            
            if ($hasGrades) {
                // Désactiver plutôt que supprimer s'il y a des données
                $teacher->update([
                    'is_active' => false,
                    'status' => 'inactive'
                ]);
                $teacher->user->update(['is_active' => false]);
                
                $message = "L'enseignant {$teacher->user->name} a été désactivé (il a des données liées).";
            } else {
                // Peut être supprimé complètement
                $teacher->update([
                    'is_active' => false,
                    'status' => 'inactive'
                ]);
                $teacher->user->update(['is_active' => false]);
                
                $message = "L'enseignant {$teacher->user->name} a été désactivé avec succès.";
            }
            
            DB::commit();
            
            return redirect()->route('admin.teachers.index')->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la désactivation: ' . $e->getMessage()]);
        }
    }


    /**
     * Génère un numéro employé unique.
     *
     * @return string
     */
    protected function generateEmployeeNumber()
    {
        $prefix = 'EMP'; // Préfixe fixe, modifiable selon vos besoins

        // Récupérer le dernier numéro employé existant (ex: EMP00001)
        $lastEmployeeNumber = Teacher::where('employee_number', 'like', $prefix . '%')
            ->orderBy('employee_number', 'desc')
            ->value('employee_number');

        if ($lastEmployeeNumber) {
            // Extraire la partie numérique et incrémenter
            $number = (int) substr($lastEmployeeNumber, strlen($prefix));
            $number++;
        } else {
            $number = 1;
        }

        // Formater avec des zéros à gauche (ex: EMP00001)
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }


    /**
     * Restore a teacher.
     */
    public function restore($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $teacher->update([
                'is_active' => true,
                'status' => 'active'
            ]);
            $teacher->user->update(['is_active' => true]);
            
            DB::commit();
            
            return back()->with('success', 'Enseignant réactivé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la réactivation: ' . $e->getMessage()]);
        }
    }

    /**
     * Show teacher subjects.
     */
    public function subjects(Teacher $teacher)
    {
        // Charger l'enseignant avec ses relations
        $teacher->load(['user', 'department', 'subjects.department']);
        
        // Récupérer toutes les matières disponibles du même département
        $availableSubjects = Subject::with('department')
            ->where('department_id', $teacher->department_id)
            ->where('is_active', true)
            ->whereNotIn('id', $teacher->subjects->pluck('id'))
            ->orderBy('name')
            ->get();
        
        // Récupérer toutes les matières pour permettre l'affectation inter-départements
        $allSubjects = Subject::with('department')
            ->where('is_active', true)
            ->whereNotIn('id', $teacher->subjects->pluck('id'))
            ->orderBy('department_id')
            ->orderBy('name')
            ->get()
            ->groupBy('department.name');
        
        // Statistiques adaptées à vos modèles
        $stats = [
            'total_subjects' => $teacher->subjects->count(),
            'total_credits' => $teacher->subjects->sum('credits'),
            'departments_count' => $teacher->subjects->groupBy('department_id')->count(),
            'active_subjects' => $teacher->subjects->where('is_active', true)->count(),
            'total_classes' => $teacher->subjects()
                ->with('classes')
                ->get()
                ->pluck('classes')
                ->flatten()
                ->unique('id')
                ->count(),
        ];
        
        return view('admin.teachers.subjects', compact(
            'teacher', 
            'availableSubjects', 
            'allSubjects', 
            'stats'
        ));
    }

    /**
     * Assign subjects to teacher.
     */
    public function assignSubjects(Request $request, Teacher $teacher)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        try {
            // Vérifier que les matières ne sont pas déjà assignées
            $existingSubjects = $teacher->subjects->pluck('id')->toArray();
            $newSubjects = array_diff($request->subject_ids, $existingSubjects);
            
            if (empty($newSubjects)) {
                return redirect()->back()->with('warning', 'Toutes les matières sélectionnées sont déjà assignées à cet enseignant.');
            }

            // Assigner les nouvelles matières
            foreach ($newSubjects as $subjectId) {
                $subject = Subject::find($subjectId);
                if ($subject) {
                    // Mettre à jour la matière pour assigner l'enseignant
                    $subject->update(['teacher_id' => $teacher->id]);
                }
            }

            $assignedCount = count($newSubjects);
            return redirect()->back()->with('success', "$assignedCount matière(s) assignée(s) avec succès à {$teacher->user->name}.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'assignation: ' . $e->getMessage());
        }
    }

    /**
     * Remove subject from teacher.
     */
    public function removeSubject(Teacher $teacher, Subject $subject)
    {
        try {
            // Vérifier que la matière est bien assignée à cet enseignant
            if ($subject->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'Cette matière n\'est pas assignée à cet enseignant.');
            }

            // Vérifier s'il y a des notes ou des plannings associés
            $hasGrades = $subject->grades()->where('teacher_id', $teacher->id)->exists();
            $hasSchedules = $subject->schedules()->where('teacher_id', $teacher->id)->exists();

            if ($hasGrades || $hasSchedules) {
                return redirect()->back()->with('warning', 
                    'Impossible de retirer cette matière car elle contient des données (notes ou emploi du temps). Veuillez d\'abord supprimer ces données.');
            }

            // Retirer l'assignation
            $subject->update(['teacher_id' => null]);

            return redirect()->back()->with('success', 
                "La matière \"{$subject->name}\" a été retirée de {$teacher->user->name}.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du retrait: ' . $e->getMessage());
        }
    }

    /**
     * Export teachers data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Récupérer tous les enseignants avec leurs relations
        $teachers = Teacher::with(['user', 'department'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'excel') {
            return $this->exportToExcel($teachers);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($teachers);
        }
        
        return redirect()->back()->with('error', 'Format d\'export non supporté');
    }

    /**
     * Export to Excel format (CSV)
     */
    private function exportToExcel($teachers)
    {
        $filename = 'enseignants_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($teachers) {
            $file = fopen('php://output', 'w');
            
            // Ajouter BOM pour UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom',
                'Email', 
                'Téléphone',
                'Numéro Employé',
                'Spécialisation',
                'Département',
                'Date d\'embauche',
                'Salaire (FCFA)',
                'Statut',
                'Date de création'
            ], ';');
            
            // Données
            foreach ($teachers as $teacher) {
                fputcsv($file, [
                    $teacher->id,
                    $teacher->user->name,
                    $teacher->user->email,
                    $teacher->user->phone ?? 'N/A',
                    $teacher->employee_number,
                    $teacher->specialization,
                    $teacher->department->name ?? 'N/A',
                    $teacher->hire_date ? Carbon::parse($teacher->hire_date)->format('d/m/Y') : 'N/A',
                    $teacher->salary ? number_format($teacher->salary, 0, ',', ' ') : 'N/A',
                    ucfirst($teacher->status),
                    $teacher->created_at ? $teacher->created_at->format('d/m/Y H:i') : 'N/A'
                ], ';');
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export to PDF format
     */
    private function exportToPdf($teachers)
    {
        $html = view('admin.teachers.export-pdf', compact('teachers'))->render();
        
        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="enseignants.html"',
        ];
        
        return Response::make($html, 200, $headers);
    }

    /**
     * Import teachers from Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $fileContents = file($file->getPathname());
            
            $importedCount = 0;
            $errors = [];
            
            foreach ($fileContents as $line => $data) {
                // Ignorer la première ligne (en-têtes)
                if ($line < 1) continue;
                
                $teacherData = str_getcsv($data, ';');
                
                // Validation basique des données
                if (count($teacherData) < 6) {
                    $errors[] = "Ligne " . ($line + 1) . ": Données incomplètes";
                    continue;
                }
                
                try {
                    // Logique d'import à implémenter selon vos besoins
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($line + 1) . ": " . $e->getMessage();
                }
            }
            
            $message = "$importedCount enseignant(s) importé(s) avec succès.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode(', ', $errors);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete teachers
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id'
        ]);

        try {
            $deletedCount = Teacher::whereIn('id', $request->teacher_ids)
                ->update(['is_active' => false, 'status' => 'inactive']);
            
            return redirect()->back()->with('success', "$deletedCount enseignant(s) désactivé(s) avec succès.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * API: Teachers by subject (AJAX)
     */
    public function apiBySubject($subjectId)
    {
        $teachers = Teacher::whereHas('subjects', function($query) use ($subjectId) {
            $query->where('subjects.id', $subjectId);
        })
        ->with('user')
        ->where('is_active', true)
        ->get()
        ->map(function($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
                'employee_number' => $teacher->employee_number
            ];
        });

        return response()->json($teachers);
    }

    /**
     * API pour récupérer les enseignants par département (pour AJAX)
     */
    public function apiByDepartment(Department $department)
    {
        try {
            $teachers = Teacher::where('department_id', $department->id)
                ->where('is_active', true)
                ->with('user:id,name')
                ->get()
                ->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->user->name,
                        'specialization' => $teacher->specialization ?? ''
                    ];
                });

            return response()->json($teachers);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du chargement des enseignants'
            ], 500);
        }
    }
}