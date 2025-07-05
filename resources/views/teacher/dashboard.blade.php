@extends('layouts.app')

@section('title', 'Tableau de Bord Enseignant')

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-chalkboard-teacher"></i> Tableau de Bord Enseignant</h2>
        <p class="text-muted">Bonjour {{ auth()->user()->name }}, bienvenue dans votre espace enseignant.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Teacher Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-id-badge"></i> Mes Informations</h5>
            </div>
            <div class="card-body">
                <p><strong>Numéro Employé:</strong> {{ $teacher->employee_number }}</p>
                <p><strong>Spécialisation:</strong> {{ $teacher->specialization }}</p>
                <p><strong>Département:</strong> {{ $teacher->department->name }}</p>
                <p><strong>Date d'embauche:</strong> {{ $teacher->hire_date->format('d/m/Y') }}</p>
                <p><strong>Statut:</strong> 
                    <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : 'warning' }}">
                        {{ ucfirst($teacher->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- My Subjects -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-book"></i> Mes Matières</h5>
            </div>
            <div class="card-body">
                @if($subjects->count() > 0)
                    <div class="row">
                        @foreach($subjects as $subject)
                        <div class="col-md-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $subject->name }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Code: {{ $subject->code }}</small><br>
                                        <span class="badge bg-info">{{ $subject->credits }} crédits</span>
                                    </p>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('teacher.grades.subject', $subject) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-star"></i> Notes
                                        </a>
                                        <a href="{{ route('teacher.attendance.subject', $subject) }}" class="btn btn-outline-success">
                                            <i class="fas fa-check"></i> Présences
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune matière assignée.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-day"></i> Mon Emploi du Temps - Aujourd'hui</h5>
            </div>
            <div class="card-body">
                @if($today_schedule->count() > 0)
                    <div class="row">
                        @foreach($today_schedule as $schedule)
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $schedule->subject->name }}</h6>
                                    <p class="card-text">
                                        <i class="fas fa-clock"></i> 
                                        {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-users"></i> 
                                        {{ $schedule->classe->name }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-door-open"></i> 
                                        Salle {{ $schedule->room }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun cours prévu pour aujourd'hui.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Grades -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-star"></i> Notes Récemment Ajoutées</h5>
            </div>
            <div class="card-body">
                @if($recent_grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Matière</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_grades as $grade)
                                <tr>
                                    <td>{{ $grade->student->user->name }}</td>
                                    <td>{{ $grade->subject->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($grade->evaluation_type) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $grade->percentage >= 50 ? 'success' : 'danger' }}">
                                            {{ $grade->score }}/{{ $grade->max_score }}
                                        </span>
                                    </td>
                                    <td>{{ $grade->date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-primary btn-sm">
                            Gérer toutes les notes
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune note récente.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection