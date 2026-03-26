@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/agenda.css') }}">
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
<th>Acciones</th>
</tr>
</thead>

<tbody>

@forelse($citas as $cita)

<tr data-tipo="{{ $cita->tipo }}">

<td>{{ $cita->hora }}</td>

<td>
{{ $cita->paciente->nombre }}
{{ $cita->paciente->apellido_paterno }}
</td>

<td class="tipoCita">
@if($cita->tipo == 'servicio')
<span class="tipo tipo-servicio">Servicio</span>
@else
<span class="tipo tipo-tratamiento">Tratamiento</span>
@endif
</td>

<td>{{ $cita->nombre_servicio }}</td>

<td>{{ $cita->motivo }}</td>

<td>
<span class="estado pendiente">Pendiente</span>
</td>

<td>
<button class="btn btn-iniciar" onclick="cambiarEstado(this,'enproceso')">
Iniciar
</button>
</td>

</tr>

@empty

<tr>
<td colspan="7">No hay citas hoy</td>
</tr>

@endforelse

</tbody>
</table>

</div>

</main>
</div>


<!-- MODAL COBRO -->

<div class="modal" id="modalCobro">

<div class="modal-content">

<a href="#" class="close-btn">
<i class="fa-solid fa-xmark"></i>
</a>

<h3>Cerrar cita y cobrar</h3>

<!-- SERVICIO -->

<div id="bloqueServicio">

<div class="form-group">
<label>Servicio</label>
<input type="text" id="servicioNombre" disabled>
</div>

<div class="form-group">
<label>Precio sugerido</label>
<input type="text" id="precioServicio" disabled>
</div>

<div class="form-group">
<label>Monto a cobrar hoy</label>
<input type="number">
</div>

<div class="form-group">
<label>Método de pago</label>
<select>
<option>Efectivo</option>
<option>Tarjeta</option>
<option>Transferencia</option>
</select>
</div>

</div>


<!-- TRATAMIENTO -->

<div id="bloqueTratamiento">

<div class="resumen-tratamiento">

<h4 id="tratamientoNombre"></h4>

<p><strong>Presupuesto:</strong> <span id="presupuesto"></span></p>

<p><strong>Pagado:</strong> <span id="pagado"></span></p>

<p><strong>Saldo:</strong> <span id="saldo"></span></p>

</div>

<div class="form-group">
<label>Monto a cobrar hoy</label>
<input type="number">
</div>

<div class="form-group">
<label>Método de pago</label>
<select>
<option>Efectivo</option>
<option>Tarjeta</option>
<option>Transferencia</option>
</select>
</div>

<div class="form-group">

<label>
<input type="checkbox">
 Finalizar tratamiento con esta cita
</label>

</div>

</div>

<button class="btn-primary">
Registrar pago
</button>

</div>

</div>

<script>

let hoy=new Date()

let opciones={
weekday:'long',
year:'numeric',
month:'long',
day:'numeric'
}

document.getElementById("fechaActual").innerText=
hoy.toLocaleDateString('es-ES',opciones)



function cambiarEstado(boton,nuevoEstado){

let fila=boton.closest("tr")
let estadoCell=fila.querySelector(".estado")
let accionCell=boton.parentElement
let tipo=fila.dataset.tipo


if(nuevoEstado==="enproceso"){

estadoCell.className="estado enproceso"
estadoCell.innerText="En proceso"

boton.className="btn btn-finalizar"
boton.innerText="Finalizar"

boton.onclick=function(){
cambiarEstado(this,"finalizado")
}

}

else if(nuevoEstado==="finalizado"){

estadoCell.className="estado finalizado"
estadoCell.innerText="Finalizado"


accionCell.innerHTML=`
<button class="btn btn-cobrar" onclick="abrirCobro('${tipo}')">
<i class="fa-solid fa-cash-register"></i> Cobrar
</button>
`

}

}


function abrirCobro(tipo){

let servicio=document.getElementById("bloqueServicio")
let tratamiento=document.getElementById("bloqueTratamiento")

if(tipo === "servicio"){

servicio.style.display="block"
tratamiento.style.display="none"

}else{

servicio.style.display="none"
tratamiento.style.display="block"

}

location.hash="modalCobro"

}
</script>

@endsection