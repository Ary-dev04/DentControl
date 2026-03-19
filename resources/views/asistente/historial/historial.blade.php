@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/historial.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Expediente Clínico</h1>

<section class="card-section" style="margin-bottom: 20px;">
    <div class="search-container" style="position: relative; max-width: 600px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
        <input type="text" id="inputBuscarPaciente" placeholder="Buscar otro paciente (Nombre o CURP)..." 
               style="width: 100%; padding: 10px 15px 10px 45px; border-radius: 8px; border: 1px solid #cbd5e1;">
        <div id="listaSugerencias" class="sugerencias-dropdown" style="display: none; position: absolute; width: 100%; background: white; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px;"></div>
    </div>
</section>

@if($paciente)
    @if($expediente && $expediente->alergias && strtolower($expediente->alergias) != 'ninguna')
        <div style="background: #fee2e2; border-left: 6px solid #ef4444; padding: 15px; margin-bottom: 20px; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.5rem; color: #ef4444;"></i>
            <div>
                <strong style="color: #991b1b; display: block;">ALERGIAS DETECTADAS:</strong> 
                <span style="color: #b91c1c; font-weight: bold; text-transform: uppercase;">{{ $expediente->alergias }}</span>
            </div>
        </div>
    @endif

    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="color: #2563eb; margin: 0;">Información del Paciente</h3>
            <span style="background: #e2e8f0; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: bold;">ID: #{{ $paciente->id_paciente }}</span>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" value="{{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}" disabled style="background: #f8fafc;">
            </div>
            <div class="form-group">
                <label>CURP</label>
                <input type="text" value="{{ $paciente->curp }}" disabled style="background: #f8fafc;">
            </div>
            <div class="form-group">
                <label>Edad</label>
                <input type="text" value="{{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años" disabled style="background: #f8fafc;">
            </div>
        </div>
    </section>

    <form action="{{ route('paciente.historial.guardar', $paciente->id_paciente) }}" method="POST" id="formHistorial">
        @csrf
        <section class="card" style="border-top: 4px solid #16a34a;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;"><i class="fa-solid fa-notes-medical"></i> Notas de Ingreso y Antecedentes</h3>
                
                <button type="button" id="btnHabilitarEdicion" class="btn-edit" style="background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    <i class="fa-solid fa-pen-to-square"></i> Actualizar Información
                </button>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Peso Actual (kg) <span style="color:red">*</span></label>
                    <input type="number" step="0.1" name="peso" value="{{ $paciente->peso }}" class="campo-editable" disabled required style="font-weight: bold; font-size: 1.1rem;">
                </div>
                <div class="form-group full-width">
                    <label>Alergias</label>
                    <input type="text" name="alergias" class="campo-editable" value="{{ old('alergias', $expediente->alergias) }}" placeholder="Ej. Penicilina, ninguna..." disabled>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label>Observaciones Generales / Motivo de Consulta de Hoy</label>
                    <textarea name="observaciones_generales" class="campo-editable" rows="2" disabled placeholder="¿Por qué viene el paciente hoy?">{{ old('observaciones_generales', $expediente->observaciones_generales) }}</textarea>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px dashed #cbd5e1;">
                <h4 style="margin-bottom: 10px; font-size: 0.9rem; color: #64748b;">Antecedentes Médicos (Consulta)</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hereditarios</label>
                        <textarea name="antecedentes_hereditarios" class="campo-editable" rows="2" disabled>{{ old('antecedentes_hereditarios', $expediente->antecedentes_hereditarios) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Patológicos</label>
                        <textarea name="antecedentes_patologicos" class="campo-editable" rows="2" disabled>{{ old('antecedentes_patologicos', $expediente->antecedentes_patologicos) }}</textarea>
                    </div>
                </div>
            </div>

            <input type="hidden" name="fecha_registro" value="{{ date('Y-m-d') }}">
        </section>

        <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
    <div style="display: flex; gap: 10px;">
        <button type="submit" id="btnGuardar" class="btn-primary" style="display: none; background: #16a34a; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold;">
            <i class="fa-solid fa-floppy-disk"></i> CONFIRMAR CAMBIOS
        </button>

        <button type="button" id="btnCancelarEdicion" class="btn-cancel" style="display: none; background: #ef4444; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            <i class="fa-solid fa-xmark"></i> CANCELAR EDICIÓN
        </button>
        
        <a href="{{ route('pacientes.index') }}" id="btnRegresar" class="btn-cancel" style="background: #f1f5f9; color: #475569; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
            <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> REGRESAR
        </a>
    </div>

    <a href="#modalHistoriales" class="btn-history" style="color: #2563eb; font-weight: bold; text-decoration: none;">
        <i class="fa-solid fa-clock-rotate-left"></i> VER NOTAS ANTERIORES
    </a>
</div>
    </form>

    <script>
    // Variables de control
    const btnEditar = document.getElementById('btnHabilitarEdicion');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnCancelar = document.getElementById('btnCancelarEdicion'); // Asegúrate que el ID coincida en tu HTML
    const btnRegresar = document.querySelector('a.btn-cancel[href*="index"]'); // Selecciona el botón de regresar a la lista
    const campos = document.querySelectorAll('.campo-editable');

    // --- 1. LÓGICA PARA HABILITAR EDICIÓN ---
    btnEditar.addEventListener('click', function() {
        campos.forEach(campo => {
            campo.disabled = false;
            campo.style.background = "#fff";
            campo.style.border = "2px solid #2563eb";
        });

        // Alternar botones
        btnGuardar.style.display = "inline-block";
        btnCancelar.style.display = "inline-block";
        btnRegresar.style.display = "none"; 
        this.style.display = "none";
        
        document.querySelector('input[name="peso"]').focus();
    });

    // --- 2. LÓGICA PARA CANCELAR EDICIÓN (LA NUEVA MODIFICACIÓN) ---
    if(btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            if (confirm('¿Estás seguro de descartar los cambios?')) {
                // Volver a bloquear campos
                campos.forEach(campo => {
                    campo.disabled = true;
                    campo.style.background = "#f8fafc";
                    campo.style.border = "1px solid #cbd5e1";
                });

                // Restaurar visibilidad de botones originales
                btnGuardar.style.display = "none";
                btnCancelar.style.display = "none";
                btnRegresar.style.display = "inline-flex";
                btnEditar.style.display = "inline-block";

                // Opcional: Recargar para limpiar lo que el asistente escribió
                window.location.reload(); 
            }
        });
    }

    // --- 3. LÓGICA DEL BUSCADOR (TU CÓDIGO ACTUAL) ---
    document.getElementById('inputBuscarPaciente').addEventListener('input', function() {
        let query = this.value;
        let lista = document.getElementById('listaSugerencias');

        if (query.length > 2) { 
            fetch("{{ route('pacientes.buscar_ajax') }}?q=" + query)
                .then(response => response.json())
                .then(data => {
                    lista.innerHTML = "";
                    if (data.length > 0) {
                        lista.style.display = "block";
                        data.forEach(paciente => {
                            let div = document.createElement('div');
                            div.className = "sugerencia-item";
                            div.style.padding = "10px";
                            div.style.cursor = "pointer";
                            div.style.borderBottom = "1px solid #eee";
                            
                            div.innerHTML = `
                                <div style="display:flex; flex-direction:column;">
                                    <strong>${paciente.nombre} ${paciente.apellido_paterno}</strong>
                                    <small style="color: #64748b;">CURP: ${paciente.curp}</small>
                                </div>
                            `;
                            
                            div.onclick = function() {
                                window.location.href = "{{ url('/asistente/historial') }}/" + paciente.id_paciente;
                            };
                            lista.appendChild(div);
                        });
                    } else {
                        lista.style.display = "none";
                    }
                });
        } else {
            lista.style.display = "none";
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.id !== 'inputBuscarPaciente') {
            document.getElementById('listaSugerencias').style.display = "none";
        }
    });
</script>

@else
    <div class="card" style="text-align: center; padding: 80px 20px; border: 2px dashed #cbd5e1; background: #f8fafc; border-radius: 15px;">
        <i class="fa-solid fa-user-injured" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
        <h2 style="color: #64748b;">No se ha seleccionado paciente</h2>
        <p style="color: #94a3b8; margin-bottom: 30px;">Busque un paciente en la barra superior o selecciónelo desde la lista general para ver su historial.</p>
        <a href="{{ route('pacientes.index') }}" class="btn-primary" style="text-decoration: none; padding: 12px 25px; border-radius: 8px;">
            Ir a Lista de Pacientes
        </a>
    </div>
@endif

@endsection