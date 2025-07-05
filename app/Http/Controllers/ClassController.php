<?php

namespace App\Http\Controllers;

use App\Models\ClassModel as ClassRoom;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::with(['teacher', 'students']);
        
        // Filtres
        if ($request->has('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }
        
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }
        
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        
        $classes = $query->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('section')
            ->paginate(20);
        
        return view('classes.index', compact('classes'));
    }
    
    public function create()
    {
        $teachers = Teacher::where('status', 'active')->get();
        $subjects = Subject::all();
        
        return view('classes.create', compact('teachers', 'subjects'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|between:1,12',
            'section' => 'required|string|max:10',
            'academic_year' => 'required|string|max:9',
            'teacher_id' => 'required|exists:teachers,id',
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'subjects' => 'required|array',
            'fee_amount' => 'required|numeric|min:0'
        ]);
        
        $class = ClassRoom::create($validated);
        
        // Attacher les matières avec les heures
        $subjectsData = [];
        foreach ($request->subjects as $subjectId) {
            $subjectsData[$subjectId] = [
                'hours_per_week' => $request->input("subject_hours.{$subjectId}", 3)
            ];
        }
        $class->subjects()->attach($subjectsData);
        
        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès.');
    }
    
    public function show(ClassRoom $class)
    {
        $class->load(['teacher', 'students', 'subjects', 'schedules']);
        
        // Statistiques
        $stats = [
            'total_students' => $class->students->count(),
            'boys_count' => $class->students->where('gender', 'male')->count(),
            'girls_count' => $class->students->where('gender', 'female')->count(),
            'avg_attendance' => $class->attendances()
                ->where('date', '>=', now()->subDays(30))
                ->avg('present_count') ?? 0,
            'total_subjects' => $class->subjects->count(),
            'weekly_hours' => $class->subjects->sum('pivot.hours_per_week')
        ];
        
        // Performance moyenne
        $stats['avg_grade'] = $class->students()
            ->join('grades', 'students.id', '=', 'grades.student_id')
            ->where('grades.academic_year', $class->academic_year)
            ->avg('grades.grade') ?? 0;
        
        return view('classes.show', compact('class', 'stats'));
    }
    
    public function edit(ClassRoom $class)
    {
        $teachers = Teacher::where('status', 'active')->get();
        $subjects = Subject::all();
        $students = Student::where('status', 'active')
            ->whereNull('class_id')
            ->orWhere('class_id', $class->id)
            ->get();
        
        return view('classes.edit', compact('class', 'teachers', 'subjects', 'students'));
    }
    
    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|between:1,12',
            'section' => 'required|string|max:10',
            'teacher_id' => 'required|exists:teachers,id',
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'subjects' => 'required|array',
            'fee_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);
        
        $class->update($validated);
        
        // Mettre à jour les matières avec les heures
        $subjectsData = [];
        foreach ($request->subjects as $subjectId) {
            $subjectsData[$subjectId] = [
                'hours_per_week' => $request->input("subject_hours.{$subjectId}", 3)
            ];
        }
        $class->subjects()->sync($subjectsData);
        
        return redirect()->route('classes.show', $class)
            ->with('success', 'Classe mise à jour avec succès.');
    }
    
    public function destroy(ClassRoom $class)
    {
        // Vérifier s'il y a des étudiants
        if ($class->students()->exists()) {
            return back()->with('error', 'Impossible de supprimer une classe avec des étudiants.');
        }
        
        $class->delete();
        
        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }
    
    public function students(ClassRoom $class)
    {
        $students = $class->students()
            ->with(['attendances' => function($q) {
                $q->where('date', '>=', now()->subDays(30));
            }])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        return view('classes.students', compact('class', 'students'));
    }
    
    public function addStudents(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);
        
        // Vérifier la capacité
        $currentCount = $class->students()->count();
        $newCount = count($validated['student_ids']);
        
        if (($currentCount + $newCount) > $class->capacity) {
            return back()->with('error', 'La capacité de la classe sera dépassée.');
        }
        
        // Ajouter les étudiants
        Student::whereIn('id', $validated['student_ids'])
            ->update(['class_id' => $class->id]);
        
        return redirect()->route('classes.students', $class)
            ->with('success', 'Étudiants ajoutés avec succès.');
    }
}