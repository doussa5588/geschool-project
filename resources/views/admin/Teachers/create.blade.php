@extends('layouts.app')

@section('title', 'Nouvel enseignant')

@section('page-header')
@endsection

@section('page-title', 'Nouvel enseignant')
@section('page-subtitle', 'Ajouter un nouveau membre du corps enseignant')

@section('page-actions')
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-plus"></i>
                    Informations de l'enseignant
                </h5>
            </div>

            <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" id="teacherForm">
                @csrf
                
                <div class="card-body">
                    <!-- Étapes du formulaire -->
                    <div class="mb-4">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="formProgress"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary fw-bold" id="step1">1. Informations personnelles</small>
                            <small class="text-muted" id="step2">2. Informations professionnelles</small>
                            <small class="text-muted" id="step3">3. Matières et finalisation</small>
                        </div>
                    </div>

                    <!-- Étape 1: Informations personnelles -->
                    <div class="form-step" id="formStep1">
                        <div class="row">
                            <div class="col-lg-8 mx-auto">
                                <h6 class="text-primary fw-bold mb-4">
                                    <i class="bi bi-person"></i> Informations personnelles
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
                                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" required>
                                        <div class="form-text" id="passwordMatch"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}" 
                                               placeholder="+221 77 123 45 67">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="profile_photo" class="form-label">Photo de profil</label>
                                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                               id="profile_photo" name="profile_photo" accept="image/*">
                                        <div class="form-text">Formats acceptés: JPG, PNG, GIF (max 2MB)</div>
                                        @error('profile_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="imagePreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2: Informations professionnelles -->
                    <div class="form-step d-none" id="formStep2">
                        <div class="row">
                            <div class="col-lg-8 mx-auto">
                                <h6 class="text-primary fw-bold mb-4">
                                    <i class="bi bi-briefcase"></i> Informations professionnelles
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="employee_number" class="form-label">Numéro employé</label>
                                        <input type="text" class="form-control @error('employee_number') is-invalid @enderror" 
                                               id="employee_number" name="employee_number" value="{{ old('employee_number') }}" readonly>
                                        <div class="form-text">Généré automatiquement</div>
                                        @error('employee_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="department_id" class="form-label">Département <span class="text-danger">*</span></label>
                                        <select class="form-select @error('department_id') is-invalid @enderror" 
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

                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">Spécialisation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                               id="specialization" name="specialization" value="{{ old('specialization') }}" required>
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="hire_date" class="form-label">Date d'embauche <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                               id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required>
                                        @error('hire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="salary" class="form-label">Salaire (FCFA)</label>
                                        <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                               id="salary" name="salary" step="1" min="0" value="{{ old('salary') }}">
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
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

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Compte actif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3: Matières et finalisation -->
                    <div class="form-step d-none" id="formStep3">
                        <div class="row">
                            <div class="col-lg-10 mx-auto">
                                <h6 class="text-primary fw-bold mb-4">
                                    <i class="bi bi-book"></i> Matières à enseigner (optionnel)
                                </h6>
                                
                                @if($subjects->isNotEmpty())
                                    <div class="row">
                                        @foreach($subjects->groupBy('department.name') as $departmentName => $departmentSubjects)
                                            <div class="col-lg-6 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">{{ $departmentName }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach($departmentSubjects as $subject)
                                                            <div class="form-check mb-2">
                                                                <input type="checkbox" class="form-check-input subject-checkbox" 
                                                                       id="subject_{{ $subject->id }}" 
                                                                       name="subjects[]" 
                                                                       value="{{ $subject->id }}"
                                                                       {{ in_array($subject->id, old('subjects', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                                    <strong>{{ $subject->name }}</strong>
                                                                    <small class="text-muted d-block">
                                                                        {{ $subject->code }} • {{ $subject->credits }} crédits
                                                                    </small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Total sélectionné:</strong> 
                                        <span id="selectedCount">0</span> matière(s) • 
                                        <span id="selectedCredits">0</span> crédits
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-book fs-1"></i>
                                        <p class="mt-2">Aucune matière disponible pour le moment.</p>
                                    </div>
                                @endif

                                <!-- Récapitulatif -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-check-circle"></i> Récapitulatif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nom:</strong> <span id="summaryName">-</span><br>
                                                <strong>Email:</strong> <span id="summaryEmail">-</span><br>
                                                <strong>Téléphone:</strong> <span id="summaryPhone">-</span><br>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Département:</strong> <span id="summaryDepartment">-</span><br>
                                                <strong>Spécialisation:</strong> <span id="summarySpecialization">-</span><br>
                                                <strong>Date d'embauche:</strong> <span id="summaryHireDate">-</span><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
                            <i class="bi bi-arrow-left"></i> Précédent
                        </button>
                        
                        <div class="ms-auto">
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-lg"></i> Annuler
                            </a>
                            <button type="button" class="btn btn-primary" id="nextBtn">
                                Suivant <i class="bi bi-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success d-none" id="submitBtn">
                                <i class="bi bi-check-lg"></i> Créer l'enseignant
                            </button>
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
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 3;
    
    // Navigation entre les étapes
    $('#nextBtn').click(function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    $('#prevBtn').click(function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    function showStep(step) {
        // Masquer toutes les étapes
        $('.form-step').addClass('d-none');
        
        // Afficher l'étape courante
        $(`#formStep${step}`).removeClass('d-none');
        
        // Mettre à jour la barre de progression
        const progress = (step / totalSteps) * 100;
        $('#formProgress').css('width', progress + '%');
        
        // Mettre à jour les indicateurs d'étapes
        for (let i = 1; i <= totalSteps; i++) {
            if (i <= step) {
                $(`#step${i}`).removeClass('text-muted').addClass('text-primary fw-bold');
            } else {
                $(`#step${i}`).removeClass('text-primary fw-bold').addClass('text-muted');
            }
        }
        
        // Gérer les boutons
        if (step === 1) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }
        
        if (step === totalSteps) {
            $('#nextBtn').addClass('d-none');
            $('#submitBtn').removeClass('d-none');
            updateSummary();
        } else {
            $('#nextBtn').removeClass('d-none');
            $('#submitBtn').addClass('d-none');
        }
    }
    
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = $(`#formStep${currentStep}`);
        
        // Valider les champs requis de l'étape courante
        currentStepElement.find('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Ce champ est requis.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validation spécifique pour l'étape 1
        if (currentStep === 1) {
            // Validation de l'email
            const email = $('#email').val();
            if (email && !validateEmail(email)) {
                $('#email').addClass('is-invalid');
                if (!$('#email').next('.invalid-feedback').length) {
                    $('#email').after('<div class="invalid-feedback">Format d\'email invalide.</div>');
                }
                isValid = false;
            }
            
            // Validation des mots de passe
            const password = $('#password').val();
            const passwordConfirm = $('#password_confirmation').val();
            if (password && passwordConfirm && password !== passwordConfirm) {
                $('#password_confirmation').addClass('is-invalid');
                $('#passwordMatch').text('Les mots de passe ne correspondent pas.').addClass('text-danger');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function updateSummary() {
        $('#summaryName').text($('#name').val() || '-');
        $('#summaryEmail').text($('#email').val() || '-');
        $('#summaryPhone').text($('#phone').val() || '-');
        $('#summaryDepartment').text($('#department_id option:selected').text() || '-');
        $('#summarySpecialization').text($('#specialization').val() || '-');
        $('#summaryHireDate').text($('#hire_date').val() || '-');
    }
    
    // Génération automatique du numéro d'employé
    $('#name').on('input', function() {
        if (!$('#employee_number').val()) {
            const name = $(this).val().toLowerCase().replace(/[^a-z]/g, '').substring(0, 3);
            const year = new Date().getFullYear();
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            $('#employee_number').val(`${name.toUpperCase()}${year}${random}`);
        }
    });
    
    // Formatage du téléphone
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 9 && value.startsWith('7')) {
            value = value.substring(0, 9);
            value = '+221 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 9);
            $(this).val(value);
        }
    });
    
    // Validation des mots de passe en temps réel
    $('#password, #password_confirmation').on('keyup', function() {
        const password = $('#password').val();
        const confirmation = $('#password_confirmation').val();
        
        if (password && confirmation) {
            if (password === confirmation) {
                $('#passwordMatch').text('Les mots de passe correspondent.').removeClass('text-danger').addClass('text-success');
                $('#password_confirmation').removeClass('is-invalid').addClass('is-valid');
            } else {
                $('#passwordMatch').text('Les mots de passe ne correspondent pas.').removeClass('text-success').addClass('text-danger');
                $('#password_confirmation').removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $('#passwordMatch').text('');
            $('#password_confirmation').removeClass('is-valid is-invalid');
        }
    });
    
    // Affichage/masquage du mot de passe
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // Prévisualisation de l'image
    $('#profile_photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`
                    <img src="${e.target.result}" alt="Prévisualisation" 
                         class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                `);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Comptage des matières sélectionnées
    $('.subject-checkbox').on('change', function() {
        updateSubjectCount();
    });
    
    function updateSubjectCount() {
        const selectedCount = $('.subject-checkbox:checked').length;
        let totalCredits = 0;
        
        $('.subject-checkbox:checked').each(function() {
            const label = $(this).next('label').text();
            const credits = label.match(/(\d+) crédits/);
            if (credits) {
                totalCredits += parseInt(credits[1]);
            }
        });
        
        $('#selectedCount').text(selectedCount);
        $('#selectedCredits').text(totalCredits);
    }
    
    // Validation finale du formulaire
    $('#teacherForm').on('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
        } else {
            // Afficher le loader
            $('#submitBtn').html('<span class="loading"></span> Création en cours...');
        }
    });
    
    // Initialisation
    updateSubjectCount();
});
</script>
@endpush