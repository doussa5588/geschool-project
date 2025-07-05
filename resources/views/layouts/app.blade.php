<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UNCHK - Gestion Scolaire')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --unchk-primary: #2563eb;
            --unchk-secondary: #64748b;
            --unchk-success: #059669;
            --unchk-danger: #dc2626;
            --unchk-warning: #d97706;
            --unchk-info: #0891b2;
            --unchk-light: #f8fafc;
            --unchk-dark: #1e293b;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--unchk-primary) 0%, #1d4ed8 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-brand h4 {
            opacity: 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.875rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 80px;
        }

        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-header {
            background: white;
            padding: 2rem 1.5rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--unchk-dark);
            margin: 0;
        }

        .page-subtitle {
            color: var(--unchk-secondary);
            margin-top: 0.5rem;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
            color: white;
            border: none;
        }

        .stats-card.primary {
            --gradient-from: var(--unchk-primary);
            --gradient-to: #1d4ed8;
        }

        .stats-card.success {
            --gradient-from: var(--unchk-success);
            --gradient-to: #047857;
        }

        .stats-card.warning {
            --gradient-from: var(--unchk-warning);
            --gradient-to: #b45309;
        }

        .stats-card.info {
            --gradient-from: var(--unchk-info);
            --gradient-to: #0e7490;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--unchk-primary);
            border-color: var(--unchk-primary);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .table {
            font-size: 0.875rem;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--unchk-secondary);
            border-bottom: 2px solid #e2e8f0;
        }

        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
        }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.25rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.625rem 0.875rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--unchk-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--unchk-secondary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--unchk-dark);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed + .main-content {
                margin-left: 0;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Notification Dot */
        .notification-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }

        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e2e8f0;
        }

        .progress-bar {
            border-radius: 4px;
        }

        /* Hover Effects */
        .hover-lift {
            transition: transform 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-mortarboard-fill me-2"></i>UNCHK</h4>
        </div>
        
        <nav class="sidebar-nav">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <!-- Admin Navigation -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                <span>Tableau de Bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Étudiants</span>
                                <span class="badge bg-light text-dark ms-auto">{{ App\Models\Student::where('is_active', true)->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">
                                <i class="bi bi-person-badge"></i>
                                <span>Enseignants</span>
                                <span class="badge bg-light text-dark ms-auto">{{ App\Models\Teacher::where('is_active', true)->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.classes*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">
                                <i class="bi bi-collection"></i>
                                <span>Classes</span>
                                <span class="badge bg-light text-dark ms-auto">{{ App\Models\Classe::where('is_active', true)->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.schedules*') ? 'active' : '' }}" href="{{ route('admin.schedules.index') }}">
                                <i class="bi bi-calendar3"></i>
                                <span>Emplois du Temps</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">
                                <i class="bi bi-book"></i>
                                <span>Matières</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.departments*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                                <i class="bi bi-building"></i>
                                <span>Départements</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="bi bi-graph-up"></i>
                                <span>Rapports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-gear"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                    </ul>
                @elseif(auth()->user()->hasRole('teacher'))
                    <!-- Teacher Navigation -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.dashboard*') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                <span>Tableau de Bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.classes*') ? 'active' : '' }}" href="{{ route('teacher.classes') }}">
                                <i class="bi bi-collection"></i>
                                <span>Mes Classes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.grades*') ? 'active' : '' }}" href="{{ route('teacher.grades.index') }}">
                                <i class="bi bi-clipboard-data"></i>
                                <span>Notes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.attendance*') ? 'active' : '' }}" href="{{ route('teacher.attendance.index') }}">
                                <i class="bi bi-check2-square"></i>
                                <span>Présences</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.schedule*') ? 'active' : '' }}" href="{{ route('teacher.schedule') }}">
                                <i class="bi bi-calendar3"></i>
                                <span>Mon Emploi du Temps</span>
                            </a>
                        </li>
                    </ul>
                @elseif(auth()->user()->hasRole('student'))
                    <!-- Student Navigation -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.dashboard*') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                <i class="bi bi-speedometer2"></i>
                                <span>Tableau de Bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.grades*') ? 'active' : '' }}" href="{{ route('student.grades') }}">
                                <i class="bi bi-clipboard-data"></i>
                                <span>Mes Notes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.attendance*') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                                <i class="bi bi-check2-square"></i>
                                <span>Mes Présences</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.schedule*') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
                                <i class="bi bi-calendar3"></i>
                                <span>Mon Emploi du Temps</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                                <i class="bi bi-person"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>
                    </ul>
                @endif
            @endauth
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark p-1 me-3" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                
                @if(isset($breadcrumbs))
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            @foreach($breadcrumbs as $breadcrumb)
                                @if($loop->last)
                                    <li class="breadcrumb-item active">{{ $breadcrumb['label'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif
            </div>

            <div class="d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-dark p-1" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5 position-relative">
                            <span class="notification-dot"></span>
                        </i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">Nouveau message de admin</a></li>
                        <li><a class="dropdown-item" href="#">Note ajoutée pour Math101</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Voir toutes</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->full_name ?? 'User' }}&background=2563eb&color=fff" 
                             alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                        <div class="d-none d-sm-block">
                            <div class="fw-semibold">{{ auth()->user()->full_name ?? 'Utilisateur' }}</div>
                            <small class="text-muted">{{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mon Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Header -->
        @hasSection('page-header')
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="page-title">@yield('page-title')</h1>
                        @hasSection('page-subtitle')
                            <p class="page-subtitle mb-0">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    @hasSection('page-actions')
                        <div class="d-flex gap-2">
                            @yield('page-actions')
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Erreur :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            $('#sidebarToggle').click(function() {
                $('#sidebar').toggleClass('collapsed');
            });

            // Mobile Sidebar Toggle
            if (window.innerWidth <= 768) {
                $('#sidebarToggle').click(function() {
                    $('#sidebar').toggleClass('show');
                });
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // Initialize DataTables with default config
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
                },
                responsive: true,
                pageLength: 25,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });

            // CSRF Token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Tooltip initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popover initialization
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });

        // Utility Functions
        function showLoading(element) {
            $(element).html('<span class="loading"></span> Chargement...');
        }

        function hideLoading(element, originalText) {
            $(element).html(originalText);
        }

        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        // Theme Toggle
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>

    @stack('scripts')
</body>
</html>