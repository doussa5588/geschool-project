@extends('layouts.admin')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<!-- Développé par SADOU MBALLO - Responsable GeSchool -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Tableau de Bord - GeSchool</h1>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Étudiants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_etudiants'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Professeurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_professeurs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_classes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Utilisateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_utilisateurs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Activités Récentes</h6>
                </div>
                <div class="card-body">
                    @foreach($activites_recentes as $activite)
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ $activite['date']->diffForHumans() }}</div>
                            <strong>{{ $activite['action'] }}</strong> par {{ $activite['utilisateur'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.etudiants.create') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> Ajouter un Étudiant
                    </a>
                    <a href="{{ route('admin.professeurs.create') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-plus"></i> Ajouter un Professeur
                    </a>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-plus"></i> Créer une Classe
                    </a>
                    <a href="{{ route('admin.rapport-general') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-chart-bar"></i> Générer un Rapport
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection