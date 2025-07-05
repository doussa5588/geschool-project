<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Classe;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SubjectController extends Controller
{
    /**
     * Afficher la liste des matières
     */
    public function index(Request $request)
    {
        try {
            $query = Subject::with(['department', 'teacher.user']);

            // Filtres de recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filtre par département
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            // Filtre par enseignant
            if ($request->filled('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            // Filtre par nombre de crédits
            if ($request->filled('credits')) {
                $query->where('credits', $request->credits);
            }

            // Filtre par statut
            if ($request->filled('status')) {
                if ($request->status === 'assigned') {
                    $query->whereNotNull('teacher_id');
                } elseif ($request->status === 'unassigned') {
                    $query->whereNull('teacher_id');
                } else {
                    $query->where('is_active', $request->status);
                }
            }

            // Tri
            $sortBy = $request->get('sort_by', 'name');
            $sortDirection = $request->get('sort_direction', 'asc');
            
            if (in_array($sortBy, ['name', 'code', 'credits', 'created_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('name', 'asc');
            }

            $subjects = $query->paginate(20)->withQueryString();

            // Données pour les filtres
            $departments = Department::where('is_active', true)->orderBy('name')->get();
            $teachers = Teacher::with('user')->where('is_active', true)->orderBy('created_at')->get();
            $availableCredits = Subject::distinct()->pluck('credits')->sort()->values();

            // Statistiques globales
            $stats = [
                'total_subjects' => Subject::count(),
                'active_subjects' => Subject::where('is_active', true)->count(),
                'assigned_subjects' => Subject::whereNotNull('teacher_id')->count(),
                'unassigned_subjects' => Subject::whereNull('teacher_id')->count(),
                'total_credits' => Subject::sum('credits'),
                'subjects_with_classes' => Subject::has('classes')->count(),
                'subjects_without_classes' => Subject::doesntHave('classes')->count(),
                'recent_subjects' => Subject::where('created_at', '>=', now()->subMonth())->count(),
            ];

            return view('admin.subjects.index', compact(
                'subjects', 
                'departments', 
                'teachers', 
                'availableCredits', 
                'stats'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage des matières: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement des matières.');
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->orderBy('created_at')->get();
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        
        return view('admin.subjects.create', compact('departments', 'teachers', 'classes'));
    }

    /**
     * Enregistrer une nouvelle matière
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => [
                    'required',
                    'string',
                    'max:20',
                    'alpha_num',
                    'unique:subjects,code'
                ],
                'description' => 'nullable|string|max:1000',
                'credits' => 'required|integer|min:1|max:10',
                'department_id' => 'required|exists:departments,id',
                'teacher_id' => 'nullable|exists:teachers,id',
                'classes' => 'nullable|array',
                'classes.*' => 'exists:classes,id',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'Le nom de la matière est obligatoire.',
                'code.required' => 'Le code de la matière est obligatoire.',
                'code.unique' => 'Ce code de matière existe déjà.',
                'code.alpha_num' => 'Le code ne peut contenir que des lettres et des chiffres.',
                'credits.required' => 'Le nombre de crédits est obligatoire.',
                'credits.integer' => 'Le nombre de crédits doit être un nombre entier.',
                'credits.min' => 'Le nombre de crédits doit être au moins 1.',
                'credits.max' => 'Le nombre de crédits ne peut pas dépasser 10.',
                'department_id.required' => 'Le département est obligatoire.',
                'department_id.exists' => 'Le département sélectionné n\'existe pas.',
                'teacher_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            ]);

            DB::beginTransaction();

            // Vérifier si l'enseignant appartient au même département
            if ($validated['teacher_id']) {
                $teacher = Teacher::find($validated['teacher_id']);
                if ($teacher && $teacher->department_id !== $validated['department_id']) {
                    return back()->withInput()
                        ->withErrors(['teacher_id' => 'L\'enseignant doit appartenir au même département que la matière.']);
                }
            }

            // Assurer que le code est en majuscules
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $validated['is_active'] ?? true;

            // Créer la matière
            $subject = Subject::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'credits' => $validated['credits'],
                'department_id' => $validated['department_id'],
                'teacher_id' => $validated['teacher_id'],
                'is_active' => $validated['is_active']
            ]);

            // Assigner les classes
            if (!empty($validated['classes'])) {
                $subject->classes()->attach($validated['classes']);
            }

            DB::commit();

            Log::info('Matière créée avec succès', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.subjects.index')
                ->with('success', "La matière '{$subject->name}' a été créée avec succès.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création de la matière: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création de la matière.')
                        ->withInput();
        }
    }

    /**
     * Afficher les détails d'une matière
     */
    public function show(Subject $subject)
    {
        try {
            // Charger toutes les relations nécessaires
            $subject->load([
                'department',
                'teacher.user',
                'classes.level',
                'classes.students' => function($query) {
                    $query->where('is_active', true);
                },
                'grades.student.user',
                'grades.student.class',
                'grades.teacher.user',
                'schedules.class',
                'schedules.teacher.user'
            ]);

            // Calculer les statistiques de base
            $stats = $this->calculateSubjectStats($subject);
            
            // Données pour les graphiques
            $chartData = $this->getChartData($subject);

            return view('admin.subjects.show', compact('subject', 'stats', 'chartData'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement de la matière: ' . $e->getMessage(), [
                'subject_id' => $subject->id ?? 'N/A',
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.subjects.index')
                ->with('error', 'Erreur lors du chargement de la matière : ' . $e->getMessage());
        }
    }

    /**
     * Préparer les données pour les graphiques
     */
    private function getChartData(Subject $subject)
    {
        $grades = $subject->grades;
        
        // Distribution des notes
        $gradeDistribution = [
            'excellent' => $grades->where('score', '>=', 16)->count(),
            'good' => $grades->whereBetween('score', [14, 15.99])->count(),
            'average' => $grades->whereBetween('score', [12, 13.99])->count(),
            'below_average' => $grades->whereBetween('score', [10, 11.99])->count(),
            'poor' => $grades->where('score', '<', 10)->count(),
        ];

        // Notes mensuelles (6 derniers mois)
        $monthlyGrades = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthGrades = $grades->filter(function($grade) use ($date) {
                return $grade->created_at->format('Y-m') === $date->format('Y-m');
            });
            
            if ($monthGrades->count() > 0) {
                $monthlyGrades[] = [
                    'month' => (int)$date->format('m'),
                    'year' => (int)$date->format('Y'),
                    'average' => round($monthGrades->avg('score'), 2),
                    'count' => $monthGrades->count()
                ];
            }
        }

        // Performance par classe
        $classPerformance = $subject->classes->map(function($class) use ($subject) {
            $classGrades = $subject->grades->filter(function($grade) use ($class) {
                return optional($grade->student)->class_id === $class->id;
            });
            
            return [
                'class_name' => $class->name,
                'average_grade' => $classGrades->count() > 0 ? round($classGrades->avg('score'), 2) : 0,
                'students_count' => $class->students->count(),
                'grades_count' => $classGrades->count()
            ];
        })->filter(function($class) {
            return $class['grades_count'] > 0;
        })->values();

        return [
            'grade_distribution' => $gradeDistribution,
            'monthly_grades' => $monthlyGrades,
            'class_performance' => $classPerformance->toArray()
        ];
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Subject $subject)
    {
        $subject->load(['classes']);
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->orderBy('created_at')->get();
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        
        return view('admin.subjects.edit', compact('subject', 'departments', 'teachers', 'classes'));
    }

    /**
     * Mettre à jour une matière
     */
    public function update(Request $request, Subject $subject)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => [
                    'required',
                    'string',
                    'max:20',
                    'alpha_num',
                    Rule::unique('subjects')->ignore($subject->id)
                ],
                'description' => 'nullable|string|max:1000',
                'credits' => 'required|integer|min:1|max:10',
                'department_id' => 'required|exists:departments,id',
                'teacher_id' => 'nullable|exists:teachers,id',
                'classes' => 'nullable|array',
                'classes.*' => 'exists:classes,id',
                'is_active' => 'boolean'
            ], [
                'name.required' => 'Le nom de la matière est obligatoire.',
                'code.required' => 'Le code de la matière est obligatoire.',
                'code.unique' => 'Ce code de matière existe déjà.',
                'code.alpha_num' => 'Le code ne peut contenir que des lettres et des chiffres.',
                'credits.required' => 'Le nombre de crédits est obligatoire.',
                'department_id.required' => 'Le département est obligatoire.',
            ]);

            DB::beginTransaction();

            // Vérifier si l'enseignant appartient au même département
            if ($validated['teacher_id']) {
                $teacher = Teacher::find($validated['teacher_id']);
                if ($teacher && $teacher->department_id !== $validated['department_id']) {
                    return back()->withInput()
                        ->withErrors(['teacher_id' => 'L\'enseignant doit appartenir au même département que la matière.']);
                }
            }

            // Vérifier si on peut changer le département (s'il y a des notes ou des emplois du temps)
            if ($subject->department_id !== $validated['department_id']) {
                $hasGrades = $subject->grades()->exists();
                $hasSchedules = $subject->schedules()->exists();
                
                if ($hasGrades || $hasSchedules) {
                    return back()->withInput()
                        ->withErrors(['department_id' => 'Impossible de changer le département car la matière contient des notes ou des emplois du temps.']);
                }
            }

            $oldData = $subject->toArray();
            $validated['code'] = strtoupper($validated['code']);
            $validated['is_active'] = $validated['is_active'] ?? true;

            // Mettre à jour la matière
            $subject->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'credits' => $validated['credits'],
                'department_id' => $validated['department_id'],
                'teacher_id' => $validated['teacher_id'],
                'is_active' => $validated['is_active']
            ]);

            // Mettre à jour les classes
            if (isset($validated['classes'])) {
                $subject->classes()->sync($validated['classes']);
            } else {
                $subject->classes()->detach();
            }

            DB::commit();

            Log::info('Matière mise à jour', [
                'subject_id' => $subject->id,
                'changes' => array_diff_assoc($validated, $oldData),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.subjects.show', $subject)
                ->with('success', "La matière '{$subject->name}' a été mise à jour avec succès.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à jour de la matière: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour de la matière.')
                        ->withInput();
        }
    }

    /**
     * Supprimer une matière
     */
    public function destroy(Subject $subject)
    {
        try {
            DB::beginTransaction();

            // Vérifier s'il y a des données liées
            $hasGrades = $subject->grades()->exists();
            $hasSchedules = $subject->schedules()->exists();
            $hasAttendances = $subject->attendances()->exists();

            if ($hasGrades || $hasSchedules || $hasAttendances) {
                return back()->with('error', 
                    "Impossible de supprimer la matière '{$subject->name}'. " .
                    "Elle contient des données liées (notes, emplois du temps ou présences). " .
                    "Veuillez d'abord supprimer ces données ou désactiver la matière."
                );
            }

            $subjectName = $subject->name;

            // Détacher les classes avant la suppression
            $subject->classes()->detach();
            
            // Désactiver plutôt que supprimer
            $subject->update([
                'is_active' => false,
                'teacher_id' => null
            ]);

            DB::commit();

            Log::info('Matière désactivée', [
                'subject_name' => $subjectName,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.subjects.index')
                ->with('success', "La matière '{$subjectName}' a été désactivée avec succès.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la suppression de la matière: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la matière.');
        }
    }

    /**
     * Restaurer une matière
     */
   /* public function restore($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            DB::beginTransaction();
            
            $subject->update(['is_active' => true]);
            
            DB::commit();
            
            Log::info('Matière restaurée', [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('success', "La matière '{$subject->name}' a été restaurée avec succès.");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la restauration de la matière: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration de la matière.');
        }
    }*/

    /**
     * Assigner/Retirer un enseignant
     */
    public function assignTeacher(Request $request, Subject $subject)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'nullable|exists:teachers,id'
            ]);

            DB::beginTransaction();

            // Vérifier si l'enseignant appartient au même département
            if ($validated['teacher_id']) {
                $teacher = Teacher::find($validated['teacher_id']);
                if ($teacher && $teacher->department_id !== $subject->department_id) {
                    return back()->withErrors(['error' => 'L\'enseignant doit appartenir au même département que la matière.']);
                }
            }

            $oldTeacher = $subject->teacher;
            $subject->update(['teacher_id' => $validated['teacher_id']]);

            DB::commit();

            if ($validated['teacher_id']) {
                $newTeacher = Teacher::find($validated['teacher_id']);
                $message = "L'enseignant {$newTeacher->user->name} a été assigné à la matière {$subject->name}.";
            } else {
                $message = "L'enseignant a été retiré de la matière {$subject->name}.";
            }

            Log::info('Assignation d\'enseignant modifiée', [
                'subject_id' => $subject->id,
                'old_teacher_id' => $oldTeacher ? $oldTeacher->id : null,
                'new_teacher_id' => $validated['teacher_id'],
                'user_id' => auth()->id()
            ]);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'assignation de l\'enseignant: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'assignation.');
        }
    }

    /**
     * Gérer les classes assignées à la matière
     */
    public function manageClasses(Request $request, Subject $subject)
    {
        try {
            $validated = $request->validate([
                'classes' => 'array',
                'classes.*' => 'exists:classes,id'
            ]);

            DB::beginTransaction();
            
            $subject->classes()->sync($validated['classes'] ?? []);
            
            DB::commit();

            $classCount = count($validated['classes'] ?? []);
            $message = $classCount > 0 
                ? "La matière a été assignée à {$classCount} classe(s)."
                : "Toutes les classes ont été retirées de cette matière.";

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la gestion des classes: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la gestion des classes.');
        }
    }

     /**
     * Export des matières
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        try {
            if ($format === 'excel') {
                // Pour l'instant, retourner une réponse temporaire
                return response()->json([
                    'message' => 'Export Excel - Fonctionnalité en cours de développement',
                    'status' => 'info'
                ]);
                
                // TODO: Implémenter l'export Excel avec Laravel Excel
                // return Excel::download(new SubjectsExport, 'subjects.xlsx');
                
            } elseif ($format === 'pdf') {
                // Pour l'instant, retourner une réponse temporaire
                return response()->json([
                    'message' => 'Export PDF - Fonctionnalité en cours de développement',
                    'status' => 'info'
                ]);
                
                // TODO: Implémenter l'export PDF
                // $pdf = PDF::loadView('admin.subjects.pdf', compact('subjects'));
                // return $pdf->download('subjects.pdf');
                
            } elseif ($format === 'template') {
                // Modèle Excel pour l'import
                return response()->json([
                    'message' => 'Modèle Excel - Fonctionnalité en cours de développement',
                    'status' => 'info'
                ]);
                
                // TODO: Retourner un modèle Excel vide
                
            } else {
                return redirect()->back()->with('error', 'Format d\'export non valide');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export : ' . $e->getMessage());
        }
    }

    /**
     * Export des détails de la matière en PDF
     */
    public function exportDetails(Subject $subject, Request $request)
    {
        try {
            $format = $request->get('format', 'pdf');
            
            // Charger toutes les relations nécessaires
            $subject->load([
                'department',
                'teacher.user',
                'classes.level',
                'classes.students',
                'grades' => function($query) {
                    $query->latest()->limit(10);
                },
                'schedules'
            ]);

            // Calculer les statistiques
            $stats = $this->calculateSubjectStats($subject);

            if ($format === 'pdf') {
                // TODO: Implémenter l'export PDF avec dompdf
                return response()->json([
                    'message' => 'Export PDF de la fiche matière - Fonctionnalité en cours de développement',
                    'data' => [
                        'subject' => $subject->name,
                        'stats' => $stats
                    ]
                ]);
                
                // Exemple d'implémentation future avec dompdf :
                // $pdf = PDF::loadView('admin.subjects.pdf.details', compact('subject', 'stats'));
                // return $pdf->download("fiche-matiere-{$subject->code}.pdf");
            }

            return redirect()->back()->with('error', 'Format non supporté');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export : ' . $e->getMessage());
        }
    }

    /**
     * Export du rapport de notes
     */
    public function exportGrades(Subject $subject, Request $request)
    {
        try {
            // Charger les notes avec les relations
            $subject->load([
                'grades.student.user',
                'grades.student.class',
                'grades.teacher.user'
            ]);

            // TODO: Implémenter l'export Excel avec Laravel Excel
            return response()->json([
                'message' => 'Export rapport de notes - Fonctionnalité en cours de développement',
                'data' => [
                    'subject' => $subject->name,
                    'total_grades' => $subject->grades->count(),
                    'average' => $subject->grades->avg('score') ?? 0
                ]
            ]);
            
            // Exemple d'implémentation future :
            // return Excel::download(new SubjectGradesExport($subject), "notes-{$subject->code}.xlsx");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export des notes : ' . $e->getMessage());
        }
    }

    /**
     * Export du planning des cours
     */
    public function exportSchedule(Subject $subject, Request $request)
    {
        try {
            // Charger l'emploi du temps avec les relations
            $subject->load([
                'schedules.class.level',
                'schedules.teacher.user'
            ]);

            // TODO: Implémenter l'export Excel de l'emploi du temps
            return response()->json([
                'message' => 'Export planning cours - Fonctionnalité en cours de développement',
                'data' => [
                    'subject' => $subject->name,
                    'total_schedules' => $subject->schedules->count(),
                    'classes' => $subject->classes->pluck('name')
                ]
            ]);
            
            // Exemple d'implémentation future :
            // return Excel::download(new SubjectScheduleExport($subject), "planning-{$subject->code}.xlsx");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export du planning : ' . $e->getMessage());
        }
    }

    /**
     * Calculer les statistiques d'une matière
     */
    private function calculateSubjectStats(Subject $subject)
    {
        $grades = $subject->grades;
        
        return [
            'total_classes' => $subject->classes->count(),
            'total_students' => $subject->classes->sum(function($class) {
                return $class->students->count();
            }),
            'total_grades' => $grades->count(),
            'total_schedules' => $subject->schedules->count(),
            'average_grade' => $grades->avg('score') ?? 0,
            'recent_grades_count' => $grades->where('created_at', '>=', now()->subMonth())->count(),
            'pass_rate' => $grades->count() > 0 ? 
                round(($grades->where('score', '>=', 10)->count() / $grades->count()) * 100, 1) : 0,
            'weekly_schedules' => $subject->schedules->whereBetween('created_at', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])->count()
        ];
    }

    /**
     * Import des matières depuis un fichier Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'update_existing' => 'boolean'
        ]);

        try {
            // Pour l'instant, retourner une réponse temporaire
            return redirect()->back()->with('info', 'Import - Fonctionnalité en cours de développement');
            
            // TODO: Implémenter l'import Excel
            // $import = new SubjectsImport($request->boolean('update_existing'));
            // Excel::import($import, $request->file('file'));
            // 
            // return redirect()->back()->with('success', 'Matières importées avec succès');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Suppression en masse des matières
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        try {
            $subjectIds = $request->subjects;
            
            // Désactiver plutôt que supprimer définitivement
            $updatedCount = Subject::whereIn('id', $subjectIds)
                ->update(['is_active' => false]);
            
            return redirect()->back()->with('success', 
                "{$updatedCount} matière(s) désactivée(s) avec succès");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Restaurer une matière désactivée
     */
    public function restore(Subject $subject)
    {
        try {
            $subject->update(['is_active' => true]);
            
            return redirect()->back()->with('success', 
                'Matière restaurée avec succès');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }
}