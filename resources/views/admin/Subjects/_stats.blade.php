{{-- 
    Composant de statistiques pour les matières
    Usage: @include('admin.subjects._stats', ['stats' => $stats, 'type' => 'overview'])
--}}

@php
    $type = $type ?? 'overview';
    $showCharts = $showCharts ?? true;
    $cardClass = $cardClass ?? 'mb-4';
@endphp

@if($type === 'overview')
    {{-- Vue d'ensemble générale --}}
    <div class="row {{ $cardClass }}">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Matières</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($stats['total_subjects'] ?? 0) }}</h3>
                            @if(isset($stats['growth_rate']))
                                <small class="text-{{ $stats['growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                    <i class="bi bi-arrow-{{ $stats['growth_rate'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($stats['growth_rate']) }}% ce mois
                                </small>
                            @endif
                        </div>
                        <div class="text-primary opacity-75">
                            <i class="bi bi-book fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Matières Actives</h6>
                            <h3 class="mb-0 text-success">{{ number_format($stats['active_subjects'] ?? 0) }}</h3>
                            @if(isset($stats['total_subjects']) && $stats['total_subjects'] > 0)
                                <small class="text-muted">
                                    {{ round(($stats['active_subjects'] / $stats['total_subjects']) * 100, 1) }}% du total
                                </small>
                            @endif
                        </div>
                        <div class="text-success opacity-75">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Avec Enseignant</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($stats['assigned_subjects'] ?? 0) }}</h3>
                            @if(isset($stats['unassigned_subjects']))
                                <small class="text-danger">
                                    {{ $stats['unassigned_subjects'] }} non assignée(s)
                                </small>
                            @endif
                        </div>
                        <div class="text-warning opacity-75">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Crédits</h6>
                            <h3 class="mb-0 text-info">{{ number_format($stats['total_credits'] ?? 0) }}</h3>
                            @if(isset($stats['average_credits']))
                                <small class="text-muted">
                                    Moy. {{ round($stats['average_credits'], 1) }} par matière
                                </small>
                            @endif
                        </div>
                        <div class="text-info opacity-75">
                            <i class="bi bi-award fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'detailed')
    {{-- Statistiques détaillées --}}
    <div class="card {{ $cardClass }}">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Statistiques Détaillées
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" onclick="updateStatsView('week')">Semaine</button>
                    <button class="btn btn-outline-primary" onclick="updateStatsView('month')">Mois</button>
                    <button class="btn btn-outline-primary" onclick="updateStatsView('year')">Année</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    @if($showCharts)
                        <canvas id="subjectsStatsChart" height="300"></canvas>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6>Répartition par Département</h6>
                    <div class="list-group list-group-flush">
                        @forelse($stats['by_department'] ?? [] as $dept)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $dept['name'] }}</strong>
                                    <br><small class="text-muted">{{ $dept['code'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary rounded-pill">{{ $dept['subjects_count'] }}</span>
                                    <br><small class="text-muted">{{ $dept['total_credits'] }} crédits</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-3">
                                Aucune donnée disponible
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'performance')
    {{-- Statistiques de performance --}}
    <div class="row {{ $cardClass }}">
        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data fs-1 mb-2 opacity-75"></i>
                    <h4 class="mb-1">{{ number_format($stats['total_grades'] ?? 0) }}</h4>
                    <small>Notes Enregistrées</small>
                    @if(isset($stats['recent_grades']))
                        <div class="mt-2">
                            <small class="opacity-75">+{{ $stats['recent_grades'] }} cette semaine</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up-arrow fs-1 mb-2 opacity-75"></i>
                    <h4 class="mb-1">{{ number_format($stats['average_grade'] ?? 0, 1) }}</h4>
                    <small>Moyenne Générale</small>
                    @if(isset($stats['pass_rate']))
                        <div class="mt-2">
                            <small class="opacity-75">{{ $stats['pass_rate'] }}% de réussite</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 mb-2 opacity-75"></i>
                    <h4 class="mb-1">{{ number_format($stats['total_students'] ?? 0) }}</h4>
                    <small>Étudiants Actifs</small>
                    @if(isset($stats['students_per_subject']))
                        <div class="mt-2">
                            <small class="opacity-75">Moy. {{ round($stats['students_per_subject'], 1) }} par matière</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'alerts')
    {{-- Alertes et notifications --}}
    <div class="card {{ $cardClass }}">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>Alertes et Notifications
            </h5>
        </div>
        <div class="card-body">
            @if(isset($stats['alerts']) && count($stats['alerts']) > 0)
                @foreach($stats['alerts'] as $alert)
                    <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center">
                        <i class="bi bi-{{ $alert['icon'] ?? 'info-circle' }} me-2"></i>
                        <div class="flex-grow-1">
                            <strong>{{ $alert['title'] }}</strong>
                            @if(isset($alert['message']))
                                <br><small>{{ $alert['message'] }}</small>
                            @endif
                        </div>
                        @if(isset($alert['action']))
                            <a href="{{ $alert['action'] }}" class="btn btn-outline-{{ $alert['type'] }} btn-sm">
                                Voir
                            </a>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-check-circle fs-1"></i>
                    <p class="mt-2">Aucune alerte</p>
                </div>
            @endif
        </div>
    </div>

@elseif($type === 'mini')
    {{-- Version mini pour tableaux de bord --}}
    <div class="row g-2">
        <div class="col-3">
            <div class="text-center p-2 bg-primary bg-opacity-10 rounded">
                <h6 class="mb-0 text-primary">{{ $stats['total_subjects'] ?? 0 }}</h6>
                <small class="text-muted">Total</small>
            </div>
        </div>
        <div class="col-3">
            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                <h6 class="mb-0 text-success">{{ $stats['active_subjects'] ?? 0 }}</h6>
                <small class="text-muted">Actives</small>
            </div>
        </div>
        <div class="col-3">
            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                <h6 class="mb-0 text-warning">{{ $stats['assigned_subjects'] ?? 0 }}</h6>
                <small class="text-muted">Assignées</small>
            </div>
        </div>
        <div class="col-3">
            <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                <h6 class="mb-0 text-info">{{ $stats['total_credits'] ?? 0 }}</h6>
                <small class="text-muted">Crédits</small>
            </div>
        </div>
    </div>
@endif

@if($showCharts && $type === 'detailed')
@push('scripts')
<script>
$(document).ready(function() {
    initializeSubjectsStatsChart();
});

function initializeSubjectsStatsChart() {
    const ctx = document.getElementById('subjectsStatsChart');
    if (!ctx) return;

    const chartData = @json($stats['chart_data'] ?? []);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Actives', 'Inactives', 'Sans enseignant'],
            datasets: [{
                data: [
                    {{ $stats['active_subjects'] ?? 0 }},
                    {{ $stats['inactive_subjects'] ?? 0 }},
                    {{ $stats['unassigned_subjects'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(239, 68, 68)',
                    'rgb(245, 158, 11)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateStatsView(period) {
    // Update active button
    $('.btn-group .btn').removeClass('active');
    $(`button[onclick="updateStatsView('${period}')"]`).addClass('active');
    
    // TODO: Implement AJAX call to update stats
    console.log('Updating stats for period:', period);
}
</script>
@endpush
@endif

@push('styles')
<style>
.stats-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.border-4 {
    border-width: 4px !important;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endpush