<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = Auth::user()->teacher;
        
        $data = [
            'teacher' => $teacher,
            'subjects' => Subject::where('teacher_id', $teacher->id)->get(),
            'recent_grades' => Grade::where('teacher_id', $teacher->id)
                ->with(['student.user', 'subject'])
                ->latest()
                ->limit(10)
                ->get(),
            'today_schedule' => Schedule::where('teacher_id', $teacher->id)
                ->where('day_of_week', strtolower(now()->format('l')))
                ->with(['subject', 'classe'])
                ->get(),
        ];

        return view('teacher.dashboard', $data);
    }

    public function grades()
    {
        $teacher = Auth::user()->teacher;
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        return view('teacher.grades.index', compact('subjects'));
    }

    public function subjectGrades(Subject $subject)
    {
        $this->authorize('manage', $subject);
        
        $grades = Grade::where('subject_id', $subject->id)
            ->with(['student.user'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('teacher.grades.subject', compact('subject', 'grades'));
    }

    public function storeGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'evaluation_type' => 'required|in:homework,quiz,exam,project',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'date' => 'required|date',
            'semester' => 'required|in:1,2',
            'academic_year' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        Grade::create(array_merge($request->all(), [
            'teacher_id' => Auth::user()->teacher->id,
        ]));

        return back()->with('success', 'Note ajoutée avec succès.');
    }

    public function updateGrade(Request $request, Grade $grade)
    {
        $this->authorize('update', $grade);

        $request->validate([
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'comments' => 'nullable|string',
        ]);

        $grade->update($request->only(['score', 'max_score', 'comments']));

        return back()->with('success', 'Note mise à jour avec succès.');
    }

    public function destroyGrade(Grade $grade)
    {
        $this->authorize('delete', $grade);
        
        $grade->delete();

        return back()->with('success', 'Note supprimée avec succès.');
    }

    public function attendance()
    {
        $teacher = Auth::user()->teacher;
        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        
        return view('teacher.attendance.index', compact('subjects'));
    }

    public function subjectAttendance(Subject $subject)
    {
        $this->authorize('manage', $subject);
        
        $students = Student::whereHas('classe.subjects', function ($query) use ($subject) {
            $query->where('subjects.id', $subject->id);
        })->with('user')->get();

        return view('teacher.attendance.subject', compact('subject', 'students'));
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
        ]);

        foreach ($request->attendances as $attendance) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendance['student_id'],
                    'subject_id' => $request->subject_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $attendance['status'],
                    'justification' => $attendance['justification'] ?? null,
                    'recorded_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Présences enregistrées avec succès.');
    }

    public function schedule()
    {
        $teacher = Auth::user()->teacher;
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'classe'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('teacher.schedule', compact('schedules'));
    }

    public function students()
    {
        $teacher = Auth::user()->teacher;
        $students = Student::whereHas('classe.subjects', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with(['user', 'classe'])->paginate(20);

        return view('teacher.students.index', compact('students'));
    }

    public function showStudent(Student $student)
    {
        $teacher = Auth::user()->teacher;
        
        // Vérifier que l'enseignant peut voir cet étudiant
        $hasAccess = $student->classe->subjects()->where('teacher_id', $teacher->id)->exists();
        
        if (!$hasAccess) {
            abort(403, 'Accès non autorisé à cet étudiant.');
        }

        $grades = Grade::where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->with('subject')
            ->latest()
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereHas('subject', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('subject')
            ->latest()
            ->limit(20)
            ->get();

        return view('teacher.students.show', compact('student', 'grades', 'attendances'));
    }
}