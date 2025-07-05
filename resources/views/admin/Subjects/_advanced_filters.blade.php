{{--
    Composant de filtres avancés pour les matières
    Usage: @include('admin.subjects._advanced_filters', ['departments' => $departments, 'teachers' => $teachers])
--}}

@php
    $showAdvanced = $showAdvanced ?? false;
    $formId = $formId ?? 'advancedFiltersForm';
    $route = $route ?? route('admin.subjects.index');
@endphp

<div class="card mb-4" id="advancedFiltersCard">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filtres de Recherche
            </h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" onclick="resetAllFilters()">
                    <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="toggleAdvancedFilters()">
                    <i class="bi bi-gear"></i> 
                    <span id="advancedToggleText">{{ $showAdvanced ? 'Masquer' : 'Avancé' }}</span>
                </button>
                <button type="button" class="btn btn-outline-success" onclick="saveFilterPreset()">
                    <i class="bi bi-bookmark"></i> Sauvegarder
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <form method="GET" action="{{ $route }}" id="{{ $formId }}" class="needs-validation" novalidate>
            
            {{-- Filtres de base --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        <i class="bi bi-search me-1"></i>Recherche globale
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom, code, description..."
                               autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="form-text">Recherche dans le nom, code et description</div>
                </div>
                
                <div class="col-md-3">
                    <label for="department_id" class="form-label">
                        <i class="bi bi-building me-1"></i>Département
                    </label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">Tous les départements</option>
                        @foreach($departments ?? [] as $department)
                            <option value="{{ $department->id }}" 
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }} ({{ $department->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">
                        <i class="bi bi-circle me-1"></i>Statut
                    </label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Avec enseignant</option>
                        <option value="unassigned" {{ request('status') === 'unassigned' ? 'selected' : '' }}>Sans enseignant</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="quick_action" class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>

            {{-- Filtres avancés (masqués par défaut) --}}
            <div id="advancedFilters" class="border-top pt-3" style="display: {{ $showAdvanced ? 'block' : 'none' }};">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="teacher_id" class="form-label">
                            <i class="bi bi-person me-1"></i>Enseignant
                        </label>
                        <select class="form-select" id="teacher_id" name="teacher_id">
                            <option value="">Tous les enseignants</option>
                            @foreach($teachers ?? [] as $teacher)
                                <option value="{{ $teacher->id }}" 
                                        data-department="{{ $teacher->department_id }}"
                                        {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }} - {{ $teacher->specialization }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="credits" class="form-label">
                            <i class="bi bi-award me-1"></i>Crédits
                        </label>
                        <select class="form-select" id="credits" name="credits">
                            <option value="">Tous</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('credits') == $i ? 'selected' : '' }}>
                                    {{ $i }} crédit{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="credits_range" class="form-label">
                            <i class="bi bi-sliders me-1"></i>Plage de crédits
                        </label>
                        <select class="form-select" id="credits_range" name="credits_range">
                            <option value="">Toutes</option>
                            <option value="1-3" {{ request('credits_range') === '1-3' ? 'selected' : '' }}>1-3 crédits</option>
                            <option value="4-6" {{ request('credits_range') === '4-6' ? 'selected' : '' }}>4-6 crédits</option>
                            <option value="7-10" {{ request('credits_range') === '7-10' ? 'selected' : '' }}>7-10 crédits</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="has_classes" class="form-label">
                            <i class="bi bi-collection me-1"></i>Assignation classes
                        </label>
                        <select class="form-select" id="has_classes" name="has_classes">
                            <option value="">Toutes</option>
                            <option value="1" {{ request('has_classes') === '1' ? 'selected' : '' }}>Avec classes</option>
                            <option value="0" {{ request('has_classes') === '0' ? 'selected' : '' }}>Sans classes</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="students_count" class="form-label">
                            <i class="bi bi-people me-1"></i>Nb. étudiants
                        </label>
                        <select class="form-select" id="students_count" name="students_count">
                            <option value="">Tous</option>
                            <option value="0" {{ request('students_count') === '0' ? 'selected' : '' }}>Aucun</option>
                            <option value="1-20" {{ request('students_count') === '1-20' ? 'selected' : '' }}>1-20</option>
                            <option value="21-50" {{ request('students_count') === '21-50' ? 'selected' : '' }}>21-50</option>
                            <option value="50+" {{ request('students_count') === '50+' ? 'selected' : '' }}>50+</option>
                        </select>
                    </div>
                </div>

                {{-- Filtres par dates --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="created_from" class="form-label">
                            <i class="bi bi-calendar me-1"></i>Créé à partir du
                        </label>
                        <input type="date" class="form-control" id="created_from" name="created_from"
                               value="{{ request('created_from') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="created_to" class="form-label">
                            <i class="bi bi-calendar me-1"></i>Créé jusqu'au
                        </label>
                        <input type="date" class="form-control" id="created_to" name="created_to"
                               value="{{ request('created_to') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="has_grades" class="form-label">
                            <i class="bi bi-clipboard-data me-1"></i>Avec notes
                        </label>
                        <select class="form-select" id="has_grades" name="has_grades">
                            <option value="">Toutes</option>
                            <option value="1" {{ request('has_grades') === '1' ? 'selected' : '' }}>Avec notes</option>
                            <option value="0" {{ request('has_grades') === '0' ? 'selected' : '' }}>Sans notes</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="has_schedule" class="form-label">
                            <i class="bi bi-calendar3 me-1"></i>Programmé
                        </label>
                        <select class="form-select" id="has_schedule" name="has_schedule">
                            <option value="">Toutes</option>
                            <option value="1" {{ request('has_schedule') === '1' ? 'selected' : '' }}>Programmé</option>
                            <option value="0" {{ request('has_schedule') === '0' ? 'selected' : '' }}>Non programmé</option>
                        </select>
                    </div>
                </div>

                {{-- Tri avancé --}}
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sort_by" class="form-label">
                            <i class="bi bi-sort-down me-1"></i>Trier par
                        </label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="name" {{ request('sort_by', 'name') === 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="code" {{ request('sort_by') === 'code' ? 'selected' : '' }}>Code</option>
                            <option value="credits" {{ request('sort_by') === 'credits' ? 'selected' : '' }}>Crédits</option>
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date création</option>
                            <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Dernière modif.</option>
                            <option value="students_count" {{ request('sort_by') === 'students_count' ? 'selected' : '' }}>Nb. étudiants</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sort_direction" class="form-label">
                            <i class="bi bi-arrow-down-up me-1"></i>Direction
                        </label>
                        <select class="form-select" id="sort_direction" name="sort_direction">
                            <option value="asc" {{ request('sort_direction', 'asc') === 'asc' ? 'selected' : '' }}>Croissant</option>
                            <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">
                            <i class="bi bi-list me-1"></i>Par page
                        </label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label">Actions rapides</label>
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-info" onclick="applyQuickFilter('recent')">
                                <i class="bi bi-clock"></i> Récentes
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="applyQuickFilter('unassigned')">
                                <i class="bi bi-person-x"></i> Sans enseignant
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="applyQuickFilter('popular')">
                                <i class="bi bi-star"></i> Populaires
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Préréglages de filtres --}}
        <div id="filterPresets" class="mt-3" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Préréglages sauvegardés :</small>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllPresets()">
                    <i class="bi bi-trash"></i> Tout supprimer
                </button>
            </div>
            <div id="presetsList" class="d-flex flex-wrap gap-1">
                <!-- Les préréglages seront ajoutés ici par JavaScript -->
            </div>
        </div>

        {{-- Résumé des filtres actifs --}}
        <div id="activeFilters" class="mt-3" style="display: none;">
            <div class="d-flex align-items-center flex-wrap gap-1">
                <small class="text-muted me-2">Filtres actifs :</small>
                <div id="activeFiltersList">
                    <!-- Les filtres actifs seront ajoutés ici par JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialisation des filtres
    initializeFilters();
    updateActiveFilters();
    loadFilterPresets();
    
    // Auto-submit sur changement des filtres de base
    $('#department_id, #status').change(function() {
        $('#{{ $formId }}').submit();
    });
    
    // Recherche avec délai
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#{{ $formId }}').submit();
        }, 800);
    });
    
    // Filtrage dynamique des enseignants par département
    $('#department_id').change(function() {
        filterTeachersByDepartment($(this).val());
    });
    
    // Validation des dates
    $('#created_from, #created_to').change(function() {
        validateDateRange();
    });
});

function initializeFilters() {
    // Charger les états sauvegardés
    const savedState = localStorage.getItem('subjects_filters_state');
    if (savedState) {
        const state = JSON.parse(savedState);
        $('#advancedFilters').toggle(state.showAdvanced);
        $('#advancedToggleText').text(state.showAdvanced ? 'Masquer' : 'Avancé');
    }
}

function toggleAdvancedFilters() {
    const isVisible = $('#advancedFilters').is(':visible');
    $('#advancedFilters').slideToggle();
    $('#advancedToggleText').text(isVisible ? 'Avancé' : 'Masquer');
    
    // Sauvegarder l'état
    localStorage.setItem('subjects_filters_state', JSON.stringify({
        showAdvanced: !isVisible
    }));
}

function resetAllFilters() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les filtres ?')) {
        // Vider tous les champs
        $('#{{ $formId }}')[0].reset();
        
        // Rediriger vers la page sans paramètres
        window.location.href = '{{ $route }}';
    }
}

function clearSearch() {
    $('#search').val('').focus();
}

function filterTeachersByDepartment(departmentId) {
    const teacherSelect = $('#teacher_id');
    
    // Montrer/cacher les options selon le département
    teacherSelect.find('option').each(function() {
        const option = $(this);
        const teacherDepartment = option.data('department');
        
        if (option.val() === '' || !departmentId || teacherDepartment == departmentId) {
            option.show();
        } else {
            option.hide();
        }
    });
    
    // Réinitialiser la sélection si l'enseignant sélectionné n'est plus visible
    const selectedOption = teacherSelect.find('option:selected');
    if (selectedOption.data('department') && selectedOption.data('department') != departmentId) {
        teacherSelect.val('');
    }
}

function validateDateRange() {
    const fromDate = $('#created_from').val();
    const toDate = $('#created_to').val();
    
    if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
        alert('La date de début ne peut pas être postérieure à la date de fin.');
        $('#created_to').val('');
    }
}

function updateActiveFilters() {
    const activeFilters = [];
    
    // Parcourir tous les champs avec des valeurs
    $('#{{ $formId }} input, #{{ $formId }} select').each(function() {
        const input = $(this);
        const value = input.val();
        const name = input.attr('name');
        
        if (value && name && name !== 'sort_by' && name !== 'sort_direction' && name !== 'per_page') {
            let label = input.prev('label').text().replace(/[\*:]/g, '').trim();
            let displayValue = value;
            
            // Personnaliser l'affichage selon le type
            if (input.is('select')) {
                displayValue = input.find('option:selected').text();
            }
            
            activeFilters.push({
                name: name,
                label: label,
                value: value,
                display: `${label}: ${displayValue}`
            });
        }
    });
    
    // Afficher les filtres actifs
    if (activeFilters.length > 0) {
        const filterTags = activeFilters.map(filter => 
            `<span class="badge bg-primary me-1">
                ${filter.display}
                <button type="button" class="btn-close btn-close-white ms-1" 
                        onclick="removeFilter('${filter.name}')" style="font-size: 0.6em;"></button>
            </span>`
        ).join('');
        
        $('#activeFiltersList').html(filterTags);
        $('#activeFilters').show();
    } else {
        $('#activeFilters').hide();
    }
}

function removeFilter(filterName) {
    $(`[name="${filterName}"]`).val('');
    $('#{{ $formId }}').submit();
}

function applyQuickFilter(type) {
    // Réinitialiser les filtres
    $('#{{ $formId }}')[0].reset();
    
    switch(type) {
        case 'recent':
            $('#created_from').val(new Date(Date.now() - 30*24*60*60*1000).toISOString().split('T')[0]);
            $('#sort_by').val('created_at');
            $('#sort_direction').val('desc');
            break;
        case 'unassigned':
            $('#status').val('unassigned');
            break;
        case 'popular':
            $('#sort_by').val('students_count');
            $('#sort_direction').val('desc');
            $('#students_count').val('21-50');
            break;
    }
    
    $('#{{ $formId }}').submit();
}

function saveFilterPreset() {
    const presetName = prompt('Nom du préréglage :');
    if (!presetName) return;
    
    const formData = $('#{{ $formId }}').serialize();
    const presets = JSON.parse(localStorage.getItem('subjects_filter_presets') || '[]');
    
    // Vérifier si le nom existe déjà
    const existingIndex = presets.findIndex(p => p.name === presetName);
    
    if (existingIndex >= 0) {
        if (!confirm('Un préréglage avec ce nom existe déjà. Le remplacer ?')) {
            return;
        }
        presets[existingIndex] = { name: presetName, data: formData };
    } else {
        presets.push({ name: presetName, data: formData });
    }
    
    localStorage.setItem('subjects_filter_presets', JSON.stringify(presets));
    loadFilterPresets();
    
    alert('Préréglage sauvegardé avec succès !');
}

function loadFilterPresets() {
    const presets = JSON.parse(localStorage.getItem('subjects_filter_presets') || '[]');
    
    if (presets.length > 0) {
        const presetButtons = presets.map(preset => 
            `<button type="button" class="btn btn-sm btn-outline-secondary" 
                     onclick="applyPreset('${preset.name}')">
                ${preset.name}
                <i class="bi bi-x ms-1" onclick="event.stopPropagation(); deletePreset('${preset.name}')"></i>
             </button>`
        ).join(' ');
        
        $('#presetsList').html(presetButtons);
        $('#filterPresets').show();
    } else {
        $('#filterPresets').hide();
    }
}

function applyPreset(presetName) {
    const presets = JSON.parse(localStorage.getItem('subjects_filter_presets') || '[]');
    const preset = presets.find(p => p.name === presetName);
    
    if (preset) {
        // Parser les données et appliquer aux champs
        const params = new URLSearchParams(preset.data);
        params.forEach((value, key) => {
            $(`[name="${key}"]`).val(value);
        });
        
        $('#{{ $formId }}').submit();
    }
}

function deletePreset(presetName) {
    if (confirm(`Supprimer le préréglage "${presetName}" ?`)) {
        let presets = JSON.parse(localStorage.getItem('subjects_filter_presets') || '[]');
        presets = presets.filter(p => p.name !== presetName);
        localStorage.setItem('subjects_filter_presets', JSON.stringify(presets));
        loadFilterPresets();
    }
}

function clearAllPresets() {
    if (confirm('Supprimer tous les préréglages sauvegardés ?')) {
        localStorage.removeItem('subjects_filter_presets');
        loadFilterPresets();
    }
}

// Mise à jour des filtres actifs après changement
$(document).on('change', '#{{ $formId }} input, #{{ $formId }} select', function() {
    setTimeout(updateActiveFilters, 100);
});
</script>

<style>
.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

#activeFiltersList .badge {
    cursor: default;
}

#activeFiltersList .btn-close {
    cursor: pointer;
}

.form-label i {
    color: #6c757d;
}

#filterPresets .btn {
    margin-bottom: 0.25rem;
}

#advancedFilters {
    background: rgba(248, 249, 250, 0.5);
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 1rem;
}

.needs-validation .form-control:invalid {
    border-color: #dc3545;
}

.needs-validation .form-control:valid {
    border-color: #198754;
}
</style>