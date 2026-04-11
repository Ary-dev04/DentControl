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

@if(session('info'))
    <div style="background: #e0f2fe; color: #0369a1; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd; margin-bottom: 20px;">
        <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
    </div>
@endif

<!--<section class="card-section" style="margin-bottom: 20px;">
    <div class="search-container" style="position: relative; max-width: 600px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
        <input type="text" id="inputBuscarPaciente" placeholder="Buscar otro paciente (Nombre o CURP)..." 
               style="width: 100%; padding: 10px 15px 10px 45px; border-radius: 8px; border: 1px solid #cbd5e1;">
        <div id="listaSugerencias" class="sugerencias-dropdown" style="display: none; position: absolute; width: 100%; background: white; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px;"></div>
    </div>
</section>-->

<section class="card-section" style="margin-bottom: 20px;">
    <div class="search-container" style="position: relative; max-width: 600px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 12px; color: #94a3b8;"></i>
        <input type="text" id="inputBuscarPaciente" placeholder="Buscar otro paciente (Nombre o CURP)..." 
               style="width: 100%; padding: 10px 15px 10px 45px; border-radius: 8px; border: 1px solid #cbd5e1;" autocomplete="off">
        
        <div id="listaSugerencias" class="sugerencias-dropdown" style="display: none; position: absolute; width: 100%; background: white; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 8px 8px; max-height: 300px; overflow-y: auto;">
            @foreach($pacientesBusqueda as $p)
                <div class="item-paciente-local" 
                     style="padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f1f5f9;"
                     data-nombre="{{ strtolower($p->nombre . ' ' . $p->apellido_paterno . ' ' . $p->apellido_materno) }}"
                     data-curp="{{ strtolower($p->curp) }}"
                     onclick="window.location.href='{{ url('/asistente/historial') }}/{{ $p->id_paciente }}'">
                    <div style="font-weight: bold; color: #1e293b;">
                        <i class="fa-solid fa-user" style="margin-right: 8px; color: #64748b;"></i>
                        {{ $p->nombre }} {{ $p->apellido_paterno }}
                    </div>
                    <small style="color: #94a3b8; margin-left: 22px;">CURP: {{ $p->curp }}</small>
                </div>
            @endforeach
        </div>
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
                    <label>
                        Antecedentes Hereditarios
                        @if(empty($expediente->antecedentes_hereditarios))
                        <span style="color: #ef4444; font-size: 0.75rem; font-style: italic; margin-left: 5px;">
                            <i class="fa-solid fa-circle-exclamation"></i> Pendiente de completar por el dentista
                        </span>
                        @endif
                    </label>
                    <textarea name="antecedentes_hereditarios" id="hereditarios" class="campo-editable" rows="3" disabled>{{ old('antecedentes_hereditarios', $expediente->antecedentes_hereditarios) }}</textarea>
                </div>
                <div class="form-group">
                    <label>
                        Antecedentes Patológicos
                        @if(empty($expediente->antecedentes_patologicos))
                        <span style="color: #ef4444; font-size: 0.75rem; font-style: italic; margin-left: 5px;">
                            <i class="fa-solid fa-circle-exclamation"></i> Pendiente de completar por el dentista
                        </span>
                    @endif
                    </label>
                    
                    <textarea name="antecedentes_patologicos" id="patologicos" class="campo-editable" rows="3" disabled>{{ old('antecedentes_patologicos', $expediente->antecedentes_patologicos) }}</textarea>
                </div>

                <div class="form-group full-width" style="margin-top: 15px;">
                <label>
                    Observaciones Generales
                    @if(empty($expediente->observaciones_generales))
                <span style="color: #64748b; font-size: 0.75rem; font-style: italic; margin-left: 5px;">
                    <i class="fa-solid fa-circle-info"></i> Detalles adicionales del caso
                </span>
                @endif
                </label>
                <textarea name="observaciones_generales" id="observaciones" class="campo-editable" 
                placeholder="Ej: Paciente muestra ansiedad dental, prefiere anestesia sin epinefrina, o detalles sobre higiene bucal..."
                rows="3" disabled>{{ old('observaciones_generales', $expediente->observaciones_generales) }}</textarea>
            </div>
            </div>
        </section>

        {{-- SECCIÓN DE PRESUPUESTO (Solo para Tratamientos Activos) --}}
@if(isset($citaActiva) && $citaActiva->id_tratamiento)
    @php
        // Buscamos el tratamiento específico de la cita para obtener su precio
        $tratamientoActual = $paciente->tratamientos->where('id_tratamiento', $citaActiva->id_tratamiento)->first();
    @endphp

    {{-- SECCIÓN DE PRESUPUESTO ACTUALIZADA --}}
@if($tratamientoActual)
<section class="card" style="border-top: 4px solid #0ea5e9; margin-top: 20px; background: #f0f9ff;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; color: #0369a1;"><i class="fa-solid fa-file-invoice-dollar"></i> Presupuesto del Tratamiento</h3>
            <p style="margin: 5px 0 0 0; color: #0c4a6e;"><strong>En curso:</strong> {{ $tratamientoActual->catalogoTratamiento->nombre }}</p>
        </div>

        <div style="text-align: right;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Precio Estimado Total ($)</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 8px; color: #64748b;">$</span>
                    <input type="number" 
                           step="0.01" 
                           id="inputPrecioEstimado"
                           value="{{ $tratamientoActual->precio_estimado }}" 
                           {{-- El campo nace bloqueado si ya tiene un precio --}}
                           {{ $tratamientoActual->precio_estimado > 0 ? 'disabled' : '' }}
                           style="padding-left: 25px; width: 150px; font-weight: bold; border-radius: 8px; border: 1px solid #cbd5e1; height: 38px; color: #16a34a; background: {{ $tratamientoActual->precio_estimado > 0 ? '#f8fafc' : 'white' }};"
                           placeholder="0.00">
                </div>
                
                @if(auth()->user()->rol === 'dentista')
                    <button type="button" 
                            id="btnPresupuesto"
                            onclick="gestionarPresupuesto({{ $tratamientoActual->id_tratamiento }})" 
                            {{-- Usamos un data-attribute para saber si estamos editando o solo viendo --}}
                            data-estado="{{ $tratamientoActual->precio_estimado > 0 ? 'lectura' : 'edicion' }}"
                            style="background: {{ $tratamientoActual->precio_estimado > 0 ? '#64748b' : '#0ea5e9' }}; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: all 0.3s ease;">
                        <i class="fa-solid {{ $tratamientoActual->precio_estimado > 0 ? 'fa-pen-to-square' : 'fa-floppy-disk' }}"></i> 
                        <span id="textoBotonPresupuesto">
                            {{ $tratamientoActual->precio_estimado > 0 ? 'Editar' : 'Guardar' }}
                        </span>
                    </button>
                @endif
            </div>
            @if(empty($tratamientoActual->precio_estimado))
                <small id="avisoPendiente" style="color: #ef4444; font-weight: bold; display: block; margin-top: 5px;">
                    <i class="fa-solid fa-circle-exclamation"></i> Pendiente definir precio
                </small>
            @endif
        </div>
    </div>
</section>
@endif
@endif

        {{-- SECCIÓN DE NOTAS DE EVOLUCIÓN (Nueva) --}}
<section class="card" style="border-top: 4px solid #8b5cf6; margin-top: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;"><i class="fa-solid fa-file-medical"></i> Notas de Evolución</h3>
        
        {{-- Solo el DENTISTA puede agregar notas --}}
        @if(auth()->user()->rol == 'dentista')
    @if(isset($citaActiva) && !empty($citaActiva->id_tratamiento))
        <button type="button" class="btn-nota-morada" onclick="document.getElementById('modalNuevaNota').style.display='block'">
    <i class="fa-solid fa-circle-plus"></i> Nueva Nota Hoy
</button>
    @else
        <span style="color: #94a3b8; font-size: 0.8rem; font-style: italic;">
            <i class="fa-solid fa-lock"></i> {{ isset($citaActiva) ? 'Servicio único: no requiere notas de evolución' : 'Inicie atención para agregar notas' }}
        </span>
    @endif
@endif
    </div>

    <div class="timeline" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
        @forelse($notas as $nota)
            <div style="border-left: 3px solid #8b5cf6; padding-left: 15px; margin-bottom: 20px; position: relative;">
                <div style="font-size: 0.8rem; color: #64748b; font-weight: bold;">
                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }} - {{ $nota->hora }}
                </div>
                <div style="font-weight: bold; color: #1e293b; margin: 5px 0;">
                    Tratamiento: {{ $nota->tratamiento->catalogoTratamiento->nombre }}
                </div>
                <p style="margin: 5px 0; font-size: 0.95rem; color: #475569;">
                    <strong>Nota:</strong> {{ $nota->nota_texto }}
                </p>
                @if($nota->indicaciones)
                    <p style="margin: 5px 0; font-size: 0.9rem; color: #059669; background: #ecfdf5; padding: 5px; border-radius: 4px;">
                        <i class="fa-solid fa-hand-holding-medical"></i> <strong>Indicaciones:</strong> {{ $nota->indicaciones }}
                    </p>
                @endif
                <small style="color: #94a3b8;">Atendió: Dr. {{ $nota->usuario->nombre }}</small>
            </div>
        @empty
            <p style="text-align: center; color: #94a3b8; padding: 20px;">No hay notas de evolución registradas.</p>
        @endforelse
    </div>
</section>


        {{-- ACCIONES DEL FORMULARIO --}}
        <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="submit" id="btnGuardar" class="btn-primary" style="display: none; background: #16a34a;">
            <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS
        </button>

        <button type="button" id="btnCancelarEdicion" class="btn-cancel" style="display: none; background: #ef4444; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
            <i class="fa-solid fa-xmark"></i> CANCELAR
        </button>

        <button type="button" onclick="document.getElementById('modalVersiones').style.display='block'" 
                style="background: #64748b; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; display: flex; align-items: center;">
            <i class="fa-solid fa-clock-rotate-left" style="margin-right: 8px;"></i> Ver Historial de Cambios
        </button>

        <button type="button" onclick="document.getElementById('modalTratamientos').style.display='block'" 
            style="background: #0ea5e9; color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-kit-medical"></i> <i class="fa-solid fa-tooth"></i> Ver Plan de Tratamientos
    </button>

    <button type="button" onclick="document.getElementById('modalCitasHistorial').style.display='block'" 
            style="background: #8b5cf6; color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-calendar-days"></i> Historial de Citas y Pagos
    </button>
        
    </div>

    

    <a href="{{ auth()->user()->rol == 'dentista' ? route('dentista.agenda') : route('pacientes.index') }}" id="btnRegresar" class="btn-cancel" 
       style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: flex; align-items: center;">
        <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> VOLVER
    </a>
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

{{-- MODAL DE TRATAMIENTOS --}}
<div id="modalTratamientos" class="modal-custom" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:white; margin:5% auto; width:95%; max-width:900px; border-radius:15px; overflow:hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="padding:15px 20px; background:#0ea5e9; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;"><i class="fa-solid fa-tooth"></i> Plan de Tratamientos del Paciente</h3>
            <button onclick="document.getElementById('modalTratamientos').style.display='none'" style="background:none; border:none; color:white; font-size:1.8rem; cursor:pointer;">&times;</button>
        </div>

        <div style="padding:20px; max-height:70vh; overflow-y:auto;">
            <table style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #3d88b0; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 12px;">Tratamiento</th>
                        <th style="padding: 12px;">Estado</th>
                        <th style="padding: 12px;">Precio Estimado</th>
                        <th style="padding: 12px;">Total Pagado</th>
                        <th style="padding: 12px;">Saldo Pendiente</th>
                        <th style="padding: 12px;">Inicio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paciente->tratamientos as $t)
    @php
        // IMPORTANTE: Aquí sumamos los montos de TODAS las citas que pertenecen a ESTE tratamiento
        // Solo sumamos las que ya fueron cobradas (finalizadas)
        $totalPagadoAcumulado = $t->citas->where('estatus_cita', 'finalizada')->sum('monto_cobrado');
        
        // El saldo es la diferencia entre el presupuesto inicial y lo que el paciente ya dio
        $saldoPendiente = $t->precio_estimado - $totalPagadoAcumulado;
    @endphp

    <tr style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px;">
            <strong>{{ $t->catalogoTratamiento->nombre }}</strong><br>
            <small style="color: #64748b;">Presupuesto inicial: ${{ number_format($t->precio_estimado, 2) }}</small>
        </td>
        
        <td style="padding: 12px;">
            <span class="badge-{{ $t->estatus }}">
                {{ strtoupper($t->estatus) }}
            </span>
        </td>

        <td style="padding: 12px; font-weight: bold; color: #1e293b;">
            ${{ number_format($t->precio_estimado, 2) }}
        </td>

        <td style="padding: 12px; color: #16a34a; font-weight: bold;">
            ${{ number_format($totalPagadoAcumulado, 2) }}
            <div style="font-size: 0.7rem; color: #64748b; font-weight: normal;">
                ({{ $t->citas->where('estatus_cita', 'finalizada')->count() }} abonos realizados)
            </div>
        </td>

        <td style="padding: 12px; font-weight: bold; color: {{ $saldoPendiente > 0 ? '#ef4444' : '#16a34a' }};">
            @if($saldoPendiente > 0)
                ${{ number_format($saldoPendiente, 2) }}
            @elseif($saldoPendiente < 0)
                <span style="color: #3b82f6;">+${{ number_format(abs($saldoPendiente), 2) }} (Ajuste)</span>
            @else
                $0.00 (Liquidado)
            @endif
        </td>

        <td style="padding: 12px; color: #64748b;">
            {{ \Carbon\Carbon::parse($t->fecha_inicio)->format('d/m/Y') }}
        </td>
    </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                No hay tratamientos registrados para este paciente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- MODAL DE CITAS Y PAGOS --}}
<div id="modalCitasHistorial" class="modal-custom" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:white; margin:3% auto; width:95%; max-width:900px; border-radius:15px; overflow:hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="padding:15px 20px; background:#8b5cf6; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;"><i class="fa-solid fa-calendar-days"></i> Historial de Visitas y Cobros</h3>
            <button onclick="document.getElementById('modalCitasHistorial').style.display='none'" style="background:none; border:none; color:white; font-size:1.8rem; cursor:pointer;">&times;</button>
        </div>

        <div style="padding:20px; max-height:75vh; overflow-y:auto;">
            <table style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #a811ff; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 12px;">Fecha y Hora</th>
                        <th style="padding: 12px;">Motivo / Servicio</th>
                        <th style="padding: 12px;">Tipo</th>
                        <th style="padding: 12px;">Estado Cita</th>
                        <th style="padding: 12px;">Monto Cobrado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Ordenamos las citas de la más reciente a la más antigua
                        $citasOrdenadas = $paciente->citas->sortByDesc(function($cita) {
                            return $cita->fecha . ' ' . $cita->hora;
                        });
                    @endphp

                    @forelse($citasOrdenadas as $cita)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px;">
                                <strong>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</strong><br>
                                <small style="color: #64748b;">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</small>
                            </td>
                            <td style="padding: 12px;">
                                {{-- Si tiene servicio de catálogo lo muestra, si no, el motivo manual --}}
                                <strong>{{ $cita->servicio->nombre ?? 'Consulta General' }}</strong><br>
                                <small style="color: #64748b;">{{ $cita->motivo_consulta }}</small>
                            </td>
                            <td style="padding: 12px;">
                                @if($cita->id_tratamiento)
                                    <span style="color: #0ea5e9; font-weight: bold;"><i class="fa-solid fa-牙"></i> Tratamiento</span>
                                @else
                                    <span style="color: #a811ff; font-weight: bold;">Servicio Único</span>
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @switch($cita->estatus_cita)
                                    @case('finalizada')
                                        <span style="color: #16a34a;"><i class="fa-solid fa-check"></i> Finalizada</span>
                                        @break
                                    @case('programada')
                                        <span style="color: #ca8a04;"><i class="fa-solid fa-calendar-day"></i> Programada</span>
                                        @break
                                    @case('enproceso')
                                    {{-- Estilo para la cita que el dentista está atendiendo actualmente --}}
                                    <span style="color: #8b5cf6; font-weight: bold; background: #f5f3ff; padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd6fe;">
                                        <i class="fa-solid fa-stethoscope fa-beat-fast"></i> En Proceso
                                    </span>
                                    @break
                                    @default
                                        <span style="color: #ef4444;"><i class="fa-solid fa-xmark"></i> Cancelada</span>
                                @endswitch
                            </td>
                            <td style="padding: 12px; font-weight: bold; font-size: 1rem; color: #1e293b;">
                                ${{ number_format($cita->monto_cobrado, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: #94a3b8;">
                                No hay historial de citas para este paciente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Resumen al final de la tabla --}}
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: bold; font-size: 1rem;">
                        <td colspan="4" style="padding: 15px; text-align: right;">Total Histórico Pagado:</td>
                        <td style="padding: 15px; color: #16a34a;">${{ number_format($paciente->citas->where('estatus_cita', 'finalizada')->sum('monto_cobrado'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div id="modalNuevaNota" class="modal-custom" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
    <div style="background:white; margin:5% auto; width:90%; max-width:500px; border-radius:15px; overflow:hidden;">
        <form action="{{ route('notas.guardar') }}" method="POST">
            @csrf
            <div style="padding:15px 20px; background:#8b5cf6; color:white; display:flex; justify-content:space-between;">
                <h3 style="margin:0;">Registrar Evolución</h3>
                <button type="button" onclick="document.getElementById('modalNuevaNota').style.display='none'" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div style="padding:20px;">
                {{-- Aquí asumo que pasas el id_tratamiento de la cita activa --}}
                <input type="hidden" name="id_tratamiento" value="{{ $citaActiva ? $citaActiva->id_tratamiento : '' }}">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">¿Qué se realizó hoy?</label>
                    <textarea name="nota_texto" rows="4" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px;" placeholder="Ej: Se realizó limpieza profunda en cuadrante superior..." required></textarea>
                </div>

                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Indicaciones al paciente</label>
                    <textarea name="indicaciones" rows="2" style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px;" placeholder="Ej: No comer alimentos sólidos en 2 horas..."></textarea>
                </div>
            </div>
            <div style="padding:15px; background:#f1f5f9; text-align:right;">
                <button type="submit" style="background:#8b5cf6; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer;">Guardar Nota</button>
            </div>
        </form>
    </div>
</div>

   <!-- <script>
        /*const btnEditar = document.getElementById('btnHabilitarEdicion');
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
        });*/

        // Buscador AJAX
        /*document.getElementById('inputBuscarPaciente').addEventListener('input', function() {
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
        });*/
    </script>-->

@else
    {{-- VISTA CUANDO NO HAY PACIENTE SELECCIONADO --}}
    <div class="card" style="text-align: center; padding: 80px 20px; border: 2px dashed #cbd5e1; background: #f8fafc; border-radius: 15px;">
        <i class="fa-solid fa-user-injured" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
        <h2 style="color: #64748b;">No se ha seleccionado paciente</h2>
        <p style="color: #94a3b8; margin-bottom: 30px;">
            @if(Auth::user()->rol === 'dentista')
                Consulta tu agenda del día para seleccionar un paciente y comenzar la atención.
            @else
                Busque un paciente en la barra superior para ver su historial.
            @endif
        </p>

        @if(Auth::user()->rol === 'dentista')
            {{-- BOTÓN PARA EL DENTISTA --}}
            <a href="{{ route('dentista.agenda') }}" class="btn-primary" style="text-decoration: none; padding: 12px 25px; border-radius: 8px; background: #2b6edc; color: white; display: inline-block;">
                <i class="fa-solid fa-calendar-day"></i> Ver mi Agenda de Hoy
            </a>
        @else
            {{-- BOTÓN PARA EL ASISTENTE (EL QUE YA TENÍAS) --}}
            <a href="{{ route('pacientes.index') }}" class="btn-primary" style="text-decoration: none; padding: 12px 25px; border-radius: 8px; background: #2b6edc; color: white; display: inline-block;">
                Ir a Lista General
            </a>
        @endif
    </div>
@endif
<script>

    // 1. BUSCADOR LOCAL (Protegido para que siempre funcione)
    const inputBuscar = document.getElementById('inputBuscarPaciente');
    const listaSugerencias = document.getElementById('listaSugerencias');
    const itemsPacientes = document.querySelectorAll('.item-paciente-local');

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            const filtro = this.value.toLowerCase().trim();
            let encontrados = 0;

            if (filtro.length > 1) {
                listaSugerencias.style.display = 'block';
                
                // En lugar de fetch (AJAX), filtramos lo que ya está en el HTML
                itemsPacientes.forEach(item => {
                    const nombre = item.getAttribute('data-nombre');
                    const curp = item.getAttribute('data-curp');

                    if (nombre.includes(filtro) || curp.includes(filtro)) {
                        item.style.display = 'block';
                        encontrados++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (encontrados === 0) {
                    listaSugerencias.innerHTML = '<div style="padding:15px; color:#94a3b8; text-align:center;">No se encontraron resultados</div>';
                }
            } else {
                listaSugerencias.style.display = 'none';
            }
        });
    }

    // 2. BOTONES DE EDICIÓN (Protegidos con un IF para evitar el error de la consola)
    const btnEditar = document.getElementById('btnHabilitarEdicion');
    
    if (btnEditar) {
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCancelar = document.getElementById('btnCancelarEdicion');
        const btnRegresar = document.getElementById('btnRegresar');
        const campos = document.querySelectorAll('.campo-editable');

        btnEditar.addEventListener('click', function() {
            campos.forEach(campo => {
                campo.disabled = false;
                campo.style.background = "white";
                campo.style.border = "2px solid #2563eb";
            });
            btnGuardar.style.display = "inline-block";
            btnCancelar.style.display = "inline-block";
            if(btnRegresar) btnRegresar.style.display = "none";
            this.style.display = "none";
        });

        btnCancelar.addEventListener('click', function() {
            if(confirm('¿Descartar cambios?')) window.location.reload();
        });
    }

    function validarFormatoClinico(el) {
        // 1. LIMPIEZA DE ESPACIOS MÚLTIPLES (Agregado aquí)
        // Reemplaza 2 o más espacios por uno solo en tiempo real
        el.value = el.value.replace(/\s{2,}/g, ' ');

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

    // Validación en tiempo real para limpiar la burbuja y ESPACIOS mientras escriben
    idsAValidar.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', function() {
                validarFormatoClinico(this); // Aquí se ejecuta la limpieza y la validación
            });
        }
    });

    // 4. ENVÍO DEL FORMULARIO (Protegido con IF)
    const formHistorial = document.getElementById('formHistorial');
    if (formHistorial) {
        formHistorial.addEventListener('submit', function(e) {
            let hayError = false;
            const pesoInput = this.querySelector('input[name="peso"]');
            if (pesoInput && !pesoInput.checkValidity()) {
                pesoInput.reportValidity();
                hayError = true;
            }
            if (!hayError) {
                for (let id of idsAValidar) {
                    const el = document.getElementById(id);
                    if (el) {
                        if (!validarFormatoClinico(el) || !el.checkValidity()) {
                            el.reportValidity(); 
                            hayError = true;
                            break; 
                        }
                    }
                }
            }
            if (hayError) e.preventDefault(); 
        });
    }

    // 5. GESTIÓN DE PRESUPUESTO
    function gestionarPresupuesto(idTratamiento) {
    const btn = document.getElementById('btnPresupuesto');
    const input = document.getElementById('inputPrecioEstimado');
    
    // PROTECCIÓN: Si los elementos no existen (porque no hay paciente), salimos de la función
    if (!btn || !input) return;

    const textoBtn = document.getElementById('textoBotonPresupuesto');
    const icono = btn.querySelector('i');
    const estado = btn.getAttribute('data-estado');

    // MODO: PASAR A EDICIÓN
    if (estado === 'lectura') {
        input.disabled = false;
        input.style.background = "white";
        input.style.border = "2px solid #0ea5e9";
        input.focus();
        
        btn.setAttribute('data-estado', 'edicion');
        btn.style.background = "#f59e0b"; // Naranja
        textoBtn.innerText = " Guardar";
        icono.className = "fa-solid fa-floppy-disk";
    } 
    // MODO: GUARDAR CAMBIOS
    else {
        const precio = input.value;

        if(!precio || precio <= 0) {
            alert("Por favor ingrese un monto válido.");
            return;
        }

        btn.disabled = true;
        textoBtn.innerText = " Guardando...";

        fetch(`/historial/actualizar-precio/${idTratamiento}`, { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ precio_estimado: precio })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Bloquear campo nuevamente
                input.disabled = true;
                input.style.background = "#f8fafc";
                input.style.border = "1px solid #cbd5e1";
                
                btn.disabled = false;
                btn.setAttribute('data-estado', 'lectura');
                btn.style.background = "#16a34a"; // Verde éxito
                textoBtn.innerText = " ¡Actualizado!";
                icono.className = "fa-solid fa-check";
                
                // Quitar aviso de pendiente si existe
                const aviso = document.getElementById('avisoPendiente');
                if(aviso) aviso.style.display = 'none';

                // Esperamos 1.5 segundos para que el dentista vea el éxito antes de recargar
                setTimeout(() => {
                    btn.style.background = "#64748b"; // Volver al color neutro
                    textoBtn.innerText = " Editar";
                    icono.className = "fa-solid fa-pen-to-square";
                    
                    // Recargar para actualizar saldos
                    location.reload(); 
                }, 1500);
            } else {
                alert("Error: " + data.message);
                btn.disabled = false;
                textoBtn.innerText = " Guardar";
                btn.style.background = "#f59e0b";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ocurrió un error en la conexión.");
            btn.disabled = false;
            textoBtn.innerText = " Guardar";
        });
    }
}

    // Cerrar sugerencias si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (listaSugerencias && e.target !== inputBuscar) {
            listaSugerencias.style.display = "none";
        }
    });


  /*function validarFormatoClinico(el) {
        // 1. LIMPIEZA DE ESPACIOS MÚLTIPLES (Agregado aquí)
        // Reemplaza 2 o más espacios por uno solo en tiempo real
        el.value = el.value.replace(/\s{2,}/g, ' ');

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

    // Validación en tiempo real para limpiar la burbuja y ESPACIOS mientras escriben
    idsAValidar.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.addEventListener('input', function() {
                validarFormatoClinico(this); // Aquí se ejecuta la limpieza y la validación
            });
        }
    });

    // Validación al enviar (Se mantiene igual)
    document.getElementById('formHistorial').addEventListener('submit', function(e) {
        let hayError = false;

        const pesoInput = this.querySelector('input[name="peso"]');
        if (pesoInput && !pesoInput.checkValidity()) {
            pesoInput.reportValidity();
            hayError = true;
        }

        if (!hayError) {
            for (let id of idsAValidar) {
                const el = document.getElementById(id);
                if (el) {
                    // Al llamar a validarFormatoClinico aquí, también hacemos una última limpieza de espacios
                    if (!validarFormatoClinico(el) || !el.checkValidity()) {
                        el.reportValidity(); 
                        hayError = true;
                        break; 
                    }
                }
            }
        }

        if (hayError) {
            e.preventDefault(); 
        }
    });

   function gestionarPresupuesto(idTratamiento) {
    const btn = document.getElementById('btnPresupuesto');
    const input = document.getElementById('inputPrecioEstimado');
    const textoBtn = document.getElementById('textoBotonPresupuesto');
    const icono = btn.querySelector('i');
    const estado = btn.getAttribute('data-estado');

    // MODO: PASAR A EDICIÓN
    if (estado === 'lectura') {
        input.disabled = false;
        input.style.background = "white";
        input.style.border = "2px solid #0ea5e9";
        input.focus();
        
        btn.setAttribute('data-estado', 'edicion');
        btn.style.background = "#f59e0b"; // Naranja
        textoBtn.innerText = " Guardar";
        icono.className = "fa-solid fa-floppy-disk";
    } 
    // MODO: GUARDAR CAMBIOS
    else {
        const precio = input.value;

        if(!precio || precio <= 0) {
            alert("Por favor ingrese un monto válido.");
            return;
        }

        btn.disabled = true;
        textoBtn.innerText = " Guardando...";

        fetch(`/historial/actualizar-precio/${idTratamiento}`, { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ precio_estimado: precio })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Bloquear campo nuevamente
                input.disabled = true;
                input.style.background = "#f8fafc";
                input.style.border = "1px solid #cbd5e1";
                
                btn.disabled = false;
                btn.setAttribute('data-estado', 'lectura');
                btn.style.background = "#16a34a"; // Verde éxito
                textoBtn.innerText = " ¡Actualizado!";
                icono.className = "fa-solid fa-check";
                
                // Quitar aviso de pendiente si existe
                const aviso = document.getElementById('avisoPendiente');
                if(aviso) aviso.style.display = 'none';

                setTimeout(() => {
                    btn.style.background = "#64748b"; // Volver al color neutro de "Editar"
                    textoBtn.innerText = " Editar";
                    icono.className = "fa-solid fa-pen-to-square";
                    
                    // Recargar para actualizar los saldos en los modales de pagos
                    location.reload(); 
                }, 1500);
            } else {
                alert("Error: " + data.message);
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ocurrió un error en la conexión.");
            btn.disabled = false;
        });
    }
}*/
</script>
@endsection