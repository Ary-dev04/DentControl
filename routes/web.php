<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClinicaController; 
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Clinica\DashboardController;
use App\Http\Controllers\Clinica\CatalogoController;
use App\Http\Controllers\Clinica\PacienteController;
use App\Http\Controllers\Clinica\HistorialController;
use App\Http\Controllers\Clinica\AccesoMovilController;
use App\Http\Controllers\Clinica\AgendaController;
use App\Http\Controllers\Clinica\DentistaAgendaController;
use App\Http\Controllers\Dentista\TratamientoController;
use App\Http\Controllers\Asistente\GestionAppController;
use App\Http\Controllers\Dentista\ReporteController;
use App\Http\Controllers\Admin\ReporteSaasController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// --- RUTAS COMPARTIDAS ---
Route::post('/agenda/iniciar/{id}', [App\Http\Controllers\Clinica\AgendaController::class, 'iniciarCita'])->name('agenda.iniciar');
Route::get('/asistente/historial/{id}', [HistorialController::class, 'verHistorial'])->name('paciente.historial');
Route::get('/pacientes/buscar-ajax', [HistorialController::class, 'buscar'])->name('pacientes.buscar_ajax');
Route::get('/asistente/historial', [HistorialController::class, 'index'])->name('asistente.historial');
Route::post('/asistente/historial/guardar/{id}', [HistorialController::class, 'guardar'])->name('paciente.historial.guardar');

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

    //Reporte
    Route::get('/admin/reportes', [ReporteSaasController::class, 'index'])->name('admin.reportes');

});

// 2. DENTISTAS (Gestión de su propia clínica)
Route::middleware(['auth', 'can:dentista-only'])->group(function () {
    Route::get('/dentista/dashboard', [DashboardController::class, 'index'])->name('dentista.dashboard');

    // Vistas principales
    Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index');

    // Rutas para Servicios
    Route::post('/catalogos/servicios', [CatalogoController::class, 'storeServicio'])->name('servicios.store');
    Route::put('/catalogos/servicios/{id}', [CatalogoController::class, 'updateServicio'])->name('servicios.update');
    
    Route::patch('/catalogos/servicios/{id}/toggle', [CatalogoController::class, 'toggleServicio'])->name('servicios.toggle');

    // Rutas para Tratamientos
    Route::post('/catalogos/tratamientos', [CatalogoController::class, 'storeTratamiento'])->name('tratamientos.store');
    Route::put('/catalogos/tratamientos/{id}', [CatalogoController::class, 'updateTratamiento'])->name('tratamientos.update');


    Route::get('/dentista/tratamientos', [TratamientoController::class, 'index'])->name('dentista.tratamientos');
    
    Route::patch('/catalogos/tratamientos/{id}/toggle', [CatalogoController::class, 'toggleTratamiento'])->name('tratamientos.toggle');

    //Agenda
    Route::get('/dentista/agenda', [DentistaAgendaController::class, 'index'])->name('dentista.agenda');

    Route::post('/historial/nota/guardar', [HistorialController::class, 'guardarNota'])->name('notas.guardar');

    // Ruta para actualizar el precio desde el historial
    Route::post('/historial/actualizar-precio/{id}', [HistorialController::class, 'actualizarPrecioTratamiento'])->name('paciente.historial.precio');

    Route::get('/dentista/reportes', [ReporteController::class, 'index'])->name('dentista.reportes');
});

// 3. ASISTENTES (Agenda y recepción)
Route::middleware(['auth', 'can:asistente-only'])->group(function () {
    
    
    Route::get('/asistente/dashboard', [DashboardController::class, 'index'])->name('asistente.dashboard');
    
   
    Route::get('/asistente/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
    
    Route::post('/asistente/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
    Route::post('/pacientes/store-cita-existente', [PacienteController::class, 'storeCitaExistente'])->name('pacientes.store_cita_existente');
    
    
    //Route::get('/asistente/pacientes/{id}/tratamientos', [PacienteController::class, 'getTratamientosActivos'])->name('pacientes.tratamientos');
    //Route::get('/asistente/pacientes/{id}/tratamientos-activos', [PacienteController::class, 'tratamientosActivos'])->name('pacientes.tratamientos_activos');

    Route::get('/obtener-duracion', [App\Http\Controllers\Clinica\PacienteController::class, 'obtenerDuracion'])->name('pacientes.duracion');
    Route::get('/pacientes/{id}/tratamientos-activos', [PacienteController::class, 'tratamientosActivos']);
    Route::get('/api/citas-ocupadas', [PacienteController::class, 'getCitasOcupadas'])->name('citas.ocupadas');
    Route::get('/validar-cita-duplicada', [App\Http\Controllers\Clinica\PacienteController::class, 'validarCitaDuplicada']);

    // 1. Ruta para el menú (entrada general)
    //Route::get('/asistente/historial', [HistorialController::class, 'index'])->name('asistente.historial');

    // 2. Ruta para ver un paciente específico (desde la tabla)
    //Route::get('/asistente/historial/{id}', [HistorialController::class, 'verHistorial'])->name('paciente.historial');

    // Ruta para procesar el guardado del expediente
    //Route::post('/asistente/historial/guardar/{id}', [HistorialController::class, 'guardar'])->name('paciente.historial.guardar');

    // Ruta para el buscador en tiempo real (AJAX)

//Route::get('/pacientes/buscar-ajax', [HistorialController::class, 'buscar'])->name('pacientes.buscar_ajax');

Route::get('/asistente/buscar-paciente-acceso', [AccesoMovilController::class, 'buscarPacientesAcceso'])->name('pacientes.buscar_acceso_ajax');
    Route::get('/asistente/acceso-movil', [AccesoMovilController::class, 'index'])->name('acceso.index');
Route::post('/asistente/acceso-movil/habilitar', [AccesoMovilController::class, 'habilitarAcceso'])->name('acceso.habilitar');


// Agenda del día
    //Route::get('/asistente/agenda', [App\Http\Controllers\Clinica\AgendaController::class, 'index'])->name('asistente.agenda');
    Route::get('/agenda', [AgendaController::class, 'index'])->name('asistente.agenda');
    Route::post('/agenda/finalizar/{id}', [AgendaController::class, 'finalizarCita']);
    //Route::post('/agenda/iniciar/{id}', [App\Http\Controllers\Clinica\AgendaController::class, 'iniciarCita'])->name('agenda.iniciar');
    Route::post('/agenda/cancelar/{id}', [AgendaController::class, 'cancelar'])->name('agenda.cancelar');

    // --- MÓDULO 2: GESTIÓN Y MANTENIMIENTO (GestionAppController) ---
    // Vista principal de la tabla de usuarios registrados
    Route::get('/asistente/gestion-app', [GestionAppController::class, 'index'])->name('asistente.gestion-app');
    // Acción para regenerar y reenviar (POST)
    Route::post('/asistente/gestion-app/reenviar/{id}', [GestionAppController::class, 'reenviar'])->name('acceso.reenviar');
    
    // Acción para activar/desactivar (POST)
    Route::post('/asistente/gestion-app/estatus/{id}', [GestionAppController::class, 'cambiarEstatus'])->name('acceso.estatus');

});