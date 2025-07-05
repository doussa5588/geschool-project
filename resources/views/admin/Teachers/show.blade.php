@extends('layouts.app')

@section('title', 'Profil de ' . $teacher->user->name . ' - UNCHK')

@section('page-header')
@section('page-title')
    <i class="bi bi-person-badge"></i> Profil de l'Enseignant
@endsection

@section('page-subtitle')
    Informations détaillées et statistiques de {{ $teacher->user->name }}
@endsection

@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?teacher_id={{ $teacher->id }}&format=profile">
                <i class="bi bi-file-person me-2"></i>Profil complet
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?teacher_id={{ $teacher->id }}&format=planning">
                <i class="bi bi-calendar3 me-2"></i>Planning
            </a></li>
        </ul>
    </div>
    <a href="{{ route('admin.teachers.subjects', $teacher) }}" class="btn btn-info me-2">
        <i class="bi bi-book"></i> Gérer les Matières
    </a>
    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-warning me-2">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête du profil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($teacher->user->profile_photo)
                                <img src="{{ Storage::url($teacher->user->profile_photo) }}" 
                                     alt="{{ $teacher->user->name }}" 
                                     class="rounded-circle shadow" 
                                     width="120" height="120"
                                     style="object-fit: cover; border: 4px solid #fff;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->user->name) }}&background=059669&color=fff&size=120" 
                                     alt="{{ $teacher->user->name }}" 
                                     class="rounded-circle shadow" 
                                     width="120" height="120"
                                     style="border: 4px solid #fff;">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h2 class="mb-1 fw-bold text-primary">{{ $teacher->user->name }}</h2>
                            <p class="text-muted mb-2 fs-5">{{ $teacher->specialization }}</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary fs-6">{{ $teacher->employee_number }}</span>
                                @if($teacher->department)
                                    <span class="badge bg-info fs-6">{{ $teacher->department->name }}</span>
                                @endif
                                <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'warning' }} fs-6">
                                    {{ ucfirst($teacher->status_french) }}
                                </span>
                            </div>
                            <div class="row text-sm">
                                <div class="col-sm-6">
                                    <i class="bi bi-envelope text-muted me-2"></i>{{ $teacher->user->email }}
                                </div>
                                <div class="col-sm-6">
                                    <i class="bi bi-telephone text-muted me-2"></i>{{ $teacher->user->phone ?? 'Non défini' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row g-3">
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded p-3">
                                        <h4 class="text-primary mb-1">{{ $stats['experience_years'] }}</h4>
                                        <small class="text-muted">Années d'expérience</small>
                                    </div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded p-3">
                                        <h4 class="text-success mb-1">{{ $stats['total_subjects'] }}</h4>
                                        <small class="text-muted">Matières enseignées</small>
                                    </div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded p-3">
                                        <h4 class="text-info mb-1">{{ $stats['total_credits'] }}</h4>
                                        <small class="text-muted">Crédits totaux</small>
                                    </div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded p-3">
                                        <h4 class="text-warning mb-1">{{ $stats['total_classes'] ?? 0 }}</h4>
                                        <small class="text-muted">Classes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Notes Données</h6>
                            <h3 class="mb-0">{{ $stats['grades_given_this_month'] ?? 0 }}</h3>
                            <small class="text-white-50">Ce mois-ci</small>
                        </div>
                        <i class="bi bi-clipboard-check fs-1 text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Heures/Semaine</h6>
                            <h3 class="mb-0">{{ ($stats['total_credits'] ?? 0) * 1.5 }}h</h3>
                            <small class="text-white-50">Charge estimée</small>
                        </div>
                        <i class="bi bi-clock fs-1 text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Ancienneté</h6>
                            <h3 class="mb-0">{{ $teacher->seniority_level_french }}</h3>
                            <small class="text-white-50">{{ $stats['experience_years'] }} an(s)</small>
                        </div>
                        <i class="bi bi-award fs-1 text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Salaire</h6>
                            <h3 class="mb-0">{{ $teacher->formatted_salary }}</h3>
                            <small class="text-white-50">Mensuel</small>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Date de naissance</label>
                        <p class="mb-0">
                            @if($teacher->user->date_of_birth)
                                {{ \Carbon\Carbon::parse($teacher->user->date_of_birth)->format('d/m/Y') }}
                                <small class="text-muted">({{ \Carbon\Carbon::parse($teacher->user->date_of_birth)->age }} ans)</small>
                            @else
                                <span class="text-muted">Non définie</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Adresse</label>
                        <p class="mb-0">{{ $teacher->user->address ?? 'Non définie' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Date d'embauche</label>
                        <p class="mb-0">
                            {{ $teacher->formatted_hire_date }}
                            @if($teacher->hire_date)
                                <small class="text-muted">({{ \Carbon\Carbon::parse($teacher->hire_date)->diffForHumans() }})</small>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Département</label>
                        <p class="mb-0">
                            @if($teacher->department)
                                <span class="badge bg-primary">{{ $teacher->department->name }}</span>
                                <br><small class="text-muted">{{ $teacher->department->description ?? '' }}</small>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Spécialisation</label>
                        <p class="mb-0">{{ $teacher->specialization }}</p>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-muted">Statut</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : ($teacher->status === 'suspended' ? 'warning' : 'danger') }} fs-6">
                                {{ $teacher->status_french }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matières enseignées -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book text-primary me-2"></i>
                        Matières Enseignées ({{ $teacher->subjects->count() }})
                    </h5>
                    <a href="{{ route('admin.teachers.subjects', $teacher) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-gear"></i> Gérer
                    </a>
                </div>
                <div class="card-body">
                    @if($teacher->subjects->count() > 0)
                        <div class="row">
                            @foreach($teacher->subjects as $subject)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-primary mb-1">{{ $subject->name }}</h6>
                                        <span class="badge bg-success">{{ $subject->credits }} crédits</span>
                                    </div>
                                    <p class="mb-2 text-muted small">
                                        <strong>Code:</strong> {{ $subject->code }}
                                    </p>
                                    @if($subject->description)
                                        <p class="mb-2 small text-muted">
                                            {{ Str::limit($subject->description, 100) }}
                                        </p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-building me-1"></i>{{ $subject->department->name ?? 'N/A' }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            @if(isset($subject->grades_count))
                                                <small class="text-success me-2">
                                                    <i class="bi bi-clipboard-check"></i> {{ $subject->grades_count ?? 0 }} notes
                                                </small>
                                            @endif
                                            <a href="{{ route('admin.subjects.show', $subject) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               data-bs-toggle="tooltip" title="Voir la matière">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Résumé des matières -->
                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-3 text-center">
                                <h5 class="text-primary">{{ $teacher->subjects->count() }}</h5>
                                <small class="text-muted">Matières</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-success">{{ $teacher->subjects->sum('credits') }}</h5>
                                <small class="text-muted">Crédits totaux</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-info">{{ $teacher->subjects->groupBy('department_id')->count() }}</h5>
                                <small class="text-muted">Départements</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-warning">{{ ($teacher->subjects->sum('credits') * 1.5) }}h</h5>
                                <small class="text-muted">Charge/semaine</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Aucune matière assignée</h5>
                            <p class="text-muted">Cet enseignant n'a encore aucune matière assignée.</p>
                            <a href="{{ route('admin.teachers.subjects', $teacher) }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Assigner des matières
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente et performance -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity text-primary me-2"></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Embauché</h6>
                                <p class="mb-1 text-muted">{{ $teacher->formatted_hire_date }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($teacher->hire_date)->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if($teacher->subjects->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Matières assignées</h6>
                                <p class="mb-1 text-muted">{{ $teacher->subjects->count() }} matière(s)</p>
                                <small class="text-muted">{{ $teacher->subjects->max('created_at') ? \Carbon\Carbon::parse($teacher->subjects->max('created_at'))->diffForHumans() : 'Récemment' }}</small>
                            </div>
                        </div>
                        @endif
                        @if($stats['grades_given_this_month'] > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Notes saisies</h6>
                                <p class="mb-1 text-muted">{{ $stats['grades_given_this_month'] }} note(s) ce mois</p>
                                <small class="text-muted">Activité récente</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $teacher->seniority_level_french }}</h4>
                                <small class="text-muted">Niveau d'expérience</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">
                                    @if($teacher->subjects->count() > 0)
                                        {{ round(($teacher->subjects->sum('credits') / $teacher->subjects->count()), 1) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                                <small class="text-muted">Crédits/Matière</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="progress" style="height: 20px;">
                                @php
                                    $maxCredits = 30; // Charge maximale estimée
                                    $currentCredits = $teacher->subjects->sum('credits');
                                    $percentage = min(($currentCredits / $maxCredits) * 100, 100);
                                @endphp
                                <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'success') }}" 
                                     style="width: {{ $percentage }}%">
                                    {{ $currentCredits }} / {{ $maxCredits }} crédits
                                </div>
                            </div>
                            <small class="text-muted">Charge de travail (crédits)</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Matières enseignées</span>
                        <span class="fw-bold">{{ $teacher->subjects->count() }}/10</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ min(($teacher->subjects->count() / 10) * 100, 100) }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Ancienneté</span>
                        <span class="fw-bold">{{ $stats['experience_years'] }}/20 ans</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min(($stats['experience_years'] / 20) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-pencil-square d-block fs-2 mb-2"></i>
                                Modifier le profil
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.teachers.subjects', $teacher) }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-book d-block fs-2 mb-2"></i>
                                Gérer les matières
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#planningModal">
                                <i class="bi bi-calendar3 d-block fs-2 mb-2"></i>
                                Voir le planning
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="bi bi-envelope d-block fs-2 mb-2"></i>
                                Envoyer un message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Planning -->
<div class="modal fade" id="planningModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar3"></i> Planning de {{ $teacher->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Le planning détaillé sera affiché ici une fois que les emplois du temps seront configurés.
                </div>
                
                <!-- Planning basique basé sur les matières -->
                @if($teacher->subjects->count() > 0)
                <h6>Matières à planifier :</h6>
                <div class="list-group">
                    @foreach($teacher->subjects as $subject)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $subject->name }}</strong>
                            <br><small class="text-muted">{{ $subject->credits }} crédits</small>
                        </div>
                        <span class="badge bg-primary">{{ $subject->credits * 1.5 }}h/semaine</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="{{ route('admin.schedules.create') }}?teacher_id={{ $teacher->id }}" class="btn btn-primary">
                    Créer un planning
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Contact -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope"></i> Envoyer un message à {{ $teacher->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{-- route('admin.messages.store') --}}" method="POST">
                @csrf
                <input type="hidden" name="recipient_id" value="{{ $teacher->user->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Sujet</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Message</label>
                        <textarea class="form-control" id="body" name="body" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Styles pour les cartes avec gradient */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.text-white-25 {
    opacity: 0.25;
}

/* Timeline styles */
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
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 25px;
}

.timeline-marker {
    position: absolute;
    left: -33px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 5px;
    }
    
    .timeline {
        padding-left: 15px;
    }
    
    .timeline-item {
        padding-left: 15px;
    }
    
    .timeline-marker {
        left: -18px;
    }
}

/* Animation pour les cartes */
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

/* Styles pour les badges */
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}

/* Progress bar personnalisé */
.progress {
    border-radius: 1rem;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 1rem;
    transition: width 0.6s ease;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Animation pour les cartes statistiques
    const statCards = document.querySelectorAll('.bg-gradient-primary, .bg-gradient-success, .bg-gradient-info, .bg-gradient-warning');
    
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
    
    // Animation pour les progress bars
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    }, 1000);
});

// Fonction pour rafraîchir les données via AJAX (optionnel)
function refreshTeacherStats() {
    fetch(`/admin/api/teachers/{{ $teacher->id }}/stats`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les statistiques en temps réel
            console.log('Stats mises à jour:', data);
        })
        .catch(error => {
            console.log('Erreur lors du rafraîchissement:', error);
        });
}

// Auto-refresh toutes les 5 minutes (optionnel)
// setInterval(refreshTeacherStats, 300000);
</script>
@endpush