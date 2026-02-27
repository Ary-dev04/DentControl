<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClinicaController; 
use App\Http\Controllers\Admin\UsuarioController;

// Mostrar login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// Procesar login (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard protegido
Route::get('/dashboard', [AdminController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Dashboard
Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

// Clínicas
Route::get('/clinicas/crear', [ClinicaController::class, 'create'])->name('clinicas.create');
Route::post('/clinicas', [ClinicaController::class, 'store'])->name('clinicas.store');

// Usuarios
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
// Obtener datos del usuario para el modal
Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
// Procesar la actualización
Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');