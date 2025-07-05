@extends('layouts.app')

@section('title', 'Gestion des Matières - UNCHK')

@section('page-header')
@section('page-title', 'Gestion des Matières')
@section('page-subtitle', 'Liste et gestion de toutes les matières de l\'établissement')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.subjects.export') }}?format=excel">
                <i class="bi bi-file-excel me-2"></i>Excel
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.subjects.export') }}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>PDF
            </a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Importer
    </button>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
        <i class="bi bi-book-half"></i> Nouvelle Matière
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
                            <h6 class="card-title text-white-50 mb-2">Total Matières</h6>
                            <h3 class="mb-0">{{ $stats['total_subjects'] }}</h3>
                        </div>
                        <i class="bi bi-book fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Matières Actives</h6>
                            <h3 class="mb-0">{{ $stats['active_subjects'] }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Avec Enseignant</h6>
                            <h3 class="mb-0">{{ $stats['assigned_subjects'] }}</h3>
                        </div>
                        <i class="bi bi-person-check fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-2">Total Crédits</h6>
                            <h3 class="mb-0">{{ $stats['total_credits'] }}</h3>
                        </div>
                        <i class="bi bi-award fs-1 text-white-50"></i>
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
            <form method="GET" action="{{ route('admin.subjects.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nom, code, description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="department_id" class="form-label">Département</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" 
                                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="teacher_id" class="form-label">Enseignant</label>
                        <select class="form-select" id="teacher_id" name="teacher_id">
                            <option value="">Tous les enseignants</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" 
                                        {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="credits" class="form-label">Crédits</label>
                        <select class="form-select" id="credits" name="credits">
                            <option value="">Tous les crédits</option>
                            @foreach($availableCredits as $credit)
                                <option value="{{ $credit }}" 
                                        {{ request('credits') == $credit ? 'selected' : '' }}>
                                    {{ $credit }} crédit{{ $credit > 1 ? 's' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Avec enseignant</option>
                            <option value="unassigned" {{ request('status') === 'unassigned' ? 'selected' : '' }}>Sans enseignant</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Effacer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des Matières -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Liste des Matières 
                    <span class="badge bg-primary ms-2">{{ $subjects->total() }} résultat(s)</span>
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
            @if($subjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="subjectsTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>Code</th>
                                <th>Nom de la Matière</th>
                                <th>Département</th>
                                <th>Enseignant</th>
                                <th>Crédits</th>
                                <th>Classes</th>
                                <th>Étudiants</th>
                                <th>Statut</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input subject-checkbox" 
                                               value="{{ $subject->id }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $subject->code }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $subject->name }}</div>
                                        @if($subject->description)
                                            <small class="text-muted">{{ Str::limit($subject->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subject->department)
                                            <span class="badge bg-light text-dark">{{ $subject->department->name }}</span>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subject->teacher)
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->user->name) }}&background=2563eb&color=fff&size=32" 
                                                     alt="{{ $subject->teacher->user->name }}" 
                                                     class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-semibold">{{ $subject->teacher->user->name }}</div>
                                                    <small class="text-muted">{{ $subject->teacher->specialization }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Non assigné
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $subject->credits }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $subject->classes->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $subject->total_students_count }}</span>
                                    </td>
                                    <td>
                                        @if($subject->is_active)
                                            @if($subject->teacher)
                                                <span class="badge bg-success">Assigné</span>
                                            @else
                                                <span class="badge bg-warning">Actif</span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.subjects.show', $subject) }}" 
                                               class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                               class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteSubject({{ $subject->id }})" 
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
                        Affichage de {{ $subjects->firstItem() }} à {{ $subjects->lastItem() }} 
                        sur {{ $subjects->total() }} résultats
                    </div>
                    {{ $subjects->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucune matière trouvée</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'department_id', 'teacher_id', 'credits', 'status']))
                            Aucune matière ne correspond aux critères de recherche.
                        @else
                            Commencez par ajouter des matières à l'établissement.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'department_id', 'teacher_id', 'credits', 'status']))
                        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                            <i class="bi bi-book-half"></i> Ajouter la première matière
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
                <h5 class="modal-title">Importer des Matières</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subjects.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Fichier Excel (.xlsx, .xls)</label>
                        <input type="file" class="form-control" id="importFile" name="file" 
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Le fichier doit contenir les colonnes : Nom, Code, Description, Crédits, Département
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing">
                            <label class="form-check-label" for="updateExisting">
                                Mettre à jour les matières existantes
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Remarque :</strong> Téléchargez d'abord le 
                        <a href="{{ route('admin.subjects.export') }}?format=template">modèle Excel</a> 
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
                <p>Êtes-vous sûr de vouloir supprimer cette matière ?</p>
                <p class="text-muted">Cette action désactivera la matière plutôt que de la supprimer définitivement.</p>
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
    
    // Auto-submit form on filter change
    $('#department_id, #teacher_id, #credits, #status').change(function() {
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
        $('.subject-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });
    
    $('.subject-checkbox').change(function() {
        toggleBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.subject-checkbox').length;
        const checkedCheckboxes = $('.subject-checkbox:checked').length;
        
        $('#selectAllCheckbox').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAllCheckbox').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
});

function toggleBulkActions() {
    const checkedCount = $('.subject-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkDeleteBtn').show();
    } else {
        $('#bulkDeleteBtn').hide();
    }
}

function selectAll() {
    $('.subject-checkbox').prop('checked', true);
    $('#selectAllCheckbox').prop('checked', true);
    toggleBulkActions();
}

function deleteSubject(subjectId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '{{ route("admin.subjects.destroy", "") }}/' + subjectId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function bulkDelete() {
    const selectedIds = $('.subject-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins une matière.');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} matière(s) ?`)) {
        // Create a form for bulk delete
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.subjects.bulk-delete") }}'
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
                name: 'subjects[]',
                value: id
            }));
        });
        
        $('body').append(form);
        form.submit();
    }
}

// Dynamic teacher loading based on department
$('#department_id').change(function() {
    const departmentId = $(this).val();
    const teacherSelect = $('#teacher_id');
    
    if (departmentId) {
        $.ajax({
            url: '{{ route("admin.api.teachers.by-department", "") }}/' + departmentId,
            method: 'GET',
            beforeSend: function() {
                teacherSelect.html('<option value="">Chargement...</option>');
            },
            success: function(teachers) {
                teacherSelect.html('<option value="">Tous les enseignants</option>');
                teachers.forEach(function(teacher) {
                    teacherSelect.append(`<option value="${teacher.id}">${teacher.name}</option>`);
                });
            },
            error: function() {
                teacherSelect.html('<option value="">Erreur de chargement</option>');
            }
        });
    } else {
        // Reset to original teachers
        location.reload();
    }
});
</script>
@endpush

@push('styles')
<style>
.stats-card.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.stats-card.success { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.stats-card.warning { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
.stats-card.info { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>
@endpush

<!-- Bouton Flottant -->
<x-floating-action-button :actions="[
    [
        'title' => 'Nouvelle matière',
        'url' => route('admin.subjects.create'),
        'icon' => 'bi bi-journal-plus',
        'color' => 'primary'
    ],
    [
        'title' => 'Importer depuis Excel',
        'url' => '#',
        'icon' => 'bi bi-file-earmark-excel',
        'color' => 'success'
    ],
    [
        'title' => 'Catégoriser',
        'url' => '#',
        'icon' => 'bi bi-tags',
        'color' => 'warning'
    ],
    [
        'title' => 'Rapport des matières',
        'url' => '#',
        'icon' => 'bi bi-graph-up',
        'color' => 'info'
    ]
]" />