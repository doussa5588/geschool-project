@extends('layouts.app')

@section('title', 'Mon Espace Étudiant - UNCHK')

@section('page-header')
@section('page-title', 'Mon Espace Étudiant')
@section('page-subtitle', 'Bienvenue ' . auth()->user()->first_name . ' - ' . auth()->user()->student->class->name ?? 'Aucune classe')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Mes Documents
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('student.grades.bulletin', 'current') }}">
                <i class="bi bi-file-pdf me-2"></i>Bulletin de Notes
            </a></li>
            <li><a class="dropdown-item" href="{{ route('student.attendance.report') }}">
                <i class="bi bi-file-excel me-2"></i>Rapport de Présences
            </a></li>
            <li><a class="dropdown-item" href="{{ route('student.schedule.export') }}">
                <i class="bi bi-calendar-week me-2"></i>Mon Emploi du Temps
            </a></li>
        </ul>
    </div>
    <a href="{{ route('student.profile') }}" class="btn btn-outline-secondary">
        <i class="bi bi-person"></i> Mon Profil
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <!-- Vue d'ensemble personnelle -->
    <div class="row mb-4">
        <!-- Ma Moyenne Générale -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Moyenne Générale</h6>
                            <h3 class="mb-0">{{ number_format($student->grades->avg('grade') ?? 0, 2) }}/20</h3>
                            <small class="text-white-50">
                                @php
                                    $average = $student->grades->avg('grade') ?? 0;
                                    $mention = $average >= 16 ? 'Très Bien' : 
                                              ($average >= 14 ? 'Bien' : 
                                              ($average >= 12 ? 'Assez Bien' : 
                                              ($average >= 10 ? 'Passable' : 'Insuffisant')));
                                @endphp
                                {{ $mention }}
                            </small>
                        </div>
                        <i class="bi bi-trophy fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mon Taux de Présence -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Taux de Présence</h6>
                            @php
                                $totalAttendances = $student->attendances->count();
                                $presentAttendances = $student->attendances->where('status', 'present')->count();
                                $attendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 100;
                            @endphp
                            <h3 class="mb-0">{{ $attendanceRate }}%</h3>
                            <small class="text-white-50">
                                {{ $presentAttendances }}/{{ $totalAttendances }} présences
                            </small>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mes Matières -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Mes Matières</h6>
                            <h3 class="mb-0">{{ $student->class->subjects->count() ?? 0 }}</h3>
                            <small class="text-white-50">
                                {{ $student->grades->groupBy('subject_id')->count() }} avec notes
                            </small>
                        </div>
                        <i class="bi bi-book fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cours cette Semaine -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Cours cette Semaine</h6>
                            <h3 class="mb-0">{{ $weeklySchedules->count() }}</h3>
                            <small class="text-white-50">
                                {{ $weeklySchedules->where('status', 'completed')->count() }} terminés
                            </small>
                        </div>
                        <i class="bi bi-calendar3 fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mon Emploi du Temps Aujourd'hui -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-day me-2"></i>Mes Cours Aujourd'hui
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" onclick="toggleScheduleView('today')">Aujourd'hui</button>
                            <button class="btn btn-outline-primary" onclick="toggleScheduleView('week')">Cette Semaine</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="todaySchedule">
                        @if($todaySchedules->count() > 0)
                            <div class="timeline">
                                @foreach($todaySchedules->sortBy('start_time') as $schedule)
                                    @php
                                        $isPast = now()->format('H:i') > $schedule->end_time;
                                        $isCurrent = now()->format('H:i') >= $schedule->start_time && now()->format('H:i') <= $schedule->end_time;
                                        $statusClass = $isPast ? 'success' : ($isCurrent ? 'warning' : 'primary');
                                    @endphp
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $statusClass }}"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="timeline-title">
                                                        {{ $schedule->subject->name }}
                                                        @if($isCurrent)
                                                            <span class="badge bg-warning ms-2">MAINTENANT</span>
                                                        @elseif($isPast)
                                                            <span class="badge bg-success ms-2">TERMINÉ</span>
                                                        @else
                                                            <span class="badge bg-primary ms-2">À VENIR</span>
                                                        @endif
                                                    </h6>
                                                    <p class="timeline-text mb-2">
                                                        <strong>Enseignant :</strong> {{ $schedule->teacher->user->full_name }}<br>
                                                        <strong>Horaire :</strong> {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}<br>
                                                        @if($schedule->room)
                                                            <strong>Salle :</strong> <i class="bi bi-geo-alt"></i> {{ $schedule->room }}
                                                        @endif
                                                    </p>
                                                    @if($schedule->notes)
                                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $schedule->notes }}</small>
                                                    @endif
                                                </div>
                                                <div class="timeline-actions">
                                                    @if(!$isPast)
                                                        @php
                                                            $timeLeft = \Carbon\Carbon::parse($schedule->start_time)->diffForHumans();
                                                        @endphp
                                                        <small class="text-muted">{{ $timeLeft }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">Aucun cours aujourd'hui</h5>
                                <p class="text-muted">Profitez de cette journée pour réviser et faire vos devoirs !</p>
                                <a href="{{ route('student.schedule') }}" class="btn btn-primary">
                                    Voir mon emploi du temps complet
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <div id="weekSchedule" style="display: none;">
                        @if($weeklySchedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Jour</th>
                                            <th>Heure</th>
                                            <th>Matière</th>
                                            <th>Enseignant</th>
                                            <th>Salle</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($weeklySchedules->sortBy(['date', 'start_time']) as $schedule)
                                            <tr class="{{ $schedule->date->isToday() ? 'table-warning' : '' }}">
                                                <td>
                                                    <div>{{ $schedule->date->format('D d/m') }}</div>
                                                    @if($schedule->date->isToday())
                                                        <small class="badge bg-warning">Aujourd'hui</small>
                                                    @endif
                                                </td>
                                                <td>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                                <td>
                                                    <span class="fw-semibold">{{ $schedule->subject->name }}</span>
                                                </td>
                                                <td>{{ $schedule->teacher->user->full_name }}</td>
                                                <td>
                                                    @if($schedule->room)
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $schedule->room }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($schedule->status)
                                                        @case('completed')
                                                            <span class="badge bg-success">Terminé</span>
                                                            @break
                                                        @case('in_progress')
                                                            <span class="badge bg-warning">En cours</span>
                                                            @break
                                                        @case('cancelled')
                                                            <span class="badge bg-danger">Annulé</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">Programmé</span>
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x fs-3 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Aucun cours programmé cette semaine</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations Personnelles et Actions -->
        <div class="col-xl-4 col-lg-5">
            <!-- Mon Profil -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Mon Profil
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" 
                                 alt="{{ auth()->user()->full_name }}" 
                                 class="rounded-circle img-fluid" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->full_name) }}&background=2563eb&color=fff&size=100" 
                                 alt="{{ auth()->user()->full_name }}" 
                                 class="rounded-circle img-fluid">
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ auth()->user()->full_name }}</h5>
                    <p class="text-muted mb-2">{{ $student->student_number }}</p>
                    
                    @if($student->class)
                        <div class="mb-3">
                            <span class="badge bg-primary px-3 py-2">{{ $student->class->name }}</span>
                            <br>
                            <small class="text-muted">{{ $student->class->level->name }} - {{ $student->class->department->name }}</small>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.profile') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-gear me-2"></i>Modifier mon Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Accès Rapide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.grades') }}" class="btn btn-primary">
                            <i class="bi bi-clipboard-data me-2"></i>Consulter mes Notes
                        </a>
                        <a href="{{ route('student.attendance') }}" class="btn btn-success">
                            <i class="bi bi-check2-square me-2"></i>Mes Présences
                        </a>
                        <a href="{{ route('student.schedule') }}" class="btn btn-info">
                            <i class="bi bi-calendar3 me-2"></i>Mon Emploi du Temps
                        </a>
                        <a href="{{ route('student.grades.bulletin', 'current') }}" class="btn btn-warning">
                            <i class="bi bi-file-pdf me-2"></i>Télécharger Bulletin
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations de Contact d'Urgence -->
            @if($student->parent_phone || $student->parent_email || $student->emergency_contact)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-exclamation me-2"></i>Contact d'Urgence
                    </h5>
                </div>
                <div class="card-body">
                    @if($student->parent_phone)
                        <div class="mb-2">
                            <small class="text-muted">Parent/Tuteur :</small><br>
                            <a href="tel:{{ $student->parent_phone }}" class="text-decoration-none">
                                <i class="bi bi-telephone me-1"></i>{{ $student->parent_phone }}
                            </a>
                        </div>
                    @endif
                    
                    @if($student->parent_email)
                        <div class="mb-2">
                            <small class="text-muted">Email Parent :</small><br>
                            <a href="mailto:{{ $student->parent_email }}" class="text-decoration-none">
                                <i class="bi bi-envelope me-1"></i>{{ $student->parent_email }}
                            </a>
                        </div>
                    @endif
                    
                    @if($student->emergency_contact)
                        <div>
                            <small class="text-muted">Urgence :</small><br>
                            <span>{{ $student->emergency_contact }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Mes Performances et Progression -->
    <div class="row">
        <!-- Mes Dernières Notes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Mes Dernières Notes
                        </h5>
                        <a href="{{ route('student.grades') }}" class="btn btn-outline-primary btn-sm">
                            Voir toutes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentGrades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Matière</th>
                                        <th>Note</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGrades as $grade)
                                        <tr>
                                            <td>{{ $grade->subject->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $grade->grade >= 16 ? 'success' : ($grade->grade >= 14 ? 'info' : ($grade->grade >= 12 ? 'warning' : ($grade->grade >= 10 ? 'secondary' : 'danger'))) }}">
                                                    {{ $grade->grade }}/20
                                                </span>
                                            </td>
                                            <td>{{ $grade->grade_type }}</td>
                                            <td>{{ $grade->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-x fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune note disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mes Moyennes par Matière -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Moyennes par Matière
                    </h5>
                </div>
                <div class="card-body">
                    @if($student->grades->count() > 0)
                        @php
                            $gradesBySubject = $student->grades->groupBy('subject.name');
                        @endphp
                        
                        @foreach($gradesBySubject as $subjectName => $grades)
                            @php
                                $average = round($grades->avg('grade'), 2);
                                $progressClass = $average >= 16 ? 'success' : ($average >= 14 ? 'info' : ($average >= 12 ? 'warning' : ($average >= 10 ? 'secondary' : 'danger')));
                                $percentage = ($average / 20) * 100;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ $subjectName }}</span>
                                    <span class="badge bg-{{ $progressClass }}">{{ $average }}/20</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $grades->count() }} note(s)</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune moyenne calculée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques de Progression -->
    <div class="row">
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Évolution de ma Moyenne</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="gradeEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ma Présence Mensuelle</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparaison avec la Classe -->
    @if($student->class)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ma Position dans la Classe</h5>
                </div>
                <div class="card-body">
                    @php
                        $classStudents = $student->class->students()
                            ->join('grades', 'students.id', '=', 'grades.student_id')
                            ->select('students.*', DB::raw('AVG(grades.grade) as average_grade'))
                            ->groupBy('students.id')
                            ->orderBy('average_grade', 'desc')
                            ->get();
                        
                        $myPosition = $classStudents->search(function($item) use ($student) {
                            return $item->id === $student->id;
                        }) + 1;
                        
                        $classAverage = $classStudents->avg('average_grade');
                        $myAverage = $student->grades->avg('grade') ?? 0;
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $myPosition ?? 'N/A' }}</h3>
                            <small class="text-muted">Position dans la classe</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success">{{ number_format($myAverage, 2) }}/20</h3>
                            <small class="text-muted">Ma moyenne</small>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info">{{ number_format($classAverage, 2) }}/20</h3>
                            <small class="text-muted">Moyenne de classe</small>
                        </div>
                        <div class="col-md-3">
                            @php
                                $difference = $myAverage - $classAverage;
                                $diffClass = $difference >= 0 ? 'success' : 'danger';
                                $diffIcon = $difference >= 0 ? 'arrow-up' : 'arrow-down';
                            @endphp
                            <h3 class="text-{{ $diffClass }}">
                                <i class="bi bi-{{ $diffIcon }}"></i>
                                {{ abs(number_format($difference, 2)) }}
                            </h3>
                            <small class="text-muted">Écart à la moyenne</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    background-color: #e9ecef;
    transform: translateX(5px);
}

.timeline-title {
    margin-bottom: 5px;
    color: #495057;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
}

.progress {
    height: 8px;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}

/* Custom badge colors based on grades */
.badge.bg-success { background-color: #198754 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-secondary { background-color: #6c757d !important; }
.badge.bg-danger { background-color: #dc3545 !important; }

/* Chart container */
.chart-container {
    position: relative;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-marker {
        left: -27px;
    }
    
    .timeline-content {
        padding: 10px;
    }
}

/* Profile photo styling */
.rounded-circle {
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Highlight today in schedule */
.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize charts
    initializeCharts();
    
    // Check for upcoming classes
    checkUpcomingClasses();
    
    // Update time display
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
});

function toggleScheduleView(view) {
    if (view === 'today') {
        $('#todaySchedule').show();
        $('#weekSchedule').hide();
        $('.btn-group .btn').removeClass('active');
        $('button[onclick="toggleScheduleView(\'today\')"]').addClass('active');
    } else {
        $('#todaySchedule').hide();
        $('#weekSchedule').show();
        $('.btn-group .btn').removeClass('active');
        $('button[onclick="toggleScheduleView(\'week\')"]').addClass('active');
    }
}

function initializeCharts() {
    // Grade Evolution Chart
    const gradeCtx = document.getElementById('gradeEvolutionChart');
    if (gradeCtx) {
        const gradeData = @json($student->grades()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(grade) as average'))
            ->groupBy('date')
            ->orderBy('date')
            ->take(30)
            ->get());
        
        new Chart(gradeCtx, {
            type: 'line',
            data: {
                labels: gradeData.map(item => new Date(item.date).toLocaleDateString('fr-FR')),
                datasets: [{
                    label: 'Ma Moyenne',
                    data: gradeData.map(item => parseFloat(item.average)),
                    borderColor: 'rgb(37, 99, 235)',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        ticks: {
                            callback: function(value) {
                                return value + '/20';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        const attendanceData = @json(collect(range(5, 0))->map(function($i) use ($student) {
            $month = now()->subMonths($i);
            $attendances = $student->attendances()->whereYear('date', $month->year)->whereMonth('date', $month->month);
            $total = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            return [
                'month' => $month->format('M Y'),
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0
            ];
        }));
        
        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: attendanceData.map(item => item.month),
                datasets: [{
                    label: 'Taux de Présence (%)',
                    data: attendanceData.map(item => item.rate),
                    backgroundColor: 'rgba(5, 150, 105, 0.8)',
                    borderColor: 'rgba(5, 150, 105, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}

function checkUpcomingClasses() {
    const now = new Date();
    
    @foreach($todaySchedules as $schedule)
        const classTime = new Date('{{ now()->format("Y-m-d") }} {{ $schedule->start_time }}');
        const timeDiff = (classTime - now) / (1000 * 60); // difference in minutes
        
        if (timeDiff > 0 && timeDiff <= 15) {
            showNotification(
                'Cours dans 15 minutes',
                '{{ $schedule->subject->name }} avec {{ $schedule->teacher->user->full_name }}',
                'warning'
            );
        }
    @endforeach
}

function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR');
    
    // Update time if element exists
    if (document.getElementById('currentTime')) {
        document.getElementById('currentTime').textContent = timeString;
    }
}

function showNotification(title, message, type = 'info') {
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;">
            <strong>${title}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut();
    }, 5000);
}

// Motivational messages based on performance
function showMotivationalMessage() {
    const average = {{ $student->grades->avg('grade') ?? 0 }};
    const attendanceRate = {{ $attendanceRate }};
    
    let message = '';
    
    if (average >= 16 && attendanceRate >= 95) {
        message = 'Excellent travail ! Continuez ainsi ! 🌟';
    } else if (average >= 14 && attendanceRate >= 90) {
        message = 'Très bon travail ! Vous êtes sur la bonne voie ! 👍';
    } else if (average >= 12 && attendanceRate >= 85) {
        message = 'Bon travail ! Encore un petit effort ! 💪';
    } else if (average >= 10 && attendanceRate >= 80) {
        message = 'Vous pouvez mieux faire ! Restez motivé(e) ! 📚';
    } else {
        message = 'Il est temps de redoubler d\'efforts ! Nous croyons en vous ! 🎯';
    }
    
    // Show motivational message occasionally
    if (Math.random() < 0.3) { // 30% chance
        showNotification('Message du jour', message, 'info');
    }
}

// Show motivational message on page load
$(document).ready(function() {
    setTimeout(showMotivationalMessage, 2000);
});

// Add goal-setting functionality
function setAcademicGoal() {
    const goal = prompt('Quelle moyenne souhaitez-vous atteindre ce trimestre ? (sur 20)');
    if (goal && !isNaN(goal) && goal >= 0 && goal <= 20) {
        localStorage.setItem('academicGoal', goal);
        showNotification('Objectif défini', `Votre objectif de ${goal}/20 a été enregistré !`, 'success');
        updateGoalProgress();
    }
}

function updateGoalProgress() {
    const goal = localStorage.getItem('academicGoal');
    const current = {{ $student->grades->avg('grade') ?? 0 }};
    
    if (goal) {
        const progress = (current / goal) * 100;
        const progressHtml = `
            <div class="mt-3">
                <small class="text-muted">Objectif: ${goal}/20</small>
                <div class="progress">
                    <div class="progress-bar" style="width: ${Math.min(progress, 100)}%"></div>
                </div>
                <small class="text-muted">${progress.toFixed(1)}% de votre objectif</small>
            </div>
        `;
        
        // Add to profile card if not exists
        if (!document.getElementById('goalProgress')) {
            $('.card-body.text-center .d-grid').before(`<div id="goalProgress">${progressHtml}</div>`);
        }
    }
}

// Initialize goal progress
$(document).ready(function() {
    updateGoalProgress();
});
</script>
@endpush