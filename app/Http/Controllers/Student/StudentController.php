<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Message;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user()->student;
        
        $data = [
            'student' => $student,
            'recent_grades' => Grade::where('student_id', $student->id)
                ->with('subject')
                ->latest()
                ->limit(5)
                ->get(),
            'attendance_rate' => $this->calculateAttendanceRate($student->id),
            'schedule' => Schedule::where('class_id', $student->class_id)
                ->with(['subject', 'teacher.user'])
                ->get(),
            'unread_messages' => Message::where('recipient_id', Auth::id())
                ->where('is_read', false)
                ->count(),
        ];

        return view('student.dashboard', $data);
    }

    public function grades()
    {
        $student = Auth::user()->student;
        $grades = Grade::where('student_id', $student->id)
            ->with(['subject', 'teacher.user'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('student.grades', compact('grades'));
    }

    public function attendance()
    {
        $student = Auth::user()->student;
        $attendances = Attendance::where('student_id', $student->id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('student.attendance', compact('attendances'));
    }

    public function schedule()
    {
        $student = Auth::user()->student;
        $schedules = Schedule::where('class_id', $student->class_id)
            ->with(['subject', 'teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('student.schedule', compact('schedules'));
    }

    public function bulletin($semester, $year, GradeService $gradeService)
    {
        $student = Auth::user()->student;
        $bulletin = $gradeService->generateBulletin($student->id, $semester, $year);

        return view('student.bulletin', compact('bulletin'));
    }

    private function calculateAttendanceRate($studentId)
    {
        $total = Attendance::where('student_id', $studentId)->count();
        $present = Attendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();

        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }


    
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            // Informations utilisateur
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|max:2048',
            
            // Informations étudiant
            'student_number' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|string',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,suspended',
            'is_active' => 'boolean',
            
            // Contacts d'urgence
            'parent_contact' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            
            // Informations médicales
            'medical_info' => 'nullable|string|max:1000',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string|max:500',
            'medications' => 'nullable|string|max:500',
            'doctor_name' => 'nullable|string|max:255',
            'doctor_phone' => 'nullable|string|max:20',
        ]);

        try {
            \DB::transaction(function () use ($validated, $student, $request) {
                // Combiner prénom et nom
                $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);
                
                // Mettre à jour les informations utilisateur
                $userData = [
                    'name' => $fullName,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'],
                    'address' => $validated['address'] ?? null,
                ];

                // Ajouter le genre si la colonne existe
                try {
                    if (\Schema::hasColumn('users', 'gender')) {
                        $userData['gender'] = $validated['gender'];
                    }
                } catch (\Exception $e) {
                    // Si la colonne n'existe pas, on l'ignore
                    \Log::info('Colonne gender non trouvée dans la table users');
                }

                // Gérer la photo de profil
                if ($request->hasFile('profile_photo')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($student->user->profile_photo) {
                        \Storage::delete($student->user->profile_photo);
                    }
                    
                    $path = $request->file('profile_photo')->store('profile_photos', 'public');
                    $userData['profile_photo'] = $path;
                }

                $student->user->update($userData);

                // Mettre à jour les informations étudiant
                $studentData = [
                    'student_number' => $validated['student_number'],
                    'class_id' => $validated['class_id'],
                    'academic_year' => $validated['academic_year'],
                    'enrollment_date' => $validated['enrollment_date'],
                    'status' => $validated['status'],
                    'is_active' => $request->has('is_active') ? 1 : 0,
                ];

                // Ajouter les champs optionnels seulement s'ils sont validés
                if (isset($validated['parent_contact'])) {
                    $studentData['parent_contact'] = $validated['parent_contact'];
                }
                
                if (isset($validated['emergency_contact'])) {
                    $studentData['emergency_contact'] = $validated['emergency_contact'];
                }

                // Informations médicales (seulement si les colonnes existent)
                $medicalFields = ['medical_info', 'blood_type', 'allergies', 'medications', 'doctor_name', 'doctor_phone'];
                
                foreach ($medicalFields as $field) {
                    if (isset($validated[$field]) && \Schema::hasColumn('students', $field)) {
                        $studentData[$field] = $validated[$field];
                    }
                }

                $student->update($studentData);
            });

            return redirect()
                ->route('admin.students.show', $student)
                ->with('success', 'Étudiant mis à jour avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'étudiant: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }
}