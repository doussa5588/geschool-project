<?php
// Fichier: app/Http/Controllers/Admin/StudentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Student;
use App\Models\User;
use App\Models\Level;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Afficher la liste des étudiants
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class.level', 'class.department']);
        //dd($query);
        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_number', 'like', "%{$search}%");
        }
        
        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        // Filtre par niveau
        if ($request->filled('level_id')) {
            $query->whereHas('classe', function($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
        
        $students = $query->paginate(20);
        
        // Données pour les filtres
        $classes = Classe::with('level')->orderBy('name')->get();
        $levels = Level::orderBy('order')->get();
        
        return view('admin.students.index', compact('students', 'classes', 'levels'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.students.create', compact('classes', 'departments'));
    }

    /**
     * Enregistrer un nouvel étudiant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string|max:500',
            'class_id' => 'required|exists:classes,id',
            'parent_contact' => 'nullable|string|max:20',
            // 'parent_email' => 'nullable|email',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_info' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();
            
            // Générer automatiquement le mot de passe et le numéro étudiant
            $password = 'unchk' . date('Y');
            $studentNumber = $this->generateStudentNumber();
            
            // Créer l'utilisateur
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($password),
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'is_active' => true
            ]);
            
            // Assigner le rôle étudiant
            $user->assignRole('student');
            
            // Gérer l'upload de photo
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }
            
            // Créer l'étudiant
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'class_id' => $validated['class_id'],
                'enrollment_date' => now(),
                'parent_contact' => $validated['parent_contact'],
                // 'parent_email' => $validated['parent_email'],
                'emergency_contact' => $validated['emergency_contact'],
                'medical_info' => $validated['medical_info'],
                'photo' => $photoPath,
                'is_active' => true
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.students.index')
                ->with('success', "Étudiant {$user->full_name} créé avec succès. Mot de passe temporaire: {$password}");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la création de l\'étudiant: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les détails d'un étudiant
     */
    public function show(Student $student)
    {
        $student->load([
            'user', 
            'class.level', 
            'class.department',
            'grades.subject',
            'attendances' => function($query) {
                $query->where('date', '>=', now()->startOfMonth());
            }
        ]);
        
        // Calculer les statistiques
        $stats = [
            'average_grade' => $student->grades()->avg('grade') ?? 0,
            'attendance_rate' => $this->calculateAttendanceRate($student),
            'total_absences' => $student->attendances()->where('status', 'absent')->count(),
            'subjects_count' => $student->class->subjects()->count()
        ];
        
        return view('admin.students.show', compact('student', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Student $student)
    {
        $student->load('user');
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.students.edit', compact('student', 'classes', 'departments'));
    }

    /**
     * Mettre à jour un étudiant
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string|max:500',
            'class_id' => 'required|exists:classes,id',
            'parent_contact' => 'nullable|string|max:20',
            // 'parent_email' => 'nullable|email',
            'emergency_contact' => 'nullable|string|max:255',
            'medical_info' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            // Mettre à jour l'utilisateur
            $student->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'is_active' => $validated['is_active'] ?? true
            ]);
            
            // Gérer l'upload de nouvelle photo
            $photoPath = $student->photo;
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo
                if ($photoPath && \Storage::disk('public')->exists($photoPath)) {
                    \Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }
            
            // Mettre à jour l'étudiant
            $student->update([
                'class_id' => $validated['class_id'],
                'parent_contact' => $validated['parent_contact'],
                //'parent_email' => $validated['parent_email'],
                'emergency_contact' => $validated['emergency_contact'],
                'medical_info' => $validated['medical_info'],
                'photo' => $photoPath,
                'is_active' => $validated['is_active'] ?? true
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.students.show', $student)
                ->with('success', 'Étudiant mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer un étudiant
     */
    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            
            // Archiver plutôt que supprimer
            $student->update(['is_active' => false]);
            $student->user->update(['is_active' => false]);
            
            DB::commit();
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Étudiant archivé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de l\'archivage: ' . $e->getMessage()]);
        }
    }

    /**
     * Réactiver un étudiant
     */
    public function restore($id)
    {
        $student = Student::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $student->update(['is_active' => true]);
            $student->user->update(['is_active' => true]);
            
            DB::commit();
            
            return back()->with('success', 'Étudiant réactivé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la réactivation: ' . $e->getMessage()]);
        }
    }

    /**
     * Export des étudiants en Excel
     */
    public function export(Request $request)
    {
        // TODO: Implémenter l'export Excel
        return response()->json(['message' => 'Export en cours de développement']);
    }

    /**
     * Import des étudiants depuis Excel
     */
    public function import(Request $request)
    {
        // TODO: Implémenter l'import Excel
        return response()->json(['message' => 'Import en cours de développement']);
    }

    /**
     * Générer un numéro étudiant unique
     */
    private function generateStudentNumber()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('student_number', 'desc')
            ->first();
        
        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculer le taux de présence d'un étudiant
     */
    private function calculateAttendanceRate(Student $student)
    {
        $totalAttendances = $student->attendances()->count();
        if ($totalAttendances === 0) return 100;
        
        $presentAttendances = $student->attendances()
            ->where('status', 'present')
            ->count();
            
        return round(($presentAttendances / $totalAttendances) * 100, 2);
    }
}