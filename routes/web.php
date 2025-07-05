<?php
// Fichier: routes/web.php - Routes Complètes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliberationController;
use App\Http\Controllers\Teacher\TeacherController as TeacherDashboardController;
use App\Http\Controllers\Student\StudentController as StudentDashboardController;
use App\Http\Controllers\MessageController;

// Page d'accueil publique
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // Department Management - Routes spéciales AVANT la resource
        Route::get('departments/export', [DepartmentController::class, 'export'])->name('departments.export');
        Route::get('departments/search', [DepartmentController::class, 'search'])->name('departments.search');
        Route::patch('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        Route::post('departments/{department}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
        
        // Department resource routes (APRÈS les routes spéciales)
        Route::resource('departments', DepartmentController::class);
        
        // Student Management - Routes spéciales AVANT la resource
        Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::delete('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
        Route::get('students/{student}/export-profile', [StudentController::class, 'exportProfile'])->name('students.export-profile');
        Route::get('students/{student}/export-grades', [StudentController::class, 'exportGrades'])->name('students.export-grades');
        Route::get('students/{student}/export-attendance', [StudentController::class, 'exportAttendance'])->name('students.export-attendance');
        Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore');
        
        // Student resource routes
        Route::resource('students', StudentController::class);
        
        // Teacher Management - Routes spéciales AVANT la resource
        Route::get('teachers/export', [TeacherController::class, 'export'])->name('teachers.export');
        Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
        Route::delete('teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])->name('teachers.bulk-delete');
        Route::get('teachers/{teacher}/subjects', [TeacherController::class, 'subjects'])->name('teachers.subjects');
        Route::post('teachers/{teacher}/subjects', [TeacherController::class, 'assignSubjects'])->name('teachers.assign-subjects');
        Route::delete('teachers/{teacher}/subjects/{subject}', [TeacherController::class, 'removeSubject'])->name('teachers.remove-subject');
        Route::post('teachers/{teacher}/restore', [TeacherController::class, 'restore'])->name('teachers.restore');
        
        // Teacher resource routes
        Route::resource('teachers', TeacherController::class);
        
        // Subject Management
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/{subject}/restore', [SubjectController::class, 'restore'])->name('subjects.restore');
        // Subject Management - Routes spéciales AVANT la resource
        Route::get('subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
        Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
        Route::delete('subjects/bulk-delete', [SubjectController::class, 'bulkDelete'])->name('subjects.bulk-delete');
        Route::get('subjects/check-code', [SubjectController::class, 'checkCode'])->name('subjects.check-code');

            // AJOUTEZ CES NOUVELLES ROUTES D'EXPORT SPÉCIFIQUES :
        Route::get('subjects/{subject}/export-details', [SubjectController::class, 'exportDetails'])->name('subjects.export-details');
        Route::get('subjects/{subject}/export-grades', [SubjectController::class, 'exportGrades'])->name('subjects.export-grades');
        Route::get('subjects/{subject}/export-schedule', [SubjectController::class, 'exportSchedule'])->name('subjects.export-schedule');
        
        // Subject resource routes (garder la ligne existante)
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/{subject}/restore', [SubjectController::class, 'restore'])->name('subjects.restore');

        // Dans la section API Routes (à ajouter dans le groupe api)
        Route::get('/teachers/by-department/{department}', [TeacherController::class, 'apiByDepartment'])->name('teachers.by-department');
        
        // Class Management
        Route::resource('classes', ClassController::class);
        Route::post('classes/{class}/restore', [ClassController::class, 'restore'])->name('classes.restore');
        Route::get('classes/{class}/students', [ClassController::class, 'students'])->name('classes.students');
        Route::post('classes/{class}/add-students', [ClassController::class, 'addStudents'])->name('classes.add-students');
        
        // Schedule Management
        Route::resource('schedules', ScheduleController::class);
        Route::get('schedules/weekly', [ScheduleController::class, 'weekly'])->name('schedules.weekly');
        Route::get('schedules/conflicts', [ScheduleController::class, 'conflicts'])->name('schedules.conflicts');
        
        // Level Management
        Route::resource('levels', LevelController::class);
        Route::post('levels/{level}/restore', [LevelController::class, 'restore'])->name('levels.restore');
        
        // Deliberation Management
        Route::get('/deliberations', [DeliberationController::class, 'index'])->name('deliberations.index');
        Route::post('/deliberations', [DeliberationController::class, 'store'])->name('deliberations.store');
        
        // Reports and Statistics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/students', [ReportController::class, 'students'])->name('students');
            Route::get('/grades', [ReportController::class, 'grades'])->name('grades');
            Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        });

        // System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
            Route::get('/academic-year', [SettingsController::class, 'academicYear'])->name('academic-year');
            Route::post('/academic-year', [SettingsController::class, 'updateAcademicYear'])->name('update-academic-year');
        });
        
        // API Routes for dynamic data (AJAX)
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/classes/by-level/{level}', [ClassController::class, 'apiByLevel'])->name('classes.by-level');
            Route::get('/students/by-class/{class}', [StudentController::class, 'apiByClass'])->name('students.by-class');
            Route::get('/teachers/by-subject/{subject}', [TeacherController::class, 'apiBySubject'])->name('teachers.by-subject');
            
            // AJOUTEZ CETTE LIGNE :
            Route::get('/teachers/by-department/{department}', [TeacherController::class, 'apiByDepartment'])->name('teachers.by-department');

            Route::get('/dashboard/stats', [AdminController::class, 'apiStats'])->name('dashboard.stats');
            Route::get('/departments/stats', [DepartmentController::class, 'apiStats'])->name('departments.stats');
        });
    });

    // Teacher Routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'dashboard'])->name('dashboard');
        
        // Grade Management
        Route::get('/grades', [TeacherDashboardController::class, 'grades'])->name('grades.index');
        Route::get('/grades/subject/{subject}', [TeacherDashboardController::class, 'subjectGrades'])->name('grades.subject');
        Route::post('/grades', [TeacherDashboardController::class, 'storeGrade'])->name('grades.store');
        Route::put('/grades/{grade}', [TeacherDashboardController::class, 'updateGrade'])->name('grades.update');
        Route::delete('/grades/{grade}', [TeacherDashboardController::class, 'destroyGrade'])->name('grades.destroy');
        
        // Attendance Management
        Route::get('/attendance', [TeacherDashboardController::class, 'attendance'])->name('attendance.index');
        Route::post('/attendance', [TeacherDashboardController::class, 'markAttendance'])->name('attendance.mark');
        Route::get('/attendance/subject/{subject}', [TeacherDashboardController::class, 'subjectAttendance'])->name('attendance.subject');
        
        // Schedule
        Route::get('/schedule', [TeacherDashboardController::class, 'schedule'])->name('schedule');
        
        // Students
        Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students.index');
        Route::get('/students/{student}', [TeacherDashboardController::class, 'showStudent'])->name('students.show');
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [StudentDashboardController::class, 'grades'])->name('grades');
        Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');
        Route::get('/schedule', [StudentDashboardController::class, 'schedule'])->name('schedule');
        Route::get('/bulletin/{semester}/{year}', [StudentDashboardController::class, 'bulletin'])->name('bulletin');
    });

    // Messaging System (Accessible to all authenticated users)
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
    });
});