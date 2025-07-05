@extends('layouts.app')

@section('title', 'Gestion des Enseignants - UNCHK')

@section('page-header')
@section('page-title', 'Gestion des Enseignants')
@section('page-subtitle', 'Liste et gestion de tous les enseignants de l\'établissement')
@section('page-actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download"></i> Exporter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?format=excel">
                <i class="bi bi-file-excel me-2"></i>Excel
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.teachers.export') }}?format=pdf">
                <i class="bi bi-file-pdf me-2"></i>PDF
            </a></li>
        </ul>
    </div>
    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Importer
    </button>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-badge"></i> Nouvel Enseignant
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
                            <h6 class="card-title text-white-50 mb-2">Total Enseignants</h6>
                            <h3 class="mb-0">{{ $teachers->total() }}</h3>
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
                            <h6 class="card-title text-white-50 mb-2">Enseignants Actifs</h6>
                            <h3 class="mb-0">{{ $teachers->where('is_active', true)->count() }}</h3>
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
                            <h3 class="mb-0">{{ $teachers->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
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
                            <h6 class="card-title text-white-50 mb-2">Départements</h6>
                            <h3 class="mb-0">{{ $departments->count() }}</h3>
                        </div>
                        <i class="bi bi-building fs-1 text-white-50"></i>
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
            <form method="GET" action="{{ route('admin.teachers.index') }}" id="filterForm">
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
                        <label for="subject_id" class="form-label">Matière</label>
                        <select class="form-select" id="subject_id" name="subject_id">
                            <option value="">Toutes les matières</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" 
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Effacer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Répartition par Département -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Répartition par Département</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($departments as $department)
                            @php
                                $teacherCount = $teachers->where('department_id', $department->id)->count();
                                $percentage = $teachers->count() > 0 ? round(($teacherCount / $teachers->count()) * 100, 1) : 0;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ $department->name }}</span>
                                    <span class="text-muted">{{ $teacherCount }} enseignant(s)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $percentage }}% du total</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ancienneté Moyenne</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $averageYears = $teachers->filter(function($teacher) {
                            return $teacher->hire_date;
                        })->map(function($teacher) {
                            return \Carbon\Carbon::parse($teacher->hire_date)->diffInYears(now());
                        })->avg();
                    @endphp
                    <h2 class="text-primary mb-2">
                        {{ $averageYears ? round($averageYears, 1) : 0 }} ans
                    </h2>
                    <p class="text-muted mb-0">Ancienneté moyenne des enseignants</p>
                    
                    <hr>
                    
                    <div class="row text-center">
                        @php
                            $senior = $teachers->filter(function($teacher) {
                                return $teacher->hire_date && \Carbon\Carbon::parse($teacher->hire_date)->diffInYears(now()) >= 10;
                            })->count();
                            
                            $intermediate = $teachers->filter(function($teacher) {
                                $years = $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->diffInYears(now()) : 0;
                                return $years >= 5 && $years < 10;
                            })->count();
                            
                            $junior = $teachers->filter(function($teacher) {
                                return !$teacher->hire_date || \Carbon\Carbon::parse($teacher->hire_date)->diffInYears(now()) < 5;
                            })->count();
                        @endphp
                        <div class="col-4">
                            <h5 class="text-success">{{ $senior }}</h5>
                            <small class="text-muted">10+ ans</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-warning">{{ $intermediate }}</h5>
                            <small class="text-muted">5-9 ans</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-info">{{ $junior }}</h5>
                            <small class="text-muted">&lt; 5 ans</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Enseignants -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Liste des Enseignants 
                    <span class="badge bg-primary ms-2">{{ $teachers->total() }} résultat(s)</span>
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
            @if($teachers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="teachersTable">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th width="80">Avatar</th>
                                <th>Nom Complet</th>
                                <th>Numéro</th>
                                <th>Contact</th>
                                <th>Département</th>
                                <th>Matières</th>
                                <th>Ancienneté</th>
                                <th>Statut</th>
                                <th>Embauche</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input teacher-checkbox" 
                                               value="{{ $teacher->id }}">
                                    </td>
                                    <td>
                                        @if($teacher->user->profile_photo)
                                            <img src="{{ Storage::url($teacher->user->profile_photo) }}" 
                                                 alt="{{ $teacher->user->name }}" 
                                                 class="rounded-circle" width="50" height="50">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->user->name) }}&background=059669&color=fff" 
                                                 alt="{{ $teacher->user->name }}" 
                                                 class="rounded-circle" width="50" height="50">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $teacher->user->name }}</div>
                                        @if($teacher->specialization)
                                            <small class="text-info">{{ $teacher->specialization }}</small>
                                        @endif
                                        @if($teacher->user->date_of_birth)
                                            <br><small class="text-muted">
                                                {{ \Carbon\Carbon::parse($teacher->user->date_of_birth)->age }} ans
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $teacher->employee_number }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $teacher->user->email }}</div>
                                        @if($teacher->user->phone)
                                            <small class="text-muted">{{ $teacher->user->phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($teacher->department)
                                            <span class="badge bg-primary">{{ $teacher->department->name }}</span>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($teacher->subjects && $teacher->subjects->count() > 0)
                                            @foreach($teacher->subjects->take(2) as $subject)
                                                <span class="badge bg-info me-1">{{ $subject->name }}</span>
                                            @endforeach
                                            @if($teacher->subjects->count() > 2)
                                                <span class="badge bg-light text-dark">+{{ $teacher->subjects->count() - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Aucune</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $years = $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->diffInYears(now()) : 0;
                                        @endphp
                                        @if($years > 0)
                                            <span class="fw-semibold">{{ $years }} an(s)</span>
                                        @else
                                            <span class="text-muted">Nouveau</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($teacher->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($teacher->status === 'inactive')
                                            <span class="badge bg-danger">Inactif</span>
                                        @elseif($teacher->status === 'suspended')
                                            <span class="badge bg-warning">Suspendu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($teacher->hire_date)
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($teacher->hire_date)->format('d/m/Y') }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                               class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                                               class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('admin.teachers.subjects', $teacher) }}" 
                                               class="btn btn-outline-info" data-bs-toggle="tooltip" title="Matières">
                                                <i class="bi bi-book"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteTeacher({{ $teacher->id }})" 
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
                        Affichage de {{ $teachers->firstItem() }} à {{ $teachers->lastItem() }} 
                        sur {{ $teachers->total() }} résultats
                    </div>
                    {{ $teachers->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-person-badge fs-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Aucun enseignant trouvé</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'department_id', 'subject_id', 'status']))
                            Aucun enseignant ne correspond aux critères de recherche.
                        @else
                            Commencez par ajouter des enseignants à l'établissement.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'department_id', 'subject_id', 'status']))
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Ajouter le premier enseignant
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
                <h5 class="modal-title">Importer des Enseignants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.teachers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                        <input type="file" class="form-control" id="importFile" name="file" 
                               accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Le fichier doit contenir les colonnes : Nom, Email, Téléphone, Département, Spécialisation, Date d'embauche
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing">
                            <label class="form-check-label" for="updateExisting">
                                Mettre à jour les enseignants existants
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Remarque :</strong> Téléchargez d'abord le 
                        <a href="{{ route('admin.teachers.export') }}?format=excel">modèle Excel</a> 
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
                <p>Êtes-vous sûr de vouloir supprimer cet enseignant ?</p>
                <p class="text-muted">Cette action désactivera l'enseignant plutôt que de le supprimer définitivement.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-submit form on filter change
    document.getElementById('department_id').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    document.getElementById('subject_id').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    document.getElementById('status').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('filterForm').submit();
        }, 500);
    });
    
    // Select all functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.teacher-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });
    
    document.querySelectorAll('.teacher-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleBulkActions();
            
            // Update select all checkbox
            const totalCheckboxes = document.querySelectorAll('.teacher-checkbox').length;
            const checkedCheckboxes = document.querySelectorAll('.teacher-checkbox:checked').length;
            
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            selectAllCheckbox.checked = checkedCheckboxes === totalCheckboxes;
        });
    });
});

function toggleBulkActions() {
    const checkedCount = document.querySelectorAll('.teacher-checkbox:checked').length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (checkedCount > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    toggleBulkActions();
}

function deleteTeacher(teacherId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '{{ route("admin.teachers.destroy", "") }}/' + teacherId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function bulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.teacher-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins un enseignant.');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedIds.length} enseignant(s) ?`)) {
        // Create a form for bulk delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.teachers.bulk-delete") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Add selected IDs
        selectedIds.forEach(function(id) {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'teacher_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush