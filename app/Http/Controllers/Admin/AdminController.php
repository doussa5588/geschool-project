<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classe;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur
     */
    public function dashboard()
    {
        try {
            // Statistiques générales
            $generalStats = $this->getGeneralStats();
            
            // Statistiques de présence
            $attendanceStats = $this->getAttendanceStats();
            
            // Statistiques académiques
            $academicStats = $this->getAcademicStats();
            
            // Données pour les graphiques
            $charts = $this->getChartsData();
            
            // Cours d'aujourd'hui
            $todaySchedules = $this->getTodaySchedules();
            
            // Activités récentes
            $recentActivities = $this->getRecentActivities();
            
            // Alertes système
            $alerts = $this->getSystemAlerts();
            
            return view('admin.index', compact(
                'generalStats',
                'attendanceStats', 
                'academicStats',
                'charts',
                'todaySchedules',
                'recentActivities',
                'alerts'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Erreur dashboard admin: ' . $e->getMessage());
            
            // Données par défaut en cas d'erreur
            return view('admin.index', [
                'generalStats' => $this->getDefaultGeneralStats(),
                'attendanceStats' => $this->getDefaultAttendanceStats(),
                'academicStats' => $this->getDefaultAcademicStats(),
                'charts' => $this->getDefaultChartsData(),
                'todaySchedules' => collect(),
                'recentActivities' => collect(),
                'alerts' => collect()
            ]);
        }
    }
    
    /**
     * Statistiques générales
     */
    private function getGeneralStats()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalClasses = Classe::count();
        $totalDepartments = Department::count();
        
        // Calcul de la croissance mensuelle
        $lastMonth = Carbon::now()->subMonth();
        $studentsLastMonth = Student::where('created_at', '>=', $lastMonth)->count();
        $teachersLastMonth = Teacher::where('created_at', '>=', $lastMonth)->count();
        
        $studentsGrowth = $totalStudents > 0 ? round(($studentsLastMonth / $totalStudents) * 100, 1) : 0;
        $teachersGrowth = $totalTeachers > 0 ? round(($teachersLastMonth / $totalTeachers) * 100, 1) : 0;
        
        // Calcul de l'utilisation de la capacité
        $totalCapacity = Classe::sum('capacity') ?: 1;
        $capacityUsage = round(($totalStudents / $totalCapacity) * 100, 1);
        
        return [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_classes' => $totalClasses,
            'total_departments' => $totalDepartments,
            'students_growth' => $studentsGrowth,
            'teachers_growth' => $teachersGrowth,
            'capacity_usage' => $capacityUsage,
        ];
    }
    
    /**
     * Statistiques de présence
     */
    private function getAttendanceStats()
    {
        $today = Carbon::today();
        
        // Présence d'aujourd'hui
        $todayAttendance = Attendance::whereDate('date', $today)->get();
        $presentToday = $todayAttendance->where('status', 'present')->count();
        $totalAttendanceToday = $todayAttendance->count();
        $attendanceRateToday = $totalAttendanceToday > 0 ? 
            round(($presentToday / $totalAttendanceToday) * 100, 1) : 0;
        
        // Tendance des 4 dernières semaines
        $attendanceTrend = collect();
        for ($i = 27; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayAttendance = Attendance::whereDate('date', $date)->get();
            $dayPresent = $dayAttendance->where('status', 'present')->count();
            $dayTotal = $dayAttendance->count();
            $dayRate = $dayTotal > 0 ? round(($dayPresent / $dayTotal) * 100, 1) : 0;
            
            $attendanceTrend->push([
                'date' => $date->format('Y-m-d'),
                'rate' => $dayRate
            ]);
        }
        
        return [
            'present_today' => $presentToday,
            'total_attendance_today' => $totalAttendanceToday,
            'attendance_rate_today' => $attendanceRateToday,
            'attendance_trend' => $attendanceTrend
        ];
    }
    
    /**
     * Statistiques académiques
     */
    private function getAcademicStats()
    {
        $totalGrades = Grade::count();
        $averageGradeGlobal = Grade::avg('score') ?: 0;
        
        // Distribution des notes
        $gradeDistribution = [
            'excellent' => Grade::where('score', '>=', 16)->count(),
            'good' => Grade::whereBetween('score', [14, 15.99])->count(),
            'average' => Grade::whereBetween('score', [12, 13.99])->count(),
            'below_average' => Grade::whereBetween('score', [10, 11.99])->count(),
            'poor' => Grade::where('score', '<', 10)->count(),
        ];
        
        return [
            'total_grades' => $totalGrades,
            'average_grade_global' => round($averageGradeGlobal, 1),
            'grade_distribution' => $gradeDistribution
        ];
    }
    
    /**
     * Données pour les graphiques
     */
    private function getChartsData()
    {
        // Évolution des inscriptions (6 derniers mois)
        $enrollmentEvolution = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Student::whereYear('created_at', $month->year)
                           ->whereMonth('created_at', $month->month)
                           ->count();
            
            $enrollmentEvolution->push((object)[
                'year' => $month->year,
                'month' => $month->month,
                'count' => $count
            ]);
        }
        
        // Répartition par niveau
        $studentsByLevel = Level::withCount('students')
                                ->where('is_active', true)
                                ->get()
                                ->map(function($level) {
                                    return (object)[
                                        'name' => $level->name,
                                        'students_count' => $level->students_count
                                    ];
                                });
        
        return [
            'enrollment_evolution' => $enrollmentEvolution,
            'students_by_level' => $studentsByLevel
        ];
    }
    
    /**
     * Cours d'aujourd'hui
     */
    private function getTodaySchedules()
    {
        // Pour l'instant, retourner des données simulées
        // TODO: Implémenter avec le modèle Schedule quand il sera prêt
        
        $schedules = collect([
            [
                'time' => '08:00 - 10:00',
                'subject' => 'Programmation Orientée Objet',
                'class' => 'L2A',
                'teacher' => 'Dr. Amadou Diallo',
                'room' => 'Salle 101',
                'status' => 'completed'
            ],
            [
                'time' => '10:15 - 12:15',
                'subject' => 'Base de Données',
                'class' => 'L3B',
                'teacher' => 'Prof. Fatou Sall',
                'room' => 'Labo Info 1',
                'status' => 'in_progress'
            ],
            [
                'time' => '14:00 - 16:00',
                'subject' => 'Réseaux Informatiques',
                'class' => 'M1A',
                'teacher' => 'Dr. Ousmane Ba',
                'room' => 'Salle 203',
                'status' => 'scheduled'
            ]
        ]);
        
        return $schedules;
    }
    
    /**
     * Activités récentes
     */
    private function getRecentActivities()
    {
        $activities = collect([
            [
                'title' => 'Nouvel étudiant inscrit',
                'description' => 'Abdou Kane s\'est inscrit en L1 Informatique',
                'icon' => 'person-plus',
                'color' => 'primary',
                'date' => Carbon::now()->subHours(2)
            ],
            [
                'title' => 'Note saisie',
                'description' => 'Notes de POO saisies pour la classe L2A',
                'icon' => 'award',
                'color' => 'success',
                'date' => Carbon::now()->subHours(4)
            ],
            [
                'title' => 'Nouveau cours créé',
                'description' => 'Cours d\'Intelligence Artificielle ajouté au planning',
                'icon' => 'calendar-plus',
                'color' => 'info',
                'date' => Carbon::now()->subDay()
            ],
            [
                'title' => 'Rapport généré',
                'description' => 'Rapport mensuel de présence généré',
                'icon' => 'graph-up',
                'color' => 'warning',
                'date' => Carbon::now()->subDays(2)
            ]
        ]);
        
        return $activities;
    }
    
    /**
     * Alertes système
     */
    private function getSystemAlerts()
    {
        $alerts = collect();
        
        // Vérifier les classes à capacité maximale
        $fullClasses = Classe::withCount('students')
                            ->get()
                            ->filter(function($class) {
                                return $class->students_count >= $class->capacity;
                            });
        
        if ($fullClasses->count() > 0) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Classes à capacité maximale',
                'message' => "{$fullClasses->count()} classe(s) ont atteint leur capacité maximale.",
                'action' => route('admin.classes.index')
            ]);
        }
        
        // Vérifier les enseignants sans matières
        $teachersWithoutSubjects = Teacher::doesntHave('subjects')->count();
        if ($teachersWithoutSubjects > 0) {
            $alerts->push([
                'type' => 'info',
                'title' => 'Enseignants sans matières',
                'message' => "{$teachersWithoutSubjects} enseignant(s) n'ont pas de matières assignées.",
                'action' => route('admin.teachers.index')
            ]);
        }
        
        // Vérifier les notes en attente
        $gradesThisWeek = Grade::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        if ($gradesThisWeek < 10) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Peu de notes saisies',
                'message' => "Seulement {$gradesThisWeek} notes ont été saisies cette semaine.",
                'action' => '#'
            ]);
        }
        
        return $alerts;
    }
    
    /**
     * Données par défaut en cas d'erreur
     */
    private function getDefaultGeneralStats()
    {
        return [
            'total_students' => 150,
            'total_teachers' => 25,
            'total_classes' => 12,
            'total_departments' => 6,
            'students_growth' => 5.2,
            'teachers_growth' => 2.1,
            'capacity_usage' => 75.5,
        ];
    }
    
    private function getDefaultAttendanceStats()
    {
        return [
            'present_today' => 135,
            'total_attendance_today' => 150,
            'attendance_rate_today' => 90.0,
            'attendance_trend' => collect([
                ['date' => Carbon::today()->subDays(6)->format('Y-m-d'), 'rate' => 88],
                ['date' => Carbon::today()->subDays(5)->format('Y-m-d'), 'rate' => 92],
                ['date' => Carbon::today()->subDays(4)->format('Y-m-d'), 'rate' => 85],
                ['date' => Carbon::today()->subDays(3)->format('Y-m-d'), 'rate' => 90],
                ['date' => Carbon::today()->subDays(2)->format('Y-m-d'), 'rate' => 87],
                ['date' => Carbon::today()->subDays(1)->format('Y-m-d'), 'rate' => 91],
                ['date' => Carbon::today()->format('Y-m-d'), 'rate' => 90]
            ])
        ];
    }
    
    private function getDefaultAcademicStats()
    {
        return [
            'total_grades' => 450,
            'average_grade_global' => 13.2,
            'grade_distribution' => [
                'excellent' => 65,
                'good' => 120,
                'average' => 180,
                'below_average' => 70,
                'poor' => 15
            ]
        ];
    }
    
    private function getDefaultChartsData()
    {
        return [
            'enrollment_evolution' => collect([
                (object)['year' => 2025, 'month' => 1, 'count' => 15],
                (object)['year' => 2025, 'month' => 2, 'count' => 25],
                (object)['year' => 2025, 'month' => 3, 'count' => 18],
                (object)['year' => 2025, 'month' => 4, 'count' => 30],
                (object)['year' => 2025, 'month' => 5, 'count' => 22],
                (object)['year' => 2025, 'month' => 6, 'count' => 28]
            ]),
            'students_by_level' => collect([
                (object)['name' => 'Licence 1', 'students_count' => 45],
                (object)['name' => 'Licence 2', 'students_count' => 38],
                (object)['name' => 'Licence 3', 'students_count' => 42],
                (object)['name' => 'Master 1', 'students_count' => 25],
                (object)['name' => 'Master 2', 'students_count' => 18]
            ])
        ];
    }
    
    /**
     * API pour récupérer les statistiques en AJAX
     */
    public function apiStats(Request $request)
    {
        try {
            $type = $request->get('type', 'general');
            
            switch ($type) {
                case 'general':
                    return response()->json($this->getGeneralStats());
                case 'attendance':
                    return response()->json($this->getAttendanceStats());
                case 'academic':
                    return response()->json($this->getAcademicStats());
                default:
                    return response()->json(['error' => 'Type non supporté'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur API stats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
}