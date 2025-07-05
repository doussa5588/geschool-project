<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Classe;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'teacher.user', 'class.level']);

        // Filtres
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('level_id')) {
            $query->whereHas('class', function($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Récupération des données
        $schedules = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Organiser par jour de la semaine pour la vue hebdomadaire
        $schedulesByDay = $this->organizeSchedulesByDay($schedules);

        // Générer les créneaux horaires
        $timeSlots = $this->generateTimeSlots();

        // Données pour les filtres
        $levels = Level::where('is_active', true)->orderBy('order')->get();
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        // Statistiques
        $totalSchedules = $schedules->count();
        $conflicts = $this->detectConflicts($schedules);

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Emplois du temps', 'url' => '']
        ];

        return view('admin.schedules.index', compact(
            'schedules',
            'schedulesByDay',
            'timeSlots',
            'levels',
            'classes',
            'teachers',
            'subjects',
            'totalSchedules',
            'conflicts',
            'breadcrumbs'
        ));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Emplois du temps', 'url' => route('admin.schedules.index')],
            ['label' => 'Nouveau créneau', 'url' => '']
        ];

        return view('admin.schedules.create', compact('classes', 'teachers', 'subjects', 'breadcrumbs'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_id' => 'required|exists:classes,id',
            'room' => 'required|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|in:1,2',
        ]);

        // Vérifier les conflits
        if ($this->hasScheduleConflict($validated)) {
            return back()->withErrors(['conflict' => 'Il y a un conflit d\'horaire avec ce créneau.'])
                ->withInput();
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau créé avec succès.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['subject', 'teacher.user', 'class.level']);

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Emplois du temps', 'url' => route('admin.schedules.index')],
            ['label' => 'Détails créneau', 'url' => '']
        ];

        return view('admin.schedules.show', compact('schedule', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load(['subject', 'teacher.user', 'class.level']);
        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => route('admin.dashboard')],
            ['label' => 'Emplois du temps', 'url' => route('admin.schedules.index')],
            ['label' => 'Modifier créneau', 'url' => '']
        ];

        return view('admin.schedules.edit', compact('schedule', 'classes', 'teachers', 'subjects', 'breadcrumbs'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_id' => 'required|exists:classes,id',
            'room' => 'required|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'academic_year' => 'required|string|max:255',
            'semester' => 'required|in:1,2',
        ]);

        // Vérifier les conflits (en excluant le créneau actuel)
        if ($this->hasScheduleConflict($validated, $schedule->id)) {
            return back()->withErrors(['conflict' => 'Il y a un conflit d\'horaire avec ce créneau.'])
                ->withInput();
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau modifié avec succès.');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Créneau supprimé avec succès.');
    }

    /**
     * Get weekly schedule for a specific class
     */
    public function weekly(Request $request)
    {
        $classId = $request->get('class_id');
        $teacherId = $request->get('teacher_id');

        $query = Schedule::with(['subject', 'teacher.user', 'class.level']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->get();
        $schedulesByDay = $this->organizeSchedulesByDay($schedules);
        $timeSlots = $this->generateTimeSlots();

        $classes = Classe::with('level')->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();

        return view('admin.schedules.weekly', compact(
            'schedules',
            'schedulesByDay',
            'timeSlots',
            'classes',
            'teachers',
            'classId',
            'teacherId'
        ));
    }

    /**
     * Detect schedule conflicts
     */
    public function conflicts()
    {
        $schedules = Schedule::with(['subject', 'teacher.user', 'class.level'])->get();
        $conflicts = $this->detectConflicts($schedules);

        return response()->json([
            'conflicts_count' => count($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Organize schedules by day of week
     */
    private function organizeSchedulesByDay($schedules)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $organized = [];

        foreach ($days as $day) {
            $organized[$day] = $schedules->where('day_of_week', $day)->sortBy('start_time');
        }

        return $organized;
    }

    /**
     * Generate time slots for schedule grid
     */
    private function generateTimeSlots()
    {
        $slots = [];
        $start = Carbon::createFromTime(8, 0);
        $end = Carbon::createFromTime(18, 0);

        while ($start <= $end) {
            $slots[] = $start->format('H:i');
            $start->addHour();
        }

        return $slots;
    }

    /**
     * Check for schedule conflicts
     */
    private function hasScheduleConflict($data, $excludeId = null)
    {
        $query = Schedule::where('day_of_week', $data['day_of_week']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Conflit de salle
        $roomConflict = (clone $query)
            ->where('room', $data['room'])
            ->where(function($q) use ($data) {
                $q->where(function($q2) use ($data) {
                    $q2->where('start_time', '<=', $data['start_time'])
                       ->where('end_time', '>', $data['start_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '<', $data['end_time'])
                       ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '>=', $data['start_time'])
                       ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->exists();

        if ($roomConflict) return true;

        // Conflit d'enseignant
        $teacherConflict = (clone $query)
            ->where('teacher_id', $data['teacher_id'])
            ->where(function($q) use ($data) {
                $q->where(function($q2) use ($data) {
                    $q2->where('start_time', '<=', $data['start_time'])
                       ->where('end_time', '>', $data['start_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '<', $data['end_time'])
                       ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '>=', $data['start_time'])
                       ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->exists();

        if ($teacherConflict) return true;

        // Conflit de classe
        $classConflict = (clone $query)
            ->where('class_id', $data['class_id'])
            ->where(function($q) use ($data) {
                $q->where(function($q2) use ($data) {
                    $q2->where('start_time', '<=', $data['start_time'])
                       ->where('end_time', '>', $data['start_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '<', $data['end_time'])
                       ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function($q2) use ($data) {
                    $q2->where('start_time', '>=', $data['start_time'])
                       ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->exists();

        return $classConflict;
    }

    /**
     * Detect all conflicts in schedules
     */
    private function detectConflicts($schedules)
    {
        $conflicts = [];

        foreach ($schedules as $schedule) {
            $scheduleData = [
                'day_of_week' => $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'teacher_id' => $schedule->teacher_id,
                'class_id' => $schedule->class_id,
                'room' => $schedule->room,
            ];

            if ($this->hasScheduleConflict($scheduleData, $schedule->id)) {
                $conflicts[] = $schedule;
            }
        }

        return $conflicts;
    }

    /**
     * Get schedule by teacher
     */
    public function byTeacher(Teacher $teacher)
    {
        $schedules = Schedule::with(['subject', 'class.level'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $schedulesByDay = $this->organizeSchedulesByDay($schedules);
        $timeSlots = $this->generateTimeSlots();

        return view('admin.schedules.by-teacher', compact(
            'teacher',
            'schedules',
            'schedulesByDay',
            'timeSlots'
        ));
    }

    /**
     * Get schedule by class
     */
    public function byClass(Classe $class)
    {
        $schedules = Schedule::with(['subject', 'teacher.user'])
            ->where('class_id', $class->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $schedulesByDay = $this->organizeSchedulesByDay($schedules);
        $timeSlots = $this->generateTimeSlots();

        return view('admin.schedules.by-class', compact(
            'class',
            'schedules',
            'schedulesByDay',
            'timeSlots'
        ));
    }

    /**
     * Bulk create schedules
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.subject_id' => 'required|exists:subjects,id',
            'schedules.*.teacher_id' => 'required|exists:teachers,id',
            'schedules.*.class_id' => 'required|exists:classes,id',
            'schedules.*.room' => 'required|string|max:255',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
            'schedules.*.academic_year' => 'required|string|max:255',
            'schedules.*.semester' => 'required|in:1,2',
        ]);

        $created = 0;
        $conflicts = 0;

        foreach ($validated['schedules'] as $scheduleData) {
            if (!$this->hasScheduleConflict($scheduleData)) {
                Schedule::create($scheduleData);
                $created++;
            } else {
                $conflicts++;
            }
        }

        $message = "Créneaux créés: {$created}";
        if ($conflicts > 0) {
            $message .= ", Conflits détectés: {$conflicts}";
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', $message);
    }
}