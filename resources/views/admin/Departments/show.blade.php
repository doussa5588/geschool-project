@extends('layouts.app')

@section('title', 'Département ' . $department->name)

@section('page-title', $department->name)
@section('page-subtitle', 'Code: ' . $department->code)

@section('page-actions')
    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">
                <i class="bi bi-file-earmark-pdf"></i> Rapport PDF
            </a></li>
            <li><a class="dropdown-item" href="#">
                <i class="bi bi-file-earmark-excel"></i> Données Excel
            </a></li>
        </ul>
    </div>
@endsection

@section('content')
<!-- Statut et informations de base -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header {{ $department->is_active ? 'bg-success' : 'bg-secondary' }} text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building"></i> Informations générales
                    <span class="badge {{ $department->is_active ? 'bg-light text-success' : 'bg-light text-secondary' }} ms-2">
                        {{ $department->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Description :</h6>
                        <p class="text-muted">
                            {{ $department->description ?: 'Aucune description disponible.' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations :</h6>
                        <ul class="list-unstyled">
                            <li><strong>Code :</strong> {{ $department->code }}</li>
                            <li><strong>Créé le :</strong> {{ $department->created_at->format('d/m/Y à H:i') }}</li>
                            <li><strong>Modifié le :</strong> {{ $department->updated_at->format('d/m/Y à H:i') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiques rapides -->
    <div class="col-md-4">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['total_teachers'] }}</h3>
                        <small>Enseignants</small>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['total_subjects'] }}</h3>
                        <small>Matières</small>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['total_credits'] }}</h3>
                        <small>Crédits totaux</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques détaillées -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up"></i> Statistiques détaillées
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h4 class="text-primary">{{ $stats['total_teachers'] }}</h4>
                        <p class="mb-0">Enseignants</p>
                        <small class="text-muted">({{ $stats['active_teachers'] }} actifs)</small>
                    </div>
                    <div class="col-md-2">
                        <h4 class="text-success">{{ $stats['total_subjects'] }}</h4>
                        <p class="mb-0">Matières</p>
                        <small class="text-muted">({{ $stats['active_subjects'] }} actives)</small>
                    </div>
                    <div class="col-md-2">
                        <h4 class="text-info">{{ $stats['total_credits'] }}</h4>
                        <p class="mb-0">Crédits</p>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $stats['subjects_with_teacher'] }}</h4>
                        <p class="mb-0">Matières assignées</p>
                        <small class="text-muted">{{ $stats['subjects_without_teacher'] }} non assignées</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-secondary">{{ number_format(($stats['total_subjects'] > 0 ? ($stats['subjects_with_teacher'] / $stats['total_subjects']) * 100 : 0), 1) }}%</h4>
                        <p class="mb-0">Taux d'assignation</p>
                        <small class="text-muted">Matières / Enseignants</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enseignants du département -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people"></i> Enseignants du département
                    <span class="badge bg-primary">{{ $department->teachers->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($department->teachers->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($department->teachers as $teacher)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->user->name) }}&background=2563eb&color=fff&size=40" 
                                         alt="{{ $teacher->user->name }}" 
                                         class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">{{ $teacher->user->name }}</h6>
                                        <small class="text-muted">{{ $teacher->specialization }}</small>
                                        <br>
                                        <small class="text-muted">{{ $teacher->subjects->count() }} matière(s)</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($teacher->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $teacher->experience_years }} ans d'exp.</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.teachers.index', ['department' => $department->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i> Voir tous les enseignants
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people display-4 text-muted"></i>
                        <p class="text-muted mt-2">Aucun enseignant assigné à ce département.</p>
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus"></i> Ajouter un enseignant
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Matières du département -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-book"></i> Matières du département
                    <span class="badge bg-success">{{ $department->subjects->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($department->subjects->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($department->subjects->take(10) as $subject)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $subject->name }}</h6>
                                    <small class="text-muted">{{ $subject->code }} - {{ $subject->credits }} crédits</small>
                                    @if($subject->teacher)
                                        <br><small class="text-success">Enseignant: {{ $subject->teacher->user->name }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($subject->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                    @if(!$subject->teacher)
                                        <br><span class="badge bg-warning">Non assignée</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($department->subjects->count() > 10)
                        <div class="text-center mt-2">
                            <small class="text-muted">Et {{ $department->subjects->count() - 10 }} autres matières...</small>
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.subjects.index', ['department' => $department->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-eye"></i> Voir toutes les matières
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-book display-4 text-muted"></i>
                        <p class="text-muted mt-2">Aucune matière dans ce département.</p>
                        <a href="{{ route('admin.subjects.create') }}" class="btn btn-outline-success">
                            <i class="bi bi-plus"></i> Ajouter une matière
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning"></i> Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.teachers.create', ['department' => $department->id]) }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-person-plus"></i><br>
                            Ajouter un enseignant
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.subjects.create', ['department' => $department->id]) }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="bi bi-book-add"></i><br>
                            Créer une matière
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-info w-100 mb-2" onclick="generateReport()">
                            <i class="bi bi-graph-up"></i><br>
                            Générer un rapport
                        </button>
                    </div>
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $department->is_active ? 'warning' : 'success' }} w-100 mb-2">
                                <i class="bi bi-{{ $department->is_active ? 'pause' : 'play' }}"></i><br>
                                {{ $department->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Modifier département',
        'url' => route('admin.departments.edit', $department),
        'icon' => 'bi bi-pencil',
        'color' => 'warning'
    ],
    [
        'title' => 'Ajouter enseignant',
        'url' => route('admin.teachers.create', ['department' => $department->id]),
        'icon' => 'bi bi-person-plus',
        'color' => 'primary'
    ],
    [
        'title' => 'Créer matière',
        'url' => route('admin.subjects.create', ['department' => $department->id]),
        'icon' => 'bi bi-book-add',
        'color' => 'success'
    ],
    [
        'title' => 'Rapport détaillé',
        'url' => '#',
        'icon' => 'bi bi-file-earmark-pdf',
        'color' => 'info'
    ]
]" />
@endsection

@push('scripts')
<script>
function generateReport() {
    // Ici vous pouvez implémenter la génération de rapport
    alert('Fonctionnalité de rapport en cours de développement');
}
</script>
@endpush