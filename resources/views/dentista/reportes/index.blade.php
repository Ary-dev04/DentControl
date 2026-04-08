@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Minería de datos clínica</h1>
<p class="subtitle">
  Análisis estadístico y comportamiento de servicios en la clínica
</p>

<!-- ================= KPI ================= -->
<section class="kpi-grid">

  <div class="kpi-card blue">
    <i class="fa-solid fa-user"></i>
    <h2>120</h2>
    <p>Total de pacientes</p>
  </div>

  <div class="kpi-card green">
    <i class="fa-solid fa-tooth"></i>
    <h2>45</h2>
    <p>Tratamientos activos</p>
  </div>

  <div class="kpi-card orange">
    <i class="fa-solid fa-calendar-check"></i>
    <h2>8</h2>
    <p>Citas de hoy</p>
  </div>

  <div class="kpi-card purple">
    <i class="fa-solid fa-dollar-sign"></i>
    <h2>$85,000</h2>
    <p>Ingresos del mes</p>
  </div>

  <div class="kpi-card blue">
    <i class="fa-solid fa-mobile-screen-button"></i>
    <h2>32</h2>
    <p>Accesos móviles generados</p>
  </div>

</section>

<!-- ================= INGRESOS POR SERVICIO ================= -->
<section class="card-section">
  <h3>Ingresos por servicio ($)</h3>

  <table>
    <thead>
      <tr>
        <th>Servicio</th>
        <th>Total realizados</th>
        <th>Ingresos</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Limpieza dental</td>
        <td>45</td>
        <td>$18,000</td>
      </tr>
      <tr>
        <td>Resina</td>
        <td>30</td>
        <td>$25,000</td>
      </tr>
      <tr>
        <td>Ortodoncia</td>
        <td>18</td>
        <td>$30,000</td>
      </tr>
      <tr>
        <td>Extracción</td>
        <td>25</td>
        <td>$12,000</td>
      </tr>
    </tbody>
  </table>
</section>

<!-- ================= INGRESOS POR MES ================= -->
<section class="card-section">
  <h3>Ingresos por mes</h3>

  <table>
    <thead>
      <tr>
        <th>Mes</th>
        <th>Ingresos</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Enero</td><td>$20,000</td></tr>
      <tr><td>Febrero</td><td>$25,000</td></tr>
      <tr><td>Marzo</td><td>$30,000</td></tr>
      <tr><td>Abril</td><td>$28,000</td></tr>
    </tbody>
  </table>
</section>

<!-- ================= ESTADO ================= -->
<section class="card-section">
  <h3>Estado de tratamientos</h3>

  <table>
    <thead>
      <tr>
        <th>Estado</th>
        <th>Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="estado activo">Activos</td>
        <td>45</td>
      </tr>
      <tr>
        <td class="estado finalizado">Finalizados</td>
        <td>62</td>
      </tr>
    </tbody>
  </table>
</section>

@endsection