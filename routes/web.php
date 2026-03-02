<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClinicaController; 
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Clinica\DashboardController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTAS PROTEGIDAS POR ROL ---

// 1. SUPER ADMIN (Dueño del SaaS)
Route::middleware(['auth', 'can:admin-only'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Clínicas (Gestión manual para que coincida con tus métodos AJAX/Modal)
    Route::get('/clinicas', [ClinicaController::class, 'index'])->name('clinicas.index');
    Route::post('/clinicas', [ClinicaController::class, 'store'])->name('clinicas.store');
    Route::get('/clinicas/{id}/edit', [ClinicaController::class, 'edit'])->name('clinicas.edit');
    Route::put('/clinicas/{id}', [ClinicaController::class, 'update'])->name('clinicas.update');
    Route::patch('/clinicas/{id}/toggle', [ClinicaController::class, 'toggleStatus'])->name('clinicas.toggle');

    // Usuarios (Gestión manual)
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{id}/toggle', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle');
});

// 2. DENTISTAS (Gestión de su propia clínica)
Route::middleware(['auth', 'can:dentista-only'])->group(function () {
    Route::get('/dentista/dashboard', [DashboardController::class, 'index'])->name('dentista.dashboard');
    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
});

// 3. ASISTENTES (Agenda y recepción)
Route::middleware(['auth', 'can:asistente-only'])->group(function () {
    Route::get('/asistente/dashboard', [DashboardController::class, 'index'])->name('asistente.dashboard');
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
});