@extends('layouts.app')

@section('title', 'Modifier Étudiant - ' . $student->user->name . ' - UNCHK')

@section('page-header')
@section('page-title', 'Modifier Étudiant')
@section('page-subtitle', $student->user->name . ' (' . $student->student_number . ')')
@section('page-actions')
    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-info me-2">
        <i class="bi bi-eye"></i> Voir le Profil
    </a>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            <!-- Alerte d'information -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Modification d'étudiant :</strong> Les modifications apportées ici affecteront les informations personnelles et académiques de l'étudiant.
            </div>

            <form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data" id="studentEditForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Informations Personnelles -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person me-2"></i>Informations Personnelles
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Prénom -->
                                    <div class="col-md-4 mb-3">
                                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name', explode(' ', $student->user->name)[0] ?? '') }}" 
                                               required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nom -->
                                    <div class="col-md-4 mb-3">
                                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="{{ old('last_name', implode(' ', array_slice(explode(' ', $student->user->name), 1)) ?: '') }}" 
                                               required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Genre -->
                                    <div class="col-md-4 mb-3">
                                        <label for="gender" class="form-label">Genre <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" 
                                                id="gender" 
                                                name="gender" 
                                                required>
                                            <option value="">Sélectionner</option>
                                            <option value="male" {{ old('gender', $student->user->gender ?? '') == 'male' ? 'selected' : '' }}>Masculin</option>
                                            <option value="female" {{ old('gender', $student->user->gender ?? '') == 'female' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $student->user->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Téléphone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $student->user->phone) }}" 
                                               placeholder="+221 77 123 45 67">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Date de naissance -->
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date de Naissance <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" 
                                               name="date_of_birth" 
                                               value="{{ old('date_of_birth', $student->user->date_of_birth?->format('Y-m-d')) }}" 
                                               required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Adresse -->
                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" 
                                                  name="address" 
                                                  rows="3" 
                                                  placeholder="Adresse complète">{{ old('address', $student->user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations Académiques -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-mortarboard me-2"></i>Informations Académiques
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Numéro étudiant -->
                                    <div class="col-md-6 mb-3">
                                        <label for="student_number" class="form-label">Numéro Étudiant <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('student_number') is-invalid @enderror" 
                                               id="student_number" 
                                               name="student_number" 
                                               value="{{ old('student_number', $student->student_number) }}" 
                                               required>
                                        @error('student_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Classe -->
                                    <div class="col-md-6 mb-3">
                                        <label for="class_id" class="form-label">Classe <span class="text-danger">*</span></label>
                                        <select class="form-select @error('class_id') is-invalid @enderror" 
                                                id="class_id" 
                                                name="class_id" 
                                                required>
                                            <option value="">Sélectionner une classe</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" 
                                                        {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }} - {{ $class->level->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Année académique -->
                                    <div class="col-md-6 mb-3">
                                        <label for="academic_year" class="form-label">Année Académique <span class="text-danger">*</span></label>
                                        <select class="form-select @error('academic_year') is-invalid @enderror" 
                                                id="academic_year" 
                                                name="academic_year" 
                                                required>
                                            <option value="">Sélectionner une année</option>
                                            <option value="2024-2025" {{ old('academic_year', $student->academic_year) == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                            <option value="2025-2026" {{ old('academic_year', $student->academic_year) == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                                            <option value="2026-2027" {{ old('academic_year', $student->academic_year) == '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                                        </select>
                                        @error('academic_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Date d'inscription -->
                                    <div class="col-md-6 mb-3">
                                        <label for="enrollment_date" class="form-label">Date d'Inscription <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control @error('enrollment_date') is-invalid @enderror" 
                                               id="enrollment_date" 
                                               name="enrollment_date" 
                                               value="{{ old('enrollment_date', $student->enrollment_date?->format('Y-m-d')) }}" 
                                               required>
                                        @error('enrollment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Statut -->
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                            <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Diplômé</option>
                                            <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Actif/Inactif -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">État du Compte</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Compte actif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contacts d'Urgence -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-shield-exclamation me-2"></i>Contacts d'Urgence
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Contact parent -->
                                    <div class="col-md-6 mb-3">
                                        <label for="parent_contact" class="form-label">Téléphone Parent/Tuteur</label>
                                        <input type="tel" 
                                               class="form-control @error('parent_contact') is-invalid @enderror" 
                                               id="parent_contact" 
                                               name="parent_contact" 
                                               value="{{ old('parent_contact', $student->parent_contact) }}" 
                                               placeholder="+221 77 123 45 67">
                                        @error('parent_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Contact d'urgence -->
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact" class="form-label">Contact d'Urgence</label>
                                        <input type="tel" 
                                               class="form-control @error('emergency_contact') is-invalid @enderror" 
                                               id="emergency_contact" 
                                               name="emergency_contact" 
                                               value="{{ old('emergency_contact', $student->emergency_contact) }}" 
                                               placeholder="+221 77 123 45 67">
                                        @error('emergency_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations Médicales -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-heart-pulse me-2"></i>Informations Médicales
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Informations médicales générales -->
                                    <div class="col-12 mb-3">
                                        <label for="medical_info" class="form-label">Informations Médicales</label>
                                        <textarea class="form-control @error('medical_info') is-invalid @enderror" 
                                                  id="medical_info" 
                                                  name="medical_info" 
                                                  rows="3" 
                                                  placeholder="Informations médicales importantes, conditions de santé...">{{ old('medical_info', $student->medical_info) }}</textarea>
                                        @error('medical_info')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Groupe sanguin -->
                                    <div class="col-md-4 mb-3">
                                        <label for="blood_type" class="form-label">Groupe Sanguin</label>
                                        <select class="form-select @error('blood_type') is-invalid @enderror" 
                                                id="blood_type" 
                                                name="blood_type">
                                            <option value="">Non renseigné</option>
                                            <option value="A+" {{ old('blood_type', $student->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('blood_type', $student->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('blood_type', $student->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('blood_type', $student->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('blood_type', $student->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('blood_type', $student->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('blood_type', $student->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('blood_type', $student->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                        @error('blood_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Allergies -->
                                    <div class="col-md-8 mb-3">
                                        <label for="allergies" class="form-label">Allergies</label>
                                        <input type="text" 
                                               class="form-control @error('allergies') is-invalid @enderror" 
                                               id="allergies" 
                                               name="allergies" 
                                               value="{{ old('allergies', $student->allergies) }}" 
                                               placeholder="Pollen, arachides, médicaments...">
                                        @error('allergies')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Médicaments -->
                                    <div class="col-md-6 mb-3">
                                        <label for="medications" class="form-label">Médicaments</label>
                                        <textarea class="form-control @error('medications') is-invalid @enderror" 
                                                  id="medications" 
                                                  name="medications" 
                                                  rows="3" 
                                                  placeholder="Médicaments pris régulièrement...">{{ old('medications', $student->medications) }}</textarea>
                                        @error('medications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Médecin traitant -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="doctor_name" class="form-label">Nom du Médecin</label>
                                            <input type="text" 
                                                   class="form-control @error('doctor_name') is-invalid @enderror" 
                                                   id="doctor_name" 
                                                   name="doctor_name" 
                                                   value="{{ old('doctor_name', $student->doctor_name) }}" 
                                                   placeholder="Dr. Nom du médecin">
                                            @error('doctor_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="doctor_phone" class="form-label">Téléphone du Médecin</label>
                                            <input type="tel" 
                                                   class="form-control @error('doctor_phone') is-invalid @enderror" 
                                                   id="doctor_phone" 
                                                   name="doctor_phone" 
                                                   value="{{ old('doctor_phone', $student->doctor_phone) }}" 
                                                   placeholder="+221 77 123 45 67">
                                            @error('doctor_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Photo de profil -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-camera me-2"></i>Photo de Profil
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if($student->user->profile_photo)
                                        <img src="{{ Storage::url($student->user->profile_photo) }}" 
                                             alt="{{ $student->user->name }}" 
                                             class="rounded-circle img-fluid mb-3" 
                                             style="width: 150px; height: 150px; object-fit: cover;" 
                                             id="profilePreview">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=2563eb&color=fff&size=150" 
                                             alt="{{ $student->user->name }}" 
                                             class="rounded-circle img-fluid mb-3" 
                                             id="profilePreview">
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <input type="file" 
                                           class="form-control @error('profile_photo') is-invalid @enderror" 
                                           id="profile_photo" 
                                           name="profile_photo" 
                                           accept="image/*" 
                                           onchange="previewImage(this)">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Résumé -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Informations Actuelles
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <strong>Statut :</strong> 
                                        <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                                            {{ $student->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Classe :</strong> {{ $student->class->name ?? 'Non assignée' }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Niveau :</strong> {{ $student->class->level->name ?? 'Non défini' }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Inscrit le :</strong> {{ $student->enrollment_date?->format('d/m/Y') ?? 'Non définie' }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Dernière modification :</strong> {{ $student->updated_at->format('d/m/Y à H:i') }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Enregistrer les Modifications
                                    </button>
                                    
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye me-2"></i>Voir le Profil
                                    </a>
                                    
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Annuler
                                    </a>
                                </div>
                                
                                <hr class="my-3">
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-shield-check me-1"></i>
                                        Toutes les modifications sont sécurisées et tracées
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Validation du formulaire
document.getElementById('studentEditForm').addEventListener('submit', function(e) {
    const requiredFields = ['first_name', 'last_name', 'gender', 'email', 'student_number', 'class_id', 'academic_year', 'enrollment_date', 'status'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
    }
});

// Auto-save draft (optionnel)
let autoSaveTimeout;
document.querySelectorAll('input, textarea, select').forEach(field => {
    field.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Ici vous pourriez implémenter un système de sauvegarde automatique
            console.log('Auto-save...');
        }, 3000);
    });
});
</script>
@endpush

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.card-title {
    color: #495057;
}

.text-danger {
    font-weight: 500;
}

.form-control:focus,
.form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.btn-primary {
    background-color: #2563eb;
    border-color: #2563eb;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
}

#profilePreview {
    border: 3px solid #e5e7eb;
    transition: all 0.3s ease;
}

#profilePreview:hover {
    border-color: #2563eb;
    transform: scale(1.02);
}

.alert-info {
    border-left: 4px solid #2563eb;
}
</style>
@endpush