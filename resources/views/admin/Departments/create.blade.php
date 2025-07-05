@extends('layouts.app')

@section('title', 'Créer un département')

@section('page-title', 'Nouveau département')
@section('page-subtitle', 'Ajouter un département à l\'organisation')

@section('page-actions')
    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building-add"></i> Informations du département
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.departments.store') }}">
                    @csrf
                    
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
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('code') }}" 
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
                                  placeholder="Description détaillée du département et de ses activités...">{{ old('description') }}</textarea>
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
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Département actif
                            </label>
                        </div>
                        <div class="form-text">Les départements inactifs ne peuvent pas avoir de nouvelles assignations</div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Créer le département
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Informations supplémentaires -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Informations importantes
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Après création :</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Vous pourrez assigner des enseignants</li>
                            <li><i class="bi bi-check text-success"></i> Vous pourrez créer des matières</li>
                            <li><i class="bi bi-check text-success"></i> Générer des rapports</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Bonnes pratiques :</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-lightbulb text-warning"></i> Utilisez des codes courts et clairs</li>
                            <li><i class="bi bi-lightbulb text-warning"></i> Décrivez précisément les objectifs</li>
                            <li><i class="bi bi-lightbulb text-warning"></i> Maintenez l'organisation cohérente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Liste départements',
        'url' => route('admin.departments.index'),
        'icon' => 'bi bi-list',
        'color' => 'secondary'
    ],
    [
        'title' => 'Aide création',
        'url' => '#',
        'icon' => 'bi bi-question-circle',
        'color' => 'info'
    ],
    [
        'title' => 'Modèles départements',
        'url' => '#',
        'icon' => 'bi bi-file-template',
        'color' => 'warning'
    ]
]" />
@endsection

@push('scripts')
<script>
// Auto-generate code from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const codeField = document.getElementById('code');
    
    // Only auto-generate if code field is empty
    if (!codeField.value) {
        // Extract first letters of each word
        const words = name.split(' ');
        let code = '';
        
        words.forEach(word => {
            if (word.length > 0) {
                code += word.charAt(0).toUpperCase();
            }
        });
        
        // Limit to 10 characters
        code = code.substring(0, 10);
        codeField.value = code;
    }
});

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
</script>
@endpush