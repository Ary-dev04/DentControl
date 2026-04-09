@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/tratamientos.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .search-results {
        position: absolute; z-index: 1000; width: 100%; background: white;
        border: 1px solid #ddd; border-radius: 0 0 8px 8px; display: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .result-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    .result-item:hover { background: #f0f4f8; }
    
    .nota-historial { 
        background: white; border-left: 4px solid #3b82f6; 
        padding: 12px; margin-bottom: 12px; border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .btn-primary:not(:disabled) {
        background-color: #059669 !important;
        transform: scale(1.02);
        transition: all 0.2s;
    }

    .btn-secondary {
        background-color: #64748b !important;
        color: white;
    }

    .is-invalid { border: 2px solid #ef4444 !important; }
    .error-msg {
        color: #ef4444;
        font-size: 0.8rem;
        display: none;
        margin-top: 5px;
    }
    .label-evolucion { color: #2563eb; font-weight: bold; }
    .label-indicaciones { color: #059669; font-weight: bold; }
</style>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<h1>Gestión de tratamientos</h1>
<p class="subtitle">Consulta y seguimiento de tratamientos por paciente</p>

<form action="{{ route('tratamientos.actualizar') }}" method="POST" id="formTratamiento">
    @csrf
    <input type="hidden" id="id_tratamiento_hidden" name="id_tratamiento" value="{{ old('id_tratamiento') }}">
    <input type="hidden" id="nombre_paciente_hidden" name="nombre_paciente_hidden" value="{{ old('nombre_paciente_hidden') }}">

    <section class="card-section">
        <h3>Buscar paciente</h3>
        <div class="form-row">
            <div class="form-group full-width" style="position: relative;">
                <label>Nombre del paciente</label>
                <input type="text" id="inputBuscarPaciente" placeholder="Escriba nombre o CURP..." autocomplete="off" value="{{ old('nombre_paciente_hidden') }}">
                <div id="listaResultados" class="search-results"></div>
            </div>
        </div>
    </section>

    <section class="card-section">
        <h3>Tratamientos del paciente</h3>
        <div class="form-group full-width">
            <label>Seleccione un tratamiento</label>
            <select id="selectTratamiento" name="id_tratamiento_select" disabled>
                <option value="">Seleccione un paciente primero...</option>
            </select>
        </div>
    </section>

    <section class="card-section">
        <h3>Detalle del tratamiento</h3>
        <div class="form-row">
            <div class="form-group full-width">
                <label>Diagnóstico clínico inicial</label>
                <textarea id="diagnostico" name="diagnostico_inicial" rows="2" readonly>{{ old('diagnostico_inicial') }}</textarea>
                <span class="error-msg" id="err-diagnostico">Caracteres no permitidos detectados.</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" readonly value="{{ old('fecha_inicio') }}">
            </div>
            <div class="form-group">
                <label>Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" readonly value="{{ old('fecha_fin') }}">
            </div>
            <div class="form-group">
                <label>Precio estimado</label>
                <input type="number" id="precio" name="precio_estimado" step="0.01" readonly value="{{ old('precio_estimado') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label><i class="fa-solid fa-clock-rotate-left"></i> Historial de evolución e indicaciones</label>
                <div id="divHistorialNotas" style="background: #f8fafc; padding: 15px; border-radius: 8px; max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0;">
                    <p class="text-muted" style="font-style: italic;">Seleccione un tratamiento para cargar el historial...</p>
                </div>
            </div>
        </div>

        <div class="form-row" style="margin-top: 15px;">
            <div class="form-group">
                <label class="label-evolucion">Evolución de hoy (Nota interna)</label>
                <textarea id="nota_evolucion" name="nota_texto" rows="4" placeholder="Pulse 'Editar' para escribir..." disabled></textarea>
                <span class="error-msg" id="err-nota">Caracteres no permitidos.</span>
            </div>
            <div class="form-group">
                <label class="label-indicaciones">Indicaciones para el paciente</label>
                <textarea id="indicaciones_hoy" name="indicaciones" rows="4" placeholder="Pulse 'Editar' para escribir..." disabled></textarea>
                <span class="error-msg" id="err-indicaciones">Caracteres no permitidos.</span>
            </div>
        </div>
    </section>

    <div class="form-actions">
        <button type="submit" class="btn-primary" id="btnGuardar" style="display: none;">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios y Nota
        </button>

        <button type="button" class="btn btn-secondary" id="btnDescartar" style="display: none;">
            <i class="fa-solid fa-rotate-left"></i> Descartar Cambios
        </button>

        <button type="button" class="btn btn-edit" id="btnHabilitarEdicion" disabled>
            <i class="fa-solid fa-pen"></i> Editar Información y Nota
        </button>

        <a href="{{ route('tratamientos.index') }}" class="btn btn-cancel">
            <i class="fa-solid fa-xmark"></i> Salir
        </a>
    </div>
</form>

<script>
let datosOriginales = {};

// Selectores
const form = document.getElementById('formTratamiento');
const inputBuscar = document.getElementById('inputBuscarPaciente');
const nombreHidden = document.getElementById('nombre_paciente_hidden');
const lista = document.getElementById('listaResultados');
const selectT = document.getElementById('selectTratamiento');
const divHistorial = document.getElementById('divHistorialNotas');
const btnGuardar = document.getElementById('btnGuardar');
const btnDescartar = document.getElementById('btnDescartar');
const btnEdit = document.getElementById('btnHabilitarEdicion');
const notaEvolucion = document.getElementById('nota_evolucion');
const indicacionesHoy = document.getElementById('indicaciones_hoy');
const diagnostico = document.getElementById('diagnostico');

// --- VALIDACIONES ---

function limpiarTexto(input, errorSpanId) {
    let valor = input.value;
    const errorSpan = document.getElementById(errorSpanId);
    const regex = /[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\-]/g;
    
    if (regex.test(valor)) {
        input.value = valor.replace(regex, '');
        errorSpan.style.display = 'block';
        setTimeout(() => { errorSpan.style.display = 'none'; }, 2000);
    }
    input.value = input.value.replace(/\s\s+/g, ' ');
}

diagnostico.addEventListener('input', () => limpiarTexto(diagnostico, 'err-diagnostico'));
notaEvolucion.addEventListener('input', () => limpiarTexto(notaEvolucion, 'err-nota'));
indicacionesHoy.addEventListener('input', () => limpiarTexto(indicacionesHoy, 'err-indicaciones'));

form.addEventListener('submit', function(e) {
    const fInicio = new Date(document.getElementById('fecha_inicio').value);
    const fFin = new Date(document.getElementById('fecha_fin').value);

    if (fFin < fInicio) {
        alert("La fecha de fin no puede ser anterior a la de inicio.");
        e.preventDefault();
    }
});

// --- LÓGICA DE INTERFAZ ---

function gestionarVisibilidadBoton() {
    const modoEdicionActivo = !diagnostico.readOnly;
    btnGuardar.style.display = modoEdicionActivo ? 'inline-block' : 'none';
    btnDescartar.style.display = modoEdicionActivo ? 'inline-block' : 'none';
    btnEdit.style.display = modoEdicionActivo ? 'none' : 'inline-block';
}

btnEdit.addEventListener('click', function() {
    ['diagnostico', 'fecha_inicio', 'fecha_fin', 'precio'].forEach(id => document.getElementById(id).readOnly = false);
    notaEvolucion.disabled = false;
    indicacionesHoy.disabled = false;
    gestionarVisibilidadBoton();
});

function resetModoEdicion() {
    ['diagnostico', 'fecha_inicio', 'fecha_fin', 'precio'].forEach(id => document.getElementById(id).readOnly = true);
    notaEvolucion.disabled = true;
    indicacionesHoy.disabled = true;
    gestionarVisibilidadBoton();
}

btnDescartar.addEventListener('click', function() {
    if(confirm('¿Deseas descartar los cambios?')) {
        diagnostico.value = datosOriginales.diagnostico_inicial || '';
        document.getElementById('fecha_inicio').value = datosOriginales.fecha_inicio || '';
        document.getElementById('fecha_fin').value = datosOriginales.fecha_fin || '';
        document.getElementById('precio').value = datosOriginales.precio_estimado || '';
        notaEvolucion.value = '';
        indicacionesHoy.value = '';
        resetModoEdicion();
    }
});

// --- AJAX ---

inputBuscar.addEventListener('input', function() {
    let query = this.value;
    if(query.length > 2) {
        fetch(`/buscar-paciente?q=${query}`)
            .then(res => res.json())
            .then(data => {
                lista.innerHTML = '';
                lista.style.display = 'block';
                data.forEach(p => {
                    let div = document.createElement('div');
                    div.className = 'result-item';
                    div.innerHTML = `<i class="fa-solid fa-user"></i> ${p.nombre} ${p.apellido_paterno}`;
                    div.onclick = () => seleccionarPaciente(p);
                    lista.appendChild(div);
                });
            });
    }
});

function seleccionarPaciente(p, autoIdTratamiento = null) {
    inputBuscar.value = p.apellido_paterno ? `${p.nombre} ${p.apellido_paterno}` : p.nombre;
    nombreHidden.value = inputBuscar.value;
    lista.style.display = 'none';

    fetch(`/obtener-tratamientos/${p.id_paciente}`)
        .then(res => res.json())
        .then(tratamientos => {
            selectT.disabled = false;
            selectT.innerHTML = '<option value="">Seleccione un tratamiento...</option>';
            tratamientos.forEach(t => {
                const selected = (autoIdTratamiento == t.id_tratamiento) ? 'selected' : '';
                selectT.innerHTML += `<option value="${t.id_tratamiento}" ${selected}>${t.nombre_tratamiento} — ${new Date(t.created_at).toLocaleDateString()}</option>`;
            });
            if(autoIdTratamiento) cargarDetalle(autoIdTratamiento);
        });
}

function cargarDetalle(id) {
    fetch(`/detalle-tratamiento/${id}`)
        .then(res => res.json())
        .then(data => {
            const t = data.tratamiento;
            const notas = data.notas;
            datosOriginales = { ...t };

            document.getElementById('id_tratamiento_hidden').value = t.id_tratamiento;
            diagnostico.value = t.diagnostico_inicial || '';
            document.getElementById('fecha_inicio').value = t.fecha_inicio || '';
            document.getElementById('fecha_fin').value = t.fecha_fin || '';
            document.getElementById('precio').value = t.precio_estimado || '';
            
            divHistorial.innerHTML = '';
            if(notas && notas.length > 0) {
                notas.forEach(n => {
                    divHistorial.innerHTML += `
                        <div class="nota-historial">
                            <small><b><i class="fa-regular fa-calendar"></i> ${n.fecha} — ${n.hora}</b></small>
                            <p style="margin-top:5px;"><b>Evolución:</b> ${n.nota_texto || 'Sin nota'}</p>
                            ${n.indicaciones ? `<p style="color: #059669;"><b>Indicaciones:</b> ${n.indicaciones}</p>` : ''}
                        </div>`;
                });
            } else {
                divHistorial.innerHTML = '<p class="text-muted">No hay notas registradas.</p>';
            }
            btnEdit.disabled = false;
            resetModoEdicion();
        });
}

selectT.addEventListener('change', function() {
    if(this.value) cargarDetalle(this.value);
});

window.onload = function() {
    const lastIdTratamiento = "{{ session('last_id') }}";
    const lastIdPaciente = "{{ session('last_paciente_id') }}";
    const lastNombrePaciente = "{{ old('nombre_paciente_hidden') }}";
    
    if(lastIdTratamiento && lastIdPaciente) {
        inputBuscar.value = lastNombrePaciente;
        nombreHidden.value = lastNombrePaciente;
        seleccionarPaciente({id_paciente: lastIdPaciente, nombre: lastNombrePaciente}, lastIdTratamiento);
    }
};
</script>
@endsection