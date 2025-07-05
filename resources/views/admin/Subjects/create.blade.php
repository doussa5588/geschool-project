@extends('layouts.app')

@section('title', 'Nouvelle Matière - UNCHK')

@section('page-header')
@section('page-title', 'Nouvelle Matière')
@section('page-subtitle', 'Ajouter une nouvelle matière au programme académique')
@section('page-actions')
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.subjects.store') }}" method="POST" id="subjectForm">
        @csrf
        
        <div class="row">
            <!-- Informations Principales -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-book me-2"></i>Informations de la Matière
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nom de la matière <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Ex: Mathématiques Appliquées">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="code" class="form-label">
                                    Code de la matière <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required
                                           placeholder="Ex: MATH101" style="text-transform: uppercase;"
                                           pattern="[A-Z0-9]{3,10}" maxlength="20">
                                </div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Code unique en lettres et chiffres (3-10 caractères)</div>
                                <div id="codeAvailability" class="mt-1"></div>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Description détaillée de la matière, objectifs pédagogiques, prérequis...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="descriptionCount">0</span>/1000 caractères
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organisation Académique -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>Organisation Académique
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
                                            {{ $department->name }} ({{ $department->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="credits" class="form-label">
                                    Nombre de crédits <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-award"></i></span>
                                    <select class="form-select @error('credits') is-invalid @enderror" 
                                            id="credits" name="credits" required>
                                        <option value="">Choisir le nombre de crédits</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" 
                                                    {{ old('credits') == $i ? 'selected' : '' }}>
                                                {{ $i }} crédit{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                @error('credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Valeur ECTS de la matière</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="teacher_id" class="form-label">Enseignant responsable</label>
                                <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                        id="teacher_id" name="teacher_id">
                                    <option value="">Sélectionner d'abord un département</option>
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="teacherInfo"></div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Statut de la matière</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Matière active
                                    </label>
                                </div>
                                <div class="form-text">Les matières inactives ne sont pas visibles dans les emplois du temps</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignation aux Classes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-collection me-2"></i>Assignation aux Classes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Sélectionnez les classes qui suivront cette matière</p>
                        
                        <div id="classesContainer">
                            <div class="text-muted text-center py-3">
                                Sélectionnez d'abord un département pour voir les classes disponibles
                            </div>
                        </div>
                        
                        @error('classes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Aperçu -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-eye me-2"></i>Aperçu de la Matière
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="subjectPreview">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-book fs-1"></i>
                                <p class="mt-2 mb-0">Remplissez le formulaire pour voir l'aperçu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations Système -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informations Système
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Code généré :</strong> <span class="text-muted" id="generatedCode">Automatique</span></li>
                            <li><strong>Date de création :</strong> {{ now()->format('d/m/Y') }}</li>
                            <li><strong>Créé par :</strong> {{ auth()->user()->name }}</li>
                            <li><strong>Type :</strong> <span class="badge bg-primary">Matière académique</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Conseils -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightbulb me-2"></i>Conseils
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading">Code de matière</h6>
                            <p class="mb-0">Utilisez une convention cohérente : CODE_DEPT + NIVEAU + NUMERO (ex: MATH101, INFO201)</p>
                        </div>
                        <div class="alert alert-warning mb-3">
                            <h6 class="alert-heading">Crédits ECTS</h6>
                            <p class="mb-0">1 crédit = environ 25-30h de travail étudiant (cours + travail personnel)</p>
                        </div>
                        <div class="alert alert-success mb-0">
                            <h6 class="alert-heading">Organisation</h6>
                            <p class="mb-0">Assignez l'enseignant après la création pour une meilleure gestion des conflits d'horaires</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle"></i> Créer la Matière
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </button>
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-danger">
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
        const teacherSelect = $('#teacher_id');
        const classesContainer = $('#classesContainer');
        
        if (departmentId) {
            // Load teachers
            loadTeachersByDepartment(departmentId);
            
            // Load classes
            loadClassesByDepartment(departmentId);
        } else {
            teacherSelect.html('<option value="">Sélectionner d\'abord un département</option>');
            classesContainer.html('<div class="text-muted text-center py-3">Sélectionnez d\'abord un département pour voir les classes disponibles</div>');
        }
        
        updatePreview();
    });

    // Code availability check
    let codeCheckTimeout;
    $('#code').on('input', function() {
        const code = $(this).val().toUpperCase();
        $(this).val(code);
        
        clearTimeout(codeCheckTimeout);
        if (code.length >= 3) {
            codeCheckTimeout = setTimeout(() => checkCodeAvailability(code), 500);
        } else {
            $('#codeAvailability').html('');
        }
        
        updatePreview();
    });

    // Form validation
    $('#subjectForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...');
        $('#submitBtn').prop('disabled', true);
    });

    // Real-time preview update
    $('#name, #description, #credits, #teacher_id, #is_active').on('input change', updatePreview);

    // Description character counter
    $('#description').on('input', function() {
        const count = $(this).val().length;
        $('#descriptionCount').text(count);
        
        if (count > 1000) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Load old values if validation failed
    @if(old('department_id'))
        $('#department_id').val('{{ old('department_id') }}').trigger('change');
        setTimeout(function() {
            $('#teacher_id').val('{{ old('teacher_id') }}');
            // Restore selected classes
            @if(old('classes'))
                @foreach(old('classes') as $classId)
                    $('input[name="classes[]"][value="{{ $classId }}"]').prop('checked', true);
                @endforeach
            @endif
        }, 1000);
    @endif
});

function loadTeachersByDepartment(departmentId) {
    const teacherSelect = $('#teacher_id');
    const teacherInfo = $('#teacherInfo');
    
    $.ajax({
        url: `/admin/api/teachers/by-department/${departmentId}`,
        method: 'GET',
        beforeSend: function() {
            teacherSelect.html('<option value="">Chargement...</option>');
            teacherInfo.text('');
        },
        success: function(teachers) {
            teacherSelect.html('<option value="">Aucun enseignant assigné</option>');
            
            if (teachers.length > 0) {
                teachers.forEach(function(teacher) {
                    teacherSelect.append(`
                        <option value="${teacher.id}" data-specialization="${teacher.specialization}">
                            ${teacher.name} - ${teacher.employee_number}
                        </option>
                    `);
                });
                teacherInfo.html('<span class="text-success"><i class="bi bi-check-circle"></i> ' + teachers.length + ' enseignant(s) disponible(s)</span>');
            } else {
                teacherInfo.html('<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Aucun enseignant dans ce département</span>');
            }
        },
        error: function() {
            teacherSelect.html('<option value="">Erreur de chargement</option>');
            teacherInfo.html('<span class="text-danger">Erreur lors du chargement des enseignants</span>');
        }
    });
}

function loadClassesByDepartment(departmentId) {
    const classesContainer = $('#classesContainer');
    
    $.ajax({
        url: `/admin/api/classes/by-department/${departmentId}`,
        method: 'GET',
        beforeSend: function() {
            classesContainer.html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
        },
        success: function(classes) {
            if (classes.length > 0) {
                let html = '<div class="row">';
                
                classes.forEach(function(classItem) {
                    const capacity = classItem.current_students + '/' + classItem.capacity;
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="classes[]" value="${classItem.id}" 
                                       id="class_${classItem.id}">
                                <label class="form-check-label" for="class_${classItem.id}">
                                    <strong>${classItem.name}</strong>
                                    <br><small class="text-muted">${classItem.level_name} - ${capacity} étudiants</small>
                                </label>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                classesContainer.html(html);
            } else {
                classesContainer.html('<div class="alert alert-warning">Aucune classe disponible dans ce département</div>');
            }
        },
        error: function() {
            classesContainer.html('<div class="alert alert-danger">Erreur lors du chargement des classes</div>');
        }
    });
}

function checkCodeAvailability(code) {
    $.ajax({
        url: '{{ route("admin.subjects.check-code") }}',
        method: 'GET',
        data: { code: code },
        success: function(response) {
            const availability = $('#codeAvailability');
            if (response.available) {
                availability.html('<span class="text-success"><i class="bi bi-check-circle"></i> Code disponible</span>');
                $('#code').removeClass('is-invalid').addClass('is-valid');
            } else {
                availability.html('<span class="text-danger"><i class="bi bi-x-circle"></i> Code déjà utilisé</span>');
                $('#code').removeClass('is-valid').addClass('is-invalid');
            }
        },
        error: function() {
            $('#codeAvailability').html('<span class="text-warning">Erreur lors de la vérification</span>');
        }
    });
}

function updatePreview() {
    const name = $('#name').val() || 'Nom de la matière';
    const code = $('#code').val() || 'CODE';
    const description = $('#description').val() || 'Aucune description';
    const credits = $('#credits').val() || '0';
    const departmentName = $('#department_id option:selected').text() || 'Aucun département';
    const teacherName = $('#teacher_id option:selected').text() || 'Aucun enseignant';
    const isActive = $('#is_active').is(':checked');
    
    const preview = `
        <div class="text-center mb-3">
            <h5 class="mb-1">${name}</h5>
            <span class="badge bg-secondary">${code}</span>
        </div>
        
        <ul class="list-unstyled">
            <li class="mb-2">
                <strong>Département :</strong><br>
                <small class="text-muted">${departmentName}</small>
            </li>
            <li class="mb-2">
                <strong>Enseignant :</strong><br>
                <small class="text-muted">${teacherName}</small>
            </li>
            <li class="mb-2">
                <strong>Crédits :</strong> 
                <span class="badge bg-info">${credits}</span>
            </li>
            <li class="mb-2">
                <strong>Statut :</strong> 
                <span class="badge bg-${isActive ? 'success' : 'danger'}">${isActive ? 'Active' : 'Inactive'}</span>
            </li>
            <li>
                <strong>Description :</strong><br>
                <small class="text-muted">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</small>
            </li>
        </ul>
    `;
    
    $('#subjectPreview').html(preview);
    $('#generatedCode').text(code || 'Automatique');
}

function validateForm() {
    let isValid = true;
    const requiredFields = ['name', 'code', 'department_id', 'credits'];
    
    requiredFields.forEach(function(field) {
        const element = document.getElementById(field);
        if (!element.value.trim()) {
            element.classList.add('is-invalid');
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    });
    
    // Code validation
    const code = $('#code').val();
    if (code && !/^[A-Z0-9]{3,20}$/.test(code)) {
        $('#code').addClass('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        alert('Veuillez corriger les erreurs dans le formulaire avant de continuer.');
    }
    
    return isValid;
}

function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ? Toutes les données saisies seront perdues.')) {
        document.getElementById('subjectForm').reset();
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('#teacher_id').html('<option value="">Sélectionner d\'abord un département</option>');
        $('#classesContainer').html('<div class="text-muted text-center py-3">Sélectionnez d\'abord un département pour voir les classes disponibles</div>');
        $('#codeAvailability').html('');
        $('#teacherInfo').text('');
        $('#descriptionCount').text('0');
        updatePreview();
    }
}

// Auto-generate code based on department and name
$('#name, #department_id').on('change input', function() {
    if (!$('#code').val()) {
        const deptCode = $('#department_id option:selected').text().match(/\(([^)]+)\)/);
        const name = $('#name').val();
        
        if (deptCode && name) {
            const suggestion = deptCode[1] + name.replace(/[^A-Z0-9]/gi, '').substring(0, 4).toUpperCase();
            $('#code').attr('placeholder', 'Suggestion: ' + suggestion);
        }
    }
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

.alert {
    border-left: 3px solid;
}

.alert-info {
    border-left-color: #2563eb;
}

.alert-warning {
    border-left-color: #f59e0b;
}

.alert-success {
    border-left-color: #10b981;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

#subjectPreview {
    min-height: 250px;
}

.form-check-label {
    cursor: pointer;
}

.is-valid {
    border-color: #198754;
}

.is-invalid {
    border-color: #dc3545;
}
</style>
@endpush