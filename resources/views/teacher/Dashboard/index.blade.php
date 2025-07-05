@extends('layouts.app')

@section('title', 'Tableau de Bord Enseignant - UNCHK')

@section('page-header')
@section('page-title', 'Tableau de Bord')
@section('page-subtitle', 'Bienvenue ' . auth()->user()->first_name . ' - ' . now()->format('l d F Y'))
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-calendar3"></i> Mes Cours
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('teacher.schedule') }}">
                <i class="bi bi-calendar-week me-2"></i>Mon Emploi du Temps
            </a></li>
            <li><a class="dropdown-item" href="{{ route('teacher.classes') }}">
                <i class="bi bi-collection me-2"></i>Mes Classes
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('teacher.grades.index') }}">
                <i class="bi bi-clipboard-data me-2"></i>Saisir des Notes
            </a></li>
            <li><a class="dropdown-item" href="{{ route('teacher.attendance.index') }}">
                <i class="bi bi-check2-square me-2"></i>Marquer Présences
            </a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-success" onclick="quickAttendance()">
        <i class="bi bi-check-circle"></i> Présences Rapides
    </button>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <!-- Vue d'ensemble personnalisée -->
    <div class="row mb-4">
        <!-- Mes Statistiques -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Mes Classes</h6>
                            <h3 class="mb-0">{{ $teacher->classes()->count() }}</h3>
                            <small class="text-white-50">
                                {{ $teacher->classes()->withCount('students')->get()->sum('students_count') }} étudiants
                            </small>
                        </div>
                        <i class="bi bi-collection fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Cours cette Semaine</h6>
                            <h3 class="mb-0">{{ $weeklySchedules->count() }}</h3>
                            <small class="text-white-50">
                                {{ $weeklySchedules->where('status', 'completed')->count() }} terminés
                            </small>
                        </div>
                        <i class="bi bi-calendar-check fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Notes à Saisir</h6>
                            <h3 class="mb-0">{{ $pendingGrades }}</h3>
                            <small class="text-white-50">
                                Evaluations en attente
                            </small>
                        </div>
                        <i class="bi bi-clipboard-data fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Taux Présence</h6>
                            <h3 class="mb-0">{{ $averageAttendanceRate }}%</h3>
                            <small class="text-white-50">
                                Moyenne de mes classes
                            </small>
                        </div>
                        <i class="bi bi-graph-up fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Emploi du Temps Aujourd'hui -->
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
                                                            <span class="badge bg-warning ms-2">EN COURS</span>
                                                        @elseif($isPast)
                                                            <span class="badge bg-success ms-2">TERMINÉ</span>
                                                        @endif
                                                    </h6>
                                                    <p class="timeline-text mb-2">
                                                        <strong>Classe :</strong> {{ $schedule->class->name }}<br>
                                                        <strong>Horaire :</strong> {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}<br>
                                                        @if($schedule->room)
                                                            <strong>Salle :</strong> <i class="bi bi-geo-alt"></i> {{ $schedule->room }}
                                                        @endif
                                                    </p>
                                                    @if($schedule->notes)
                                                        <small class="text-muted">{{ $schedule->notes }}</small>
                                                    @endif
                                                </div>
                                                <div class="timeline-actions">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('teacher.attendance.class', $schedule->class->id) }}" 
                                                           class="btn btn-outline-success" data-bs-toggle="tooltip" title="Marquer présences">
                                                            <i class="bi bi-check2-square"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.grades.class-subject', [$schedule->class->id, $schedule->subject->id]) }}" 
                                                           class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Saisir notes">
                                                            <i class="bi bi-clipboard-data"></i>
                                                        </a>
                                                        <button class="btn btn-outline-info" 
                                                                onclick="viewClassDetails({{ $schedule->class->id }})" 
                                                                data-bs-toggle="tooltip" title="Voir classe">
                                                            <i class="bi bi-people"></i>
                                                        </button>
                                                    </div>
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
                                <p class="text-muted">Profitez de cette journée pour préparer vos prochains cours !</p>
                            </div>
                        @endif
                    </div>
                    
                    <div id="weekSchedule" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jour</th>
                                        <th>Heure</th>
                                        <th>Matière</th>
                                        <th>Classe</th>
                                        <th>Salle</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weeklySchedules->sortBy(['date', 'start_time']) as $schedule)
                                        <tr>
                                            <td>{{ $schedule->date->format('D d/m') }}</td>
                                            <td>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                            <td>{{ $schedule->subject->name }}</td>
                                            <td>{{ $schedule->class->name }}</td>
                                            <td>{{ $schedule->room ?? '-' }}</td>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides et Mes Classes -->
        <div class="col-xl-4 col-lg-5">
            <!-- Actions Rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Saisir des Notes
                        </a>
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-success">
                            <i class="bi bi-check-square me-2"></i>Marquer les Présences
                        </a>
                        <a href="{{ route('teacher.schedule') }}" class="btn btn-info">
                            <i class="bi bi-calendar3 me-2"></i>Voir Mon Planning
                        </a>
                        <a href="{{ route('teacher.classes') }}" class="btn btn-warning">
                            <i class="bi bi-people me-2"></i>Gérer Mes Classes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mes Classes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-collection me-2"></i>Mes Classes
                    </h5>
                </div>
                <div class="card-body">
                    @if($teacher->classes->count() > 0)
                        @foreach($teacher->classes as $class)
                            <div class="d-flex align-items-center justify-content-between p-3 mb-2 bg-light rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $class->name }}</h6>
                                    <small class="text-muted">
                                        {{ $class->level->name }} - {{ $class->students_count }} étudiants
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teacher.classes.show', $class) }}" 
                                       class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('teacher.attendance.class', $class) }}" 
                                       class="btn btn-outline-success" data-bs-toggle="tooltip" title="Présences">
                                        <i class="bi bi-check2-square"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-collection fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune classe assignée</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mes Matières -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book me-2"></i>Mes Matières
                    </h5>
                </div>
                <div class="card-body">
                    @if($teacher->subjects->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($teacher->subjects as $subject)
                                <span class="badge bg-info px-3 py-2">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-book fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune matière assignée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activités Récentes et Performances -->
    <div class="row">
        <!-- Notes Récentes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Notes Récentes
                        </h5>
                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-outline-primary btn-sm">
                            Voir tout
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentGrades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Matière</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGrades as $grade)
                                        <tr>
                                            <td>{{ $grade->student->user->full_name }}</td>
                                            <td>{{ $grade->subject->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $grade->grade >= 10 ? 'success' : 'danger' }}">
                                                    {{ $grade->grade }}/20
                                                </span>
                                            </td>
                                            <td>{{ $grade->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-x fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune note saisie récemment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Présences du Jour -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check2-square me-2"></i>Présences du Jour
                        </h5>
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-success btn-sm">
                            Gérer tout
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($todayAttendances->count() > 0)
                        <div class="row text-center mb-3">
                            <div class="col-3">
                                <h4 class="text-success">{{ $todayAttendances->where('status', 'present')->count() }}</h4>
                                <small class="text-muted">Présents</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-danger">{{ $todayAttendances->where('status', 'absent')->count() }}</h4>
                                <small class="text-muted">Absents</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-warning">{{ $todayAttendances->where('status', 'late')->count() }}</h4>
                                <small class="text-muted">Retards</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-info">{{ $todayAttendances->where('status', 'excused')->count() }}</h4>
                                <small class="text-muted">Excusés</small>
                            </div>
                        </div>
                        
                        @php
                            $attendanceRate = $todayAttendances->count() > 0 
                                ? round(($todayAttendances->where('status', 'present')->count() / $todayAttendances->count()) * 100, 1)
                                : 100;
                        @endphp
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Taux de présence: {{ $attendanceRate }}%</small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Aucune présence marquée aujourd'hui</p>
                            <button class="btn btn-success btn-sm mt-2" onclick="quickAttendance()">
                                Marquer les présences
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques de Performance -->
    <div class="row">
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Moyennes par Classe</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="classAveragesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Évolution des Présences</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="attendanceEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Présences Rapides -->
<div class="modal fade" id="quickAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Présences Rapides</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="quickAttendanceContent">
                    <div class="text-center py-4">
                        <span class="loading"></span> Chargement...
                    </div>
                </div>
            </div>
        </div>
    </div>
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
}

.timeline-title {
    margin-bottom: 5px;
    color: #495057;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
}

.timeline-actions {
    margin-top: 10px;
}

/* Custom badge styles for better visibility */
.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

/* Hover effects for interactive elements */
.timeline-content:hover {
    background-color: #e9ecef;
    transform: translateX(5px);
    transition: all 0.3s ease;
}

/* Chart container responsive */
.chart-container {
    position: relative;
}

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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize charts
    initializeCharts();
    
    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboard, 300000);
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

function quickAttendance() {
    const modal = new bootstrap.Modal(document.getElementById('quickAttendanceModal'));
    modal.show();
    
    // Load current classes for attendance
    $.ajax({
        url: '{{ route("teacher.attendance.quick") }}',
        method: 'GET',
        beforeSend: function() {
            $('#quickAttendanceContent').html(`
                <div class="text-center py-4">
                    <span class="loading"></span> Chargement des classes...
                </div>
            `);
        },
        success: function(data) {
            $('#quickAttendanceContent').html(data);
        },
        error: function() {
            $('#quickAttendanceContent').html(`
                <div class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mt-2">Erreur lors du chargement</p>
                </div>
            `);
        }
    });
}

function viewClassDetails(classId) {
    window.location.href = '{{ route("teacher.classes.show", "") }}/' + classId;
}

function initializeCharts() {
    // Class Averages Chart
    const classAveragesCtx = document.getElementById('classAveragesChart');
    if (classAveragesCtx) {
        new Chart(classAveragesCtx, {
            type: 'bar',
            data: {
                labels: @json($teacher->classes->pluck('name')),
                datasets: [{
                    label: 'Moyenne Classe',
                    data: @json($teacher->classes->map(function($class) {
                        return $class->students()->join('grades', 'students.id', '=', 'grades.student_id')->avg('grades.grade') ?? 0;
                    })),
                    backgroundColor: 'rgba(37, 99, 235, 0.8)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1
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

    // Attendance Evolution Chart
    const attendanceCtx = document.getElementById('attendanceEvolutionChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: @json(collect(range(6, 0))->map(function($i) {
                    return now()->subDays($i)->format('d/m');
                })),
                datasets: [{
                    label: 'Taux de Présence (%)',
                    data: @json(collect(range(6, 0))->map(function($i) {
                        $date = now()->subDays($i);
                        $attendances = auth()->user()->teacher->attendances()->whereDate('date', $date);
                        $total = $attendances->count();
                        $present = $attendances->where('status', 'present')->count();
                        return $total > 0 ? round(($present / $total) * 100, 1) : 0;
                    })),
                    borderColor: 'rgb(5, 150, 105)',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
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

function refreshDashboard() {
    // Refresh statistics
    $.ajax({
        url: '{{ route("teacher.dashboard.stats") }}',
        method: 'GET',
        success: function(data) {
            updateStats(data);
        },
        error: function() {
            console.log('Erreur lors de l\'actualisation');
        }
    });
}

function updateStats(data) {
    // Update main stats
    $('.stats-card.primary h3').text(data.total_classes);
    $('.stats-card.success h3').text(data.weekly_courses);
    $('.stats-card.warning h3').text(data.pending_grades);
    $('.stats-card.info h3').text(data.attendance_rate + '%');
}

// Real-time clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Add clock to page if element exists
    if (document.getElementById('currentTime')) {
        document.getElementById('currentTime').textContent = timeString;
    }
}

// Update clock every second
setInterval(updateClock, 1000);

// Notification system for upcoming classes
function checkUpcomingClasses() {
    const now = new Date();
    const upcomingThreshold = 15; // 15 minutes before class
    
    @foreach($todaySchedules as $schedule)
        const classTime = new Date('{{ now()->format("Y-m-d") }} {{ $schedule->start_time }}');
        const timeDiff = (classTime - now) / (1000 * 60); // difference in minutes
        
        if (timeDiff > 0 && timeDiff <= upcomingThreshold) {
            showNotification(
                'Cours dans {{ $schedule->start_time }}',
                '{{ $schedule->subject->name }} - {{ $schedule->class->name }}',
                'info'
            );
        }
    @endforeach
}

function showNotification(title, message, type = 'info') {
    // Simple notification system
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 1060; min-width: 300px;">
            <strong>${title}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        notification.fadeOut();
    }, 5000);
}

// Check for upcoming classes every minute
setInterval(checkUpcomingClasses, 60000);

// Keyboard shortcuts for teachers
document.addEventListener('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch(event.key) {
            case 'g':
                event.preventDefault();
                window.location.href = '{{ route("teacher.grades.index") }}';
                break;
            case 'a':
                event.preventDefault();
                window.location.href = '{{ route("teacher.attendance.index") }}';
                break;
            case 's':
                event.preventDefault();
                window.location.href = '{{ route("teacher.schedule") }}';
                break;
            case 'q':
                event.preventDefault();
                quickAttendance();
                break;
        }
    }
});
</script>
@endpush