@extends('layouts.admin')

@section('title', 'Reportes SaaS')

@section('content')

<link rel="stylesheet" href="{{ asset('css/reporte-saas.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Reportes Estratégicos de la Plataforma</h1>
<p class="subtitle">
    Análisis global del sistema DentControl
</p>

<section class="kpi-grid">

    <div class="kpi-card kpi-blue">
        <i class="fa-solid fa-hospital"></i>
        <h2>{{ $totalClinicas }}</h2>
        <p>Clínicas activas</p>
    </div>

    <div class="kpi-card kpi-green">
        <i class="fa-solid fa-users"></i>
        <h2>{{ $totalUsuarios }}</h2>
        <p>Usuarios activos</p>
    </div>

    <div class="kpi-card kpi-orange">
        <i class="fa-solid fa-tooth"></i>
        <h2>{{ $totalTratamientos }}</h2>
        <p>Tratamientos totales</p>
    </div>

    <div class="kpi-card kpi-purple">
        <i class="fa-solid fa-chart-pie"></i>
        <h2>{{ $promedioTratamientos }}</h2>
        <p>Promedio por clínica</p>
    </div>

</section>

<section class="report-card">

    <h3>Análisis de uso por clínica</h3>

    <table>
        <thead>
            <tr>
                <th>Clínica</th>
                <th>Usuarios</th>
                <th>Tratamientos</th>
                <th>Nivel de Actividad</th>
            </tr>
        </thead>

        <tbody>
            @forelse($clinicasDetalle as $clinica)
            <tr>
                <td>{{ $clinica->nombre }}</td>
                <td>{{ $clinica->usuarios_count }}</td>
                <td>{{ $clinica->tratamientos_count }}</td>
                <td>
                    @if($clinica->tratamientos_count >= 50)
                        <span class="status high" style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px;">Alta</span>
                    @elseif($clinica->tratamientos_count >= 10)
                        <span class="status medium" style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px;">Media</span>
                    @else
                        <span class="status low" style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px;">Baja</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">No hay clínicas registradas aún.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</section>

@endsection