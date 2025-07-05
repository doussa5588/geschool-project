<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Enseignants - UNCHK</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 10px 0 0 0;
            color: #34495e;
            font-size: 18px;
            font-weight: normal;
        }
        
        .header .subtitle {
            margin: 5px 0 0 0;
            color: #7f8c8d;
            font-size: 14px;
            font-style: italic;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-left, .info-right {
            flex: 1;
        }
        
        .info-right {
            text-align: right;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stats-summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .stat-box {
            background-color: #3498db;
            color: white;
            padding: 15px;
            border-radius: 8px;
            min-width: 120px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        th, td {
            border: 1px solid #bdc3c7;
            padding: 10px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e8f4f8;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-active {
            color: #27ae60;
            font-weight: bold;
            background-color: #d5f4e6;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
        }
        
        .status-inactive {
            color: #e74c3c;
            font-weight: bold;
            background-color: #fadbd8;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
        }
        
        .status-suspended {
            color: #f39c12;
            font-weight: bold;
            background-color: #fef2e0;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
        }
        
        .department {
            background-color: #f1c40f;
            color: #2c3e50;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .salary {
            font-weight: bold;
            color: #27ae60;
        }
        
        .employee-number {
            background-color: #95a5a6;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 2px solid #ecf0f1;
            padding-top: 15px;
        }
        
        .footer .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            text-align: center;
        }
        
        .signature-box {
            border: 1px dashed #bdc3c7;
            padding: 20px;
            width: 200px;
            height: 80px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
            }
            
            .info-section {
                break-inside: avoid;
            }
            
            table {
                break-inside: avoid;
            }
            
            tr {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Université Numérique Cheikh Hamidou Kane</h1>
        <h2>Liste des Enseignants</h2>
        <div class="subtitle">Année académique {{ date('Y') }}-{{ date('Y')+1 }}</div>
    </div>
    
    <div class="info-section">
        <div class="info-left">
            <div class="info-item">
                <span class="info-label">Date d'export :</span> {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
            </div>
            <div class="info-item">
                <span class="info-label">Exporté par :</span> {{ auth()->user()->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Département :</span> Tous les départements
            </div>
        </div>
        <div class="info-right">
            <div class="info-item">
                <span class="info-label">Nombre total :</span> {{ $teachers->count() }} enseignant(s)
            </div>
            <div class="info-item">
                <span class="info-label">Enseignants actifs :</span> {{ $teachers->where('status', 'active')->count() }}
            </div>
            <div class="info-item">
                <span class="info-label">Enseignants inactifs :</span> {{ $teachers->where('status', 'inactive')->count() }}
            </div>
        </div>
    </div>
    
    <div class="stats-summary">
        <div class="stat-box">
            <span class="stat-number">{{ $teachers->count() }}</span>
            <div class="stat-label">Total Enseignants</div>
        </div>
        <div class="stat-box" style="background-color: #27ae60;">
            <span class="stat-number">{{ $teachers->where('status', 'active')->count() }}</span>
            <div class="stat-label">Actifs</div>
        </div>
        <div class="stat-box" style="background-color: #e74c3c;">
            <span class="stat-number">{{ $teachers->where('status', 'inactive')->count() }}</span>
            <div class="stat-label">Inactifs</div>
        </div>
        <div class="stat-box" style="background-color: #f39c12;">
            <span class="stat-number">{{ $teachers->groupBy('department.name')->count() }}</span>
            <div class="stat-label">Départements</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Nom</th>
                <th>Contact</th>
                <th>N° Employé</th>
                <th>Spécialisation</th>
                <th>Département</th>
                <th>Embauche</th>
                <th>Salaire</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $index => $teacher)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $teacher->user->name }}</strong><br>
                    <small style="color: #7f8c8d;">{{ $teacher->user->email }}</small>
                </td>
                <td>
                    @if($teacher->user->phone)
                        {{ $teacher->user->phone }}<br>
                    @endif
                    @if($teacher->user->address)
                        <small style="color: #7f8c8d;">{{ Str::limit($teacher->user->address, 30) }}</small>
                    @endif
                </td>
                <td class="text-center">
                    <span class="employee-number">{{ $teacher->employee_number }}</span>
                </td>
                <td>{{ $teacher->specialization }}</td>
                <td class="text-center">
                    <span class="department">{{ $teacher->department->name ?? 'N/A' }}</span>
                </td>
                <td class="text-center">
                    {{ $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->format('d/m/Y') : 'N/A' }}
                </td>
                <td class="text-right">
                    @if($teacher->salary)
                        <span class="salary">{{ number_format($teacher->salary, 0, ',', ' ') }} FCFA</span>
                    @else
                        <span style="color: #bdc3c7;">Non défini</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="status-{{ $teacher->status }}">
                        {{ ucfirst($teacher->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Document confidentiel</strong> - Usage interne uniquement</p>
        <p>Généré automatiquement le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }} par le système GeSchool</p>
        <p>Université Numérique Cheikh Hamidou Kane - Dakar, Sénégal</p>
        
        <div class="signature-section">
            <div class="signature-box">
                <strong>Directeur Académique</strong><br>
                <small>Signature et cachet</small>
            </div>
            <div class="signature-box">
                <strong>Responsable RH</strong><br>
                <small>Signature et cachet</small>
            </div>
            <div class="signature-box">
                <strong>Secrétaire Général</strong><br>
                <small>Signature et cachet</small>
            </div>
        </div>
    </div>
    
    <div class="no-print" style="margin-top: 30px; text-align: center; background-color: #ecf0f1; padding: 20px; border-radius: 10px;">
        <button onclick="window.print()" style="background: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 14px; margin-right: 10px;">
            🖨️ Imprimer / Sauvegarder en PDF
        </button>
        <button onclick="window.close()" style="background: #95a5a6; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 14px;">
            ❌ Fermer
        </button>
    </div>
</body>
</html>