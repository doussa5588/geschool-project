@extends('layouts.app')

@section('title', 'Tableau de Bord Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-tachometer-alt"></i> Tableau de Bord Administrateur</h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_students'] }}</h4>
                        <p class="mb-0">Étudiants</p>
                    </div>
                    <div>
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_teachers'] }}</h4>
                        <p class="mb-0">Enseignants</p>
                    </div>
                    <div>
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_users'] }}</h4>
                        <p class="mb-0">Utilisateurs</p>
                    </div>
                    <div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['attendance_today'] }}</h4>
                        <p class="mb-0">Présences Aujourd'hui</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Notes Récentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Matière</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_grades'] as $grade)
                            <tr>
                                <td>{{ $grade->student->user->name }}</td>
                                <td>{{ $grade->subject->name }}</td>
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
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Statistiques</h5>
            </div>
            <div class="card-body">
                <canvas id="statsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-user-plus"></i> Nouvel Utilisateur
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-user-graduate"></i> Nouvel Étudiant
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-chalkboard-teacher"></i> Nouvel Enseignant
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-chart-bar"></i> Rapports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chart for statistics
const ctx = document.getElementById('statsChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Étudiants', 'Enseignants', 'Autres'],
        datasets: [{
            data: [{{ $stats['total_students'] }}, {{ $stats['total_teachers'] }}, {{ $stats['total_users'] - $stats['total_students'] - $stats['total_teachers'] }}],
            backgroundColor: ['#007bff', '#28a745', '#ffc107']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush