<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ClassModel as ClassRoom;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['class', 'teacher', 'subject', 'room']);
        
        // Filtres
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        $schedules = $query->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        
        $classes = ClassRoom::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        
        return view('schedules.index', compact('schedules', 'classes', 'teachers'));
    }
    
    public function create()
    {
        $classes = ClassRoom::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        $subjects = Subject::all();
        $rooms = Room::where('is_available', true)->get();
        
        return view('schedules.create', compact('classes', 'teachers', 'subjects', 'rooms'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'room_id' => 'required|exists:rooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:lecture,lab,tutorial',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2'
        ]);
        
        // Vérifier les conflits
        if ($this->hasConflict($validated)) {
            return back()->with('error', 'Il y a un conflit d\'horaire.')
                ->withInput();
        }
        
        Schedule::create($validated);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Horaire créé avec succès.');
    }
    
    public function edit(Schedule $schedule)
    {
        $classes = ClassRoom::where('status', 'active')->get();
        $teachers = Teacher::where('status', 'active')->get();
        $subjects = Subject::all();
        $rooms = Room::where('is_available', true)->get();
        
        return view('schedules.edit', compact('schedule', 'classes', 'teachers', 'subjects', 'rooms'));
    }
    
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'room_id' => 'required|exists:rooms,id',
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:lecture,lab,tutorial',
            'is_active' => 'boolean'
        ]);
        
        // Vérifier les conflits (sauf avec lui-même)
        if ($this->hasConflict($validated, $schedule->id)) {
            return back()->with('error', 'Il y a un conflit d\'horaire.')
                ->withInput();
        }
        
        $schedule->update($validated);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Horaire mis à jour avec succès.');
    }
    
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        
        return redirect()->route('schedules.index')
            ->with('success', 'Horaire supprimé avec succès.');
    }
    
    public function weekly(Request $request)
    {
        $classId = $request->get('class_id');
        $teacherId = $request->get('teacher_id');
        
        $query = Schedule::with(['class', 'teacher', 'subject', 'room'])
            ->where('is_active', true);
        
        if ($classId) {
            $query->where('class_id', $classId);
        }
        
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }
        
        $schedules = $query->get()->groupBy('day_of_week');
        
        // Créer la grille horaire
        $timeSlots = $this->generateTimeSlots();
        $weekDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        return view('schedules.weekly', compact('schedules', 'timeSlots', 'weekDays'));
    }
    
    public function conflicts()
    {
        $conflicts = [];
        
        $schedules = Schedule::where('is_active', true)->get();
        
        foreach ($schedules as $schedule) {
            // Conflits de salle
            $roomConflicts = Schedule::where('id', '!=', $schedule->id)
                ->where('room_id', $schedule->room_id)
                ->where('day_of_week', $schedule->day_of_week)
                ->where('is_active', true)
                ->where(function($q) use ($schedule) {
                    $q->whereBetween('start_time', [$schedule->start_time, $schedule->end_time])
                      ->orWhereBetween('end_time', [$schedule->start_time, $schedule->end_time]);
                })
                ->get();
            
            if ($roomConflicts->count() > 0) {
                $conflicts[] = [
                    'type' => 'room',
                    'schedule' => $schedule,
                    'conflicts' => $roomConflicts
                ];
            }
            
            // Conflits d'enseignant
            $teacherConflicts = Schedule::where('id', '!=', $schedule->id)
                ->where('teacher_id', $schedule->teacher_id)
                ->where('day_of_week', $schedule->day_of_week)
                ->where('is_active', true)
                ->where(function($q) use ($schedule) {
                    $q->whereBetween('start_time', [$schedule->start_time, $schedule->end_time])
                      ->orWhereBetween('end_time', [$schedule->start_time, $schedule->end_time]);
                })
                ->get();
            
            if ($teacherConflicts->count() > 0) {
                $conflicts[] = [
                    'type' => 'teacher',
                    'schedule' => $schedule,
                    'conflicts' => $teacherConflicts
                ];
            }
        }
        
        return view('schedules.conflicts', compact('conflicts'));
    }
    
    private function hasConflict($data, $excludeId = null)
    {
        $query = Schedule::where('day_of_week', $data['day_of_week'])
            ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        // Conflit de salle
        $roomConflict = (clone $query)
            ->where('room_id', $data['room_id'])
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($q2) use ($data) {
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })
            ->exists();
        
        if ($roomConflict) return true;
        
        // Conflit d'enseignant
        $teacherConflict = (clone $query)
            ->where('teacher_id', $data['teacher_id'])
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($q2) use ($data) {
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })
            ->exists();
        
        if ($teacherConflict) return true;
        
        // Conflit de classe
        $classConflict = (clone $query)
            ->where('class_id', $data['class_id'])
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($q2) use ($data) {
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })
            ->exists();
        
        return $classConflict;
    }
    
    private function generateTimeSlots()
    {
        $slots = [];
        $start = Carbon::createFromTime(8, 0);
        $end = Carbon::createFromTime(18, 0);
        
        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addHour();
        }
        
        return $slots;
    }
}
