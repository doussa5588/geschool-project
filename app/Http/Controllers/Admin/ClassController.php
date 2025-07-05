<?php
// Fichier: app/Http/Controllers/Admin/ClassController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ClassController extends Controller
{
    /**
     * Afficher la liste des classes
     */
    public function index(Request $request)
    {
        $query = Classe::with(['level', 'department', 'students', 'subjects']);
        
        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
            });
        }
        
        // Filtre par niveau
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }
        
        // Filtre par département
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filtre par année académique
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        
        $classes = $query->withCount('students')->paginate(20);
        
        // Données pour les filtres
        $levels = Level::orderBy('order')->get();
        $departments = Department::orderBy('name')->get();
        $academicYears = Classe::distinct()->pluck('academic_year')->sort()->values();
        
        return view('admin.classes.index', compact('classes', 'levels', 'departments', 'academicYears'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $levels = Level::where('is_active', true)->orderBy('order')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $currentAcademicYear = $this->getCurrentAcademicYear();
        
        return view('admin.classes.create', compact('levels', 'departments', 'subjects', 'currentAcademicYear'));
    }

    /**
     * Enregistrer une nouvelle classe
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:classes,code',
            'level_id' => 'required|exists:levels,id',
            'department_id' => 'required|exists:departments,id',
            'academic_year' => 'required|string|max:9',
            'capacity' => 'required|integer|min:1|max:100',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        try {
            DB::beginTransaction();
            
            // Créer la classe
            $class = Classe::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'level_id' => $validated['level_id'],
                'department_id' => $validated['department_id'],
                'academic_year' => $validated['academic_year'],
                'capacity' => $validated['capacity'],
                'room' => $validated['room'],
                'description' => $validated['description'],
                'is_active' => true
            ]);
            
            // Assigner les matières
            if (!empty($validated['subjects'])) {
                $class->subjects()->attach($validated['subjects']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.classes.index')
                ->with('success', "Classe {$class->name} créée avec succès.");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la création de la classe: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les détails d'une classe
     */
    public function show(Classe $class)
    {
        $class->load([
            'level', 
            'department', 
            'subjects',
            'students.user', // Charger les étudiants avec leurs utilisateurs
            'schedules' => function($query) {
                $query->with(['subject', 'teacher.user'])
                    ->where('day_of_week', '>=', now()->startOfWeek())
                    ->where('day_of_week', '<=', now()->endOfWeek())
                    ->orderBy('day_of_week')
                    ->orderBy('start_time');
            }
        ]);
        
        // Calculer les statistiques
        $stats = [
            'total_students' => $class->students()->count(),
            'capacity_usage' => round(($class->students()->count() / $class->capacity) * 100, 2),
            'male_students' => $class->students()->whereHas('user', function($q) {
                $q->where('gender', 'male');
            })->count(),
            'female_students' => $class->students()->whereHas('user', function($q) {
                $q->where('gender', 'female');
            })->count(),
            'total_subjects' => $class->subjects()->count(),
            'weekly_hours' => $class->schedules()
                ->where('day_of_week', '>=', now()->startOfWeek())
                ->where('day_of_week', '<=', now()->endOfWeek())
                ->count(),
            'average_grade' => $class->students()
                ->join('grades', 'students.id', '=', 'grades.student_id')
                ->avg('grades.grade') ?? 0,
            'attendance_rate' => $this->calculateClassAttendanceRate($class)
        ];
        
        return view('admin.classes.show', compact('class', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Classe $class)
    {
        $class->load('subjects');
        $levels = Level::where('is_active', true)->orderBy('order')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.classes.edit', compact('class', 'levels', 'departments', 'subjects'));
    }

    /**
     * Mettre à jour une classe
     */
    public function update(Request $request, Classe $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:10', Rule::unique('classes')->ignore($class->id)],
            'level_id' => 'required|exists:levels,id',
            'department_id' => 'required|exists:departments,id',
            'academic_year' => 'required|string|max:9',
            'capacity' => 'required|integer|min:1|max:100',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            // Vérifier si la capacité n'est pas inférieure au nombre d'étudiants actuels
            $currentStudentCount = $class->students()->count();
            if ($validated['capacity'] < $currentStudentCount) {
                return back()->withInput()
                    ->withErrors(['capacity' => "La capacité ne peut pas être inférieure au nombre d'étudiants actuels ({$currentStudentCount})."]);
            }
            
            // Mettre à jour la classe
            $class->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'level_id' => $validated['level_id'],
                'department_id' => $validated['department_id'],
                'academic_year' => $validated['academic_year'],
                'capacity' => $validated['capacity'],
                'room' => $validated['room'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'] ?? true
            ]);
            
            // Mettre à jour les matières
            if (isset($validated['subjects'])) {
                $class->subjects()->sync($validated['subjects']);
            } else {
                $class->subjects()->detach();
            }
            
            DB::commit();
            
            return redirect()->route('admin.classes.show', $class)
                ->with('success', 'Classe mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer une classe
     */
    public function destroy(Classe $class)
    {
        try {
            DB::beginTransaction();
            
            // Vérifier s'il y a des étudiants dans la classe
            if ($class->students()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer une classe qui contient des étudiants. Transférez d\'abord les étudiants.']);
            }
            
            // Archiver plutôt que supprimer
            $class->update(['is_active' => false]);
            
            DB::commit();
            
            return redirect()->route('admin.classes.index')
                ->with('success', 'Classe archivée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de l\'archivage: ' . $e->getMessage()]);
        }
    }

    /**
     * Réactiver une classe
     */
    public function restore($id)
    {
        $class = Classe::findOrFail($id);
        
        try {
            $class->update(['is_active' => true]);
            
            return back()->with('success', 'Classe réactivée avec succès.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la réactivation: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les étudiants d'une classe
     */
    public function students(Classe $class)
    {
        $students = $class->students()
            ->with(['user', 'grades' => function($query) {
                $query->with('subject');
            }])
            ->paginate(30);
        
        // Calculer les moyennes pour chaque étudiant
        foreach ($students as $student) {
            $student->average_grade = $student->grades->avg('grade') ?? 0;
            $student->total_grades = $student->grades->count();
        }
        
        return view('admin.classes.students', compact('class', 'students'));
    }

    /**
     * Afficher l'emploi du temps d'une classe
     */
    public function schedule(Classe $class)
    {
        $currentWeek = now()->startOfWeek();
        $schedules = $class->schedules()
            ->with(['subject', 'teacher.user'])
            ->where('date', '>=', $currentWeek)
            ->where('date', '<=', $currentWeek->copy()->endOfWeek())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        // Organiser les emplois du temps par jour
        $weekSchedule = [];
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        foreach ($daysOfWeek as $day) {
            $weekSchedule[$day] = $schedules->filter(function($schedule) use ($day) {
                return strtolower($schedule->date->format('l')) === $day;
            })->sortBy('start_time');
        }
        
        return view('admin.classes.schedule', compact('class', 'weekSchedule', 'currentWeek'));
    }

    /**
     * Transférer des étudiants vers une autre classe
     */
    public function transferStudents(Request $request, Classe $class)
    {
        $validated = $request->validate([
            'target_class_id' => 'required|exists:classes,id|different:' . $class->id,
            'students' => 'required|array|min:1',
            'students.*' => 'exists:students,id'
        ]);

        try {
            DB::beginTransaction();
            
            $targetClass = Classe::findOrFail($validated['target_class_id']);
            
            // Vérifier la capacité de la classe cible
            $currentTargetCount = $targetClass->students()->count();
            $transferCount = count($validated['students']);
            
            if (($currentTargetCount + $transferCount) > $targetClass->capacity) {
                return back()->withErrors(['error' => 'La classe cible n\'a pas assez de place pour accueillir tous les étudiants sélectionnés.']);
            }
            
            // Effectuer le transfert
            Student::whereIn('id', $validated['students'])
                ->update(['class_id' => $targetClass->id]);
            
            DB::commit();
            
            return back()->with('success', "Transfert de {$transferCount} étudiant(s) vers {$targetClass->name} effectué avec succès.");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors du transfert: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Classes par niveau (pour AJAX)
     */
    public function apiByLevel($levelId)
    {
        $classes = Classe::where('level_id', $levelId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                    'capacity' => $class->capacity,
                    'current_students' => $class->students()->count()
                ];
            });

        return response()->json($classes);
    }

    /**
     * Export des classes en Excel
     */
    public function export(Request $request)
    {
        // TODO: Implémenter l'export Excel
        return response()->json(['message' => 'Export en cours de développement']);
    }

    /**
     * Générer un rapport de classe
     */
    public function generateReport(Classe $class, Request $request)
    {
        $type = $request->get('type', 'general'); // general, grades, attendance
        
        switch ($type) {
            case 'grades':
                return $this->generateGradesReport($class);
            case 'attendance':
                return $this->generateAttendanceReport($class);
            default:
                return $this->generateGeneralReport($class);
        }
    }

    /**
     * Obtenir l'année académique actuelle
     */
    private function getCurrentAcademicYear()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        if ($currentMonth >= 9) {
            return $currentYear . '-' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '-' . $currentYear;
        }
    }

    /**
     * Calculer le taux de présence d'une classe
     */
    private function calculateClassAttendanceRate(Classe $class)
    {
        $totalAttendances = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('students.class_id', $class->id)
            ->count();
            
        if ($totalAttendances === 0) return 100;
        
        $presentAttendances = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('students.class_id', $class->id)
            ->where('attendances.status', 'present')
            ->count();
            
        return round(($presentAttendances / $totalAttendances) * 100, 2);
    }

    /**
     * Générer un rapport général de classe
     */
    private function generateGeneralReport(Classe $class)
    {
        // TODO: Implémenter la génération de rapport PDF
        return response()->json(['message' => 'Génération de rapport en cours de développement']);
    }

    /**
     * Générer un rapport de notes de classe
     */
    private function generateGradesReport(Classe $class)
    {
        // TODO: Implémenter la génération de rapport de notes PDF
        return response()->json(['message' => 'Rapport de notes en cours de développement']);
    }

    /**
     * Générer un rapport de présence de classe
     */
    private function generateAttendanceReport(Classe $class)
    {
        // TODO: Implémenter la génération de rapport de présence PDF
        return response()->json(['message' => 'Rapport de présence en cours de développement']);
    }
}