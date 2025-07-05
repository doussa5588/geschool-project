@extends('layouts.app')

@section('title', 'Gestion des Classes - UNCHK')

@section('page-header')
@section('page-title', 'Gestion des Classes')
@section('page-subtitle', 'Organisation et gestion de toutes les classes de l\'établissement')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{-- route('admin.classes.export') --}}?format=excel">
                <i class="bi bi-file-excel me-2"></i>Liste Excel
            </a></li>
            <li><a class="dropdown-item" href="{{-- route('admin.classes.export') --}}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>Rapport PDF
            </a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-info me-2" onclick="viewScheduleOverview()">
        <i class="bi bi-calendar3"></i> Vue Emplois du Temps
    </button>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
        <i class="bi bi-collection"></i> Nouvelle Classe
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Classes</h6>
                            <h3 class="mb-0">{{ $classes->total() }}</h3>
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
                            <h6 class="card-title text-white-50 mb-2">Classes Actives</h6>
                            <h3 class="mb-0">{{ $classes->where('is_active', true)->count() }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Étudiants Total</h6>
                            <h3 class="mb-0">{{ $classes->sum('students_count') }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Taux d'Occupation</h6>
                            @php
                                $totalCapacity = $classes->sum('capacity');
                                $totalStudents = $classes->sum('students_count');
                                $occupancyRate = $totalCapacity > 0 ? round(($totalStudents / $totalCapacity) * 100, 1) : 0;
                            @endphp
                            <h3 class="mb-0">{{ $occupancyRate }}%</h3>
                        </div>
                        <i class="bi bi-pie-chart fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtres de Recherche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.classes.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nom, code, salle...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="level_id" class="form-label">Niveau</label>
                        <select class="form-select" id="level_id" name="level_id">
                            <option value="">Tous les niveaux</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" 
                                        {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="department_id" class="form-label">Département</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" 
                                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="academic_year" class="form-label">Année Académique</label>
                        <select class="form-select" id="academic_year" name="academic_year">
                            <option value="">Toutes les années</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" 
                                        {{ request('academic_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Effacer
                            </a>

                            <!-- Bouton Export -->
                            <button type="button" class="btn btn-success" onclick="exportClasses('xlsx')">
                                <i class="bi bi-file-earmark-excel"></i> Exporter Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vue d'ensemble par Niveau et Département -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Répartition par Niveau</h5>
                </div>
                <div class="card-body">
                    @foreach($levels as $level)
                        @php
                            $levelClasses = $classes->where('level_id', $level->id);
                            $classCount = $levelClasses->count();
                            $studentCount = $levelClasses->sum('students_count');
                            $capacityCount = $levelClasses->sum('capacity');
                            $percentage = $classes->count() > 0 ? round(($classCount / $classes->count()) * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-semibold">{{ $level->name }}</span>
                                    <small class="text-muted ms-2">{{ $classCount }} classe(s)</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">{{ $studentCount }}/{{ $capacityCount }}</div>
                                    <small class="text-muted">étudiants</small>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $percentage }}% des classes</small>
                                @if($capacityCount > 0)
                                    <small class="text-muted">
                                        {{ round(($studentCount / $capacityCount) * 100, 1) }}% d'occupation
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Répartition par Département</h5>
                </div>
                <div class="card-body">
                    @foreach($departments as $department)
                        @php
                            $deptClasses = $classes->where('department_id', $department->id);
                            $classCount = $deptClasses->count();
                            $studentCount = $deptClasses->sum('students_count');
                            $capacityCount = $deptClasses->sum('capacity');
                            $percentage = $classes->count() > 0 ? round(($classCount / $classes->count()) * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-semibold">{{ $department->name }}</span>
                                    <small class="text-muted ms-2">{{ $classCount }} classe(s)</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">{{ $studentCount }}/{{ $capacityCount }}</div>
                                    <small class="text-muted">étudiants</small>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $percentage }}% des classes</small>
                                @if($capacityCount > 0)
                                    <small class="text-muted">
                                        {{ round(($studentCount / $capacityCount) * 100, 1) }}% d'occupation
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Classes -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Liste des Classes 
                    <span class="badge bg-primary ms-2">{{ $classes->total() }} résultat(s)</span>
                </h5>
                <div class="d-flex gap-2">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" onclick="toggleView('table')">
                            <i class="bi bi-table"></i> Tableau
                        </button>
                        <button class="btn btn-outline-secondary" onclick="toggleView('grid')">
                            <i class="bi bi-grid"></i> Grille
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($classes->count() > 0)
                <!-- Vue Tableau -->
                <div id="tableView">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="classesTable">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Niveau</th>
                                    <th>Département</th>
                                    <th>Étudiants</th>
                                    <th>Capacité</th>
                                    <th>Occupation</th>
                                    <th>Salle</th>
                                    <th>Année</th>
                                    <th>Statut</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $class)
                                    @php
                                        $occupancyRate = $class->capacity > 0 ? round(($class->students_count / $class->capacity) * 100, 1) : 0;
                                        $occupancyClass = $occupancyRate >= 100 ? 'danger' : ($occupancyRate >= 80 ? 'warning' : 'success');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <strong>{{ substr($class->code, 0, 2) }}</strong>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $class->name }}</div>
                                                    <small class="text-muted">{{ $class->code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $class->level->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{-- $class->department->name --}}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $class->students_count }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $class->capacity }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $occupancyClass }}" 
                                                         style="width: {{ min($occupancyRate, 100) }}%"></div>
                                                </div>
                                                <small class="text-{{ $occupancyClass }}">{{ $occupancyRate }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($class->room)
                                                <i class="bi bi-geo-alt me-1"></i>{{ $class->room }}
                                            @else
                                                <span class="text-muted">Non assignée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $class->academic_year }}</small>
                                        </td>
                                        <td>
                                            @if($class->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.classes.show', $class) }}" 
                                                   class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir détails">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{-- route('admin.classes.students', $class) --}}" 
                                                   class="btn btn-outline-info" data-bs-toggle="tooltip" title="Étudiants">
                                                    <i class="bi bi-people"></i>
                                                </a>
                                                <a href="{{-- route('admin.classes.schedule', $class) --}}" 
                                                   class="btn btn-outline-success" data-bs-toggle="tooltip" title="Emploi du temps">
                                                    <i class="bi bi-calendar3"></i>
                                                </a>
                                                <a href="{{ route('admin.classes.edit', $class) }}" 
                                                   class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deleteClass({{ $class->id }})" 
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

                <!-- Vue Grille -->
                <div id="gridView" style="display: none;">
                    <div class="row">
                        @foreach($classes as $class)
                            @php
                                $occupancyRate = $class->capacity > 0 ? round(($class->students_count / $class->capacity) * 100, 1) : 0;
                                $occupancyClass = $occupancyRate >= 100 ? 'danger' : ($occupancyRate >= 80 ? 'warning' : 'success');
                            @endphp
                            <div class="col-xl-4 col-lg-6 mb-4">
                                <div class="card class-card h-100 hover-lift">
                                    <div class="card-header bg-{{ $occupancyClass }} text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $class->name }}</h6>
                                            @if($class->is_active)
                                                <i class="bi bi-check-circle"></i>
                                            @else
                                                <i class="bi bi-x-circle"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <h5 class="mb-0 text-primary">{{ $class->students_count }}</h5>
                                                <small class="text-muted">Étudiants</small>
                                            </div>
                                            <div class="col-4">
                                                <h5 class="mb-0 text-info">{{ $class->capacity }}</h5>
                                                <small class="text-muted">Capacité</small>
                                            </div>
                                            <div class="col-4">
                                                <h5 class="mb-0 text-{{ $occupancyClass }}">{{ $occupancyRate }}%</h5>
                                                <small class="text-muted">Occupation</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Niveau:</strong></small>
                                                <span class="badge bg-info">{{ $class->level->name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Département:</strong></small>
                                                <span class="badge bg-secondary">{{-- $class->department->name --}}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Code:</strong></small>
                                                <code>{{ $class->code }}</code>
                                            </div>
                                            @if($class->room)
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Salle:</strong></small>
                                                <span><i class="bi bi-geo-alt me-1"></i>{{ $class->room }}</span>
                                            </div>
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <small><strong>Année:</strong></small>
                                                <span>{{ $class->academic_year }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="progress mb-3" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $occupancyClass }}" 
                                                 style="width: {{ min($occupancyRate, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-grid gap-2">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Détails
                                                </a>
                                                <a href="{{-- route('admin.classes.students', $class) --}}" class="btn btn-outline-info">
                                                    <i class="bi bi-people"></i> Étudiants
                                                </a>
                                                <a href="{{-- route('admin.classes.schedule', $class) --}}" class="btn btn-outline-success">
                                                    <i class="bi bi-calendar3"></i> Planning
                                                </a>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-warning">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                <button type="button" class="btn btn-danger" onclick="deleteClass({{ $class->id }})">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Affichage de {{ $classes->firstItem() }} à {{ $classes->lastItem() }} 
                        sur {{ $classes->total() }} résultats
                    </div>
                    {{ $classes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-collection fs-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucune classe trouvée</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'level_id', 'department_id', 'academic_year']))
                            Aucune classe ne correspond aux critères de recherche.
                        @else
                            Commencez par créer des classes pour organiser les étudiants.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'level_id', 'department_id', 'academic_year']))
                        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Créer la première classe
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmation de Suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette classe ?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action ne peut pas être effectuée si la classe contient des étudiants. 
                    Vous devez d'abord transférer tous les étudiants vers d'autres classes.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aperçu Emplois du Temps -->
<div class="modal fade" id="scheduleOverviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vue d'ensemble - Emplois du Temps</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="scheduleOverviewContent">
                    <div class="text-center py-4">
                        <span class="loading"></span> Chargement des emplois du temps...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.class-card {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.class-card:hover {
    border-color: #2563eb;
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.schedule-overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.schedule-class-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
}

.schedule-slot {
    background: #e3f2fd;
    border: 1px solid #1976d2;
    border-radius: 4px;
    padding: 0.5rem;
    margin: 0.25rem 0;
    font-size: 0.8rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // DataTable for table view
    $('#classesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']], // Sort by class name
        columnDefs: [
            { orderable: false, targets: [9] }, // Disable sorting for actions
        ],
        dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });
    
    // Auto-submit form on filter change
    $('#level_id, #department_id, #academic_year').change(function() {
        $('#filterForm').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
});

function toggleView(viewType) {
    if (viewType === 'table') {
        $('#tableView').show();
        $('#gridView').hide();
        $('.btn-group .btn').removeClass('active');
        $('button[onclick="toggleView(\'table\')"]').addClass('active');
    } else {
        $('#tableView').hide();
        $('#gridView').show();
        $('.btn-group .btn').removeClass('active');
        $('button[onclick="toggleView(\'grid\')"]').addClass('active');
    }
}

function deleteClass(classId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '{{ route("admin.classes.destroy", "") }}/' + classId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function viewScheduleOverview() {
    const modal = new bootstrap.Modal(document.getElementById('scheduleOverviewModal'));
    modal.show();
    
    // Load schedule overview via AJAX
    $.ajax({
        url: '{{-- route("admin.schedules.overview") --}}',
        method: 'GET',
        beforeSend: function() {
            $('#scheduleOverviewContent').html(`
                <div class="text-center py-4">
                    <span class="loading"></span> Chargement des emplois du temps...
                </div>
            `);
        },
        success: function(data) {
            $('#scheduleOverviewContent').html(data);
        },
        error: function() {
            $('#scheduleOverviewContent').html(`
                <div class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mt-2">Erreur lors du chargement des emplois du temps</p>
                </div>
            `);
        }
    });
}

// Class capacity management
function checkCapacityAlerts() {
    $('.progress-bar').each(function() {
        const percentage = parseFloat($(this).css('width'));
        if (percentage >= 100) {
            $(this).closest('tr').addClass('table-danger');
        } else if (percentage >= 90) {
            $(this).closest('tr').addClass('table-warning');
        }
    });
}

// Call on page load
$(document).ready(function() {
    checkCapacityAlerts();
});

// Quick actions for classes
function quickAssignRoom(classId) {
    const room = prompt('Entrez le nom de la salle:');
    if (room) {
        $.ajax({
            url: `{{ route("admin.classes.update", "") }}/${classId}`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                room: room
            },
            success: function() {
                location.reload();
            },
            error: function() {
                alert('Erreur lors de l\'assignation de la salle');
            }
        });
    }
}

function transferStudents(classId) {
    // TODO: Open modal for student transfer
    alert('Fonction de transfert d\'étudiants à implémenter - Classe ID: ' + classId);
}

// Export functionality
function exportClasses(format) {
    const currentUrl = new URL(window.location);
    currentUrl.pathname = '{{-- route("admin.classes.export") --}}';
    currentUrl.searchParams.set('format', format);
    
    // Add current filters to export
    const formData = new FormData(document.getElementById('filterForm'));
    for (let [key, value] of formData.entries()) {
        if (value) {
            currentUrl.searchParams.set(key, value);
        }
    }
    
    window.open(currentUrl.toString(), '_blank');
}

// Real-time occupancy monitoring
function updateOccupancyRates() {
    // TODO: Implement real-time updates via WebSocket or periodic AJAX
    console.log('Updating occupancy rates...');
}

// Update every 5 minutes
setInterval(updateOccupancyRates, 300000);
</script>
@endpush

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Nouvelle classe',
        'url' => route('admin.classes.create'),
        'icon' => 'bi bi-person-add',
        'color' => 'primary'
    ],
    [
        'title' => 'Importer depuis Excel',
        'url' => '#',
        'icon' => 'bi bi-file-earmark-excel',
        'color' => 'success'
    ],
    [
        'title' => 'Réorganiser les classes',
        'url' => '#',
        'icon' => 'bi bi-ui-checks-grid',
        'color' => 'warning'
    ],
    [
        'title' => 'Rapport des classes',
        'url' => '#',
        'icon' => 'bi bi-bar-chart-line',
        'color' => 'info'
    ]
]" />