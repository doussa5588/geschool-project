@extends('layouts.app')

@section('title', $subject->name . ' - Détails Matière - UNCHK')

@section('page-header')
@section('page-title', 'Détails de la Matière')
@section('page-subtitle', $subject->name . ' (' . $subject->code . ')')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.subjects.export-details', $subject) }}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>Fiche Matière PDF
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.subjects.export-grades', $subject) }}">
                <i class="bi bi-file-excel me-2"></i>Rapport Notes
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.subjects.export-schedule', $subject) }}">
                <i class="bi bi-file-excel me-2"></i>Planning Cours
            </a></li>
        </ul>
    </div>
    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning me-2">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Informations Principales -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <!-- Profil Principal -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="subject-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; font-size: 3rem;">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                    
                    <h4 class="mb-1">{{ $subject->name }}</h4>
                    <p class="text-muted mb-2">{{ $subject->code }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($subject->is_active)
                            @if($subject->teacher)
                                <span class="badge bg-success px-3 py-2">Assignée</span>
                            @else
                                <span class="badge bg-warning px-3 py-2">Active</span>
                            @endif
                        @else
                            <span class="badge bg-danger px-3 py-2">Inactive</span>
                        @endif
                        
                        <span class="badge bg-info px-3 py-2">{{ $subject->credits }} crédit{{ $subject->credits > 1 ? 's' : '' }}</span>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-primary">{{ $subject->classes->count() }}</h5>
                                <small class="text-muted">Classes</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-success">{{ $subject->studentsCount() }}</h5>
                                <small class="text-muted">Étudiants</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-info">{{ $subject->grades->count() }}</h5>
                            <small class="text-muted">Notes</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Académiques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informations Académiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Département</label>
                            @if($subject->department)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-muted"></i>
                                    <div>
                                        <div>{{ $subject->department->name }}</div>
                                        <small class="text-muted">{{ $subject->department->code }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted">Non assigné</div>
                            @endif
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Enseignant Responsable</label>
                            @if($subject->teacher)
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->user->name) }}&background=2563eb&color=fff&size=40" 
                                         alt="{{ $subject->teacher->user->name }}" 
                                         class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <div class="fw-semibold">{{ $subject->teacher->user->name }}</div>
                                        <small class="text-muted">{{ $subject->teacher->specialization }}</small>
                                        <br><small class="text-muted">{{ $subject->teacher->employee_number }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Aucun enseignant assigné
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Code Complet</label>
                            <div>
                                <span class="badge bg-secondary fs-6">{{ $subject->full_code }}</span>
                            </div>
                        </div>
                        
                        @if($subject->description)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <p class="text-muted mb-0">{{ $subject->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($subject->teacher)
                            <a href="{{ route('messages.create', ['recipient_id' => $subject->teacher->user_id]) }}" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>Contacter l'Enseignant
                            </a>
                        @else
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
                                <i class="bi bi-person-plus me-2"></i>Assigner un Enseignant
                            </button>
                        @endif
                        
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                            <i class="bi bi-calendar-plus me-2"></i>Programmer un Cours
                        </button>
                        
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#manageClassesModal">
                            <i class="bi bi-collection me-2"></i>Gérer les Classes
                        </button>
                        
                        @if($subject->is_active)
                            <form action="{{ route('admin.subjects.deactivate', $subject) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Désactiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.subjects.restore', $subject) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="bi bi-check-circle me-2"></i>Réactiver
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu Principal -->
        <div class="col-xl-8 col-lg-7">
            <!-- Onglets -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                                <i class="bi bi-speedometer2 me-2"></i>Vue d'ensemble
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#classes" type="button">
                                <i class="bi bi-collection me-2"></i>Classes ({{ $subject->classes->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#grades" type="button">
                                <i class="bi bi-clipboard-data me-2"></i>Notes ({{ $subject->grades->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                                <i class="bi bi-calendar3 me-2"></i>Emploi du Temps
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#analytics" type="button">
                                <i class="bi bi-graph-up me-2"></i>Analyses
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Vue d'ensemble -->
                        <div class="tab-pane fade show active" id="overview">
                            
                            <!-- Statistiques Détaillées -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-primary mb-1">{{ $subject->classes->count() }}</h3>
                                        <small class="text-muted">Classes Assignées</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-success mb-1">{{ $subject->studentsCount() }}</h3>
                                        <small class="text-muted">Total Étudiants</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-warning mb-1">{{ $subject->schedules->count() }}</h3>
                                        <small class="text-muted">Cours Programmés</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-info mb-1">{{ number_format($subject->averageGrade(), 1) }}</h3>
                                        <small class="text-muted">Moyenne Générale</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Overview -->
                            @if($subject->grades->count() > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">Performance des Étudiants</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Distribution des Notes</h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Excellent (16-20)</small>
                                                            <div class="progress mb-2" style="height: 8px;">
                                                                <div class="progress-bar bg-success" style="width: {{ $subject->gradeDistributionPercentage('excellent') }}%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Bon (14-16)</small>
                                                            <div class="progress mb-2" style="height: 8px;">
                                                                <div class="progress-bar bg-info" style="width: {{ $subject->gradeDistributionPercentage('good') }}%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Moyen (12-14)</small>
                                                            <div class="progress mb-2" style="height: 8px;">
                                                                <div class="progress-bar bg-warning" style="width: {{ $subject->gradeDistributionPercentage('average') }}%"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Faible (<12)</small>
                                                            <div class="progress mb-2" style="height: 8px;">
                                                                <div class="progress-bar bg-danger" style="width: {{ $subject->gradeDistributionPercentage('below_average') + $subject->gradeDistributionPercentage('poor') }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Statistiques Rapides</h6>
                                                    <p><strong>Taux de réussite :</strong> {{ $subject->passRate() }}%</p>
                                                    <p><strong>Moyenne générale :</strong> {{ number_format($subject->averageGrade(), 2) }}/20</p>
                                                    <p><strong>Notes récentes :</strong> {{ $subject->recentGradesCount() }}</p>
                                                    <p class="mb-0"><strong>Dernière note :</strong> 
                                                        @if($subject->grades->count() > 0)
                                                            {{ $subject->grades->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                                                        @else
                                                            Aucune
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Emploi du temps de la semaine -->
                            @if($subject->schedules->count() > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">Cours de cette semaine</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Jour</th>
                                                    <th>Heure</th>
                                                    <th>Classe</th>
                                                    <th>Salle</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subject->schedules->take(5) as $schedule)
                                                <tr>
                                                    <td>{{ ucfirst($schedule->day_of_week) }}</td>
                                                    <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                                    <td>{{ $schedule->class->name }}</td>
                                                    <td>{{ $schedule->room ?? 'Non assignée' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">Programmé</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Classes assignées -->
                            @if($subject->classes->count() > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">Classes Assignées</h5>
                                    <div class="row">
                                        @foreach($subject->classes->take(6) as $class)
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title mb-1">{{ $class->name }}</h6>
                                                        <p class="card-text text-muted mb-2">{{ $class->level->name ?? 'Niveau non défini' }}</p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">{{ $class->students->count() }} étudiants</small>
                                                            <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($subject->classes->count() > 6)
                                        <div class="text-center">
                                            <button class="btn btn-outline-primary btn-sm" onclick="switchToTab('classes')">
                                                Voir toutes les classes ({{ $subject->classes->count() }})
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Classes -->
                        <div class="tab-pane fade" id="classes">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Classes Assignées</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manageClassesModal">
                                    <i class="bi bi-plus"></i> Gérer les Classes
                                </button>
                            </div>

                            @if($subject->classes->count() > 0)
                                <div class="row">
                                    @foreach($subject->classes as $class)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">{{ $class->name }}</h6>
                                                        <span class="badge bg-{{ $class->is_active ? 'success' : 'danger' }}">
                                                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                    
                                                    <p class="text-muted mb-2">{{ $class->level->name ?? 'Niveau non défini' }}</p>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-4">
                                                            <div class="border-end">
                                                                <h6 class="mb-0">{{ $class->students->count() }}</h6>
                                                                <small class="text-muted">Étudiants</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="border-end">
                                                                <h6 class="mb-0">{{ $class->capacity }}</h6>
                                                                <small class="text-muted">Capacité</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <h6 class="mb-0">{{ $class->room ?? 'N/A' }}</h6>
                                                            <small class="text-muted">Salle</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar" style="width: {{ ($class->students->count() / $class->capacity) * 100 }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ round(($class->students->count() / $class->capacity) * 100) }}% d'occupation</small>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="btn-group w-100">
                                                        <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-eye"></i> Voir
                                                        </a>
                                                        <form action="{{ route('admin.subjects.remove-class', [$subject, $class]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-warning btn-sm">
                                                                <i class="bi bi-x"></i> Retirer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-collection fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucune classe assignée</h5>
                                    <p class="text-muted">Cette matière n'est encore assignée à aucune classe.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manageClassesModal">
                                        <i class="bi bi-plus"></i> Assigner des classes
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Notes -->
                        <div class="tab-pane fade" id="grades">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Toutes les Notes</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="filterGrades('week')">Cette Semaine</button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="filterGrades('month')">Ce Mois</button>
                                    <button class="btn btn-outline-primary btn-sm active" onclick="filterGrades('all')">Toutes</button>
                                </div>
                            </div>

                            @if($subject->grades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="gradesTable">
                                        <thead>
                                            <tr>
                                                <th>Étudiant</th>
                                                <th>Classe</th>
                                                <th>Note</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Enseignant</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subject->grades->sortByDesc('created_at') as $grade)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($grade->student->user->name) }}&background=2563eb&color=fff&size=32" 
                                                             alt="{{ $grade->student->user->name }}" 
                                                             class="rounded-circle me-2" width="32" height="32">
                                                        <div>
                                                            <div class="fw-semibold">{{ $grade->student->user->name }}</div>
                                                            <small class="text-muted">{{ $grade->student->student_number }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $grade->student->class->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $grade->score >= 16 ? 'success' : ($grade->score >= 14 ? 'info' : ($grade->score >= 12 ? 'warning' : ($grade->score >= 10 ? 'secondary' : 'danger'))) }}">
                                                        {{ $grade->score }}/{{ $grade->max_score }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ ucfirst($grade->evaluation_type) }}</span>
                                                </td>
                                                <td>{{ $grade->date ? $grade->date->format('d/m/Y') : $grade->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $grade->teacher->user->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editGradeModal" 
                                                            data-grade-id="{{ $grade->id }}" 
                                                            data-student-name="{{ $grade->student->user->name }}"
                                                            data-score="{{ $grade->score }}"
                                                            data-max-score="{{ $grade->max_score }}"
                                                            data-evaluation-type="{{ $grade->evaluation_type }}"
                                                            data-date="{{ $grade->date ? $grade->date->format('Y-m-d') : $grade->created_at->format('Y-m-d') }}"
                                                            data-comments="{{ $grade->comments }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('admin.grades.destroy', $grade) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Statistiques des notes -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ number_format($subject->grades->avg('score'), 2) }}</h4>
                                                <small>Moyenne générale</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ $subject->grades->where('score', '>=', 10)->count() }}</h4>
                                                <small>Notes ≥ 10</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ $subject->grades->count() }}</h4>
                                                <small>Total notes</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h4>{{ $subject->passRate() }}%</h4>
                                                <small>Taux de réussite</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-clipboard-data fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucune note disponible</h5>
                                    <p class="text-muted">Les notes pour cette matière apparaîtront ici une fois saisies.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Emploi du Temps -->
                        <div class="tab-pane fade" id="schedule">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Emploi du Temps</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                    <i class="bi bi-plus"></i> Ajouter un Cours
                                </button>
                            </div>
                            
                            @if($subject->schedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered schedule-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 100px;">Heure</th>
                                                <th>Lundi</th>
                                                <th>Mardi</th>
                                                <th>Mercredi</th>
                                                <th>Jeudi</th>
                                                <th>Vendredi</th>
                                                <th>Samedi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($hour = 8; $hour <= 17; $hour++)
                                            <tr>
                                                <td class="fw-bold">{{ sprintf('%02d:00', $hour) }}</td>
                                                @php
                                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                                @endphp
                                                @foreach($days as $day)
                                                    <td>
                                                        @php
                                                            $schedule = $subject->schedules->where('day_of_week', $day)
                                                                ->where('start_time', 'like', sprintf('%02d:%', $hour))->first();
                                                        @endphp
                                                        @if($schedule)
                                                            <div class="schedule-slot bg-primary text-white p-2 rounded">
                                                                <strong>{{ $schedule->class->name }}</strong><br>
                                                                <small>{{ $schedule->teacher->user->name ?? 'Enseignant non assigné' }}</small><br>
                                                                <small><i class="bi bi-geo-alt"></i> {{ $schedule->room ?? 'Salle non assignée' }}</small>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar3 fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucun cours programmé</h5>
                                    <p class="text-muted">L'emploi du temps pour cette matière n'est pas encore configuré.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                        <i class="bi bi-plus"></i> Programmer le premier cours
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Analyses -->
                        <div class="tab-pane fade" id="analytics">
                            <h5 class="mb-3">Analyses et Statistiques</h5>
                            
                            @if($subject->grades->count() > 0)
                                <!-- Graphiques -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Évolution des Notes Mensuelles</h6>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="monthlyGradesChart" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Performance par Classe</h6>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="classPerformanceChart" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tableaux d'analyse -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Top 5 - Meilleurs Étudiants</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Rang</th>
                                                                <th>Étudiant</th>
                                                                <th>Moyenne</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $topStudents = $subject->grades->groupBy('student_id')
                                                                    ->map(function($grades) {
                                                                        return [
                                                                            'student' => $grades->first()->student,
                                                                            'average' => $grades->avg('score')
                                                                        ];
                                                                    })
                                                                    ->sortByDesc('average')
                                                                    ->take(5);
                                                            @endphp
                                                            @foreach($topStudents as $index => $data)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $data['student']->user->name }}</td>
                                                                <td>
                                                                    <span class="badge bg-success">{{ number_format($data['average'], 2) }}</span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Étudiants en Difficulté</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Étudiant</th>
                                                                <th>Moyenne</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $strugglingStudents = $subject->grades->groupBy('student_id')
                                                                    ->map(function($grades) {
                                                                        return [
                                                                            'student' => $grades->first()->student,
                                                                            'average' => $grades->avg('score')
                                                                        ];
                                                                    })
                                                                    ->where('average', '<', 10)
                                                                    ->sortBy('average')
                                                                    ->take(5);
                                                            @endphp
                                                            @forelse($strugglingStudents as $data)
                                                            <tr>
                                                                <td>{{ $data['student']->user->name }}</td>
                                                                <td>
                                                                    <span class="badge bg-danger">{{ number_format($data['average'], 2) }}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('messages.create', ['recipient_id' => $data['student']->user_id]) }}" class="btn btn-outline-warning btn-sm">
                                                                        <i class="bi bi-envelope"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">Aucun étudiant en difficulté</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-graph-up fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Analyses non disponibles</h5>
                                    <p class="text-muted">Les analyses seront disponibles une fois que des notes auront été saisies.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div