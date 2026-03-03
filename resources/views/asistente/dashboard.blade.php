@extends('layouts.clinica')

@section('title', 'Panel de Control - Asistente')

@section('content')
    <h1>Bienvenido a DentControl, Asistente {{ auth()->user()->nombre }}</h1>
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
            <p class="number">{{ $citasHoy }}</p>
        </div>
    </section>

    <section class="alerts-section">
        <div class="card card-alerts">
            <h3>Alertas importantes</h3>
            @if($alertas->isEmpty())
                <p class="alert-placeholder">No hay alertas registradas por el momento</p>
            @else
                <ul class="alert-list">
                    @foreach($alertas as $alerta)
                        <li><i class="fa-solid fa-circle-exclamation"></i> {{ $alerta->mensaje }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection