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
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $hayPacienteEnAtencion = $citas->contains('estatus_cita', 'enproceso');
                    @endphp
                    @forelse($citas as $cita)
                    <tr data-id="{{ $cita->id_cita }}">
                        <td >{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</td>
                        <td>{{ $cita->paciente->nombre }} {{ $cita->paciente->apellido_paterno }}</td>
                        <td class="tipoCita">
                            @php $esTrat = (bool)$cita->id_tratamiento; @endphp
                            @if($esTrat)
                                <span class="tipo tipo-tratamiento">Tratamiento</span>
                            @else
                                <span class="tipo tipo-servicio">Servicio</span>
                            @endif
                        </td>
                        <td>
                            @if($esTrat)
                                {{ $cita->tratamiento->catalogoTratamiento->nombre ?? 'Tratamiento' }}
                            @else
                                {{ $cita->servicio->nombre ?? 'Consulta' }}
                            @endif
                        </td>
                        <td>{{ $cita->motivo_consulta }}</td>
                        
                        {{-- COLUMNA ESTATUS CON HORAS REALES --}}
                        <td class="estado-celda">
                            <span class="estado {{ $cita->estatus_cita }}">
                                {{ $cita->estatus_cita == 'enproceso' ? 'En proceso' : ucfirst($cita->estatus_cita) }}
                            </span>
                            
                            @if($cita->estatus_cita == 'enproceso' && $cita->hora_inicio_real)
                                <div style="font-size: 0.7rem; color: #3b82f6; margin-top: 5px;">
                                    <i class="fa-solid fa-clock"></i> Inició: {{ \Carbon\Carbon::parse($cita->hora_inicio_real)->format('h:i A') }}
                                </div>
                            @elseif($cita->estatus_cita == 'finalizada')
                                <div style="font-size: 0.7rem; color: #18191a; margin-top: 5px;">
                                    <i class="fa-solid fa-hourglass-half"></i> Duración: {{ $cita->duracion }} min
                                </div>
                            @endif
                        </td>

                        <td class="acciones">
                            <div class="acciones-wrapper">
                                @if($cita->estatus_cita == 'programada')
                                    <div class="grupo-botones">
                                        @if(!$hayPacienteEnAtencion)
                                            <button class="btn btn-iniciar" onclick="iniciarCitaBD(this, {{ $cita->id_cita }})">
                                                <i class="fa-solid fa-play"></i> Iniciar
                                            </button>
                                        @else
                                            <span class="texto-ocupado" title="El doctor ya está atendiendo a un paciente">
                                                <i class="fa-solid fa-lock"></i> Doctor Ocupado
                                            </span>
                                        @endif
                                        <button class="btn-circular btn-cancelar" onclick="cancelarCita({{ $cita->id_cita }})" title="Cancelar">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                @elseif($cita->estatus_cita == 'enproceso')
                                    <div class="grupo-botones">
                                        <button class="btn btn-finalizar" 
                                            onclick="abrirCobro({{ $cita->id_cita }}, {{ $esTrat ? 'true' : 'false' }}, '{{ $esTrat ? ($cita->tratamiento?->catalogoTratamiento?->nombre ?? 'Tratamiento') : ($cita->servicio?->nombre ?? 'Consulta') }}', {{ $esTrat ? ($cita->tratamiento?->precio_estimado ?? 0) : 0 }})">
                                            <i class="fa-solid fa-flag-checkered"></i> Finalizar
                                        </button>
                                        <button class="btn-circular btn-cancelar" onclick="cancelarCita({{ $cita->id_cita }})" title="Cancelar">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                @elseif($cita->estatus_cita == 'finalizada')
                                    <div class="status-cobrado" style="display: flex; flex-direction: column; align-items: center;">
                                        <span class="badge-exito"><i class="fa-solid fa-circle-check"></i> Cobrado</span>
                                        <span class="monto" style="font-size: 0.85rem; font-weight: bold; color: #16a34a;">
                                            ${{ number_format($cita->monto_cobrado, 2) }}
                                        </span>
                                        <small style="color: #1b1b1c; font-size: 0.65rem;">
                                            Fin: {{ \Carbon\Carbon::parse($cita->hora_final_real)->format('h:i A') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding: 20px;">No hay citas agendadas para hoy</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>


<div class="modal" id="modalCobro">
    <div class="modal-content">
        <a href="#" class="close-btn" onclick="cerrarModal()"><i class="fa-solid fa-xmark"></i></a>
        <h3>Finalizar y Cobrar</h3>
        
        <input type="hidden" id="modal_id_cita">

        <div id="infoContenedor" style="margin-bottom: 20px;">
            <div id="infoServicio" style="display:none;">
                <p style="color: #64748b; font-size: 0.9em; margin-bottom: 5px;">Servicio:</p>
                <h4 id="txtServicioNombre" style="margin:0; color: #1e293b;"></h4>
            </div>

            <div id="infoTratamiento" style="display:none; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #cbd5e1;">
                <h4 id="txtTratNombre" style="margin:0 0 10px 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;"></h4>
                <p style="margin: 5px 0; font-size: 0.9em; color: #475569;">
                    <i class="fa-solid fa-file-invoice-dollar"></i> 
                    <strong>Presupuesto Total Estimado:</strong> 
                    <span id="txtPrecioEstimadoTotal" style="color: #0f172a; font-weight: bold;"></span>
                </p>
                <small style="display:block; margin-top: 5px; color: #64748b; line-height: 1.2;">
                    * El presupuesto es global. Ingrese abajo el pago de la sesión de hoy.
                </small>
            </div>
        </div>

        <div class="form-group">
            <label style="font-weight: bold; display: block; margin-bottom: 8px; color: #1e293b;">Monto cobrado hoy ($)</label>
            <input type="number" id="monto_cobro" class="form-control" step="0.01" placeholder="0.00" 
                   style="font-size: 1.4em; height: 50px; border: 2px solid #3b82f6; text-align: center;">
        </div>

        <div id="checkFinalizarContainer" style="display:none; margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
            <label style="display: flex; align-items: center; cursor: pointer; background: #fffbeb; padding: 10px; border-radius: 6px; border: 1px solid #fef3c7;">
                <input type="checkbox" id="check_finalizar_trat" style="width: 20px; height: 20px; margin-right: 12px;">
                <span style="font-size: 0.9em; color: #92400e;">
                    ¿El paciente terminó su tratamiento <strong>completamente</strong> hoy?
                </span>
            </label>
        </div>

        <button class="btn-primary" style="width:100%; margin-top:25px; padding: 15px; font-weight: bold;" onclick="guardarCobroFinal()">
            REGISTRAR COBRO Y FINALIZAR
        </button>
    </div>
</div>

<script>
// Fecha actual
document.getElementById("fechaActual").innerText = new Date().toLocaleDateString('es-ES', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

// 1. Iniciar Cita en BD
function iniciarCitaBD(boton, idCita) {
    boton.disabled = true;
    boton.innerText = "Iniciando...";

    fetch(`/agenda/iniciar/${idCita}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) location.reload();
        else alert("Error al iniciar");
    })
    .catch(err => console.error(err));
}

// 2. Abrir Modal Dinámico (CORREGIDO PARA EVITAR MEZCLA DE BLOQUES)
function abrirCobro(id, esTratamiento, nombre, precioTotal) {
    document.getElementById("modal_id_cita").value = id;
    document.getElementById("monto_cobro").value = "";
    document.getElementById("check_finalizar_trat").checked = false;
    
    // Quitamos bordes rojos de errores previos si los hubiera
    document.getElementById("monto_cobro").style.borderColor = "#3b82f6";

    // Lógica de visibilidad estricta
    if (esTratamiento === true || esTratamiento === 'true') {
        document.getElementById("infoTratamiento").style.display = "block";
        document.getElementById("infoServicio").style.display = "none";
        document.getElementById("checkFinalizarContainer").style.display = "block";
        
        document.getElementById("txtTratNombre").innerText = nombre;
        document.getElementById("txtPrecioEstimadoTotal").innerText = "$" + parseFloat(precioTotal).toLocaleString('es-MX', {minimumFractionDigits: 2});
    } else {
        document.getElementById("infoTratamiento").style.display = "none";
        document.getElementById("infoServicio").style.display = "block";
        document.getElementById("checkFinalizarContainer").style.display = "none";
        
        document.getElementById("txtServicioNombre").innerText = nombre;
    }

    window.location.hash = "modalCobro";
    setTimeout(() => document.getElementById("monto_cobro").focus(), 200);
}

function cerrarModal() {
    // Esto quita el #modalCobro de la barra de direcciones sin recargar
    history.replaceState(null, null, window.location.pathname);
    window.location.hash = ""; 
}

// 3. Guardar en BD (CON VALIDACIÓN DE NO CERO)
function guardarCobroFinal() {
    const id = document.getElementById("modal_id_cita").value;
    const montoInput = document.getElementById("monto_cobro");
    const monto = parseFloat(montoInput.value);
    const finTrat = document.getElementById("check_finalizar_trat").checked ? 1 : 0;

    // VALIDACIÓN: No permite vacío, 0 o negativos
    if(!monto || monto <= 0) {
        alert("El monto a cobrar debe ser mayor a 0.");
        montoInput.style.borderColor = "red";
        montoInput.focus();
        return; // Detiene la ejecución
    }

    fetch(`/agenda/finalizar/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            monto_cobrado: monto, 
            finalizar_tratamiento: finTrat 
        })
    })
    .then(res => res.json())
    .then(data => { 
        if(data.success) {
            // PRIMERO: Quitamos el hash de la URL para que no se reabra
            history.replaceState(null, null, window.location.pathname);
            location.reload(); 
        } else {
            alert("Error al procesar el pago en el servidor.");
        }
    })
    .catch(err => alert("Error de conexión al guardar el cobro."));
}

// 4. Cancelar Cita
function cancelarCita(idCita) {
    if (confirm("¿Está seguro de que desea cancelar esta cita? Esta acción no se puede deshacer.")) {
        fetch(`/agenda/cancelar/${idCita}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Cita cancelada correctamente.");
                location.reload();
            } else {
                alert("Error al cancelar la cita: " + (data.message || "Error desconocido"));
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error de conexión al intentar cancelar.");
        });
    }
}
</script>
@endsection