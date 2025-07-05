@extends('layouts.app')

@section('title', 'Modifier Matière - ' . $subject->name . ' - UNCHK')

@section('page-header')
@section('page-title', 'Modifier Matière')
@section('page-subtitle', $subject->name . ' (' . $subject->code . ')')
@section('page-actions')
    <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-outline-info me-2">
        <i class="bi bi-eye"></i> Voir les Détails
    </a>
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
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
                <strong>Modification de matière :</strong> Les modifications apportées ici affecteront les emplois du temps, les notes et les assignations existantes.
                @if($subject->grades->count() > 0 || $subject->schedules->count() > 0)
                    <br><strong>Attention :</strong> Cette matière contient des données existantes ({{ $subject->grades->count() }} note(s), {{ $subject->schedules->count() }} cours programmé(s)).
                @endif
            </div>

            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" id="subjectEditForm">
                @csrf
                @method('PUT')
                
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
                                <div class="row">
                                    <!-- Nom -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom de la matière <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $subject->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Code -->
                                    <div class="col-md-6 mb-3">
                                        <label for="code" class="form-label">Code de la matière <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                            <input type="text" 
                                                   class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" 
                                                   name="code" 
                                                   value="{{ old('code', $subject->code) }}" 
                                                   required
                                                   style="text-transform: uppercase;"
                                                   pattern="[A-Z0-9]{3,20}" 
                                                   maxlength="20">
                                        </div>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="codeAvailability" class="mt-1"></div>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="4" 
                                                  placeholder="Description détaillée de la matière...">{{ old('description', $subject->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <span id="descriptionCount">{{ strlen($subject->description) }}</span>/1000 caractères
                                        </div>
                                    </div>

                                    <!-- Crédits -->
                                    <div class="col-md-4 mb-3">
                                        <label for="credits" class="form-label">Nombre de crédits <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-award"></i></span>
                                            <select class="form-select @error('credits') is-invalid @enderror" 
                                                    id="credits" 
                                                    name="credits" 
                                                    required>
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" 
                                                            {{ old('credits', $subject->credits) == $i ? 'selected' : '' }}>
                                                        {{ $i }} crédit{{ $i > 1 ? 's' : '' }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        @error('credits')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Département -->
                                    <div class="col-md-4 mb-3">
                                        <label for="department_id" class="form-label">Département <span class="text-danger">*</span></label>
                                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                                id="department_id" 
                                                name="department_id" 
                                                required>
                                            <option value="">Sélectionner un département</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" 
                                                        {{ old('department_id', $subject->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }} ({{ $department->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Statut -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Statut de la matière</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Matière active
                                            </label>
                                        </div>
                                        <div class="form-text">Les matières inactives ne sont pas visibles dans les nouveaux emplois du temps</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enseignant Responsable -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person-badge me-2"></i>Enseignant Responsable
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="teacher_id" class="form-label">Enseignant assigné</label>
                                        <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                                id="teacher_id" 
                                                name="teacher_id">
                                            <option value="">Aucun enseignant assigné</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" 
                                                        data-department="{{ $teacher->department_id }}"
                                                        {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->user->name }} - {{ $teacher->specialization }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('teacher_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text" id="teacherInfo"></div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Enseignant actuel</label>
                                        @if($subject->teacher)
                                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->user->name) }}&background=2563eb&color=fff&size=40" 
                                                     alt="{{ $subject->teacher->user->name }}" 
                                                     class="rounded-circle me-2" width="40" height="40">
                                                <div>
                                                    <div class="fw-semibold">{{ $subject->teacher->user->name }}</div>
                                                    <small class="text-muted">{{ $subject->teacher->specialization }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted p-2 bg-light rounded">
                                                <i class="bi bi-person-x"></i> Aucun enseignant assigné
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classes Assignées -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-collection me-2"></i>Classes Assignées
                                    </h5>
                                    <span class="badge bg-primary">{{ $subject->classes->count() }} classe(s)</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Classes actuellement assignées à cette matière. 
                                    @if($subject->grades->count() > 0)
                                        <span class="text-warning">⚠️ Attention : Modifier les classes peut affecter les notes existantes.</span>
                                    @endif
                                </p>
                                
                                <div id="classesContainer">
                                    <!-- Les classes seront chargées par JavaScript -->
                                </div>
                                
                                @error('classes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Historique et Statistiques -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-graph-up me-2"></i>Statistiques et Historique
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="text-center p-3 bg-primary text-white rounded">
                                            <h4>{{ $subject->grades->count() }}</h4>
                                            <small>Notes enregistrées</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="text-center p-3 bg-success text-white rounded">
                                            <h4>{{ $subject->schedules->count() }}</h4>
                                            <small>Cours programmés</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="text-center p-3 bg-info text-white rounded">
                                            <h4>{{ $subject->classes->count() }}</h4>
                                            <small>Classes assignées</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="text-center p-3 bg-warning text-white rounded">
                                            <h4>{{ $subject->total_students_count }}</h4>
                                            <small>Total étudiants</small>
                                        </div>
                                    </div>
                                </div>

                                @if($subject->grades->count() > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Moyenne générale :</strong> 
                                            {{ number_format($subject->grades->avg('grade'), 2) }}/20
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Dernière note :</strong> 
                                            {{ $subject->grades->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
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
                                    <!-- Sera mis à jour par JavaScript -->
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
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <strong>ID :</strong> {{ $subject->id }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Créé le :</strong> {{ $subject->created_at->format('d/m/Y à H:i') }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Modifié le :</strong> {{ $subject->updated_at->format('d/m/Y à H:i') }}
                                    </li>
                                    <li class="mb-2">
                                        <strong>Code complet :</strong> 
                                        <span class="badge bg-secondary">{{ $subject->full_code }}</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Statut système :</strong> 
                                        <span class="badge bg-{{ $subject->is_active ? 'success' : 'danger' }}">
                                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Avertissements -->
                        @if($subject->grades->count() > 0 || $subject->schedules->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title mb-0 text-dark">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Avertissements
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        @if($subject->grades->count() > 0)
                                            <li class="mb-2">
                                                <i class="bi bi-clipboard-data text-warning"></i>
                                                {{ $subject->grades->count() }} note(s) enregistrée(s)
                                            </li>
                                        @endif
                                        @if($subject->schedules->count() > 0)
                                            <li class="mb-2">
                                                <i class="bi bi-calendar text-warning"></i>
                                                {{ $subject->schedules->count() }} cours programmé(s)
                                            </li>
                                        @endif
                                        <li class="text-muted">
                                            <small>Ces données seront conservées lors des modifications</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" id="saveBtn">
                                        <i class="bi bi-check-circle me-2"></i>Enregistrer les Modifications
                                    </button>
                                    
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye me-2"></i>Voir les Détails
                                    </a>
                                    
                                    @if($subject->teacher)
                                        <button type="button" class="btn btn-outline-warning" onclick="removeTeacher()">
                                            <i class="bi bi-person-x me-2"></i>Retirer l'Enseignant
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
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
$(document).ready(function() {
    // Initialize
    loadClassesByDepartment($('#department_id').val());
    updatePreview();
    updateDescriptionCount();

    // Department change handler
    $('#department_id').change(function() {
        const departmentId = $(this).val();
        
        // Filter teachers by department
        filterTeachersByDepartment(departmentId);
        
        // Load classes
        loadClassesByDepartment(departmentId);
        
        updatePreview();
    });

    // Code availability check
    let codeCheckTimeout;
    $('#code').on('input', function() {
        const code = $(this).val().toUpperCase();
        $(this).val(code);
        
        clearTimeout(codeCheckTimeout);
        if (code.length >= 3 && code !== '{{ $subject->code }}') {
            codeCheckTimeout = setTimeout(() => checkCodeAvailability(code), 500);
        } else {
            $('#codeAvailability').html('');
        }
        
        updatePreview();
    });

    // Real-time updates
    $('#name, #description, #credits, #teacher_id, #is_active').on('input change', updatePreview);
    $('#description').on('input', updateDescriptionCount);

    // Form validation
    $('#subjectEditForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        $('#saveBtn').html('<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...');
        $('#saveBtn').prop('disabled', true);
    });
});

function filterTeachersByDepartment(departmentId) {
    const teacherSelect = $('#teacher_id');
    const currentTeacherId = '{{ $subject->teacher_id }}';
    
    // Show/hide teachers based on department
    teacherSelect.find('option').each(function() {
        const option = $(this);
        const teacherDepartment = option.data('department');
        
        if (option.val() === '' || option.val() === currentTeacherId || teacherDepartment == departmentId) {
            option.show();
        } else {
            option.hide();
        }
    });
    
    // Update teacher info
    const selectedTeacher = teacherSelect.find('option:selected');
    const teacherInfo = $('#teacherInfo');
    
    if (selectedTeacher.val() && selectedTeacher.data('department') != departmentId) {
        teacherInfo.html('<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Cet enseignant n\'appartient pas au département sélectionné</span>');
    } else {
        teacherInfo.html('');
    }
}

function loadClassesByDepartment(departmentId) {
    const classesContainer = $('#classesContainer');
    
    if (!departmentId) {
        classesContainer.html('<div class="text-muted text-center py-3">Sélectionnez un département pour voir les classes</div>');
        return;
    }
    
    $.ajax({
        url: `/admin/api/classes/by-department/${departmentId}`,
        method: 'GET',
        beforeSend: function() {
            classesContainer.html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
        },
        success: function(classes) {
            if (classes.length > 0) {
                let html = '<div class="row">';
                
                const assignedClasses = @json($subject->classes->pluck('id'));
                
                classes.forEach(function(classItem) {
                    const isAssigned = assignedClasses.includes(classItem.id);
                    const capacity = classItem.current_students + '/' + classItem.capacity;
                    
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="classes[]" value="${classItem.id}" 
                                       id="class_${classItem.id}" ${isAssigned ? 'checked' : ''}>
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

// Version améliorée avec debounce et meilleure gestion d'erreurs

let codeCheckTimeout;

function checkCodeAvailability(code) {
    // Debounce pour éviter trop d'appels AJAX
    clearTimeout(codeCheckTimeout);
    
    const availability = $('#codeAvailability');
    const codeInput = $('#code');
    
    // Si le code est vide, effacer la validation
    if (!code || code.trim() === '') {
        availability.html('');
        codeInput.removeClass('is-invalid is-valid');
        return;
    }
    
    // Si le code est trop court, ne pas vérifier
    if (code.trim().length < 2) {
        availability.html('<span class="text-muted"><i class="bi bi-info-circle"></i> Le code doit contenir au moins 2 caractères</span>');
        codeInput.removeClass('is-invalid is-valid');
        return;
    }
    
    // Attendre 500ms avant de faire la vérification
    codeCheckTimeout = setTimeout(function() {
        availability.html('<span class="text-info"><i class="bi bi-hourglass-split"></i> Vérification...</span>');
        
        $.ajax({
            url: '{{ route("admin.subjects.check-code") }}',
            method: 'GET',
            data: {
                code: code.trim(),
                subject_id: {{ $subject->id ?? 'null' }}
            },
            success: function(response) {
                if (response.available) {
                    availability.html('<span class="text-success"><i class="bi bi-check-circle"></i> ' + response.message + '</span>');
                    codeInput.removeClass('is-invalid').addClass('is-valid');
                } else {
                    availability.html('<span class="text-danger"><i class="bi bi-x-circle"></i> ' + response.message + '</span>');
                    codeInput.removeClass('is-valid').addClass('is-invalid');
                }
            },
            error: function(xhr) {
                console.error('Erreur lors de la vérification du code:', xhr);
                availability.html('<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Erreur de vérification</span>');
                codeInput.removeClass('is-invalid is-valid');
            }
        });
    }, 500);
}

// Utilisation avec l'événement input pour vérification en temps réel
$(document).ready(function() {
    $('#code').on('input', function() {
        checkCodeAvailability($(this).val());
    });
});

function updatePreview() {
    const name = $('#name').val() || '{{ $subject->name }}';
    const code = $('#code').val() || '{{ $subject->code }}';
    const description = $('#description').val() || 'Aucune description';
    const credits = $('#credits').val() || '{{ $subject->credits }}';
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
}

function updateDescriptionCount() {
    const count = $('#description').val().length;
    $('#descriptionCount').text(count);
    
    if (count > 1000) {
        $('#description').addClass('is-invalid');
    } else {
        $('#description').removeClass('is-invalid');
    }
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

function removeTeacher() {
    if (confirm('Êtes-vous sûr de vouloir retirer l\'enseignant de cette matière ?')) {
        $('#teacher_id').val('');
        updatePreview();
    }
}

// Auto-save draft functionality (optional)
let autoSaveTimeout;
$('input, textarea, select').on('input change', function() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        console.log('Auto-save...');
        // Implement auto-save logic here if needed
    }, 3000);
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

.alert-info {
    border-left: 4px solid #2563eb;
}

.bg-warning .card-title {
    color: #000 !important;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.is-valid {
    border-color: #198754;
}

.is-invalid {
    border-color: #dc3545;
}

.form-check-label {
    cursor: pointer;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush