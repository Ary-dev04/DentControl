@extends('layouts.admin')

@section('title', 'Reportes SaaS')

@section('content')

<link rel="stylesheet" href="{{ asset('css/reporte-saas.css') }}">

<h1>Reportes Estratégicos de la Plataforma</h1>
<p class="subtitle">
  Análisis global del sistema DentControl
</p>

<!-- KPIs -->
<section class="kpi-grid">

  <div class="kpi-card kpi-blue">
    <i class="fa-solid fa-hospital"></i>
    <h2>12</h2>
    <p>Clínicas registradas</p>
  </div>

  <div class="kpi-card kpi-green">
    <i class="fa-solid fa-users"></i>
    <h2>48</h2>
    <p>Usuarios activos</p>
  </div>

  <div class="kpi-card kpi-orange">
    <i class="fa-solid fa-tooth"></i>
    <h2>325</h2>
    <p>Tratamientos registrados</p>
  </div>

  <div class="kpi-card kpi-purple">
    <i class="fa-solid fa-chart-pie"></i>
    <h2>27</h2>
    <p>Promedio por clínica</p>
  </div>

</section>

<!-- TABLA -->
<section class="report-card">

  <h3>Análisis de uso por clínica</h3>

  <table>
    <thead>
      <tr>
        <th>Clínica</th>
        <th>Usuarios</th>
        <th>Tratamientos</th>
        <th>Nivel</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td>Clínica Sonrisas</td>
        <td>5</td>
        <td>42</td>
        <td class="status high">Alta</td>
      </tr>
    </tbody>
  </table>

</section>

@endsection