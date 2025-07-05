@extends('layouts.app')

@section('title', 'Tableau de Bord Étudiant')

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-user-graduate"></i> Tableau de Bord Étudiant</h2>
        <p class="text-muted">Bonjour {{ auth()->user()->name }}, bienvenue dans votre espace étudiant.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Student Info Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-id-card"></i> Mes Informations</h5>
            </div>
            <div class="card-body">
                <p><strong>Numéro Étudiant:</strong> {{ $student->student_number }}</p>
                <p><strong>Classe:</strong> {{ $student->classe->name }}</p>
                <p><strong>Niveau:</strong> {{ $student->classe->level->name }}</p>
                <p><strong>Année Académique:</strong> {{ $student->academic_year }}</p>
                <p><strong>Taux de Présence:</strong> 
                    <span class="badge bg-{{ $attendance_rate >= 80 ? 'success' : ($attendance_rate >= 60 ? 'warning' : 'danger') }}">
                        {{ $attendance_rate }}%
                    </span>
                </p>
                @if($unread_messages > 0)
                <p><strong>Messages non lus:</strong> 
                    <span class="badge bg-danger">{{ $unread_messages }}</span>
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Grades -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-star"></i> Notes Récentes</h5>
            </div>
            <div class="card-body">
                @if($recent_grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_grades as $grade)
                                <tr>
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
                        <a href="{{ route('student.grades') }}" class="btn btn-primary btn-sm">
                            Voir toutes les notes
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune note disponible pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Schedule for Today -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-day"></i> Emploi du Temps - Aujourd'hui</h5>
            </div>
            <div class="card-body">
                @php
                    $today = strtolower(now()->format('l'));
                    $todaySchedule = $schedule->where('day_of_week', $today)->sortBy('start_time');
                @endphp
                
                @if($todaySchedule->count() > 0)
                    <div class="row">
                        @foreach($todaySchedule as $item)
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $item->subject->name }}</h6>
                                    <p class="card-text">
                                        <i class="fas fa-clock"></i> 
                                        {{ $item->start_time->format('H:i') }} - {{ $item->end_time->format('H:i') }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-chalkboard-teacher"></i> 
                                        {{ $item->teacher->user->name }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-door-open"></i> 
                                        Salle {{ $item->room }}
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
                
                <div class="text-end">
                    <a href="{{ route('student.schedule') }}" class="btn btn-primary">
                        Voir l'emploi du temps complet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection