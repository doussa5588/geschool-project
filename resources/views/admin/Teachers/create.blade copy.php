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






















## 2. Vue index des enseignants (`resources/views/admin/teachers/index.blade.php`)

```blade
@extends('layouts.admin')

@section('title', 'Gestion des enseignants')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Gestion des enseignants
                        </h3>
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel enseignant
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="department_filter" class="form-label">Département</label>
                            <select class="form-control" id="department_filter">
                                <option value="">Tous les départements</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_filter" class="form-label">Statut</label>
                            <select class="form-control" id="status_filter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="suspended">Suspendu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" placeholder="Nom, email, téléphone...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary d-block" onclick="resetFilters()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total enseignants</span>
                                    <span class="info-box-number">{{ $teachers->total() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Actifs</span>
                                    <span class="info-box-number">{{ $teachers->where('status', 'active')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avec matières</span>
                                    <span class="info-box-number">{{ $teachers->filter(fn($t) => $t->subjects_count > 0)->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sans matières</span>
                                    <span class="info-box-number">{{ $teachers->filter(fn($t) => $t->subjects_count == 0)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des enseignants -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="teachersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Département</th>
                                    <th>Matières</th>
                                    <th>Expérience</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $teacher)
                                    <tr>
                                        <td class="text-center">
                                            @if($teacher->user->profile_photo)
                                                <img src="{{ asset('storage/' . $teacher->user->profile_photo) }}" 
                                                     alt="{{ $teacher->user->name }}" 
                                                     class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white font-weight-bold">
                                                        {{ $teacher->user->initials }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $teacher->user->name }}</strong><br>
                                            <small class="text-muted">{{ $teacher->employee_number }}</small>
                                        </td>
                                        <td>{{ $teacher->user->email }}</td>
                                        <td>{{ $teacher->user->formatted_phone ?? 'Non défini' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $teacher->department->name ?? 'Non assigné' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $teacher->subjects_count }} matière(s)
                                            </span><br>
                                            <small class="text-muted">{{ $teacher->total_credits }} crédits</small>
                                        </td>
                                        <td>
                                            {{ $teacher->experience_years }} an(s)<br>
                                            <small class="text-muted">{{ $teacher->seniority_level_french }}</small>
                                        </td>
                                        <td>
                                            @switch($teacher->status)
                                                @case('active')
                                                    <span class="badge bg-success">{{ $teacher->status_french }}</span>
                                                    @break
                                                @case('inactive')
                                                    <span class="badge bg-warning">{{ $teacher->status_french }}</span>
                                                    @break
                                                @case('suspended')
                                                    <span class="badge bg-danger">{{ $teacher->status_french }}</span>
                                                    @break
                                            @endswitch
                                            @if(!$teacher->is_active)
                                                <br><small class="text-danger">Compte désactivé</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                                   class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                                                   class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($teacher->canBeDeleted())
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete({{ $teacher->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun enseignant trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($teachers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $teachers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet enseignant ?</p>
                <p class="text-danger"><strong>Cette action est irréversible.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(teacherId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/teachers/${teacherId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function resetFilters() {
    document.getElementById('department_filter').value = '';
    document.getElementById('status_filter').value = '';
    document.getElementById('search').value = '';
    // Recharger la page ou appliquer les filtres
    window.location.href = window.location.pathname;
}

$(document).ready(function() {
    // Filtrage en temps réel
    $('#search').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#teachersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Filtres par département et statut
    $('#department_filter, #status_filter').on('change', function() {
        // Implémentation du filtrage
        filterTable();
    });
});

function filterTable() {
    const department = $('#department_filter').val();
    const status = $('#status_filter').val();
    const search = $('#search').val().toLowerCase();
    
    $('#teachersTable tbody tr').each(function() {
        let show = true;
        const row = $(this);
        
        if (department && !row.find('td:eq(4)').text().includes(department)) {
            show = false;
        }
        
        if (status && !row.find('td:eq(7)').text().toLowerCase().includes(status)) {
            show = false;
        }
        
        if (search && !row.text().toLowerCase().includes(search)) {
            show = false;
        }
        
        row.toggle(show);
    });
}
</script>
@endpush
```

## 3. Vue de détail d'un enseignant (`resources/views/admin/teachers/show.blade.php`)

```blade
@extends('layouts.admin')

@section('title', 'Profil enseignant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête du profil -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-graduate"></i>
                            Profil de l'enseignant
                        </h3>
                        <div>
                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Informations personnelles -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($teacher->user->profile_photo)
                                <img src="{{ asset('storage/' . $teacher->user->profile_photo) }}" 
                                     alt="{{ $teacher->user->name }}" 
                                     class="rounded-circle mb-3" width="120" height="120">
                            @else
                                <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <span class="text-white h2 mb-0">{{ $teacher->user->initials }}</span>
                                </div>
                            @endif
                            
                            <h4 class="mb-1">{{ $teacher->user->name }}</h4>
                            <p class="text-muted mb-2">{{ $teacher->specialization }}</p>
                            <p class="text-muted">{{ $teacher->employee_number }}</p>
                            
                            <div class="mt-3">
                                @switch($teacher->status)
                                    @case('active')
                                        <span class="badge bg-success fs-6">{{ $teacher->status_french }}</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge bg-warning fs-6">{{ $teacher->status_french }}</span>
                                        @break
                                    @case('suspended')
                                        <span class="badge bg-danger fs-6">{{ $teacher->status_french }}</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-address-book"></i> Contact</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong><i class="fas fa-envelope text-primary"></i> Email:</strong><br>
                                <a href="mailto:{{ $teacher->user->email }}">{{ $teacher->user->email }}</a>
                            </div>
                            
                            @if($teacher->user->phone)
                                <div class="mb-3">
                                    <strong><i class="fas fa-phone text-primary"></i> Téléphone:</strong><br>
                                    <a href="tel:{{ $teacher->user->phone }}">{{ $teacher->user->formatted_phone }}</a>
                                </div>
                            @endif
                            
                            @if($teacher->user->address)
                                <div class="mb-3">
                                    <strong><i class="fas fa-map-marker-alt text-primary"></i> Adresse:</strong><br>
                                    {{ $teacher->user->address }}
                                </div>
                            @endif
                            
                            @if($teacher->user->date_of_birth)
                                <div class="mb-3">
                                    <strong><i class="fas fa-birthday-cake text-primary"></i> Date de naissance:</strong><br>
                                    {{ $teacher->user->formatted_birth_date }} ({{ $teacher->user->age }} ans)
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations professionnelles et statistiques -->
                <div class="col-md-8">
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Matières</span>
                                    <span class="info-box-number">{{ $teacher->subjects_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Étudiants</span>
                                    <span class="info-box-number">{{ $teacher->total_students_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-star"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Crédits</span>
                                    <span class="info-box-number">{{ $teacher->total_credits }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expérience</span>
                                    <span class="info-box-number">{{ $teacher->experience_years }} ans</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations professionnelles -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-briefcase"></i> Informations professionnelles</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Département:</strong><br>
                                    <span class="badge bg-info">
                                        {{ $teacher->department->name ?? 'Non assigné' }}
                                        @if($teacher->department)
                                            ({{ $teacher->department->code }})
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Spécialisation:</strong><br>
                                    {{ $teacher->specialization }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Date d'embauche:</strong><br>
                                    {{ $teacher->formatted_hire_date }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Expérience:</strong><br>
                                    {{ $teacher->experience_years }} année(s) - {{ $teacher->seniority_level_french }}
                                </div>
                                @if($teacher->salary)
                                    <div class="col-md-6 mb-3">
                                        <strong>Salaire:</strong><br>
                                        {{ $teacher->formatted_salary }}
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <strong>Charge de travail:</strong><br>
                                    {{ $teacher->workload_description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Matières enseignées -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chalkboard"></i> Matières enseignées</h5>
                        </div>
                        <div class="card-body">
                            @if($teacher->subjects->isNotEmpty())
                                <div class="row">
                                    @foreach($teacher->subjects as $subject)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h6 class="mb-2">
                                                    <i class="fas fa-book text-primary"></i>
                                                    {{ $subject->name }}
                                                </h6>
                                                <p class="text-muted mb-2">{{ $subject->code }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-secondary">{{ $subject->credits }} crédits</span>
                                                    <small class="text-muted">
                                                        {{ $subject->department->name ?? 'N/A' }}
                                                    </small>
                                                </div>
                                                @if($subject->description)
                                                    <p class="text-muted mt-2 mb-0">{{ $subject->formatted_description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune matière assignée à cet enseignant.</p>
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Assigner des matières
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Classes enseignées -->
                    @if($teacher->classes_count > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users"></i> Classes enseignées</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($teacher->getDistinctClasses() as $class)
                                        <div class="col-md-4 mb-2">
                                            <div class="border rounded p-2 text-center">
                                                <strong>{{ $class->name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $class->level->name ?? 'N/A' }} - {{ $class->students_count }} étudiants
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animation d'entrée des cartes
    $('.card').hide().fadeIn(800);
});
</script>
@endpush
```

## 4. Vue de création d'enseignant (`resources/views/admin/teachers/create.blade.php`)

```blade
@extends('layouts.admin')

@section('title', 'Nouvel enseignant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus"></i>
                            Ajouter un nouvel enseignant
                        </h3>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
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
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           placeholder="+221 77 123 45 67">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="date_of_birth" class="form-label">Date de naissance *</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Adresse</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address') }}</textarea>
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
                                    <label for="employee_number" class="form-label">Numéro employé *</label>
                                    <input type="text" class="form-control @error('employee_number') is-invalid @enderror" 
                                           id="employee_number" name="employee_number" value="{{ old('employee_number', $nextEmployeeNumber) }}" required>
                                    <small class="form-text text-muted">Sera généré automatiquement si laissé vide</small>
                                    @error('employee_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="department_id" class="form-label">Département *</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" 
                                            id="department_id" name="department_id" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                           id="specialization" name="specialization" value="{{ old('specialization') }}" required>
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="hire_date" class="form-label">Date d'embauche *</label>
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="salary" class="form-label">Salaire (FCFA)</label>
                                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" step="0.01" min="0" value="{{ old('salary') }}">
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Statut *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Actif
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactif
                                        </option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                            Suspendu
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Compte actif
                                    </label>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="profile_photo" class="form-label">Photo de profil</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                           id="profile_photo" name="profile_photo" accept="image/*">
                                    <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Matières à enseigner -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-book"></i> Matières à enseigner (optionnel)
                                </h5>
                                
                                <div class="row">
                                    @foreach($subjects as $subject)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" 
                                                       id="subject_{{ $subject->id }}" 
                                                       name="subjects[]" 
                                                       value="{{ $subject->id }}"
                                                       {{ in_array($subject->id, old('subjects', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                    {{ $subject->name }} 
                                                    <small class="text-muted">({{ $subject->credits }} crédits)</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($subjects->isEmpty())
                                    <p class="text-muted">Aucune matière disponible pour le moment.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer l'enseignant
                            </button>
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
    // Génération automatique du numéro d'employé
    $('#name').on('input', function() {
        if (!$('#employee_number').val()) {
            const name = $(this).val().toLowerCase().replace(/\s+/g, '');
            const year = new Date().getFullYear();
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            $('#employee_number').val(`EMP${year}${random}`);
        }
    });
    
    // Validation du mot de passe
    $('#password_confirmation').on('keyup', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (password !== confirmation) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Formatage du téléphone
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
    
    // Prévisualisation de l'image
    $('#profile_photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Ajouter une prévisualisation si nécessaire
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Validation du formulaire
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier les mots de passe
        if ($('#password').val() !== $('#password_confirmation').val()) {
            isValid = false;
            alert('Les mots de passe ne correspondent pas.');
        }
        
        // Vérifier les champs requis
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush