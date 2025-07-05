@extends('layouts.app')

@section('title', 'Gestion des Étudiants - UNCHK')

@section('page-header')
@section('page-title', 'Gestion des Étudiants')
@section('page-subtitle', 'Liste et gestion de tous les étudiants de l\'établissement')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.students.export') }}?format=excel">
                <i class="bi bi-file-excel me-2"></i>Excel
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.students.export') }}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>PDF
            </a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Importer
    </button>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Nouvel Étudiant
    </a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistiques Rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Étudiants</h6>
                            <h3 class="mb-0">{{ $students->total() }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Étudiants Actifs</h6>
                            <h3 class="mb-0">{{ $students->where('is_active', true)->count() }}</h3>
                        </div>
                        <i class="bi bi-person-check fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Nouveaux ce Mois</h6>
                            <h3 class="mb-0">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <i class="bi bi-person-plus fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Classes Utilisées</h6>
                            <h3 class="mb-0">{{ $classes->count() }}</h3>
                        </div>
                        <i class="bi bi-collection fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtres de Recherche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nom, email, numéro...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="class_id" class="form-label">Classe</label>
                        <select class="form-select" id="class_id" name="class_id">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" 
                                        {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="level_id" class="form-label">Niveau</label>
                        <select class="form-select" id="level_id" name="level_id">
                            <option value="">Tous les niveaux</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" 
                                        {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Effacer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des Étudiants -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Liste des Étudiants 
                    <span class="badge bg-primary ms-2">{{ $students->total() }} résultat(s)</span>
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                        <i class="bi bi-check-all"></i> Tout sélectionner
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;">
                        <i class="bi bi-trash"></i> Supprimer sélectionnés
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="studentsTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th width="80">Photo</th>
                                <th>Nom Complet</th>
                                <th>Numéro</th>
                                <th>Email</th>
                                <th>Classe</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Inscription</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input student-checkbox" 
                                               value="{{ $student->id }}">
                                    </td>
                                    <td>
                                        @if($student->photo)
                                            <img src="{{ Storage::url($student->photo) }}" 
                                                 alt="{{ $student->user->name }}" 
                                                 class="rounded-circle" width="50" height="50">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=2563eb&color=fff" 
                                                 alt="{{ $student->user->name }}" 
                                                 class="rounded-circle" width="50" height="50">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $student->user->name }}</div>
                                        <small class="text-muted">{{ $student->user->gender === 'male' ? 'Masculin' : 'Féminin' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $student->student_number }}</span>
                                    </td>
                                    <td>{{ $student->user->email }}</td>
                                    <td>
                                        @if($student->class)
                                            <div>{{ $student->class->name }}</div>
                                            <small class="text-muted">{{ $student->class->level->name }}</small>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>{{ $student->user->phone ?? '-' }}</td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->enrollment_date->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.students.show', $student) }}" 
                                               class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" 
                                               class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteStudent({{ $student->id }})" 
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Affichage de {{ $students->firstItem() }} à {{ $students->lastItem() }} 
                        sur {{ $students->total() }} résultats
                    </div>
                    {{ $students->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucun étudiant trouvé</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'class_id', 'level_id', 'status']))
                            Aucun étudiant ne correspond aux critères de recherche.
                        @else
                            Commencez par ajouter des étudiants à l'établissement.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'class_id', 'level_id', 'status']))
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Ajouter le premier étudiant
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importer des Étudiants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Fichier Excel (.xlsx, .xls)</label>
                        <input type="file" class="form-control" id="importFile" name="file" 
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Le fichier doit contenir les colonnes : Prénom, Nom, Email, Téléphone, Date de naissance, Sexe, Classe
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing">
                            <label class="form-check-label" for="updateExisting">
                                Mettre à jour les étudiants existants
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Remarque :</strong> Téléchargez d'abord le 
                        <a href="{{ route('admin.students.export') }}?format=template">modèle Excel</a> 
                        pour voir le format requis.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmation de Suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet étudiant ?</p>
                <p class="text-muted">Cette action archivera l'étudiant plutôt que de le supprimer définitivement.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Enhanced DataTable
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[2, 'asc']], // Sort by name
        columnDefs: [
            { orderable: false, targets: [0, 1, 9] }, // Disable sorting for checkbox, photo, and actions
        ],
        dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });
    
    // Auto-submit form on filter change
    $('#class_id, #level_id, #status').change(function() {
        $('#filterForm').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
    
    // Select all functionality
    $('#selectAllCheckbox').change(function() {
        $('.student-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });
    
    $('.student-checkbox').change(function() {
        toggleBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.student-checkbox').length;
        const checkedCheckboxes = $('.student-checkbox:checked').length;
        
        $('#selectAllCheckbox').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAllCheckbox').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
});

function toggleBulkActions() {
    const checkedCount = $('.student-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkDeleteBtn').show();
    } else {
        $('#bulkDeleteBtn').hide();
    }
}

function selectAll() {
    $('.student-checkbox').prop('checked', true);
    $('#selectAllCheckbox').prop('checked', true);
    toggleBulkActions();
}

function deleteStudent(studentId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '{{ route("admin.students.destroy", "") }}/' + studentId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function bulkDelete() {
    const selectedIds = $('.student-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins un étudiant.');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} étudiant(s) ?`)) {
        // Create a form for bulk delete
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.students.bulk-delete") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_method',
            value: 'DELETE'
        }));
        
        selectedIds.forEach(function(id) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'students[]',
                value: id
            }));
        });
        
        $('body').append(form);
        form.submit();
    }
}

// Dynamic class loading based on level
$('#level_id').change(function() {
    const levelId = $(this).val();
    const classSelect = $('#class_id');
    
    if (levelId) {
        $.ajax({
            url: '{{ route("admin.api.classes.by-level", "") }}/' + levelId,
            method: 'GET',
            beforeSend: function() {
                classSelect.html('<option value="">Chargement...</option>');
            },
            success: function(classes) {
                classSelect.html('<option value="">Toutes les classes</option>');
                classes.forEach(function(classItem) {
                    classSelect.append(`<option value="${classItem.id}">${classItem.name} (${classItem.current_students}/${classItem.capacity})</option>`);
                });
            },
            error: function() {
                classSelect.html('<option value="">Erreur de chargement</option>');
            }
        });
    } else {
        // Reset to original classes
        location.reload();
    }
});

// Export functionality
function exportStudents(format) {
    const currentUrl = new URL(window.location);
    currentUrl.pathname = '{{ route("admin.students.export") }}';
    currentUrl.searchParams.set('format', format);
    
    // Add current filters to export
    const formData = new FormData(document.getElementById('filterForm'));
    for (let [key, value] of formData.entries()) {
        if (value) {
            currentUrl.searchParams.set(key, value);
        }
    }
    
    window.open(currentUrl.toString(), '_blank');
}

// Auto-refresh page every 5 minutes to show new data
setInterval(function() {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 300000);
</script>
@endpush