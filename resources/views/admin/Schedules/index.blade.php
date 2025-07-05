@extends('layouts.app')

@section('title', 'Gestion des emplois du temps')

@section('page-header')
@endsection

@section('page-title', 'Emplois du temps')
@section('page-subtitle', 'Planification et organisation des cours')

@section('page-actions')
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-calendar-week"></i> Vue
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?view=week">Vue hebdomadaire</a></li>
            <li><a class="dropdown-item" href="?view=day">Vue journalière</a></li>
            <li><a class="dropdown-item" href="?view=list">Vue liste</a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#conflictsModal">
        <i class="bi bi-exclamation-triangle"></i> Conflits
    </button>
    <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nouveau créneau
    </a>
@endsection

@section('content')
<!-- Filtres et Statistiques -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Niveau</label>
                        <select name="level_id" class="form-select form-select-sm">
                            <option value="">Tous les niveaux</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Classe</label>
                        <select name="class_id" class="form-select form-select-sm">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Enseignant</label>
                        <select name="teacher_id" class="form-select form-select-sm">
                            <option value="">Tous les enseignants</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Filtrer</button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-6">
                <div class="card stats-card primary hover-lift">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar3 fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ $totalSchedules }}</h4>
                        <small>Total créneaux</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card stats-card {{ $conflicts > 0 ? 'warning' : 'success' }} hover-lift">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                        <h4 class="mb-0">{{ is_array($conflicts) ? count($conflicts) : $conflicts }}</h4>
                        <small>Conflits détectés</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vue Hebdomadaire -->
@if(request('view', 'week') === 'week')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="bi bi-calendar-week"></i> Planning hebdomadaire
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" onclick="previousWeek()">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-outline-secondary" onclick="currentWeek()">Aujourd'hui</button>
                <button class="btn btn-outline-secondary" onclick="nextWeek()">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" id="scheduleTable">
                <thead class="table-light">
                    <tr>
                        <th width="100">Horaires</th>
                        <th>Lundi</th>
                        <th>Mardi</th>
                        <th>Mercredi</th>
                        <th>Jeudi</th>
                        <th>Vendredi</th>
                        <th>Samedi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $slot)
                        <tr>
                            <td class="fw-bold text-center bg-light">
                                {{ \Carbon\Carbon::parse($slot)->format('H:i') }}
                            </td>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <td class="schedule-cell p-1" data-day="{{ $day }}" data-time="{{ $slot }}">
                                    @foreach($schedulesByDay[$day] ?? [] as $schedule)
                                        @if(\Carbon\Carbon::parse($schedule->start_time)->format('H:i') === \Carbon\Carbon::parse($slot)->format('H:i'))
                                            <div class="schedule-item p-2 rounded mb-1 {{ $schedule->type === 'lecture' ? 'bg-primary' : ($schedule->type === 'lab' ? 'bg-success' : 'bg-info') }} text-white"
                                                 data-bs-toggle="popover" 
                                                 data-bs-trigger="hover"
                                                 data-bs-html="true"
                                                 data-bs-content="
                                                    <strong>{{ $schedule->subject->name }}</strong><br>
                                                    Enseignant: {{ $schedule->teacher->user->name }}<br>
                                                    Classe: {{ $schedule->class->name }}<br>
                                                    Salle: {{ $schedule->room }}<br>
                                                    Type: {{ ucfirst($schedule->type) }}
                                                 ">
                                                <div class="fw-bold" style="font-size: 0.75rem;">
                                                    {{ $schedule->subject->code }}
                                                </div>
                                                <div style="font-size: 0.65rem;">
                                                    {{ $schedule->class->name }} • {{ $schedule->room }}
                                                </div>
                                                <div style="font-size: 0.6rem;">
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Vue Liste -->
@if(request('view') === 'list')
<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="bi bi-list"></i> Liste des créneaux
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>Jour</th>
                        <th>Horaires</th>
                        <th>Matière</th>
                        <th>Enseignant</th>
                        <th>Classe</th>
                        <th>Salle</th>
                        <th>Type</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($schedule->day_of_week) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</strong>
                                -
                                <strong>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $schedule->subject->name }}</strong>
                                    <br><small class="text-muted">{{ $schedule->subject->code }}</small>
                                </div>
                            </td>
                            <td>{{ $schedule->teacher->user->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $schedule->class->name }}</span>
                            </td>
                            <td>{{ $schedule->room }}</td>
                            <td>
                                @switch($schedule->type)
                                    @case('lecture')
                                        <span class="badge bg-primary">Cours</span>
                                        @break
                                    @case('lab')
                                        <span class="badge bg-success">TP</span>
                                        @break
                                    @case('tutorial')
                                        <span class="badge bg-info">TD</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" 
                                       class="btn btn-outline-warning"
                                       data-bs-toggle="tooltip" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger"
                                            onclick="confirmDelete({{ $schedule->id }})"
                                            data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Créer créneau',
        'url' => route('admin.schedules.create'),
        'icon' => 'bi bi-calendar-plus',
        'color' => 'primary'
    ],
    [
        'title' => 'Import Excel',
        'url' => '#',
        'icon' => 'bi bi-file-earmark-excel',
        'color' => 'success'
    ],
    [
        'title' => 'Planification auto',
        'url' => '#',
        'icon' => 'bi bi-magic',
        'color' => 'warning'
    ],
    [
        'title' => 'Rapport planning',
        'url' => '#',
        'icon' => 'bi bi-graph-up',
        'color' => 'info'
    ]
]" />

<!-- Modal Conflits -->
<div class="modal fade" id="conflictsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Conflits détectés
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(count($conflicts) > 0)
                    <div class="alert alert-warning">
                        <strong>{{ count($conflicts) }} conflit(s) détecté(s)</strong> dans les plannings.
                    </div>

                    <!-- Liste des conflits à implémenter ici -->
                    <ul class="list-group">
                        @foreach($conflicts as $conflict)
                            <li class="list-group-item">
                                {{ $conflict['message'] ?? 'Conflit non spécifié' }}
                            </li>
                        @endforeach
                    </ul>

                @else
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        Aucun conflit détecté dans les plannings.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser les popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

function confirmDelete(scheduleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) {
        // Implémentation de la suppression
        console.log('Suppression du créneau:', scheduleId);
    }
}

function previousWeek() {
    // Navigation semaine précédente
    console.log('Semaine précédente');
}

function currentWeek() {
    // Retour à la semaine courante
    console.log('Semaine courante');
}

function nextWeek() {
    // Navigation semaine suivante
    console.log('Semaine suivante');
}
</script>
@endpush