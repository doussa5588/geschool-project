<?php
// Fichier: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Department;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     */
    public function index(Request $request)
    {
        // Statistiques générales
        $generalStats = $this->getGeneralStats();
        
        // Statistiques académiques
        $academicStats = $this->getAcademicStats();
        
        // Statistiques de présence
        $attendanceStats = $this->getAttendanceStats();
        
        // Graphiques et tendances
        $charts = $this->getChartsData();
        
        // Activités récentes
        $recentActivities = $this->getRecentActivities();
        
        // Alertes et notifications
        $alerts = $this->getSystemAlerts();
        
        // Emplois du temps du jour
        $todaySchedules = $this->getTodaySchedules();
        
        // Événements à venir
        $upcomingEvents = $this->getUpcomingEvents();
        
        return view('admin.dashboard.index', compact(
            'generalStats',
            'academicStats', 
            'attendanceStats',
            'charts',
            'recentActivities',
            'alerts',
            'todaySchedules',
            'upcomingEvents'
        ));
    }

    /**
     * API pour les statistiques du tableau de bord (AJAX)
     */
    public function apiStats(Request $request)
    {
        $type = $request->get('type', 'general');
        
        switch ($type) {
            case 'enrollment':
                return response()->json($this->getEnrollmentTrends());
            case 'grades':
                return response()->json($this->getGradesTrends());
            case 'attendance':
                return response()->json($this->getAttendanceTrends());
            case 'teachers':
                return response()->json($this->getTeachersStats());
            case 'classes':
                return response()->json($this->getClassesStats());
            default:
                return response()->json($this->getGeneralStats());
        }
    }

    /**
     * Obtenir les statistiques générales
     */
    private function getGeneralStats()
    {
        $currentYear = now()->year;
        $currentAcademicYear = $this->getCurrentAcademicYear();
        
        return [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
            'total_classes' => ClassModel::where('is_active', true)->count(),
            'total_subjects' => Subject::where('is_active', true)->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_levels' => Level::where('is_active', true)->count(),
            
            // Nouvelles inscriptions ce mois
            'new_students_this_month' => Student::whereMonth('created_at', now()->month)
                ->whereYear('created_at', $currentYear)
                ->count(),
            
            // Nouveaux enseignants ce mois
            'new_teachers_this_month' => Teacher::whereMonth('created_at', now()->month)
                ->whereYear('created_at', $currentYear)
                ->count(),
            
            // Évolution par rapport au mois précédent
            'students_growth' => $this->calculateGrowthRate('students'),
            'teachers_growth' => $this->calculateGrowthRate('teachers'),
            'classes_growth' => $this->calculateGrowthRate('classes'),
            
            // Capacité globale
            'total_capacity' => ClassModel::where('is_active', true)->sum('capacity'),
            'capacity_usage' => $this->calculateCapacityUsage(),
            
            // Utilisateurs actifs
            'active_users_today' => User::whereDate('last_login_at', now()->toDateString())->count(),
            'active_users_week' => User::where('last_login_at', '>=', now()->subWeek())->count(),
        ];
    }

    /**
     * Obtenir les statistiques académiques
     */
    private function getAcademicStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        return [
            // Notes et évaluations
            'total_grades' => Grade::count(),
            'grades_this_month' => Grade::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
            'average_grade_global' => round(Grade::avg('grade'), 2),
            'average_grade_this_month' => round(
                Grade::whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->avg('grade'), 2
            ),
            
            // Distribution des notes
            'grade_distribution' => $this->getGradeDistribution(),
            
            // Emplois du temps
            'total_schedules_week' => Schedule::whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            
            'completed_schedules_week' => Schedule::whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->where('status', 'completed')->count(),
            
            // Matières les plus enseignées
            'top_subjects' => $this->getTopSubjects(),
            
            // Classes les plus actives
            'most_active_classes' => $this->getMostActiveClasses(),
            
            // Performance par département
            'departments_performance' => $this->getDepartmentsPerformance(),
        ];
    }

    /**
     * Obtenir les statistiques de présence
     */
    private function getAttendanceStats()
    {
        $today = now()->toDateString();
        $thisWeek = [now()->startOfWeek(), now()->endOfWeek()];
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];
        
        return [
            // Présences aujourd'hui
            'total_attendance_today' => Attendance::whereDate('date', $today)->count(),
            'present_today' => Attendance::whereDate('date', $today)
                ->where('status', 'present')->count(),
            'absent_today' => Attendance::whereDate('date', $today)
                ->where('status', 'absent')->count(),
            'late_today' => Attendance::whereDate('date', $today)
                ->where('status', 'late')->count(),
            
            // Taux de présence
            'attendance_rate_today' => $this->calculateAttendanceRate($today, $today),
            'attendance_rate_week' => $this->calculateAttendanceRate($thisWeek[0], $thisWeek[1]),
            'attendance_rate_month' => $this->calculateAttendanceRate($thisMonth[0], $thisMonth[1]),
            
            // Tendances de présence
            'attendance_trend' => $this->getAttendanceTrend(),
            
            // Étudiants avec problèmes d'assiduité
            'low_attendance_students' => $this->getLowAttendanceStudents(),
            
            // Classes avec meilleure/plus faible présence
            'best_attendance_classes' => $this->getBestAttendanceClasses(),
            'worst_attendance_classes' => $this->getWorstAttendanceClasses(),
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     */
    private function getChartsData()
    {
        return [
            // Évolution des inscriptions sur 12 mois
            'enrollment_evolution' => $this->getEnrollmentEvolution(),
            
            // Distribution des notes par matière
            'grades_by_subject' => $this->getGradesBySubject(),
            
            // Présences par jour de la semaine
            'attendance_by_day' => $this->getAttendanceByDay(),
            
            // Répartition des étudiants par niveau
            'students_by_level' => $this->getStudentsByLevel(),
            
            // Répartition des étudiants par département
            'students_by_department' => $this->getStudentsByDepartment(),
            
            // Évolution des moyennes mensuelles
            'monthly_averages' => $this->getMonthlyAverages(),
            
            // Charge de travail des enseignants
            'teachers_workload' => $this->getTeachersWorkload(),
        ];
    }

    /**
     * Obtenir les activités récentes
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // Nouveaux étudiants (derniers 7 jours)
        $newStudents = Student::with('user')
            ->where('created_at', '>=', now()->subWeek())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($student) {
                return [
                    'type' => 'student_enrolled',
                    'title' => 'Nouvel étudiant inscrit',
                    'description' => $student->user->full_name . ' (' . $student->student_number . ')',
                    'date' => $student->created_at,
                    'icon' => 'user-plus',
                    'color' => 'success'
                ];
            });
        
        // Nouveaux enseignants (derniers 7 jours)
        $newTeachers = Teacher::with('user')
            ->where('created_at', '>=', now()->subWeek())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($teacher) {
                return [
                    'type' => 'teacher_hired',
                    'title' => 'Nouvel enseignant embauché',
                    'description' => $teacher->user->full_name . ' (' . $teacher->employee_number . ')',
                    'date' => $teacher->created_at,
                    'icon' => 'user-check',
                    'color' => 'info'
                ];
            });
        
        // Notes récentes (dernières 24h)
        $recentGrades = Grade::with(['student.user', 'subject'])
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($grade) {
                return [
                    'type' => 'grade_added',
                    'title' => 'Note ajoutée',
                    'description' => $grade->student->user->full_name . ' - ' . 
                                   $grade->subject->name . ': ' . $grade->grade . '/20',
                    'date' => $grade->created_at,
                    'icon' => 'edit',
                    'color' => 'primary'
                ];
            });
        
        return $activities->merge($newStudents)
            ->merge($newTeachers)
            ->merge($recentGrades)
            ->sortByDesc('date')
            ->take(15)
            ->values();
    }

    /**
     * Obtenir les alertes système
     */
    private function getSystemAlerts()
    {
        $alerts = collect();
        
        // Classes dépassant la capacité
        $overcrowdedClasses = ClassModel::withCount('students')
            ->having('students_count', '>', DB::raw('capacity'))
            ->get();
        
        foreach ($overcrowdedClasses as $class) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Capacité dépassée',
                'message' => "La classe {$class->name} a {$class->students_count} étudiants pour {$class->capacity} places.",
                'action' => route('admin.classes.show', $class)
            ]);
        }
        
        // Étudiants avec faible taux de présence
        $lowAttendanceCount = $this->getLowAttendanceStudents()->count();
        if ($lowAttendanceCount > 0) {
            $alerts->push([
                'type' => 'danger',
                'title' => 'Problèmes d\'assiduité',
                'message' => "{$lowAttendanceCount} étudiant(s) ont un taux de présence inférieur à 75%.",
                'action' => route('admin.reports.attendance')
            ]);
        }
        
        // Emplois du temps sans salle assignée
        $schedulesWithoutRoom = Schedule::whereNull('room')
            ->where('date', '>=', now())
            ->count();
        
        if ($schedulesWithoutRoom > 0) {
            $alerts->push([
                'type' => 'info',
                'title' => 'Salles non assignées',
                'message' => "{$schedulesWithoutRoom} cours n'ont pas de salle assignée.",
                'action' => route('admin.schedules.index')
            ]);
        }
        
        // Enseignants avec trop de charge
        $overloadedTeachers = $this->getOverloadedTeachers();
        if ($overloadedTeachers > 0) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Surcharge enseignants',
                'message' => "{$overloadedTeachers} enseignant(s) ont plus de 25h de cours cette semaine.",
                'action' => route('admin.teachers.index')
            ]);
        }
        
        return $alerts->take(5);
    }

    /**
     * Obtenir les emplois du temps d'aujourd'hui
     */
    private function getTodaySchedules()
    {
        return Schedule::with(['class.level', 'teacher.user', 'subject'])
            ->whereDate('date', now())
            ->orderBy('start_time')
            ->limit(8)
            ->get()
            ->map(function($schedule) {
                return [
                    'time' => $schedule->start_time . ' - ' . $schedule->end_time,
                    'subject' => $schedule->subject->name,
                    'class' => $schedule->class->name,
                    'teacher' => $schedule->teacher->user->full_name,
                    'room' => $schedule->room ?? 'Non assignée',
                    'status' => $schedule->status
                ];
            });
    }

    /**
     * Obtenir les événements à venir
     */
    private function getUpcomingEvents()
    {
        // TODO: Implémenter un système d'événements/calendrier
        return collect([
            [
                'title' => 'Réunion pédagogique',
                'date' => now()->addDays(2),
                'type' => 'meeting',
                'description' => 'Réunion mensuelle avec tous les enseignants'
            ],
            [
                'title' => 'Fin du trimestre',
                'date' => now()->addWeeks(2),
                'type' => 'academic',
                'description' => 'Clôture du premier trimestre'
            ],
            [
                'title' => 'Conseils de classe',
                'date' => now()->addWeeks(3),
                'type' => 'evaluation',
                'description' => 'Conseils de classe pour toutes les classes'
            ]
        ]);
    }

    /**
     * Calculer le taux de croissance
     */
    private function calculateGrowthRate($entity)
    {
        $currentMonth = now()->month;
        $previousMonth = now()->subMonth()->month;
        $year = now()->year;
        
        $model = match($entity) {
            'students' => Student::class,
            'teachers' => Teacher::class,
            'classes' => ClassModel::class,
            default => Student::class
        };
        
        $current = $model::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $year)
            ->count();
            
        $previous = $model::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $year)
            ->count();
        
        if ($previous == 0) return $current > 0 ? 100 : 0;
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculer l'utilisation de la capacité
     */
    private function calculateCapacityUsage()
    {
        $totalCapacity = ClassModel::where('is_active', true)->sum('capacity');
        $totalStudents = Student::where('is_active', true)->count();
        
        if ($totalCapacity == 0) return 0;
        
        return round(($totalStudents / $totalCapacity) * 100, 1);
    }

    /**
     * Obtenir la distribution des notes
     */
    private function getGradeDistribution()
    {
        return [
            'excellent' => Grade::whereBetween('grade', [16, 20])->count(),
            'good' => Grade::whereBetween('grade', [14, 15.99])->count(),
            'average' => Grade::whereBetween('grade', [12, 13.99])->count(),
            'below_average' => Grade::whereBetween('grade', [10, 11.99])->count(),
            'poor' => Grade::where('grade', '<', 10)->count(),
        ];
    }

    /**
     * Calculer le taux de présence
     */
    private function calculateAttendanceRate($startDate, $endDate)
    {
        $totalAttendances = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        
        if ($totalAttendances == 0) return 100;
        
        $presentAttendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();
        
        return round(($presentAttendances / $totalAttendances) * 100, 1);
    }

    /**
     * Obtenir les étudiants avec faible présence
     */
    private function getLowAttendanceStudents()
    {
        return Student::with('user')
            ->whereHas('attendances', function($query) {
                $query->selectRaw('student_id, 
                    (SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as attendance_rate')
                    ->groupBy('student_id')
                    ->havingRaw('attendance_rate < 75');
            })
            ->limit(10)
            ->get();
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
     * Méthodes supplémentaires pour les graphiques et statistiques
     */
    
    private function getEnrollmentEvolution()
    {
        return Student::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function getStudentsByLevel()
    {
        return Level::withCount(['classes as students_count' => function($query) {
            $query->join('students', 'classes.id', '=', 'students.class_id')
                  ->where('students.is_active', true);
        }])
        ->orderBy('order')
        ->get();
    }

    private function getStudentsByDepartment()
    {
        return Department::withCount(['classes as students_count' => function($query) {
            $query->join('students', 'classes.id', '=', 'students.class_id')
                  ->where('students.is_active', true);
        }])
        ->orderBy('name')
        ->get();
    }

    private function getTopSubjects()
    {
        return Subject::withCount('schedules')
            ->orderBy('schedules_count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getMostActiveClasses()
    {
        return ClassModel::withCount('schedules')
            ->orderBy('schedules_count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getDepartmentsPerformance()
    {
        return Department::with(['classes.students.grades'])
            ->get()
            ->map(function($department) {
                $allGrades = $department->classes->flatMap(function($class) {
                    return $class->students->flatMap(function($student) {
                        return $student->grades;
                    });
                });
                
                return [
                    'name' => $department->name,
                    'average_grade' => $allGrades->avg('grade') ?? 0,
                    'total_students' => $department->classes->sum(function($class) {
                        return $class->students->count();
                    })
                ];
            });
    }

    private function getAttendanceTrend()
    {
        return Attendance::selectRaw('DATE(date) as date, 
            (SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate')
            ->where('date', '>=', now()->subWeeks(4))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getBestAttendanceClasses()
    {
        return ClassModel::with(['students.attendances'])
            ->get()
            ->map(function($class) {
                $totalAttendances = $class->students->sum(function($student) {
                    return $student->attendances->count();
                });
                
                $presentAttendances = $class->students->sum(function($student) {
                    return $student->attendances->where('status', 'present')->count();
                });
                
                $rate = $totalAttendances > 0 ? ($presentAttendances / $totalAttendances) * 100 : 100;
                
                return [
                    'class' => $class,
                    'attendance_rate' => $rate
                ];
            })
            ->sortByDesc('attendance_rate')
            ->take(3);
    }

    private function getWorstAttendanceClasses()
    {
        return $this->getBestAttendanceClasses()->reverse()->take(3);
    }

    private function getOverloadedTeachers()
    {
        return Teacher::whereHas('schedules', function($query) {
            $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        }, '>', 25)->count();
    }

    private function getGradesBySubject()
    {
        return Subject::withAvg('grades', 'grade')
            ->having('grades_avg_grade', '>', 0)
            ->orderBy('grades_avg_grade', 'desc')
            ->get();
    }

    private function getAttendanceByDay()
    {
        return Attendance::selectRaw('DAYOFWEEK(date) as day_of_week, 
            (SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate')
            ->where('date', '>=', now()->subMonth())
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();
    }

    private function getMonthlyAverages()
    {
        return Grade::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, AVG(grade) as average')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function getTeachersWorkload()
    {
        return Teacher::with('user')
            ->withCount(['schedules as weekly_hours' => function($query) {
                $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->orderBy('weekly_hours', 'desc')
            ->limit(10)
            ->get();
    }
}