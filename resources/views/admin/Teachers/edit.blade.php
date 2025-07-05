@extends('layouts.app')

@section('title', 'Modifier l\'enseignant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>
                            Modifier l'enseignant : {{ $teacher->user->name }}
                        </h3>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user"></i> Informations personnelles
                                </h5>
                                
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $teacher->user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $teacher->user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $teacher->user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="date_of_birth" class="form-label">Date de naissance *</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" 
                                           value="{{ old('date_of_birth', $teacher->user->date_of_birth?->format('Y-m-d')) }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Adresse</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $teacher->user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informations professionnelles -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-briefcase"></i> Informations professionnelles
                                </h5>

                                <div class="form-group mb-3">
                                    <label for="employee_number" class="form-label">Numéro employé</label>
                                    <input type="text" class="form-control" id="employee_number" 
                                           value="{{ $teacher->employee_number }}" readonly>
                                    <small class="form-text text-muted">Ce champ ne peut pas être modifié</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="department_id" class="form-label">Département *</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" 
                                            id="department_id" name="department_id" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('department_id', $teacher->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }} ({{ $department->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="specialization" class="form-label">Spécialisation *</label>
                                    <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                           id="specialization" name="specialization" 
                                           value="{{ old('specialization', $teacher->specialization) }}" required>
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="hire_date" class="form-label">Date d'embauche *</label>
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" name="hire_date" 
                                           value="{{ old('hire_date', $teacher->hire_date?->format('Y-m-d')) }}" required>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="salary" class="form-label">Salaire (FCFA)</label>
                                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" step="0.01" min="0"
                                           value="{{ old('salary', $teacher->salary) }}">
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Statut *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>
                                            Actif
                                        </option>
                                        <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>
                                            Inactif
                                        </option>
                                        <option value="suspended" {{ old('status', $teacher->status) == 'suspended' ? 'selected' : '' }}>
                                            Suspendu
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Compte actif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Matières enseignées -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-book"></i> Matières enseignées
                                </h5>
                                
                                <div class="row">
                                    @foreach($subjects as $subject)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" 
                                                       id="subject_{{ $subject->id }}" 
                                                       name="subjects[]" 
                                                       value="{{ $subject->id }}"
                                                       {{ $teacher->subjects->contains($subject->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                    {{ $subject->name }} 
                                                    <small class="text-muted">({{ $subject->credits }} crédits)</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($subjects->isEmpty())
                                    <p class="text-muted">Aucune matière disponible.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Voir le profil
                            </a>
                            <div>
                                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validation côté client
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier les champs requis
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs requis.');
        }
    });
    
    // Formatage du numéro de téléphone
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 9) {
            value = value.substring(0, 9);
            if (value.startsWith('7')) {
                value = '+221 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 9);
            }
        }
        $(this).val(value);
    });
});
</script>
@endpush
```
