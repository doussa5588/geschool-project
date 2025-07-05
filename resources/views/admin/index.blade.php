@extends('layouts.app')

@section('title', 'Tableau de Bord - Administration UNCHK')

@section('page-header')
    <!-- Meta tags et CSS pour Chart.js -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-title', 'Tableau de Bord')
@section('page-subtitle', 'Vue d\'ensemble de l\'établissement - ' . now()->format('d/m/Y'))

@section('page-actions')
    <button class="btn btn-outline-primary" onclick="refreshDashboard()">
        <i class="bi bi-arrow-clockwise"></i> Actualiser
    </button>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#"><i class="bi bi-file-pdf me-2"></i>Rapport PDF</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel me-2"></i>Rapport Excel</a></li>
        </ul>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistiques Générales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Étudiants</h6>
                            <h2 class="mb-0 text-white">{{ number_format($generalStats['total_students'] ?? 0) }}</h2>
                            <small class="text-white-50">
                                @php $growth = $generalStats['students_growth'] ?? 0; @endphp
                                @if($growth > 0)
                                    <i class="bi bi-arrow-up"></i> +{{ $growth }}%
                                @elseif($growth < 0)
                                    <i class="bi bi-arrow-down"></i> {{ $growth }}%
                                @else
                                    <i class="bi bi-dash"></i> 0%
                                @endif
                                ce mois
                            </small>
                        </div>
                        <div class="text-white-50">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Enseignants</h6>
                            <h2 class="mb-0 text-white">{{ number_format($generalStats['total_teachers'] ?? 0) }}</h2>
                            <small class="text-white-50">
                                @php $growth = $generalStats['teachers_growth'] ?? 0; @endphp
                                @if($growth > 0)
                                    <i class="bi bi-arrow-up"></i> +{{ $growth }}%
                                @elseif($growth < 0)
                                    <i class="bi bi-arrow-down"></i> {{ $growth }}%
                                @else
                                    <i class="bi bi-dash"></i> 0%
                                @endif
                                ce mois
                            </small>
                        </div>
                        <div class="text-white-50">
                            <i class="bi bi-person-badge fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Classes</h6>
                            <h2 class="mb-0 text-white">{{ number_format($generalStats['total_classes'] ?? 0) }}</h2>
                            <small class="text-white-50">
                                Capacité: {{ $generalStats['capacity_usage'] ?? 0 }}%
                            </small>
                        </div>
                        <div class="text-white-50">
                            <i class="bi bi-collection fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Présence Aujourd'hui</h6>
                            <h2 class="mb-0 text-white">{{ $attendanceStats['attendance_rate_today'] ?? 0 }}%</h2>
                            <small class="text-white-50">
                                {{ $attendanceStats['present_today'] ?? 0 }}/{{ $attendanceStats['total_attendance_today'] ?? 0 }} présents
                            </small>
                        </div>
                        <div class="text-white-50">
                            <i class="bi bi-check2-square fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Tendances -->
    <div class="row mb-4">
        <!-- Évolution des Inscriptions -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Évolution des Inscriptions
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="enrollmentPeriod" id="period6m" value="6" checked>
                            <label class="btn btn-outline-primary" for="period6m">6M</label>
                            
                            <input type="radio" class="btn-check" name="enrollmentPeriod" id="period1y" value="12">
                            <label class="btn btn-outline-primary" for="period1y">1A</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par Niveau -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Répartition par Niveau
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="levelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Détaillées et Activités -->
    <div class="row mb-4">
        <!-- Performance Académique -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-award me-2"></i>Performance Académique
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">{{ number_format($academicStats['average_grade_global'] ?? 0, 1) }}/20</h3>
                            <small class="text-muted">Moyenne Générale</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ number_format($academicStats['total_grades'] ?? 0) }}</h3>
                            <small class="text-muted">Notes Saisies</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Distribution des Notes</h6>
                    @php
                        $distribution = $academicStats['grade_distribution'] ?? [
                            'excellent' => 0,
                            'good' => 0,
                            'average' => 0,
                            'below_average' => 0,
                            'poor' => 0
                        ];
                        $total = array_sum($distribution);
                    @endphp
                    
                    @if($total > 0)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <small>Excellent (16-20)</small>
                                <small>{{ round(($distribution['excellent'] / $total) * 100, 1) }}%</small>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ ($distribution['excellent'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <small>Bien (14-15.99)</small>
                                <small>{{ round(($distribution['good'] / $total) * 100, 1) }}%</small>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ ($distribution['good'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <small>Assez Bien (12-13.99)</small>
                                <small>{{ round(($distribution['average'] / $total) * 100, 1) }}%</small>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($distribution['average'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <small>Passable (10-11.99)</small>
                                <small>{{ round(($distribution['below_average'] / $total) * 100, 1) }}%</small>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-secondary" style="width: {{ ($distribution['below_average'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <small>Insuffisant (&lt;10)</small>
                                <small>{{ round(($distribution['poor'] / $total) * 100, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ ($distribution['poor'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-graph-down fs-1"></i>
                            <p class="mb-0 mt-2">Aucune note disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Emplois du Temps Aujourd'hui -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-day me-2"></i>Cours Aujourd'hui
                        </h5>
                        <span class="badge bg-primary">{{ ($todaySchedules ?? collect())->count() }} cours</span>
                    </div>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($todaySchedules ?? [] as $schedule)
                        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                            <div class="me-3">
                                <div class="fw-bold text-primary">{{ $schedule['time'] ?? '' }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $schedule['subject'] ?? '' }}</div>
                                <small class="text-muted">{{ $schedule['class'] ?? '' }} - {{ $schedule['teacher'] ?? '' }}</small>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $schedule['room'] ?? '' }}
                                </small>
                            </div>
                            <div>
                                @switch($schedule['status'] ?? 'scheduled')
                                    @case('completed')
                                        <span class="badge bg-success">Terminé</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-warning">En cours</span>
                                        @break
                                    @case('scheduled')
                                        <span class="badge bg-secondary">Programmé</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Annulé</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-1"></i>
                            <p class="mb-0 mt-2">Aucun cours programmé aujourd'hui</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Activités Récentes -->
        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2"></i>Activités Récentes
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivities ?? [] as $activity)
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-{{ $activity['color'] ?? 'primary' }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-{{ $activity['icon'] ?? 'activity' }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $activity['title'] ?? '' }}</div>
                                <div class="text-muted small">{{ $activity['description'] ?? '' }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock"></i> {{ $activity['date']->diffForHumans() ?? '' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-activity fs-1"></i>
                            <p class="mb-0 mt-2">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Actions Rapides -->
    <div class="row mb-4">
        <!-- Alertes Système -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Alertes Système
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($alerts ?? [] as $alert)
                        <div class="alert alert-{{ $alert['type'] ?? 'info' }} d-flex align-items-center" role="alert">
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1">{{ $alert['title'] ?? '' }}</h6>
                                <p class="mb-0">{{ $alert['message'] ?? '' }}</p>
                            </div>
                            @if(isset($alert['action']))
                                <div class="ms-3">
                                    <a href="{{ $alert['action'] }}" class="btn btn-outline-{{ $alert['type'] ?? 'info' }} btn-sm">
                                        Voir
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-shield-check fs-1 text-success"></i>
                            <p class="mb-0 mt-2">Aucune alerte système</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i>Nouvel Étudiant
                        </a>
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-success">
                            <i class="bi bi-person-badge me-2"></i>Nouvel Enseignant
                        </a>
                        <a href="{{ route('admin.classes.create') }}" class="btn btn-warning">
                            <i class="bi bi-collection me-2"></i>Nouvelle Classe
                        </a>
                        <a href="{{ route('admin.schedules.create') }}" class="btn btn-info">
                            <i class="bi bi-calendar-plus me-2"></i>Nouvel Emploi du Temps
                        </a>
                        <hr>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-graph-up me-2"></i>Générer un Rapport
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-gear me-2"></i>Paramètres
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique de Présence par Jour -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>Taux de Présence - Dernières 4 Semaines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton Flottant pour Actions Rapides -->
<x-floating-action-button :actions="[
    [
        'title' => 'Nouvel Étudiant',
        'url' => route('admin.students.create'),
        'icon' => 'bi bi-person-plus',
        'color' => 'primary'
    ],
    [
        'title' => 'Nouvel Enseignant',
        'url' => route('admin.teachers.create'),
        'icon' => 'bi bi-person-badge',
        'color' => 'success'
    ],
    [
        'title' => 'Nouvelle Classe',
        'url' => route('admin.classes.create'),
        'icon' => 'bi bi-collection',
        'color' => 'warning'
    ],
    [
        'title' => 'Actualiser Dashboard',
        'url' => '#',
        'icon' => 'bi bi-arrow-clockwise',
        'color' => 'info',
        'onclick' => 'refreshDashboard()'
    ]
]" />
@endsection

@push('styles')
<style>
.stats-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    overflow: hidden;
}

.stats-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stats-card.info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.chart-container {
    position: relative;
}

.chart-container canvas {
    max-height: 100%;
}

.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.alert {
    border: none;
    border-radius: 10px;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Animation pour les cartes de statistiques */
.stats-card .card-body {
    background: transparent;
}

.stats-card h2, .stats-card h6 {
    color: white !important;
}

/* Amélioration des progress bars */
.progress {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Style pour les badges de statut */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
}

/* Animation pour les activités récentes */
.activity-item {
    transition: background-color 0.2s ease-in-out;
}

.activity-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .chart-container {
        height: 250px !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Script de diagnostic
console.log('=== DIAGNOSTIC DASHBOARD ===');
console.log('Chart.js chargé:', typeof Chart !== 'undefined');
console.log('Données reçues:', {
    generalStats: @json($generalStats ?? []),
    attendanceStats: @json($attendanceStats ?? []),
    academicStats: @json($academicStats ?? []),
    charts: @json($charts ?? [])
});

// Variables globales pour les graphiques
let enrollmentChart, levelChart, attendanceChart;

// Données par défaut si aucune donnée n'est fournie
const defaultEnrollmentData = {
    labels: ['Jan 2025', 'Fév 2025', 'Mar 2025', 'Avr 2025', 'Mai 2025', 'Juin 2025'],
    data: [15, 25, 18, 30, 22, 28]
};

const defaultLevelData = {
    labels: ['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'],
    data: [45, 38, 42, 25, 18]
};

const defaultAttendanceData = {
    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
    data: [85, 88, 82, 90, 87, 75]
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation du dashboard...');
    
    // Vérifier que Chart.js est disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé !');
        showNotification('Erreur: Chart.js non chargé. Les graphiques ne peuvent pas s\'afficher.', 'error');
        return;
    }
    
    console.log('Chart.js version:', Chart.version);
    
    // Vérifier que les canvas existent
    const canvases = {
        enrollment: document.getElementById('enrollmentChart'),
        level: document.getElementById('levelChart'),
        attendance: document.getElementById('attendanceChart')
    };
    
    console.log('Canvas trouvés:', {
        enrollment: !!canvases.enrollment,
        level: !!canvases.level,
        attendance: !!canvases.attendance
    });
    
    initializeCharts();
    
    // Actualisation automatique toutes les 5 minutes
    setInterval(refreshDashboard, 300000);
    
    // Gestion du changement de période
    initializePeriodHandlers();
});

function initializeCharts() {
    try {
        console.log('Initialisation des graphiques...');
        
        // Graphique d'évolution des inscriptions
        initializeEnrollmentChart();
        
        // Graphique de répartition par niveau
        initializeLevelChart();
        
        // Graphique de présence
        initializeAttendanceChart();
        
        console.log('Tous les graphiques ont été initialisés avec succès');
    } catch (error) {
        console.error('Erreur lors de l\'initialisation des graphiques:', error);
        showNotification('Erreur lors du chargement des graphiques', 'error');
    }
}

function initializeEnrollmentChart() {
    const enrollmentCtx = document.getElementById('enrollmentChart');
    if (!enrollmentCtx) {
        console.error('Canvas enrollmentChart non trouvé');
        return;
    }

    // Préparation des données
    let enrollmentLabels = defaultEnrollmentData.labels;
    let enrollmentData = defaultEnrollmentData.data;

    // Utiliser les données PHP si disponibles
    @if(isset($charts['enrollment_evolution']) && $charts['enrollment_evolution']->count() > 0)
        try {
            enrollmentLabels = @json($charts['enrollment_evolution']->map(function($item) {
                return \Carbon\Carbon::create($item->year, $item->month)->translatedFormat('M Y');
            }));
            enrollmentData = @json($charts['enrollment_evolution']->pluck('count'));
            console.log('Données d\'inscription utilisées:', { labels: enrollmentLabels, data: enrollmentData });
        } catch (e) {
            console.warn('Erreur dans les données d\'inscription, utilisation des données par défaut:', e);
        }
    @endif

    enrollmentChart = new Chart(enrollmentCtx, {
        type: 'line',
        data: {
            labels: enrollmentLabels,
            datasets: [{
                label: 'Nouvelles Inscriptions',
                data: enrollmentData,
                borderColor: 'rgb(37, 99, 235)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(37, 99, 235)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
    
    console.log('Graphique d\'inscription initialisé');
}

function initializeLevelChart() {
    const levelCtx = document.getElementById('levelChart');
    if (!levelCtx) {
        console.error('Canvas levelChart non trouvé');
        return;
    }

    // Préparation des données
    let levelLabels = defaultLevelData.labels;
    let levelData = defaultLevelData.data;

    // Utiliser les données PHP si disponibles
    @if(isset($charts['students_by_level']) && $charts['students_by_level']->count() > 0)
        try {
            levelLabels = @json($charts['students_by_level']->pluck('name'));
            levelData = @json($charts['students_by_level']->pluck('students_count'));
            console.log('Données de niveau utilisées:', { labels: levelLabels, data: levelData });
        } catch (e) {
            console.warn('Erreur dans les données de niveau, utilisation des données par défaut:', e);
        }
    @endif

    levelChart = new Chart(levelCtx, {
        type: 'doughnut',
        data: {
            labels: levelLabels,
            datasets: [{
                data: levelData,
                backgroundColor: [
                    'rgb(37, 99, 235)',
                    'rgb(5, 150, 105)',
                    'rgb(217, 119, 6)',
                    'rgb(8, 145, 178)',
                    'rgb(220, 38, 38)',
                    'rgb(147, 51, 234)'
                ],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });
    
    console.log('Graphique de niveau initialisé');
}

function initializeAttendanceChart() {
    const attendanceCtx = document.getElementById('attendanceChart');
    if (!attendanceCtx) {
        console.error('Canvas attendanceChart non trouvé');
        return;
    }

    // Préparation des données
    let attendanceLabels = defaultAttendanceData.labels;
    let attendanceData = defaultAttendanceData.data;

    // Utiliser les données PHP si disponibles
    @if(isset($attendanceStats['attendance_trend']) && $attendanceStats['attendance_trend']->count() > 0)
        try {
            attendanceLabels = @json($attendanceStats['attendance_trend']->pluck('date')->map(function($date) {
                return date('d/m', strtotime($date));
            }));
            attendanceData = @json($attendanceStats['attendance_trend']->pluck('rate'));
            console.log('Données de présence utilisées:', { labels: attendanceLabels, data: attendanceData });
        } catch (e) {
            console.warn('Erreur dans les données de présence, utilisation des données par défaut:', e);
        }
    @endif

    attendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: attendanceLabels,
            datasets: [{
                label: 'Taux de Présence (%)',
                data: attendanceData,
                borderColor: 'rgb(5, 150, 105)',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(5, 150, 105)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(5, 150, 105)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
    
    console.log('Graphique de présence initialisé');
}

function initializePeriodHandlers() {
    const periodInputs = document.querySelectorAll('input[name="enrollmentPeriod"]');
    periodInputs.forEach(input => {
        input.addEventListener('change', function() {
            const period = this.value;
            console.log('Changement de période:', period + ' mois');
            updateEnrollmentChart(period);
        });
    });
}

function updateEnrollmentChart(period) {
    console.log('Mise à jour du graphique pour la période:', period);
    
    if (enrollmentChart) {
        // Simuler de nouvelles données
        const newData = period === '6' ? 
            [15, 25, 18, 30, 22, 28] : 
            [12, 18, 22, 28, 35, 30, 25, 32, 28, 35, 40, 38];
        
        const newLabels = period === '6' ?
            ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'] :
            ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        enrollmentChart.data.labels = newLabels;
        enrollmentChart.data.datasets[0].data = newData;
        enrollmentChart.update();
    }
}

function refreshDashboard() {
    const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
    
    if (refreshBtn) {
        refreshBtn.innerHTML = '<span class="loading"></span> Actualisation...';
        refreshBtn.disabled = true;
    }
    
    // Appel AJAX pour récupérer de nouvelles données
    fetch('/admin/api/dashboard/stats', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        updateStatsCards(data);
        showNotification('Tableau de bord actualisé avec succès', 'success');
    })
    .catch(error => {
        console.error('Erreur lors de l\'actualisation:', error);
        showNotification('Erreur lors de l\'actualisation', 'error');
    })
    .finally(() => {
        if (refreshBtn) {
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Actualiser';
            refreshBtn.disabled = false;
        }
    });
}

function updateStatsCards(data) {
    // Mise à jour du total d'étudiants
    const studentsCard = document.querySelector('.stats-card.primary h2');
    if (studentsCard && data.total_students) {
        studentsCard.textContent = data.total_students.toLocaleString();
    }
    
    // Mise à jour du total d'enseignants
    const teachersCard = document.querySelector('.stats-card.success h2');
    if (teachersCard && data.total_teachers) {
        teachersCard.textContent = data.total_teachers.toLocaleString();
    }
    
    // Mise à jour du total de classes
    const classesCard = document.querySelector('.stats-card.warning h2');
    if (classesCard && data.total_classes) {
        classesCard.textContent = data.total_classes.toLocaleString();
    }
    
    // Mise à jour des indicateurs de croissance
    if (data.students_growth !== undefined) {
        updateGrowthIndicator('.stats-card.primary', data.students_growth);
    }
    if (data.teachers_growth !== undefined) {
        updateGrowthIndicator('.stats-card.success', data.teachers_growth);
    }
}

function updateGrowthIndicator(selector, growth) {
    const element = document.querySelector(selector + ' small');
    if (element) {
        let icon = 'bi-dash';
        if (growth > 0) icon = 'bi-arrow-up';
        else if (growth < 0) icon = 'bi-arrow-down';
        
        element.innerHTML = `<i class="bi ${icon}"></i> ${growth > 0 ? '+' : ''}${growth}% ce mois`;
    }
}

function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Export des fonctions globales
window.refreshDashboard = refreshDashboard;
window.updateEnrollmentChart = updateEnrollmentChart;
</script>
@endpush