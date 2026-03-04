@extends('layouts.admin')

@section('title', 'Inicio | DentControl SaaS')

@section('content')
    <h1>Panel administrativo SaaS</h1>
    <p class="subtitle">Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido_paterno }}</p>

    <section class="saas-cards">

      <div class="card card-blue">
        <h3>Clínicas activas</h3>
        <!--<div class="number">{{ $totalClinicas ?? 0 }}</div>-->
        <div class="number">{{ $totalClinicasActivas ?? 0 }}</div>
      </div>

      <div class="card card-green">
        <h3>Usuarios activos</h3>
        <div class="number">{{ $totalUsuariosActivos ?? 0 }}</div>
        <!--<div class="number">{{ $totalUsuario?? 0 }}</div>-->
      </div>

      <div class="card card-orange">
        <h3>Pacientes totales</h3>
        <div class="number">{{ $totalPacientes ?? 0 }}</div>
      </div>

      <div class="card card-red">
        <h3>Alertas del sistema</h3>
        <div class="number">0</div>
      </div>

    </section>

    <section class="alerts-section">
      <h2>Últimas Clínicas Integradas</h2>
      <div class="alerts-container">
        @if(isset($ultimasClinicas) && $ultimasClinicas->count() > 0)
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Nombre</th>
                        <th style="padding: 10px;">RFC</th>
                        <th style="padding: 10px;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasClinicas as $clinica)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">{{ $clinica->nombre }}</td>
                        <td style="padding: 10px;">{{ $clinica->rfc }}</td>
                        <td style="padding: 10px;">{{ $clinica->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay clínicas registradas actualmente.</p>
        @endif
      </div>
    </section>
@endsection