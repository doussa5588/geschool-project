<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Matières - UNCHK</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        
        .header h2 {
            color: #64748b;
            font-size: 16px;
            margin: 0;
            font-weight: normal;
        }
        
        .header .subtitle {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .meta-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #2563eb;
        }
        
        .meta-info table {
            width: 100%;
        }
        
        .meta-info td {
            padding: 3px 10px;
            font-size: 10px;
        }
        
        .meta-info strong {
            color: #1e293b;
        }
        
        .stats-section {
            margin-bottom: 25px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #1e293b;
        }
        
        .stat-card .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card.primary h3 { color: #2563eb; }
        .stat-card.success h3 { color: #059669; }
        .stat-card.warning h3 { color: #d97706; }
        .stat-card.info h3 { color: #0891b2; }
        
        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        .subjects-table th,
        .subjects-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        
        .subjects-table th {
            background: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .subjects-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .subjects-table tbody tr:hover {
            background: #e2e8f0;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        
        .badge.success {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge.warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge.info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge.secondary {
            background: #f1f5f9;
            color: #475569;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 25px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .department-section {
            margin-bottom: 30px;
        }
        
        .department-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        
        .no-data {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        
        .credits-badge {
            background: #0891b2;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    {{-- En-tête du rapport --}}
    <div class="header">
        <h1>UNIVERSITÉ NUMÉRIQUE CHEIKH HAMIDOU KANE</h1>
        <h2>Rapport des Matières Académiques</h2>
        <div class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    {{-- Informations du rapport --}}
    <div class="meta-info">
        <table>
            <tr>
                <td><strong>Type de rapport :</strong> {{ $reportType ?? 'Rapport complet des matières' }}</td>
                <td><strong>Période :</strong> {{ $period ?? 'Année académique ' . date('Y') . '-' . (date('Y') + 1) }}</td>
            </tr>
            <tr>
                <td><strong>Généré par :</strong> {{ auth()->user()->name }}</td>
                <td><strong>Total matières :</strong> {{ $subjects->count() }}</td>
            </tr>
            @if(isset($filters) && !empty($filters))
            <tr>
                <td colspan="2"><strong>Filtres appliqués :</strong> 
                    @foreach($filters as $key => $value)
                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Statistiques globales --}}
    @if(isset($stats))
    <div class="stats-section">
        <div class="section-title">Statistiques Générales</div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <h3>{{ $stats['total_subjects'] ?? $subjects->count() }}</h3>
                <div class="label">Total Matières</div>
            </div>
            <div class="stat-card success">
                <h3>{{ $stats['active_subjects'] ?? $subjects->where('is_active', true)->count() }}</h3>
                <div class="label">Matières Actives</div>
            </div>
            <div class="stat-card warning">
                <h3>{{ $stats['assigned_subjects'] ?? $subjects->whereNotNull('teacher_id')->count() }}</h3>
                <div class="label">Avec Enseignant</div>
            </div>
            <div class="stat-card info">
                <h3>{{ $stats['total_credits'] ?? $subjects->sum('credits') }}</h3>
                <div class="label">Total Crédits</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Matières par département --}}
    @if($groupByDepartment ?? false)
        @foreach($subjects->groupBy('department.name') as $departmentName => $departmentSubjects)
            <div class="department-section">
                <div class="department-header">
                    {{ $departmentName ?: 'Sans département' }} 
                    ({{ $departmentSubjects->count() }} matière{{ $departmentSubjects->count() > 1 ? 's' : '' }})
                </div>
                
                @include('admin.subjects.export-pdf-table', ['subjects' => $departmentSubjects])
            </div>
        @endforeach
    @else
        {{-- Tableau unique de toutes les matières --}}
        <div class="section-title">Liste des Matières</div>
        @include('admin.subjects.export-pdf-table', ['subjects' => $subjects])
    @endif

    {{-- Résumé par département --}}
    @if($subjects->count() > 0)
    <div class="section-title">Résumé par Département</div>
    <table class="subjects-table">
        <thead>
            <tr>
                <th>Département</th>
                <th class="text-center">Matières</th>
                <th class="text-center">Actives</th>
                <th class="text-center">Avec Enseignant</th>
                <th class="text-center">Total Crédits</th>
                <th class="text-center">Moy. Crédits</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects->groupBy('department.name') as $deptName => $deptSubjects)
                @php
                    $activeSubjects = $deptSubjects->where('is_active', true);
                    $assignedSubjects = $deptSubjects->whereNotNull('teacher_id');
                    $totalCredits = $deptSubjects->sum('credits');
                    $avgCredits = $deptSubjects->count() > 0 ? round($totalCredits / $deptSubjects->count(), 1) : 0;
                @endphp
                <tr>
                    <td><strong>{{ $deptName ?: 'Sans département' }}</strong></td>
                    <td class="text-center">{{ $deptSubjects->count() }}</td>
                    <td class="text-center">{{ $activeSubjects->count() }}</td>
                    <td class="text-center">{{ $assignedSubjects->count() }}</td>
                    <td class="text-center">{{ $totalCredits }}</td>
                    <td class="text-center">{{ $avgCredits }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Alertes et remarques --}}
    @if(isset($alerts) && !empty($alerts))
    <div class="section-title">Alertes et Remarques</div>
    <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($alerts as $alert)
                <li style="margin-bottom: 5px; color: #92400e;">{{ $alert }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Pied de page --}}
    <div class="footer">
        <div>
            <strong>UNCHK - Université Numérique Cheikh Hamidou Kane</strong><br>
            Ce rapport a été généré automatiquement le {{ now()->format('d/m/Y à H:i:s') }}<br>
            Données extraites du système de gestion académique - Version {{ config('app.version', '1.0') }}
        </div>
    </div>
</body>
</html>

{{-- Template séparé pour le tableau des matières --}}
@php
// Ce contenu serait dans un fichier séparé : export-pdf-table.blade.php
@endphp

{{-- Contenu du fichier export-pdf-table.blade.php : --}}

@if($subjects->count() > 0)
<table class="subjects-table">
    <thead>
        <tr>
            <th style="width: 8%;">Code</th>
            <th style="width: 25%;">Nom</th>
            <th style="width: 15%;">Département</th>
            <th style="width: 20%;">Enseignant</th>
            <th style="width: 8%;">Crédits</th>
            <th style="width: 8%;">Classes</th>
            <th style="width: 8%;">Étudiants</th>
            <th style="width: 8%;">Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subjects as $subject)
        <tr>
            <td><span class="badge secondary">{{ $subject->code }}</span></td>
            <td>
                <strong>{{ $subject->name }}</strong>
                @if($subject->description && strlen($subject->description) > 0)
                    <br><small style="color: #64748b;">{{ Str::limit($subject->description, 60) }}</small>
                @endif
            </td>
            <td>{{ $subject->department->name ?? 'N/A' }}</td>
            <td>
                @if($subject->teacher)
                    {{ $subject->teacher->user->name }}
                    <br><small style="color: #64748b;">{{ $subject->teacher->specialization }}</small>
                @else
                    <span style="color: #dc2626; font-style: italic;">Non assigné</span>
                @endif
            </td>
            <td class="text-center">
                <span class="credits-badge">{{ $subject->credits }}</span>
            </td>
            <td class="text-center">{{ $subject->classes->count() }}</td>
            <td class="text-center">{{ $subject->total_students_count }}</td>
            <td class="text-center">
                @if($subject->is_active)
                    @if($subject->teacher)
                        <span class="badge success">Assignée</span>
                    @else
                        <span class="badge warning">Active</span>
                    @endif
                @else
                    <span class="badge danger">Inactive</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">
    Aucune matière trouvée pour les critères sélectionnés.
</div>
@endif
