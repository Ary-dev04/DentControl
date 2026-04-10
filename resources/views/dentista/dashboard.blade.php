@extends('layouts.clinica')

@section('title', 'Bienvenido | DentControl')

@section('content')
    <h1>Bienvenido a DentControl, Dr. {{ auth()->user()->nombre }}</h1>
    <p class="subtitle">Resumen general de la clínica: <strong>{{ auth()->user()->clinica->nombre }}</strong></p>

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
            {{-- Ajustado de $citasHoy a $citasHoyCount para que coincida con el controlador --}}
            <p class="number">{{ $citasHoyCount }}</p>
        </div>
    </section>

    <section class="alerts-section">
        <div class="card card-alerts">
            <h3><i class="fa-solid fa-bell"></i> Alertas importantes</h3>
            
            @if($alertas->isEmpty())
                <p class="alert-placeholder">Todo está en orden. No hay alertas pendientes.</p>
            @else
                <ul class="alert-list">
                    @foreach($alertas as $alerta)
                        <li class="alert-item">
                            {{-- Usamos el icono dinámico que definimos en el controlador --}}
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
        background: #fff5f5;
        border-left: 4px solid #ff4d4d;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 4px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.2s;
    }
    .alert-item:hover {
        transform: translateX(5px);
    }
    .alert-item i { 
        color: #ff4d4d; 
        width: 20px;
        text-align: center;
    }
    .alert-placeholder {
        color: #666;
        font-style: italic;
        margin-top: 10px;
    }
</style>
@endsection