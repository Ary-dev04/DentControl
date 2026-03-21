@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/historial.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Expediente Clínico</h1>

@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 20px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<section class="card-section" style="margin-bottom: 20px;">
    <div class="search-container" style="position: relative; max-width: 600px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
        <input type="text" id="inputBuscarPaciente" placeholder="Buscar otro paciente (Nombre o CURP)..." 
               style="width: 100%; padding: 10px 15px 10px 45px; border-radius: 8px; border: 1px solid #cbd5e1;">
        <div id="listaSugerencias" class="sugerencias-dropdown" style="display: none; position: absolute; width: 100%; background: white; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px;"></div>
    </div>
</section>

@if($paciente)
    {{-- ALERTA DE ALERGIAS --}}
    @if($expediente && $expediente->alergias && strtolower($expediente->alergias) != 'ninguna' && strtolower($expediente->alergias) != 'n/a')
        <div style="background: #fee2e2; border-left: 6px solid #ef4444; padding: 15px; margin-bottom: 20px; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.5rem; color: #ef4444;"></i>
            <div>
                <strong style="color: #991b1b; display: block;">ALERGIAS DETECTADAS:</strong> 
                <span style="color: #b91c1c; font-weight: bold; text-transform: uppercase;">{{ $expediente->alergias }}</span>
            </div>
        </div>
    @endif

    {{-- INFORMACIÓN GENERAL DEL PACIENTE --}}
    <section class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="color: #2563eb; margin: 0;">Información General</h3>
            <div>
                @if(\Carbon\Carbon::parse($paciente->fecha_nacimiento)->age < 18)
                    <span style="background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: bold; border: 1px solid #f59e0b; margin-right: 10px;">
                        <i class="fa-solid fa-child"></i> PACIENTE MENOR
                    </span>
                @endif
                <span style="background: #e2e8f0; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: bold;">ID: #{{ $paciente->id_paciente }}</span>
            </div>
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
                <label>Edad / Fecha Nac.</label>
                <input type="text" value="{{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años ({{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }})" disabled style="background: #f8fafc;">
            </div>
        </div>

        {{-- SECCIÓN DINÁMICA: DATOS DE CONTACTO ADULTO VS TUTOR --}}
        <div style="margin-top: 15px; padding: 15px; border-radius: 10px; background: {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age < 18 ? '#fffbeb' : '#f8fafc' }}; border: 1px solid {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age < 18 ? '#fef3c7' : '#e2e8f0' }};">
            @if(\Carbon\Carbon::parse($paciente->fecha_nacimiento)->age < 18)
                <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #92400e;"><i class="fa-solid fa-user-shield"></i> Información del Tutor Responsable</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre del Tutor</label>
                        <input type="text" value="{{ $paciente->nombre_tutor }}" disabled style="background: white;">
                    </div>
                    <div class="form-group">
                        <label>Parentesco</label>
                        <input type="text" value="{{ $paciente->parentesco_tutor }}" disabled style="background: white;">
                    </div>
                    <div class="form-group">
                        <label>Teléfono (Tutor)</label>
                        <input type="text" value="{{ $paciente->telefono_tutor }}" disabled style="background: white; font-weight: bold; color: #b45309;">
                    </div>
                </div>
            @else
                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono Celular</label>
                        <input type="text" value="{{ $paciente->telefono }}" disabled style="background: white; font-weight: bold; color: #2563eb;">
                    </div>
                    <div class="form-group">
                        <label>Ocupación</label>
                        <input type="text" value="{{ $paciente->ocupacion ?? 'No especificado' }}" disabled style="background: white;">
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="text" value="{{ $paciente->email ?? 'Sin correo' }}" disabled style="background: white;">
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- FORMULARIO DE ANTECEDENTES Y NOTAS --}}
    <form action="{{ route('paciente.historial.guardar', $paciente->id_paciente) }}" method="POST" id="formHistorial">
        @csrf
        <section class="card" style="border-top: 4px solid #16a34a; margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;"><i class="fa-solid fa-notes-medical"></i> Antecedentes Médicos Actuales</h3>
                
                <button type="button" id="btnHabilitarEdicion" class="btn-edit" style="background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Antecedentes
                </button>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Peso Actual (kg) <span style="color:red">*</span></label>
                    <input type="number" step="0.1" name="peso" value="{{ $paciente->peso }}" class="campo-editable" disabled required min="5" max="250" style="font-weight: bold;">
                </div>
                <div class="form-group full-width">
                    <label>Alergias</label>
                    <input type="text" name="alergias" id="alergias" class="campo-editable" 
               value="{{ old('alergias', $expediente->alergias ?? 'Ninguna') }}" disabled 
               pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\(\)\.,]+$" 
               title="Solo letras, espacios, guiones y paréntesis">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Antecedentes Hereditarios</label>
                    <textarea name="antecedentes_hereditarios" id="hereditarios" class="campo-editable" rows="3" disabled>{{ old('antecedentes_hereditarios', $expediente->antecedentes_hereditarios) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Antecedentes Patológicos</label>
                    
                    <textarea name="antecedentes_patologicos" id="patologicos" class="campo-editable" rows="3" disabled>{{ old('antecedentes_patologicos', $expediente->antecedentes_patologicos) }}</textarea>
                </div>

                <div class="form-group full-width" style="margin-top: 15px;">
                <label>Observaciones Generales</label>
                <textarea name="observaciones_generales" id="observaciones" class="campo-editable" rows="3" disabled>{{ old('observaciones_generales', $expediente->observaciones_generales) }}</textarea>
            </div>
            </div>
        </section>

        {{-- ACCIONES DEL FORMULARIO --}}
        <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
            <div style="display: flex; gap: 10px;">
                <button type="submit" id="btnGuardar" class="btn-primary" style="display: none; background: #16a34a;">
                    <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS
                </button>

                <button type="button" id="btnCancelarEdicion" class="btn-cancel" style="display: none; background: #ef4444; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i> CANCELAR
                </button>
                
                <a href="{{ route('pacientes.index') }}" id="btnRegresar" class="btn-cancel" style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: flex; align-items: center;">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> VOLVER
                </a>

                <button type="button" onclick="document.getElementById('modalVersiones').style.display='block'" 
        style="background: #64748b; color: white; padding: 8px 15px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold;">
    <i class="fa-solid fa-clock-rotate-left"></i> Ver Historial de Cambios
</button>
            </div>
        </div>
    </form>


    <div id="modalVersiones" class="modal-custom" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:white; margin:2% auto; width:90%; max-width:650px; border-radius:15px; overflow:hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="padding:15px 20px; background:#2563eb; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:1.2rem;">Historial de Versiones Anteriores</h3>
            <button onclick="document.getElementById('modalVersiones').style.display='none'" style="background:none; border:none; color:white; font-size:1.8rem; cursor:pointer;">&times;</button>
        </div>

        <div style="padding:20px; max-height:70vh; overflow-y:auto; background:#f8fafc;">
            @forelse($versionesAnteriores as $v)
                <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:15px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <div style="background:#f1f5f9; padding:10px 15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0;">
                        <span style="font-weight:bold; color:#1e293b;"><i class="fa-solid fa-calendar-day"></i> {{ \Carbon\Carbon::parse($v->fecha_modificacion)->format('d/m/Y h:i A') }}</span>
                        <span style="background:#dcfce7; color:#166534; padding:2px 10px; border-radius:15px; font-size:0.8rem; font-weight:bold;">Peso: {{ $v->peso }}kg</span>
                    </div>
                    <div style="padding:15px; font-size:0.9rem; color:#475569;">
                        <p style="margin:0 0 8px 0;"><strong>Alergias:</strong> {{ $v->alergias ?? 'Ninguna' }}</p>
                        <p style="margin:0 0 8px 0;"><strong>Antecedentes Hereditarios:</strong> {{ Str::limit($v->antecedentes_hereditarios, 100) }}</p>
                        <p style="margin:0 0 8px 0;"><strong>Antecedentes Patológicos:</strong> {{ Str::limit($v->antecedentes_patologicos, 100) }}</p>
                        <p style="margin:0; font-style:italic; border-top:1px dashed #eee; padding-top:8px;">
                            <strong>Observaciones:</strong> {{ $v->observaciones_generales ?? 'Sin notas' }}
                        </p>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:40px; color:#94a3b8;">
                    <i class="fa-solid fa-history" style="font-size:3rem; margin-bottom:10px;"></i>
                    <p>No hay cambios registrados aún.</p>
                </div>
            @endforelse
        </div>
        
        <div style="padding:15px; background:#f1f5f9; text-align:right;">
            <button onclick="document.getElementById('modalVersiones').style.display='none'" style="padding:8px 20px; border-radius:6px; border:none; background:#475569; color:white; cursor:pointer;">Cerrar</button>
        </div>
    </div>
</div>
    <script>
        const btnEditar = document.getElementById('btnHabilitarEdicion');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCancelar = document.getElementById('btnCancelarEdicion');
        const btnRegresar = document.getElementById('btnRegresar');
        const campos = document.querySelectorAll('.campo-editable');

        btnEditar.addEventListener('click', function() {
            campos.forEach(campo => {
                campo.disabled = false;
                campo.style.background = "#white";
                campo.style.border = "2px solid #2563eb";
            });
            btnGuardar.style.display = "inline-block";
            btnCancelar.style.display = "inline-block";
            btnRegresar.style.display = "none";
            this.style.display = "none";
        });

        btnCancelar.addEventListener('click', function() {
            if(confirm('¿Descartar cambios?')) window.location.reload();
        });

        // Buscador AJAX
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
                            data.forEach(p => {
                                let div = document.createElement('div');
                                div.style.padding = "10px";
                                div.style.cursor = "pointer";
                                div.style.borderBottom = "1px solid #eee";
                                div.innerHTML = `<strong>${p.nombre} ${p.apellido_paterno}</strong><br><small>${p.curp}</small>`;
                                div.onclick = () => window.location.href = "{{ url('/asistente/historial') }}/" + p.id_paciente;
                                lista.appendChild(div);
                            });
                        }
                    });
            } else { lista.style.display = "none"; }
        });
    </script>

@else
    {{-- VISTA CUANDO NO HAY PACIENTE SELECCIONADO --}}
    <div class="card" style="text-align: center; padding: 80px 20px; border: 2px dashed #cbd5e1; background: #f8fafc; border-radius: 15px;">
        <i class="fa-solid fa-user-injured" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
        <h2 style="color: #64748b;">No se ha seleccionado paciente</h2>
        <p style="color: #94a3b8; margin-bottom: 30px;">Busque un paciente en la barra superior para ver su historial.</p>
        <a href="{{ route('pacientes.index') }}" class="btn-primary" style="text-decoration: none; padding: 12px 25px; border-radius: 8px;">Ir a Lista General</a>
    </div>
@endif
<script>
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
                        data.forEach(p => {
                            let div = document.createElement('div');
                            div.style.padding = "12px 15px";
                            div.style.cursor = "pointer";
                            div.style.borderBottom = "1px solid #f1f5f9";
                            div.innerHTML = `
                                <div style="font-weight: bold; color: #1e293b;">
                                    <i class="fa-solid fa-user" style="margin-right: 8px; color: #64748b;"></i>
                                    ${p.nombre} ${p.apellido_paterno}
                                </div>
                                <small style="color: #94a3b8; margin-left: 22px;">CURP: ${p.curp}</small>
                            `;
                            div.onclick = () => {
                                window.location.href = "{{ url('/asistente/historial') }}/" + p.id_paciente;
                            };
                            lista.appendChild(div);
                        });
                    } else {
                        lista.innerHTML = "<div style='padding:10px; color:#94a3b8;'>No se encontraron resultados</div>";
                        lista.style.display = "block";
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


    function validarFormatoClinico(el) {
        // La expresión regular debe ser idéntica a la del servidor
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\(\)\.,]*$/;
        if (el && el.value !== "" && !regex.test(el.value)) {
            el.setCustomValidity("Caracteres no permitidos (solo letras, espacios, paréntesis, guiones, puntos y comas).");
            return false;
        } else {
            el.setCustomValidity(""); 
            return true;
        }
    }

    // IDs de todos los campos que queremos validar
    const idsAValidar = ['hereditarios', 'patologicos', 'observaciones', 'alergias'];

    // Validación en tiempo real para limpiar la burbuja mientras escriben
    idsAValidar.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', function() {
                validarFormatoClinico(this);
            });
        }
    });

    // Validación al enviar
    document.getElementById('formHistorial').addEventListener('submit', function(e) {
        let hayError = false;

        // Validar primero el peso (nativo por el min/max del HTML)
        const pesoInput = this.querySelector('input[name="peso"]');
        if (!pesoInput.checkValidity()) {
            pesoInput.reportValidity();
            hayError = true;
        }

        // Validar campos de texto si el peso está bien
        if (!hayError) {
            for (let id of idsAValidar) {
                const el = document.getElementById(id);
                if (el) {
                    if (!validarFormatoClinico(el) || !el.checkValidity()) {
                        el.reportValidity(); // Muestra la burbuja naranja
                        hayError = true;
                        break; // Detener en el primer error encontrado
                    }
                }
            }
        }

        if (hayError) {
            e.preventDefault(); // Evita que se cierre el modo edición o se envíe
        }
    });
</script>
@endsection