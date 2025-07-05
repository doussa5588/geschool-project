@extends('layouts.app')

@section('title', 'Modifier le département')

@section('page-title', 'Modifier le département')
@section('page-subtitle', $department->name . ' (' . $department->code . ')')

@section('page-actions')
    <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour aux détails
    </a>
    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-list"></i> Liste des départements
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil"></i> Modifier les informations
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nom du département <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $department->name) }}" 
                                       placeholder="Ex: Informatique et Réseaux"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">
                                    Code du département <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $department->code) }}" 
                                       placeholder="Ex: INFO"
                                       maxlength="10"
                                       style="text-transform: uppercase;"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Code unique pour identifier le département (max 10 caractères)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Description détaillée du département et de ses activités...">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Description optionnelle du département (max 1000 caractères)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Département actif
                            </label>
                        </div>
                        <div class="form-text">
                            Les départements inactifs ne peuvent pas avoir de nouvelles assignations
                            @if(!$department->is_active && ($department->teachers->count() > 0 || $department->subjects->count() > 0))
                                <br><span class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Ce département contient {{ $department->teachers->count() }} enseignant(s) et {{ $department->subjects->count() }} matière(s)
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Informations sur les impacts -->
        @if($department->teachers->count() > 0 || $department->subjects->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-warning text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Impact des modifications
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Éléments liés :</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-people text-primary"></i> {{ $department->teachers->count() }} enseignant(s)</li>
                            <li><i class="bi bi-book text-success"></i> {{ $department->subjects->count() }} matière(s)</li>
                            <li><i class="bi bi-award text-info"></i> {{ $department->subjects->sum('credits') }} crédits au total</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Attention :</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-info-circle text-info"></i> Le changement de code affectera les références</li>
                            <li><i class="bi bi-info-circle text-info"></i> La désactivation bloque les nouvelles assignations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Actions rapides -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning"></i> Actions rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.teachers.index', ['department' => $department->id]) }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-people"></i><br>
                            Voir les enseignants<br>
                            <small>({{ $department->teachers->count() }})</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.subjects.index', ['department' => $department->id]) }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="bi bi-book"></i><br>
                            Voir les matières<br>
                            <small>({{ $department->subjects->count() }})</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-danger w-100 mb-2" onclick="confirmDelete()" 
                                {{ ($department->teachers->count() > 0 || $department->subjects->count() > 0) ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i><br>
                            Supprimer<br>
                            <small>
                                @if($department->teachers->count() > 0 || $department->subjects->count() > 0)
                                    (Non disponible)
                                @else
                                    (Définitif)
                                @endif
                            </small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $department->is_active ? 'warning' : 'success' }} w-100 mb-2">
                                <i class="bi bi-{{ $department->is_active ? 'pause' : 'play' }}"></i><br>
                                {{ $department->is_active ? 'Désactiver' : 'Activer' }}<br>
                                <small>{{ $department->is_active ? '(Pause)' : '(Reprise)' }}</small>
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
        'title' => 'Voir détails',
        'url' => route('admin.departments.show', $department),
        'icon' => 'bi bi-eye',
        'color' => 'info'
    ],
    [
        'title' => 'Liste départements',
        'url' => route('admin.departments.index'),
        'icon' => 'bi bi-list',
        'color' => 'secondary'
    ],
    [
        'title' => 'Dupliquer département',
        'url' => '#',
        'icon' => 'bi bi-files',
        'color' => 'primary'
    ],
    [
        'title' => 'Historique modifications',
        'url' => '#',
        'icon' => 'bi bi-clock-history',
        'color' => 'warning'
    ]
]" />

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous absolument sûr de vouloir supprimer le département <strong>{{ $department->name }}</strong> ?</p>
                
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Cette action est irréversible !</strong>
                    <ul class="mb-0 mt-2">
                        <li>Toutes les données du département seront perdues</li>
                        <li>Cette action ne peut pas être annulée</li>
                    </ul>
                </div>
                
                <p>Pour confirmer, tapez le nom du département : <strong>{{ $department->name }}</strong></p>
                <input type="text" id="confirmName" class="form-control" placeholder="Tapez le nom du département">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" action="{{ route('admin.departments.destroy', $department) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmDeleteBtn" class="btn btn-danger" disabled>
                        <i class="bi bi-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Force uppercase for code
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Character counter for description
const descriptionField = document.getElementById('description');
const maxLength = 1000;

// Create character counter
const counterDiv = document.createElement('div');
counterDiv.className = 'form-text text-end';
counterDiv.id = 'description-counter';
descriptionField.parentNode.appendChild(counterDiv);

function updateCounter() {
    const currentLength = descriptionField.value.length;
    const remaining = maxLength - currentLength;
    
    counterDiv.textContent = `${currentLength}/${maxLength} caractères`;
    
    if (remaining < 100) {
        counterDiv.classList.add('text-warning');
    } else {
        counterDiv.classList.remove('text-warning');
    }
    
    if (remaining < 0) {
        counterDiv.classList.add('text-danger');
        counterDiv.classList.remove('text-warning');
    } else {
        counterDiv.classList.remove('text-danger');
    }
}

descriptionField.addEventListener('input', updateCounter);
updateCounter(); // Initial call

// Confirmation de suppression
function confirmDelete() {
    @if($department->teachers->count() > 0 || $department->subjects->count() > 0)
        alert('Impossible de supprimer ce département car il contient des enseignants ou des matières. Veuillez d\'abord les réassigner ou les supprimer.');
        return;
    @endif
    
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Validation du nom pour la suppression
document.getElementById('confirmName').addEventListener('input', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const departmentName = '{{ $department->name }}';
    
    if (this.value === departmentName) {
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('btn-danger');
        confirmBtn.classList.add('btn-outline-danger');
    } else {
        confirmBtn.disabled = true;
        confirmBtn.classList.remove('btn-outline-danger');
        confirmBtn.classList.add('btn-danger');
    }
});
</script>
@endpush