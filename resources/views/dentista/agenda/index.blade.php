@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/agendaDentista.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<div class="app-container">

<main class="content">

<h1>Agenda del día</h1>

<div class="fecha-hoy" id="fechaActual"></div>

<div class="card">

<table class="data-table">

<thead>
<tr>
<th>Hora</th>
<th>Paciente</th>
<th>Tipo</th>
<th>Servicio / Tratamiento</th>
<th>Motivo</th>
<th>Estado</th>
</tr>
</thead>

<tbody>

@forelse($citas as $cita)

<tr>

<td>{{ $cita['hora'] }}</td>

<td>{{ $cita['paciente'] }}</td>

<td>

@if($cita['tipo'] == 'servicio')
<span class="tipo servicio">Servicio</span>
@else
<span class="tipo tratamiento">Tratamiento</span>
@endif

</td>

<td>{{ $cita['nombre'] }}</td>

<td>{{ $cita['motivo'] }}</td>

<td>

@if($cita['estado'] == 'pendiente')
<span class="estado pendiente">Pendiente</span>

@elseif($cita['estado'] == 'enproceso')
<span class="estado enproceso">En proceso</span>

@elseif($cita['estado'] == 'finalizado')
<span class="estado finalizado">Finalizado</span>

@else
<span class="estado pendiente">Pendiente</span>
@endif

</td>

</tr>
@empty

<tr>
<td colspan="6" style="text-align:center;">No hay citas hoy</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</main>

</div>

<script>

let hoy = new Date();

let opciones = {
weekday:'long',
year:'numeric',
month:'long',
day:'numeric'
};

document.getElementById("fechaActual").innerText =
hoy.toLocaleDateString('es-ES', opciones);

</script>

@endsection