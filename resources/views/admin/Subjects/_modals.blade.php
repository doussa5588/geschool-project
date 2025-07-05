{{-- Modal d'Assignation d'Enseignant --}}
<div class="modal fade" id="assignTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner un Enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignTeacherForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Matière Sélectionnée</h6>
                                    <div id="selectedSubjectInfo">
                                        <!-- Sera rempli par JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Enseignant Actuel</h6>
                                    <div id="currentTeacherInfo">
                                        <!-- Sera rempli par JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="modal_teacher_id" class="form-label">Nouvel Enseignant</label>
                        <select class="form-select" id="modal_teacher_id" name="teacher_id" required>
                            <option value="">Rechercher un enseignant...</option>
                        </select>
                        <div class="form-text">Seuls les enseignants du même département sont affichés</div>
                    </div>

                    <div class="mb-3">
                        <label for="assignment_reason" class="form-label">Motif de l'assignation</label>
                        <textarea class="form-control" id="assignment_reason" name="reason" rows="3" 
                                  placeholder="Optionnel : motif de l'assignation ou du changement"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Information :</strong> L'assignation d'un nouvel enseignant mettra à jour automatiquement les emplois du temps et notifications.
                    </div>

                    <!-- Liste des enseignants disponibles -->
                    <div id="availableTeachers">
                        <h6>Enseignants Disponibles</h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Enseignant</th>
                                        <th>Spécialisation</th>
                                        <th>Charge</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="teachersTableBody">
                                    <!-- Sera rempli par JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" id="removeTeacherBtn" style="display: none;">
                        <i class="bi bi-person-x"></i> Retirer l'Enseignant
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Assigner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de Gestion des Classes --}}
<div class="modal fade" id="manageClassesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gérer les Classes Assignées</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manageClassesForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Classes Actuellement Assignées</h6>
                            <div id="assignedClasses" class="border rounded p-3" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Classes Disponibles</h6>
                            <div class="mb-3">
                                <select class="form-select" id="filterByDepartment">
                                    <option value="">Tous les départements</option>
                                    @foreach($departments ?? [] as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="availableClasses" class="border rounded p-3" style="min-height: 200px; max-height: 350px; overflow-y: auto;">
                                <!-- Sera rempli par JavaScript -->
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Retirer une classe supprimera cette matière de l'emploi du temps de la classe</li>
                            <li>Les notes existantes seront conservées</li>
                            <li>Les étudiants de la classe n'auront plus accès à cette matière</li>
                        </ul>
                    </div>

                    <!-- Actions en lot -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-success w-100" onclick="assignAllByLevel()">
                                <i class="bi bi-plus-circle"></i> Assigner toutes les classes d'un niveau
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeAllClasses()">
                                <i class="bi bi-x-circle"></i> Retirer toutes les classes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal d'Ajout à l'Emploi du Temps --}}
<div class="modal fade" id="addToScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programmer un Cours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addToScheduleForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="schedule_class_id" class="form-label">Classe <span class="text-danger">*</span></label>
                            <select class="form-select" id="schedule_class_id" name="class_id" required>
                                <option value="">Sélectionner une classe</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule_teacher_id" class="form-label">Enseignant</label>
                            <select class="form-select" id="schedule_teacher_id" name="teacher_id">
                                <option value="">Enseignant de la matière</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="schedule_day" class="form-label">Jour <span class="text-danger">*</span></label>
                            <select class="form-select" id="schedule_day" name="day_of_week" required>
                                <option value="">Sélectionner un jour</option>
                                <option value="monday">Lundi</option>
                                <option value="tuesday">Mardi</option>
                                <option value="wednesday">Mercredi</option>
                                <option value="thursday">Jeudi</option>
                                <option value="friday">Vendredi</option>
                                <option value="saturday">Samedi</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="schedule_start_time" class="form-label">Heure de début <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="schedule_start_time" name="start_time" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="schedule_end_time" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="schedule_end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="schedule_room" class="form-label">Salle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="schedule_room" name="room" 
                                   placeholder="Ex: Salle 101" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule_semester" class="form-label">Semestre</label>
                            <select class="form-select" id="schedule_semester" name="semester">
                                <option value="1">Semestre 1</option>
                                <option value="2">Semestre 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="schedule_academic_year" class="form-label">Année Académique</label>
                        <select class="form-select" id="schedule_academic_year" name="academic_year">
                            <option value="2024-2025">2024-2025</option>
                            <option value="2025-2026">2025-2026</option>
                        </select>
                    </div>

                    <!-- Vérification des conflits -->
                    <div id="scheduleConflicts" class="alert alert-warning" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Conflits détectés :</strong>
                        <ul id="conflictsList" class="mb-0 mt-2"></ul>
                    </div>

                    <!-- Récurrence -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Options de Récurrence</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="schedule_recurring" name="recurring">
                                <label class="form-check-label" for="schedule_recurring">
                                    Cours récurrent (toutes les semaines)
                                </label>
                            </div>
                            <div id="recurringOptions" style="display: none;" class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="recurring_start" class="form-label">Date de début</label>
                                        <input type="date" class="form-control" id="recurring_start" name="recurring_start">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="recurring_end" class="form-label">Date de fin</label>
                                        <input type="date" class="form-control" id="recurring_end" name="recurring_end">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-outline-info" onclick="checkConflicts()">
                        <i class="bi bi-search"></i> Vérifier les Conflits
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> Programmer le Cours
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de Confirmation de Suppression --}}
<div class="modal fade" id="deleteSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la Suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                </div>
                <h6 class="text-center mb-3">Êtes-vous sûr de vouloir supprimer cette matière ?</h6>
                
                <div id="deleteWarnings">
                    <!-- Sera rempli par JavaScript avec les avertissements -->
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Information :</strong> La matière sera désactivée plutôt que supprimée définitivement pour préserver l'intégrité des données.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteSubjectForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal d'Import en Masse --}}
<div class="modal fade" id="bulkImportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import en Masse des Matières</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkImportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="import_file" class="form-label">Fichier Excel/CSV</label>
                                <input type="file" class="form-control" id="import_file" name="file" 
                                       accept=".xlsx,.xls,.csv" required>
                                <div class="form-text">Formats acceptés: .xlsx, .xls, .csv (max 10MB)</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_headers" name="has_headers" checked>
                                    <label class="form-check-label" for="has_headers">
                                        Le fichier contient des en-têtes
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing">
                                    <label class="form-check-label" for="update_existing">
                                        Mettre à jour les matières existantes
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="default_department" class="form-label">Département par défaut</label>
                                <select class="form-select" id="default_department" name="default_department_id">
                                    <option value="">Aucun</option>
                                    @foreach($departments ?? [] as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Utilisé si aucun département n'est spécifié dans le fichier</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Format Requis</h6>
                                    <small class="text-muted">
                                        Le fichier doit contenir les colonnes suivantes :
                                    </small>
                                    <ul class="list-unstyled mt-2" style="font-size: 0.85rem;">
                                        <li><strong>nom</strong> - Nom de la matière</li>
                                        <li><strong>code</strong> - Code unique</li>
                                        <li><strong>credits</strong> - Nombre de crédits</li>
                                        <li><strong>departement</strong> - Nom/Code du département</li>
                                        <li class="text-muted">description - Description (optionnel)</li>
                                        <li class="text-muted">enseignant - Email enseignant (optionnel)</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.subjects.download-template') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-download"></i> Télécharger le Modèle
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Aperçu des données -->
                    <div id="importPreview" style="display: none;">
                        <hr>
                        <h6>Aperçu des Données</h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm" id="previewTable">
                                <!-- Sera rempli par JavaScript -->
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-outline-info" onclick="previewImport()">
                        <i class="bi bi-eye"></i> Aperçu
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales pour les modals
let currentSubjectId = null;
let currentSubjectData = null;

// Fonctions pour ouvrir les modals
function openAssignTeacherModal(subjectId, subjectData) {
    currentSubjectId = subjectId;
    currentSubjectData = subjectData;
    
    // Remplir les informations de la matière
    $('#selectedSubjectInfo').html(`
        <h6>${subjectData.name}</h6>
        <p class="mb-1"><strong>Code:</strong> ${subjectData.code}</p>
        <p class="mb-0"><strong>Crédits:</strong> ${subjectData.credits}</p>
    `);
    
    // Remplir les informations de l'enseignant actuel
    if (subjectData.teacher) {
        $('#currentTeacherInfo').html(`
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(subjectData.teacher.name)}&size=40" 
                     class="rounded-circle me-2" width="40" height="40">
                <div>
                    <div class="fw-semibold">${subjectData.teacher.name}</div>
                    <small class="text-muted">${subjectData.teacher.specialization}</small>
                </div>
            </div>
        `);
        $('#removeTeacherBtn').show();
    } else {
        $('#currentTeacherInfo').html('<p class="text-muted mb-0">Aucun enseignant assigné</p>');
        $('#removeTeacherBtn').hide();
    }
    
    // Charger les enseignants disponibles
    loadAvailableTeachers(subjectData.department_id);
    
    $('#assignTeacherModal').modal('show');
}

function openManageClassesModal(subjectId, subjectData) {
    currentSubjectId = subjectId;
    currentSubjectData = subjectData;
    
    // Charger les classes assignées et disponibles
    loadAssignedClasses(subjectId);
    loadAvailableClassesForSubject(subjectData.department_id);
    
    $('#manageClassesModal').modal('show');
}

function openAddToScheduleModal(subjectId, subjectData) {
    currentSubjectId = subjectId;
    currentSubjectData = subjectData;
    
    // Remplir la liste des classes assignées
    $('#schedule_class_id').html('<option value="">Sélectionner une classe</option>');
    if (subjectData.classes) {
        subjectData.classes.forEach(function(classItem) {
            $('#schedule_class_id').append(`
                <option value="${classItem.id}">${classItem.name} (${classItem.level_name})</option>
            `);
        });
    }
    
    // Remplir l'enseignant par défaut
    if (subjectData.teacher) {
        $('#schedule_teacher_id').html(`
            <option value="${subjectData.teacher.id}" selected>${subjectData.teacher.name}</option>
        `);
    }
    
    $('#addToScheduleModal').modal('show');
}

function openDeleteSubjectModal(subjectId, subjectData) {
    currentSubjectId = subjectId;
    
    // Construire les avertissements
    let warnings = [];
    if (subjectData.grades_count > 0) {
        warnings.push(`${subjectData.grades_count} note(s) enregistrée(s)`);
    }
    if (subjectData.schedules_count > 0) {
        warnings.push(`${subjectData.schedules_count} cours programmé(s)`);
    }
    if (subjectData.classes_count > 0) {
        warnings.push(`${subjectData.classes_count} classe(s) assignée(s)`);
    }
    
    if (warnings.length > 0) {
        $('#deleteWarnings').html(`
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Cette matière contient :</strong>
                <ul class="mb-0 mt-2">
                    ${warnings.map(w => `<li>${w}</li>`).join('')}
                </ul>
            </div>
        `);
    } else {
        $('#deleteWarnings').html('');
    }
    
    // Définir l'action du formulaire
    $('#deleteSubjectForm').attr('action', `/admin/subjects/${subjectId}`);
    
    $('#deleteSubjectModal').modal('show');
}

// Fonctions utilitaires
function loadAvailableTeachers(departmentId) {
    $.ajax({
        url: `/admin/api/teachers/by-department/${departmentId}`,
        method: 'GET',
        success: function(teachers) {
            $('#modal_teacher_id').html('<option value="">Sélectionner un enseignant</option>');
            $('#teachersTableBody').empty();
            
            teachers.forEach(function(teacher) {
                $('#modal_teacher_id').append(`
                    <option value="${teacher.id}">${teacher.name} - ${teacher.specialization}</option>
                `);
                
                $('#teachersTableBody').append(`
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(teacher.name)}&size=32" 
                                     class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-semibold">${teacher.name}</div>
                                    <small class="text-muted">${teacher.employee_number}</small>
                                </div>
                            </div>
                        </td>
                        <td>${teacher.specialization}</td>
                        <td>
                            <span class="badge bg-${teacher.workload > 20 ? 'warning' : 'success'}">
                                ${teacher.workload || 0}h
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                    onclick="selectTeacher(${teacher.id}, '${teacher.name}')">
                                Sélectionner
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
    });
}

function selectTeacher(teacherId, teacherName) {
    $('#modal_teacher_id').val(teacherId);
    // Highlight selected row
    $('#teachersTableBody tr').removeClass('table-active');
    $(`#teachersTableBody tr:contains("${teacherName}")`).addClass('table-active');
}

// Gestion des formulaires
$('#assignTeacherForm').on('submit', function(e) {
    e.preventDefault();
    
    const teacherId = $('#modal_teacher_id').val();
    const reason = $('#assignment_reason').val();
    
    $.ajax({
        url: `/admin/subjects/${currentSubjectId}/assign-teacher`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            teacher_id: teacherId,
            reason: reason
        },
        success: function(response) {
            $('#assignTeacherModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Erreur lors de l\'assignation');
        }
    });
});

$('#schedule_recurring').change(function() {
    if ($(this).is(':checked')) {
        $('#recurringOptions').show();
    } else {
        $('#recurringOptions').hide();
    }
});

function checkConflicts() {
    const formData = {
        class_id: $('#schedule_class_id').val(),
        teacher_id: $('#schedule_teacher_id').val(),
        day_of_week: $('#schedule_day').val(),
        start_time: $('#schedule_start_time').val(),
        end_time: $('#schedule_end_time').val(),
        room: $('#schedule_room').val()
    };
    
    if (!formData.class_id || !formData.day_of_week || !formData.start_time || !formData.end_time) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    $.ajax({
        url: '/admin/schedules/check-conflicts',
        method: 'POST',
        data: {
            ...formData,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.conflicts && response.conflicts.length > 0) {
                $('#conflictsList').empty();
                response.conflicts.forEach(function(conflict) {
                    $('#conflictsList').append(`<li>${conflict.message}</li>`);
                });
                $('#scheduleConflicts').show();
            } else {
                $('#scheduleConflicts').hide();
                alert('Aucun conflit détecté. Vous pouvez programmer ce cours.');
            }
        }
    });
}
</script>