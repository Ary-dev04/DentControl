@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Minería de datos clínica</h1>
<p class="subtitle">
    Análisis estadístico y comportamiento de servicios y tratamientos en la clínica
</p>

<section class="kpi-grid">

    <div class="kpi-card blue">
        <i class="fa-solid fa-user"></i>
        <h2>{{ $totalPacientes }}</h2>
        <p>Total de pacientes</p>
    </div>

    <div class="kpi-card green">
        <i class="fa-solid fa-tooth"></i>
        <h2>{{ $citasProgramadas }}</h2>
        <p>Citas programadas</p>
    </div>

    <div class="kpi-card orange">
        <i class="fa-solid fa-calendar-check"></i>
        <h2>{{ $citasHoy }}</h2>
        <p>Citas de hoy</p>
    </div>

    <div class="kpi-card purple">
        <i class="fa-solid fa-dollar-sign"></i>
        <h2>${{ number_format($ingresosMes, 2) }}</h2>
        <p>Ingresos del mes</p>
    </div>

    <div class="kpi-card blue">
        <i class="fa-solid fa-mobile-screen-button"></i>
        <h2>{{ $accesosMoviles }}</h2>
        <p>Accesos móviles generados</p>
    </div>

</section>

<section class="card-section">
    <h3>Desglose de Ingresos por Concepto ($)</h3>
    <p style="font-size: 0.8rem; color: gray; margin-bottom: 10px;">Incluye servicios directos y procedimientos de tratamientos finalizados.</p>

    <table>
        <thead>
            <tr>
                <th>Concepto (Servicio/Tratamiento)</th>
                <th>Total realizados</th>
                <th>Ingresos totales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ingresosPorServicio as $item)
            <tr>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->cantidad }}</td>
                <td>${{ number_format($item->ingresos, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">No se han registrado cobros en citas finalizadas todavía.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="card-section">
    <h3>Rendimiento Mensual (Año {{ date('Y') }})</h3>

    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Ingresos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingresosPorMes as $mes)
            <tr>
                <td>{{ $mes['nombre'] }}</td>
                <td>${{ number_format($mes['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

<section class="card-section">
    <h3>Estatus General de Consultas</h3>

    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="estado activo">Programadas (Pendientes)</td>
                <td>{{ $citasProgramadas }}</td>
            </tr>
            <tr>
                <td class="estado finalizado">Finalizadas (Completadas)</td>
                <td>{{ $citasFinalizadas }}</td>
            </tr>
        </tbody>
    </table>
</section>

@endsection