<?php

/*
|--------------------------------------------------------------------------
| Routes Web - Coordination par SADOU MBALLO
|--------------------------------------------------------------------------
| Architecture de routage du système GeSchool
| Responsable: SADOU MBALLO
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;

// Routes d'authentification
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware(['auth'])->group(function () {
    
    // Dashboard général
    Route::get('/dashboard', function () {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'professeur':
                return redirect()->route('professeur.dashboard');
            case 'etudiant':
                return redirect()->route('etudiant.dashboard');
            case 'parent':
                return redirect()->route('parent.dashboard');
            default:
                return view('dashboard');
        }
    })->name('dashboard');

    // Routes Admin (développées par SADOU MBALLO)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/parametres', [AdminController::class, 'parametres'])->name('parametres');
        Route::post('/parametres', [AdminController::class, 'updateParametres'])->name('parametres.update');
        Route::get('/rapport-general', [AdminController::class, 'rapportGeneral'])->name('rapport-general');
    });

    // Routes des autres modules (à développer par les autres étudiants)
    // Module Étudiants - NDEYE-FATIME-CISSE
    Route::middleware(['role:admin,professeur'])->prefix('etudiants')->name('etudiants.')->group(function () {
        // Routes à implémenter par NDEYE-FATIME-CISSE
    });

    // Module Professeurs - ADJA-BOTHIE-TALL
    Route::middleware(['role:admin'])->prefix('professeurs')->name('professeurs.')->group(function () {
        // Routes à implémenter par ADJA-BOTHIE-TALL
    });

    // Module Classes - KHADIIATOU-IBNOMAR-TALL
    Route::middleware(['role:admin,professeur'])->prefix('classes')->name('classes.')->group(function () {
        // Routes à implémenter par KHADIIATOU-IBNOMAR-TALL
    });

    // Module Notes - AMINATA-WANE
    Route::middleware(['role:admin,professeur'])->prefix('notes')->name('notes.')->group(function () {
        // Routes à implémenter par AMINATA-WANE
    });

    // Module Emploi du temps - ADJIA-BOTHIE-BALDE
    Route::middleware(['role:admin,professeur'])->prefix('emploi-temps')->name('emploi-temps.')->group(function () {
        // Routes à implémenter par ADJIA-BOTHIE-BALDE
    });
});