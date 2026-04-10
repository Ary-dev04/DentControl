@extends('layouts.clinica')

@section('title', 'Panel de Control - Asistente')

@section('content')
    <h1>Bienvenido a DentControl, {{ auth()->user()->nombre }}</h1>
    <p class="subtitle">Gestión operativa de la clínica: <strong>{{ auth()->user()->clinica->nombre }}</strong></p>

    <section class="dashboard-cards">
        <div class="card card-blue">
            <h3>Total de pacientes</h3>
            <p class="number">{{ $totalPacientes }}</p>
        </div>

        <div class="card card-green">
            <h3>Tratamientos activos</h3>
            <p class="number">{{ $tratamientosActivos }}</p>
        </div>

        <div class="card card-orange">
            <h3>Citas de hoy</h3>
            {{-- Usamos $citasHoyCount para que coincida con el controlador --}}
            <p class="number">{{ $citasHoyCount }}</p>
        </div>
    </section>

    <section class="alerts-section">
        <div class="card card-alerts">
            <h3><i class="fa-solid fa-bell"></i> Tareas y Alertas Administrativas</h3>
            
            @if($alertas->isEmpty())
                <p class="alert-placeholder">No hay tareas pendientes por el momento.</p>
            @else
                <ul class="alert-list">
                    @foreach($alertas as $alerta)
                        <li class="alert-item">
                            {{-- Mostramos el icono que el controlador asigne (fa-user-pen, fa-cake-candles, etc) --}}
                            <i class="fa-solid {{ $alerta->icono ?? 'fa-circle-exclamation' }}"></i> 
                            {{ $alerta->mensaje }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

<style>
    .alert-list { list-style: none; padding: 0; margin-top: 15px; }
    .alert-item {
        background: #f0f7ff; /* Un azul muy claro para diferenciarlo de alertas críticas del doctor */
        border-left: 4px solid #3b82f6; 
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 4px;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-item i { color: #3b82f6; width: 20px; text-align: center; }
    .alert-placeholder { color: #666; font-style: italic; }
</style>
@endsection