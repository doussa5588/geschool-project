@extends('layouts.app')

@section('title', 'Gestion des départements')

@section('page-header')
    <!-- Meta tags pour CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
<style>
.stats-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.stats-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stats-card.warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.department-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e3e6f0;
}

.department-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.filter-card {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 10px;
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.progress-mini {
    height: 3px;
    border-radius: 3px;
}

.avatar-group {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
}

.avatar-group .avatar {
    border: 2px solid white;
    border-radius: 50%;
}

.department-stats {
    font-size: 0.875rem;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.table-responsive {
    border-radius: 0.375rem;
}

.department-row {
    transition: background-color 0.15s ease-in-out;
}

.department-row:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Animation pour le changement de vue */
#departmentsGrid, #departmentsList {
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    transform: translateY(0);
}

#departmentsGrid.d-none, #departmentsList.d-none {
    opacity: 0;
    transform: translateY(10px);
}

/* Amélioration des boutons de vue */
.btn-check:checked + .btn {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
}

.btn-check + .btn {
    transition: all 0.2s ease-in-out;
}

.btn-check + .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Amélioration de la vue liste */
.department-row td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.department-row:nth-child(even) {
    background-color: rgba(0, 0, 0, 0.01);
}

/* Badge animé pour les nouveaux départements */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.badge.bg-warning {
    animation: pulse 2s infinite;
}

/* Amélioration des avatars en groupe */
.avatar-group .avatar {
    position: relative;
    z-index: 1;
}

.avatar-group .avatar:not(:first-child) {
    margin-left: -0.5rem;
}

.avatar-group .avatar:hover {
    z-index: 2;
    transform: scale(1.1);
    transition: all 0.2s ease-in-out;
}
</style>
@endpush

@section('page-title', 'Départements')
@section('page-subtitle', 'Organisation académique et administrative')

@section('page-actions')
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#statsModal">
        <i class="bi bi-graph-up"></i> Statistiques
    </button>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.departments.export', ['format' => 'excel']) }}">Excel</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.departments.export', ['format' => 'pdf']) }}">PDF</a></li>
        </ul>
    </div>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nouveau département
    </a>
@endsection

@section('content')
<!-- Filtres et recherche améliorés -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.departments.index') }}" class="row align-items-end">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-search"></i> Rechercher
                        </label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nom, code ou description..." 
                               value="{{ request('search') }}"
                               id="searchInput">
                    </div>
                    <div class="col-lg-2 col-md-3 mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-funnel"></i> Statut
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actifs seulement</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactifs seulement</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 mb-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 text-end mb-3">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="view_mode" id="grid_view" autocomplete="off" checked>
                            <label class="btn btn-outline-primary btn-sm" for="grid_view">
                                <i class="bi bi-grid"></i> Grille
                            </label>
                            
                            <input type="radio" class="btn-check" name="view_mode" id="list_view" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm" for="list_view">
                                <i class="bi bi-list"></i> Liste
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Résumé statistiques amélioré -->
@if(isset($stats) && $departments->count() > 0)
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card primary hover-lift">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_departments'] }}</h3>
                        <small>Départements</small>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                    <i class="bi bi-building fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card success hover-lift">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-0">{{ $stats['active_departments'] }}</h3>
                        <small>Actifs</small>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-white" 
                                 style="width: {{ $stats['total_departments'] > 0 ? ($stats['active_departments'] / $stats['total_departments']) * 100 : 0 }}%"></div>
                        </div>
                        <small class="opacity-75">{{ $stats['inactive_departments'] }} inactifs</small>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card info hover-lift">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_teachers'] }}</h3>
                        <small>Enseignants</small>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 85%"></div>
                        </div>
                        <small class="opacity-75">{{ $stats['departments_without_teachers'] }} dép. sans enseignant</small>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card warning hover-lift">
            <div class="card-body text-center text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_subjects'] }}</h3>
                        <small>Matières</small>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 75%"></div>
                        </div>
                        <small class="opacity-75">{{ $stats['departments_without_subjects'] }} dép. sans matière</small>
                    </div>
                    <i class="bi bi-book fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Vue en grille des départements (améliorée) -->
<div class="row" id="departmentsGrid">
    @forelse($departments as $department)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 department-item" 
             data-status="{{ $department->is_active ? 'active' : 'inactive' }}"
             data-name="{{ strtolower($department->name) }}"
             data-code="{{ strtolower($department->code) }}">
            <div class="card department-card h-100">
                <div class="card-header {{ $department->is_active ? 'bg-primary' : 'bg-secondary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-building"></i> {{ $department->code }}
                            @if($department->created_at->isToday())
                                <span class="badge bg-warning ms-2">Nouveau</span>
                            @endif
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-white p-1" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.departments.show', $department) }}">
                                        <i class="bi bi-eye text-info"></i> Voir détails
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.departments.edit', $department) }}">
                                        <i class="bi bi-pencil text-warning"></i> Modifier
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-{{ $department->is_active ? 'pause text-warning' : 'play text-success' }}"></i> 
                                            {{ $department->is_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="confirmDelete({{ $department->id }}, '{{ $department->name }}')">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $department->name }}</h5>
                        @if($department->is_active)
                            <span class="badge bg-success badge-status">Actif</span>
                        @else
                            <span class="badge bg-secondary badge-status">Inactif</span>
                        @endif
                    </div>
                    
                    <p class="card-text text-muted small mb-3">
                        {{ $department->description ? Str::limit($department->description, 100) : 'Aucune description disponible.' }}
                    </p>
                    
                    <!-- Statistiques du département améliorées -->
                    <div class="row g-0 mb-3">
                        <div class="col-4 text-center">
                            <div class="border-end h-100 d-flex flex-column justify-content-center py-2">
                                <h6 class="mb-0 text-primary fw-bold">{{ $department->teachers_count ?? 0 }}</h6>
                                <small class="text-muted">Enseignants</small>
                                <div class="progress progress-mini mt-1">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ $department->teachers_count > 0 ? min(($department->teachers_count / 10) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="border-end h-100 d-flex flex-column justify-content-center py-2">
                                <h6 class="mb-0 text-success fw-bold">{{ $department->subjects_count ?? 0 }}</h6>
                                <small class="text-muted">Matières</small>
                                <div class="progress progress-mini mt-1">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $department->subjects_count > 0 ? min(($department->subjects_count / 15) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="d-flex flex-column justify-content-center py-2">
                                <h6 class="mb-0 text-info fw-bold">{{ $department->subjects->sum('credits') ?? 0 }}</h6>
                                <small class="text-muted">Crédits</small>
                                <div class="progress progress-mini mt-1">
                                    <div class="progress-bar bg-info" 
                                         style="width: {{ $department->subjects->sum('credits') > 0 ? min(($department->subjects->sum('credits') / 50) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enseignants du département -->
                    @if($department->teachers && $department->teachers->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted fw-bold d-block mb-2">
                                <i class="bi bi-people"></i> Enseignants:
                            </small>
                            <div class="avatar-group">
                                @foreach($department->teachers->take(4) as $teacher)
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->user->name) }}&background={{ $department->is_active ? '2563eb' : '6c757d' }}&color=fff&size=32" 
                                         alt="{{ $teacher->user->name }}" 
                                         class="avatar" width="32" height="32"
                                         data-bs-toggle="tooltip" 
                                         title="{{ $teacher->user->name }} - {{ $teacher->specialization }}">
                                @endforeach
                                @if($department->teachers->count() > 4)
                                    <div class="avatar bg-light text-dark d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px; border-radius: 50%; font-size: 0.75rem; border: 2px solid white;">
                                        +{{ $department->teachers->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Matières principales -->
                    @if($department->subjects && $department->subjects->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted fw-bold d-block mb-2">
                                <i class="bi bi-book"></i> Matières principales:
                            </small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($department->subjects->take(3) as $subject)
                                    <span class="badge bg-light text-dark border">{{ $subject->code }}</span>
                                @endforeach
                                @if($department->subjects->count() > 3)
                                    <span class="badge bg-secondary">+{{ $department->subjects->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Aucune matière assignée
                            </small>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Créé le {{ $department->created_at->format('d/m/Y') }}
                        </small>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.departments.show', $department) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.departments.edit', $department) }}" 
                               class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-building display-1 text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">
                    @if(request('search'))
                        Aucun résultat pour "{{ request('search') }}"
                    @else
                        Aucun département trouvé
                    @endif
                </h3>
                <p class="text-muted mb-4">
                    @if(request('search'))
                        Essayez avec d'autres termes de recherche ou consultez tous les départements.
                    @else
                        Commencez par créer votre premier département pour organiser votre établissement.
                    @endif
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    @if(request('search'))
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Voir tous les départements
                        </a>
                    @endif
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Créer un département
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Vue Liste des départements (cachée par défaut) -->
<div class="card d-none" id="departmentsList">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="bi bi-list"></i> Liste des départements
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Département</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Enseignants</th>
                        <th>Matières</th>
                        <th>Crédits</th>
                        <th>Statut</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr class="department-row" 
                            data-status="{{ $department->is_active ? 'active' : 'inactive' }}"
                            data-name="{{ strtolower($department->name) }}"
                            data-code="{{ strtolower($department->code) }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-{{ $department->is_active ? 'primary' : 'secondary' }} text-white rounded">
                                            {{ substr($department->code, 0, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $department->name }}</h6>
                                        <small class="text-muted">
                                            Créé le {{ $department->created_at->format('d/m/Y') }}
                                            @if($department->created_at->isToday())
                                                <span class="badge bg-warning ms-1">Nouveau</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold">{{ $department->code }}</span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $department->description ? Str::limit($department->description, 60) : 'Aucune description' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-primary me-2">{{ $department->teachers_count ?? 0 }}</span>
                                    @if($department->teachers && $department->teachers->count() > 0)
                                        <div class="avatar-group">
                                            @foreach($department->teachers->take(3) as $teacher)
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->user->name) }}&background=2563eb&color=fff&size=24" 
                                                     alt="{{ $teacher->user->name }}" 
                                                     class="rounded-circle border border-white" width="24" height="24"
                                                     data-bs-toggle="tooltip" title="{{ $teacher->user->name }}">
                                            @endforeach
                                            @if($department->teachers->count() > 3)
                                                <span class="badge bg-secondary rounded-circle" style="width: 24px; height: 24px; font-size: 0.6rem;">
                                                    +{{ $department->teachers->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ $department->subjects_count ?? 0 }}</span>
                                @if($department->subjects && $department->subjects->count() > 0)
                                    <br>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @foreach($department->subjects->take(2) as $subject)
                                            <span class="badge bg-light text-dark" style="font-size: 0.65rem;">{{ $subject->code }}</span>
                                        @endforeach
                                        @if($department->subjects->count() > 2)
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">+{{ $department->subjects->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-info">{{ $department->subjects->sum('credits') ?? 0 }}</span>
                                <small class="text-muted d-block">crédits</small>
                            </td>
                            <td>
                                @if($department->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.departments.show', $department) }}" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.departments.edit', $department) }}" 
                                       class="btn btn-outline-warning"
                                       data-bs-toggle="tooltip" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-{{ $department->is_active ? 'pause text-warning' : 'play text-success' }}"></i> 
                                                        {{ $department->is_active ? 'Désactiver' : 'Activer' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="confirmDelete({{ $department->id }}, '{{ $department->name }}')">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building display-4 mb-3"></i>
                                    <h5>Aucun département trouvé</h5>
                                    <p>Commencez par créer votre premier département.</p>
                                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-lg"></i> Créer un département
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Statistiques -->
@if($departments->count() > 0)
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-graph-up"></i> Statistiques des départements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <canvas id="departmentChart" width="400" height="300"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6>Répartition par département</h6>
                        @foreach($departments as $dept)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $dept->name }}</span>
                                <div>
                                    <span class="badge bg-primary me-1">{{ $dept->teachers_count ?? 0 }} enseignants</span>
                                    <span class="badge bg-success">{{ $dept->subjects_count ?? 0 }} matières</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Nouveau département',
        'url' => route('admin.departments.create'),
        'icon' => 'bi bi-building-add',
        'color' => 'primary'
    ],
    [
        'title' => 'Import Excel',
        'url' => '#',
        'icon' => 'bi bi-file-earmark-excel',
        'color' => 'success'
    ],
    [
        'title' => 'Réorganiser',
        'url' => '#',
        'icon' => 'bi bi-diagram-3',
        'color' => 'warning'
    ],
    [
        'title' => 'Rapport départements',
        'url' => '#',
        'icon' => 'bi bi-graph-up',
        'color' => 'info'
    ]
]" />

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce département ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variables globales
let departmentChart;

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    initializeTooltips();
    
    // Initialiser les événements
    initializeEvents();
    
    // Restaurer la préférence de vue
    restoreViewPreference();
    
    // Initialiser la recherche en temps réel
    initializeSearch();
    
    // Initialiser le graphique si on a des données
    @if(isset($departments) && $departments->count() > 0)
        initializeDepartmentChart();
    @endif
});

function initializeTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initializeEvents() {
    // Gestion des modes d'affichage
    const viewModeInputs = document.querySelectorAll('input[name="view_mode"]');
    viewModeInputs.forEach(input => {
        input.addEventListener('change', function() {
            const viewMode = this.id;
            toggleViewMode(viewMode);
        });
    });
    
    // Filtre par statut (boutons radio anciens) - garder pour compatibilité
    const filterInputs = document.querySelectorAll('input[name="filter"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            const filter = this.id;
            filterDepartments(filter);
        });
    });
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.toLowerCase();
                filterDepartmentsBySearch(searchTerm);
            }, 300);
        });
    }
}

function restoreViewPreference() {
    // Récupérer la préférence sauvegardée
    const savedViewMode = localStorage.getItem('departments_view_mode');
    
    if (savedViewMode) {
        // Mettre à jour le bouton radio correspondant
        const radioButton = document.getElementById(savedViewMode);
        if (radioButton) {
            radioButton.checked = true;
            // Appliquer la vue
            toggleViewMode(savedViewMode);
        }
    }
}

function filterDepartmentsBySearch(searchTerm) {
    // Filtrer la vue grille
    const departmentItems = document.querySelectorAll('.department-item');
    let visibleCountGrid = 0;
    
    departmentItems.forEach(item => {
        const name = item.getAttribute('data-name');
        const code = item.getAttribute('data-code');
        const description = item.querySelector('.card-text').textContent.toLowerCase();
        
        const isVisible = !searchTerm || 
                         name.includes(searchTerm) || 
                         code.includes(searchTerm) || 
                         description.includes(searchTerm);
        
        item.style.display = isVisible ? 'block' : 'none';
        if (isVisible) visibleCountGrid++;
    });
    
    // Filtrer la vue liste
    const departmentRows = document.querySelectorAll('.department-row');
    let visibleCountList = 0;
    
    departmentRows.forEach(row => {
        const name = row.getAttribute('data-name');
        const code = row.getAttribute('data-code');
        const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        const isVisible = !searchTerm || 
                         name.includes(searchTerm) || 
                         code.includes(searchTerm) || 
                         description.includes(searchTerm);
        
        row.style.display = isVisible ? 'table-row' : 'none';
        if (isVisible) visibleCountList++;
    });
    
    // Afficher/masquer le message "aucun résultat"
    updateEmptyState(visibleCountGrid === 0 && visibleCountList === 0, searchTerm);
}

function filterDepartments(filter) {
    // Filtrer la vue grille
    const cards = document.querySelectorAll('.department-item[data-status]');
    cards.forEach(card => {
        if (filter === 'all' || card.dataset.status === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Filtrer la vue liste
    const rows = document.querySelectorAll('.department-row[data-status]');
    rows.forEach(row => {
        if (filter === 'all' || row.dataset.status === filter) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
}

function toggleViewMode(viewMode) {
    const grid = document.getElementById('departmentsGrid');
    const list = document.getElementById('departmentsList');
    
    if (!grid || !list) {
        console.error('Elements departmentsGrid ou departmentsList non trouvés');
        return;
    }
    
    if (viewMode === 'list_view') {
        // Afficher la vue liste et masquer la grille
        grid.classList.add('d-none');
        list.classList.remove('d-none');
        
        console.log('Vue liste activée');
        
        // Réinitialiser les tooltips pour la vue liste
        setTimeout(() => {
            initializeTooltips();
        }, 100);
        
    } else {
        // Afficher la vue grille et masquer la liste
        grid.classList.remove('d-none');
        list.classList.add('d-none');
        
        console.log('Vue grille activée');
        
        // Réinitialiser les tooltips pour la vue grille
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }
    
    // Sauvegarder la préférence dans localStorage
    try {
        localStorage.setItem('departments_view_mode', viewMode);
    } catch (e) {
        console.warn('Impossible de sauvegarder la préférence de vue:', e);
    }
}

function updateEmptyState(isEmpty, searchTerm = '') {
    // Cette fonction pourrait être améliorée pour afficher dynamiquement
    // un message "aucun résultat" sans recharger la page
}

function confirmDelete(departmentId, departmentName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le département "${departmentName}" ?\n\nCette action est irréversible.`)) {
        // Créer et soumettre un formulaire de suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/departments/${departmentId}`;
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Méthode DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function initializeDepartmentChart() {
    const ctx = document.getElementById('departmentChart');
    if (ctx) {
        departmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($departments->pluck('name')) !!},
                datasets: [{
                    label: 'Enseignants',
                    data: {!! json_encode($departments->pluck('teachers_count')) !!},
                    backgroundColor: [
                        '#2563eb', '#059669', '#d97706', '#0891b2', '#7c3aed', '#dc2626',
                        '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#10b981', '#f97316'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
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
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} enseignants (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
}

// Fonction pour recharger les statistiques via AJAX
function refreshStats() {
    fetch('/admin/api/departments/stats')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les cartes de statistiques
            updateStatsCards(data);
        })
        .catch(error => {
            console.error('Erreur lors du rechargement des statistiques:', error);
        });
}

function updateStatsCards(stats) {
    // Fonction pour mettre à jour les cartes de statistiques
    // Mettre à jour les éléments avec les nouvelles données
    const totalElement = document.querySelector('[data-stat="total"]');
    const activeElement = document.querySelector('[data-stat="active"]');
    const teachersElement = document.querySelector('[data-stat="teachers"]');
    const subjectsElement = document.querySelector('[data-stat="subjects"]');
    
    if (totalElement) totalElement.textContent = stats.total;
    if (activeElement) activeElement.textContent = stats.active;
    if (teachersElement) teachersElement.textContent = stats.total_teachers;
    if (subjectsElement) subjectsElement.textContent = stats.total_subjects;
}

// Export des fonctions globales
window.confirmDelete = confirmDelete;
window.refreshStats = refreshStats;
window.toggleViewMode = toggleViewMode;
</script>
@endpush