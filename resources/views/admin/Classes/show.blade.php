@extends('layouts.app')

@section('title', optional($class->user)->name . ' - Détails Classe - UNCHK')

@section('page-subtitle', optional($class->user)->full_name . ' (' . $class->student_number . ')')

@section('page-header')
@section('page-title', 'Profil Étudiant')
@section('page-subtitle', optional($class->user)->name . ' (' . $class->student_number . ')')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.students.export-profile', $class) }}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>Fiche Étudiant PDF
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.students.export-grades', $class) }}">
                <i class="bi bi-file-excel me-2"></i>Relevé de Notes
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.students.export-attendance', $class) }}">
                <i class="bi bi-file-excel me-2"></i>Rapport Présences
            </a></li>
        </ul>
    </div>
    <a href="{{ route('admin.students.edit', $class) }}" class="btn btn-warning me-2">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
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
                        @if($class->photo)
                            <img src="{{ Storage::url($class->photo) }}" 
                                 alt="{{ $class->user->full_name }}" 
                                 class="rounded-circle img-fluid" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($class->user->full_name) }}&background=2563eb&color=fff&size=150" 
                                 alt="{{ $class->user->full_name }}" 
                                 class="rounded-circle img-fluid">
                        @endif
                    </div>
                    
                    <h4 class="mb-1">{{ $class->user->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $class->student_number }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($class->is_active)
                            <span class="badge bg-success px-3 py-2">Actif</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Inactif</span>
                        @endif
                        
                        <span class="badge bg-info px-3 py-2">
                            {{ $class->user->gender === 'male' ? 'Masculin' : 'Féminin' }}
                        </span>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-primary">{{ number_format($stats['average_grade'], 2) }}</h5>
                                <small class="text-muted">Moyenne</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="mb-0 text-success">{{ $stats['attendance_rate'] }}%</h5>
                                <small class="text-muted">Présence</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-info">{{ $stats['subjects_count'] }}</h5>
                            <small class="text-muted">Matières</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de Contact -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>Informations de Contact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-envelope me-2 text-muted"></i>
                                <a href="mailto:{{ $class->user->email }}" class="text-decoration-none">
                                    {{ $class->user->email }}
                                </a>
                            </div>
                        </div>
                        
                        @if($class->user->phone)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone me-2 text-muted"></i>
                                <a href="tel:{{ $class->user->phone }}" class="text-decoration-none">
                                    {{ $class->user->phone }}
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($class->user->address)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-geo-alt me-2 text-muted mt-1"></i>
                                <span>{{ $class->user->address }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Date de naissance</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date me-2 text-muted"></i>
                                <span>{{ $class->user->date_of_birth->format('d/m/Y') }}</span>
                                <small class="text-muted ms-2">
                                    ({{ $class->user->date_of_birth->age }} ans)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact d'Urgence -->
            @if($class->parent_phone || $class->parent_email || $class->emergency_contact)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-exclamation me-2"></i>Contact d'Urgence
                    </h5>
                </div>
                <div class="card-body">
                    @if($class->parent_phone)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Téléphone Parent/Tuteur</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-hearts me-2 text-muted"></i>
                            <a href="tel:{{ $class->parent_phone }}" class="text-decoration-none">
                                {{ $class->parent_phone }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if($class->parent_email)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Parent/Tuteur</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope-heart me-2 text-muted"></i>
                            <a href="mailto:{{ $class->parent_email }}" class="text-decoration-none">
                                {{ $class->parent_email }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if($class->emergency_contact)
                    <div>
                        <label class="form-label fw-semibold">Contact d'Urgence</label>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone-plus me-2 text-muted"></i>
                            <span>{{ $class->emergency_contact }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions Rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="sendMessage()">
                            <i class="bi bi-envelope me-2"></i>Envoyer un Message
                        </button>
                        <button class="btn btn-outline-success" onclick="addGrade()">
                            <i class="bi bi-plus-circle me-2"></i>Ajouter une Note
                        </button>
                        <button class="btn btn-outline-warning" onclick="markAttendance()">
                            <i class="bi bi-check-square me-2"></i>Marquer Présence
                        </button>
                        @if($class->is_active)
                            <button class="btn btn-outline-danger" onclick="deactivateStudent()">
                                <i class="bi bi-person-x me-2"></i>Désactiver
                            </button>
                        @else
                            <button class="btn btn-outline-success" onclick="activateStudent()">
                                <i class="bi bi-person-check me-2"></i>Réactiver
                            </button>
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
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#grades" type="button">
                                <i class="bi bi-clipboard-data me-2"></i>Notes
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendance" type="button">
                                <i class="bi bi-check2-square me-2"></i>Présences
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                                <i class="bi bi-calendar3 me-2"></i>Emploi du Temps
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#academic" type="button">
                                <i class="bi bi-mortarboard me-2"></i>Parcours
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Vue d'ensemble -->
                        <div class="tab-pane fade show active" id="overview">
                            <!-- Informations Académiques -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-collection me-2"></i>Classe Actuelle
                                            </h6>
                                            @if($class->class)
                                                <h4 class="mb-1">{{ $class->class->name }}</h4>
                                                <p class="text-muted mb-0">{{ $class->class->level->name }}</p>
                                                <p class="text-muted mb-0">{{ $class->class->department->name }}</p>
                                                <small class="text-muted">
                                                    Année académique : {{ $class->class->academic_year }}
                                                </small>
                                            @else
                                                <p class="text-muted">Aucune classe assignée</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-calendar-check me-2"></i>Inscription
                                            </h6>
                                            <h4 class="mb-1">{{ $class->enrollment_date->format('d/m/Y') }}</h4>
                                            <p class="text-muted mb-0">
                                                Il y a {{ $class->enrollment_date->diffForHumans() }}
                                            </p>
                                            <small class="text-muted">
                                                Durée d'études : {{ $class->enrollment_date->diffInMonths(now()) }} mois
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques Détaillées -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-primary mb-1">{{ $stats['average_grade'] }}</h3>
                                        <small class="text-muted">Moyenne Générale</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-success mb-1">{{ $stats['attendance_rate'] }}%</h3>
                                        <small class="text-muted">Taux de Présence</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-warning mb-1">{{ $stats['total_absences'] }}</h3>
                                        <small class="text-muted">Total Absences</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h3 class="text-info mb-1">{{ $stats['subjects_count'] }}</h3>
                                        <small class="text-muted">Matières</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Dernières Notes -->
                            <div class="mb-4">
                                <h5 class="mb-3">Dernières Notes</h5>
                                @if($class->grades->take(5)->count() > 0)
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
                                                @foreach($class->grades->take(5) as $grade)
                                                <tr>
                                                    <td>{{ $grade->subject->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $grade->grade >= 10 ? 'success' : 'danger' }}">
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
                                    <p class="text-muted">Aucune note disponible</p>
                                @endif
                            </div>

                            <!-- Informations Médicales -->
                            @if($class->medical_info)
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="bi bi-heart-pulse me-2"></i>Informations Médicales
                                </h6>
                                <p class="mb-0">{{ $class->medical_info }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Onglet Notes -->
                        <div class="tab-pane fade" id="grades">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Toutes les Notes</h5>
                                <button class="btn btn-primary btn-sm" onclick="addGrade()">
                                    <i class="bi bi-plus"></i> Ajouter une Note
                                </button>
                            </div>

                            @if($class->grades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="gradesTable">
                                        <thead>
                                            <tr>
                                                <th>Matière</th>
                                                <th>Note</th>
                                                <th>Coefficient</th>
                                                <th>Type</th>
                                                <th>Enseignant</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($class->grades as $grade)
                                            <tr>
                                                <td>{{ $grade->subject->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $grade->grade >= 16 ? 'success' : ($grade->grade >= 14 ? 'info' : ($grade->grade >= 12 ? 'warning' : ($grade->grade >= 10 ? 'secondary' : 'danger'))) }}">
                                                        {{ $grade->grade }}/20
                                                    </span>
                                                </td>
                                                <td>{{ $grade->coefficient ?? 1 }}</td>
                                                <td>{{ $grade->grade_type }}</td>
                                                <td>{{ $grade->teacher->user->full_name ?? 'N/A' }}</td>
                                                <td>{{ $grade->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-warning" onclick="editGrade({{ $grade->id }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="deleteGrade({{ $grade->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Graphique des moyennes par matière -->
                                <div class="mt-4">
                                    <h6>Moyennes par Matière</h6>
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="subjectAveragesChart"></canvas>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-clipboard-data fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucune note disponible</h5>
                                    <p class="text-muted">Les notes de cet étudiant apparaîtront ici une fois saisies.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Présences -->
                        <div class="tab-pane fade" id="attendance">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Historique des Présences</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="filterAttendance('week')">Cette Semaine</button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="filterAttendance('month')">Ce Mois</button>
                                    <button class="btn btn-outline-primary btn-sm active" onclick="filterAttendance('all')">Tout</button>
                                </div>
                            </div>

                            @if($class->attendances->count() > 0)
                                <!-- Résumé des présences -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-success text-white rounded">
                                            <h4>{{ $class->attendances->where('status', 'present')->count() }}</h4>
                                            <small>Présent</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-danger text-white rounded">
                                            <h4>{{ $class->attendances->where('status', 'absent')->count() }}</h4>
                                            <small>Absent</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-warning text-white rounded">
                                            <h4>{{ $class->attendances->where('status', 'late')->count() }}</h4>
                                            <small>Retard</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-info text-white rounded">
                                            <h4>{{ $class->attendances->where('status', 'excused')->count() }}</h4>
                                            <small>Excusé</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="attendanceTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Matière</th>
                                                <th>Enseignant</th>
                                                <th>Statut</th>
                                                <th>Heure</th>
                                                <th>Remarques</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($class->attendances->sortByDesc('date') as $attendance)
                                            <tr>
                                                <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                                <td>{{ $attendance->subject->name ?? 'N/A' }}</td>
                                                <td>{{ $attendance->teacher->user->full_name ?? 'N/A' }}</td>
                                                <td>
                                                    @switch($attendance->status)
                                                        @case('present')
                                                            <span class="badge bg-success">Présent</span>
                                                            @break
                                                        @case('absent')
                                                            <span class="badge bg-danger">Absent</span>
                                                            @break
                                                        @case('late')
                                                            <span class="badge bg-warning">Retard</span>
                                                            @break
                                                        @case('excused')
                                                            <span class="badge bg-info">Excusé</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $attendance->time ?? '-' }}</td>
                                                <td>{{ $attendance->remarks ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-check2-square fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucune donnée de présence</h5>
                                    <p class="text-muted">L'historique des présences apparaîtra ici.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Emploi du Temps -->
                        <div class="tab-pane fade" id="schedule">
                            <h5 class="mb-3">Emploi du Temps - Semaine du {{ now()->startOfWeek()->format('d/m/Y') }}</h5>
                            
                            @if($class->class && $class->class->schedules->count() > 0)
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
                                                @for($day = 1; $day <= 6; $day++)
                                                    <td>
                                                        @php
                                                            $schedule = $class->class->schedules->where('day_of_week', $day)->where('start_time', sprintf('%02d:00', $hour))->first();
                                                        @endphp
                                                        @if($schedule)
                                                            <div class="schedule-slot bg-primary text-white p-2 rounded">
                                                                <strong>{{ $schedule->subject->name }}</strong><br>
                                                                <small>{{ $schedule->teacher->user->full_name }}</small><br>
                                                                <small><i class="bi bi-geo-alt"></i> {{ $schedule->room ?? 'N/A' }}</small>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endfor
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar3 fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucun emploi du temps</h5>
                                    <p class="text-muted">L'emploi du temps de la classe n'est pas encore configuré.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Onglet Parcours Académique -->
                        <div class="tab-pane fade" id="academic">
                            <h5 class="mb-3">Parcours Académique</h5>
                            
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Inscription UNCHK</h6>
                                        <p class="timeline-text">Première inscription à l'université</p>
                                        <small class="text-muted">{{ $class->enrollment_date->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                                
                                @if($class->class)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Assignation {{ $class->class->name }}</h6>
                                        <p class="timeline-text">{{ $class->class->level->name }} - {{ $class->class->department->name }}</p>
                                        <small class="text-muted">Année académique {{ $class->class->academic_year }}</small>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- TODO: Ajouter d'autres événements du parcours -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Statut Actuel</h6>
                                        <p class="timeline-text">
                                            @if($class->is_active)
                                                Étudiant actif avec une moyenne de {{ number_format($stats['average_grade'], 2) }}/20
                                            @else
                                                Compte désactivé
                                            @endif
                                        </p>
                                        <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.schedule-table td {
    min-height: 60px;
    vertical-align: top;
}

.schedule-slot {
    font-size: 0.8rem;
    line-height: 1.2;
}

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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#gradesTable, #attendanceTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
        },
        pageLength: 10,
        order: [[5, 'desc']] // Sort by date descending
    });

    // Initialize Charts
    initializeCharts();
});

function initializeCharts() {
    // Subject Averages Chart
    const subjectData = @json($class->grades->groupBy('subject.name')->map(function($grades) {
        return [
            'subject' => $grades->first()->subject->name,
            'average' => round($grades->avg('grade'), 2)
        ];
    })->values());

    if (subjectData.length > 0) {
        const ctx = document.getElementById('subjectAveragesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: subjectData.map(item => item.subject),
                    datasets: [{
                        label: 'Moyenne',
                        data: subjectData.map(item => item.average),
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
                            max: 20
                        }
                    }
                }
            });
        }
    }
}

// Action Functions
function sendMessage() {
    // TODO: Implement messaging system
    alert('Fonction de messagerie à implémenter');
}

function addGrade() {
    // TODO: Open modal to add grade
    alert('Fonction d\'ajout de note à implémenter');
}

function editGrade(gradeId) {
    // TODO: Open modal to edit grade
    alert('Fonction de modification de note à implémenter - ID: ' + gradeId);
}

function deleteGrade(gradeId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
        // TODO: Implement grade deletion
        alert('Suppression de la note ID: ' + gradeId);
    }
}

function markAttendance() {
    // TODO: Open modal to mark attendance
    alert('Fonction de marquage de présence à implémenter');
}

function deactivateStudent() {
    if (confirm('Êtes-vous sûr de vouloir désactiver cet étudiant ?')) {
        // TODO: Implement student deactivation
        location.reload();
    }
}

function activateStudent() {
    if (confirm('Êtes-vous sûr de vouloir réactiver cet étudiant ?')) {
        $.ajax({
            url: '{{ route("admin.students.restore", $class->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function() {
                alert('Erreur lors de la réactivation');
            }
        });
    }
}

function filterAttendance(period) {
    // TODO: Implement attendance filtering
    $('.btn-group .btn').removeClass('active');
    event.target.classList.add('active');
}
</script>
@endpush