@extends('layouts.app')

@section('title', 'Nouvel Étudiant - UNCHK')

@section('page-header')
@section('page-title', 'Nouvel Étudiant')
@section('page-subtitle', 'Ajouter un nouvel étudiant à l\'établissement')
@section('page-actions')
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" id="studentForm">
        @csrf
        
        <div class="row">
            <!-- Informations Personnelles -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-fill me-2"></i>Informations Personnelles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    Prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">
                                    Nom <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Un mot de passe temporaire sera envoyé à cette adresse</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           placeholder="+221 XX XXX XX XX">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">
                                    Date de naissance <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                                       max="{{ now()->subYears(15)->format('Y-m-d') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="gender" class="form-label">
                                    Sexe <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror" 
                                        id="gender" name="gender" required>
                                    <option value="">Sélectionner le sexe</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Masculin</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="Adresse complète de l'étudiant">{{ old('address') }}</textarea>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="department_id" class="form-label">
                                    Département <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" name="department_id" required>
                                    <option value="">Sélectionner un département</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="class_id" class="form-label">
                                    Classe <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Sélectionner d'abord un département</option>
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="classInfo"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de Contact d'Urgence -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-exclamation me-2"></i>Contact d'Urgence
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="parent_phone" class="form-label">Téléphone du Parent/Tuteur</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-hearts"></i></span>
                                    <input type="tel" class="form-control @error('parent_phone') is-invalid @enderror" 
                                           id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}" 
                                           placeholder="+221 XX XXX XX XX">
                                </div>
                                @error('parent_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="parent_email" class="form-label">Email du Parent/Tuteur</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-heart"></i></span>
                                    <input type="email" class="form-control @error('parent_email') is-invalid @enderror" 
                                           id="parent_email" name="parent_email" value="{{ old('parent_email') }}">
                                </div>
                                @error('parent_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="emergency_contact" class="form-label">Contact d'Urgence</label>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                       id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" 
                                       placeholder="Nom et téléphone du contact d'urgence">
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
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="medical_info" class="form-label">Informations Médicales</label>
                                <textarea class="form-control @error('medical_info') is-invalid @enderror" 
                                          id="medical_info" name="medical_info" rows="4" 
                                          placeholder="Allergies, maladies chroniques, traitements, etc.">{{ old('medical_info') }}</textarea>
                                @error('medical_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ces informations resteront confidentielles et ne seront utilisées qu'en cas d'urgence</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photo et Actions -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-camera me-2"></i>Photo de l'Étudiant
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="photo-preview" id="photoPreview">
                                <img src="https://ui-avatars.com/api/?name=Nouvel+Etudiant&background=e2e8f0&color=64748b&size=200" 
                                     alt="Aperçu" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/jpeg,image/png,image/jpg" onchange="previewPhoto()">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPEG, PNG uniquement. Max 2MB.</div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearPhoto()">
                            <i class="bi bi-x-circle"></i> Supprimer la photo
                        </button>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Résumé
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Numéro Étudiant :</strong> <span class="text-muted">Généré automatiquement</span></li>
                            <li><strong>Mot de passe :</strong> <span class="text-muted">unchk{{ date('Y') }}</span></li>
                            <li><strong>Date d'inscription :</strong> {{ now()->format('d/m/Y') }}</li>
                            <li><strong>Statut :</strong> <span class="badge bg-success">Actif</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-person-plus"></i> Créer l'Étudiant
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Department change handler
    $('#department_id').change(function() {
        const departmentId = $(this).val();
        const classSelect = $('#class_id');
        const classInfo = $('#classInfo');
        
        if (departmentId) {
            $.ajax({
                url: `/admin/api/classes/by-department/${departmentId}`,
                method: 'GET',
                beforeSend: function() {
                    classSelect.html('<option value="">Chargement...</option>');
                    classInfo.text('');
                },
                success: function(classes) {
                    classSelect.html('<option value="">Sélectionner une classe</option>');
                    
                    if (classes.length > 0) {
                        classes.forEach(function(classItem) {
                            const capacity = classItem.current_students + '/' + classItem.capacity;
                            const isAvailable = classItem.current_students < classItem.capacity;
                            
                            classSelect.append(`
                                <option value="${classItem.id}" ${!isAvailable ? 'disabled' : ''}>
                                    ${classItem.name} (${classItem.level_name}) - ${capacity}
                                    ${!isAvailable ? ' - PLEINE' : ''}
                                </option>
                            `);
                        });
                    } else {
                        classSelect.append('<option value="">Aucune classe disponible</option>');
                    }
                },
                error: function() {
                    classSelect.html('<option value="">Erreur de chargement</option>');
                    classInfo.html('<span class="text-danger">Erreur lors du chargement des classes</span>');
                }
            });
        } else {
            classSelect.html('<option value="">Sélectionner d\'abord un département</option>');
            classInfo.text('');
        }
    });

    // Class change handler
    $('#class_id').change(function() {
        const classId = $(this).val();
        const classInfo = $('#classInfo');
        
        if (classId) {
            $.ajax({
                url: `/admin/api/classes/${classId}/info`,
                method: 'GET',
                success: function(data) {
                    const availableSpots = data.capacity - data.current_students;
                    const percentage = Math.round((data.current_students / data.capacity) * 100);
                    
                    let statusClass = 'text-success';
                    if (percentage > 80) statusClass = 'text-warning';
                    if (percentage >= 100) statusClass = 'text-danger';
                    
                    classInfo.html(`
                        <span class="${statusClass}">
                            ${data.current_students}/${data.capacity} étudiants 
                            (${availableSpots} places disponibles)
                        </span>
                    `);
                },
                error: function() {
                    classInfo.html('<span class="text-danger">Erreur lors du chargement des informations</span>');
                }
            });
        } else {
            classInfo.text('');
        }
    });

    // Form validation
    $('#studentForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        $('#submitBtn').html('<span class="loading"></span> Création en cours...');
        $('#submitBtn').prop('disabled', true);
    });

    // Auto-generate email if needed
    $('#first_name, #last_name').on('input', function() {
        updateEmailSuggestion();
    });

    // Load old values if validation failed
    @if(old('department_id'))
        $('#department_id').val('{{ old('department_id') }}').trigger('change');
        setTimeout(function() {
            $('#class_id').val('{{ old('class_id') }}');
        }, 1000);
    @endif
});

function previewPhoto() {
    const file = document.getElementById('photo').files[0];
    const preview = document.querySelector('#photoPreview img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function clearPhoto() {
    document.getElementById('photo').value = '';
    const preview = document.querySelector('#photoPreview img');
    preview.src = 'https://ui-avatars.com/api/?name=Nouvel+Etudiant&background=e2e8f0&color=64748b&size=200';
}

function validateForm() {
    let isValid = true;
    const requiredFields = ['first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'class_id'];
    
    requiredFields.forEach(function(field) {
        const element = document.getElementById(field);
        if (!element.value.trim()) {
            element.classList.add('is-invalid');
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    });
    
    // Email validation
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        document.getElementById('email').classList.add('is-invalid');
        isValid = false;
    }
    
    // Age validation (minimum 15 years)
    const birthDate = new Date(document.getElementById('date_of_birth').value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    if (age < 15) {
        document.getElementById('date_of_birth').classList.add('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        alert('Veuillez corriger les erreurs dans le formulaire avant de continuer.');
    }
    
    return isValid;
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ? Toutes les données saisies seront perdues.')) {
        document.getElementById('studentForm').reset();
        clearPhoto();
        $('.is-invalid').removeClass('is-invalid');
        $('#class_id').html('<option value="">Sélectionner d\'abord un département</option>');
        $('#classInfo').text('');
    }
}

function updateEmailSuggestion() {
    const firstName = $('#first_name').val().toLowerCase().trim();
    const lastName = $('#last_name').val().toLowerCase().trim();
    const emailField = $('#email');
    
    // Only suggest if email is empty
    if (!emailField.val() && firstName && lastName) {
        const suggestion = `${firstName}.${lastName}@etudiant.unchk.edu.sn`;
        emailField.attr('placeholder', `Suggestion: ${suggestion}`);
        
        // Show a small button to accept suggestion
        if (!$('#emailSuggestion').length) {
            emailField.parent().append(`
                <button type="button" class="btn btn-outline-secondary btn-sm mt-1" id="emailSuggestion" onclick="acceptEmailSuggestion()">
                    <i class="bi bi-check"></i> Utiliser: ${suggestion}
                </button>
            `);
        } else {
            $('#emailSuggestion').html(`<i class="bi bi-check"></i> Utiliser: ${suggestion}`);
            $('#emailSuggestion').attr('onclick', `acceptEmailSuggestion('${suggestion}')`);
        }
    }
}

function acceptEmailSuggestion(email) {
    if (!email) {
        const firstName = $('#first_name').val().toLowerCase().trim();
        const lastName = $('#last_name').val().toLowerCase().trim();
        email = `${firstName}.${lastName}@etudiant.unchk.edu.sn`;
    }
    
    $('#email').val(email);
    $('#emailSuggestion').remove();
}

// Real-time field validation
$('.form-control, .form-select').on('blur', function() {
    if ($(this).hasClass('is-invalid') && $(this).val().trim()) {
        $(this).removeClass('is-invalid');
    }
});

// Phone number formatting
$('#phone, #parent_phone').on('input', function() {
    let value = $(this).val().replace(/\D/g, '');
    
    // Senegal phone number format
    if (value.startsWith('221')) {
        value = value.substring(3);
    }
    
    if (value.length <= 9) {
        if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '+221 $1 $2 $3 $4');
        } else {
            value = '+221 ' + value;
        }
    }
    
    $(this).val(value);
});
</script>
@endpush