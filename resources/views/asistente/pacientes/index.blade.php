@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pacientes.css') }}">
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/es.global.min.js"></script>

<div class="app-container">
    <main class="content">
        <h1>Gestión de pacientes y citas</h1>

        <div class="form-actions">
            <button type="button" class="btn-primary" onclick="abrirModal('modalSeleccion')">
                <i class="fa-solid fa-calendar-plus"></i> Nueva cita
            </button>
        </div>

        @if(session('success'))
            <div class="alerta-temporal" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alerta-temporal" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="search-wrapper">
    <i class="fa-solid fa-magnifying-glass search-icon"></i>
    <input type="text" 
           id="patientSearch" 
           class="search-input" 
           placeholder="Buscar por nombre, CURP o tutor..." 
           onkeyup="filterPatients()">
</div>

        <section class="table-section" style="width: 100%; background: #fff; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; margin-top: 20px;">
    <table class="data-table" style="width: 100%; border-collapse: collapse; min-width: 900px;">
        <thead>
            <tr style="background-color: #2563eb; color: #ffffff;">
                <th style="padding: 18px 15px; width: 90px; text-align: center; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none;">Perfil</th>
                <th style="padding: 18px 15px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none;">Paciente / Identificación</th>
                <th style="padding: 18px 15px; width: 110px; text-align: center; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none;">Edad</th>
                <th style="padding: 18px 15px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none;">Información de Contacto</th>
                <th style="padding: 18px 15px; width: 140px; text-align: center; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pacientes as $p)
            <tr style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;">
                <td style="padding: 20px 15px; text-align: center; vertical-align: middle;">
                    @if($p->nombre_tutor)
                        <div style="background: #e0f2fe; color: #0369a1; padding: 6px; border-radius: 8px; font-size: 10px; font-weight: 800; border: 1px solid #bae6fd;">
                            <i class="fa-solid fa-child" style="font-size: 1.2rem;"></i><br>MENOR
                        </div>
                    @else
                        <div style="background: #f1f5f9; color: #475569; padding: 6px; border-radius: 8px; font-size: 10px; font-weight: 800; border: 1px solid #e2e8f0;">
                            <i class="fa-solid fa-user" style="font-size: 1.2rem;"></i><br>ADULTO
                        </div>
                    @endif
                </td>

                <td style="padding: 20px 15px; vertical-align: middle;">
                    <div style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                        {{ $p->nombre }} {{ $p->apellido_paterno }} {{ $p->apellido_materno }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-family: 'Courier New', monospace; background: #f8fafc; color: #64748b; padding: 2px 6px; border-radius: 4px; font-size: 0.85rem; border: 1px solid #e2e8f0;">
                            {{ $p->curp }}
                        </span>
                    </div>
                </td>

                <td style="padding: 20px 15px; text-align: center; vertical-align: middle;">
                    <div style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 8px 12px; border-radius: 8px; font-weight: 700; font-size: 1rem;">
                        {{ \Carbon\Carbon::parse($p->fecha_nacimiento)->age }} 
                        <span style="font-size: 0.75rem; font-weight: 400; display: block;">años</span>
                    </div>
                </td>

                <td style="padding: 20px 15px; vertical-align: middle;">
                    @if($p->nombre_tutor)
                        <div style="margin-bottom: 5px;">
                            <span style="color: #2563eb; font-weight: 700; font-size: 0.95rem;">
                                <i class="fa-solid fa-phone"></i> {{ $p->telefono_tutor }}
                            </span>
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-user-shield"></i> {{ $p->nombre_tutor }}
                        </div>
                    @else
                        <div style="margin-bottom: 5px;">
                            <span style="color: #16a34a; font-weight: 700; font-size: 0.95rem;">
                                <i class="fa-solid fa-phone"></i> {{ $p->telefono ?? 'S/N' }}
                            </span>
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b; word-break: break-all;">
                            <i class="fa-solid fa-envelope"></i> {{ $p->email }}
                        </div>
                    @endif
                </td>

                <td style="padding: 20px 15px; text-align: center; vertical-align: middle;">
                    <button class="btn-ver" onclick="verPaciente({{ $p->id_paciente }})" 
                            style="background: #2563eb; color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                        <i class="fa-solid fa-file-medical"></i> VER EXPEDIENTE CLINICO
                    </button>
                </td>
            </tr>
            @endforeach
            <tr id="noResultsRow" style="display: none;">
            <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-user-slash" style="font-size: 2rem; color: #cbd5e1;"></i>
                    <span style="font-size: 1rem; font-weight: 500;">No se encontraron pacientes que coincidan con la búsqueda.</span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</section>
    </main>
</div>

<div id="modalSeleccion" class="custom-modal">
    <div class="custom-modal-content" style="max-width: 850px;"> <span class="close-btn" onclick="cerrarModal('modalSeleccion')">&times;</span>
        <h3 style="text-align: center; margin-bottom: 25px; color: #1e293b;">¿A quién registraremos hoy?</h3>
        
        <div class="selection-cards-container">
            <div class="selection-card" onclick="cambiarModal('modalSeleccion', 'modalExistente')">
                <div class="card-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h4>Paciente Existente</h4>
                <p>Buscar en el expediente y agendar nueva cita o seguimiento.</p>
            </div>

            <div class="selection-card" onclick="abrirRegistroAdulto()">
                <div class="card-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h4>Paciente Nuevo</h4>
                <p>Crear un nuevo expediente clínico para un paciente adulto.</p>
            </div>

            <div class="selection-card" onclick="abrirRegistroMenor()">
                <div class="card-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fa-solid fa-child"></i>
                </div>
                <h4>Menor de Edad</h4>
                <p>Registrar a un menor incluyendo obligatoriamente los datos del tutor.</p>
            </div>
        </div>
    </div>
</div>

<div id="modalNuevo" class="custom-modal">
    <div class="custom-modal-content large">
        <span class="close-btn" onclick="cerrarModal('modalNuevo')">&times;</span>
        <h3>Registrar paciente y cita</h3>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f87171;">
                <p style="margin: 0; font-weight: bold;">Por favor, corrige los siguientes errores:</p>
                <ul style="margin: 5px 0 0 20px; font-size: 0.9em;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pacientes.store') }}" method="POST" id="formNuevo">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre(s) *</label>
                    <input type="text" name="nombre" maxlength="15" onkeypress="return soloLetras(event)" required value="{{ old('nombre') }}">
                </div>
                <div class="form-group">
                    <label>Apellido Paterno *</label>
                    <input type="text" name="apellido_paterno" maxlength="15" onkeypress="return soloLetras(event)" required value="{{ old('apellido_paterno') }}">
                </div>
                <div class="form-group">
                    <label>Apellido Materno *</label>
                    <input type="text" name="apellido_materno" maxlength="15" onkeypress="return soloLetras(event)" required value="{{ old('apellido_materno') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" id="contenedor_email">
                    <label>Email *</label>
                    <input type="email" name="email" maxlength="100" value="{{ old('email') }}" oninput="validarEmailInput(this)" onblur="limpiarEmailFinal(this)">
                </div>
                <div class="form-group" id="contenedor_telefono">
                    <label>Teléfono (Paciente) *</label>
                    <input type="text" name="telefono" id="tel_paciente" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('telefono') }}">
                </div>
                <div class="form-group" id="contenedor_grado" style="display: none;">
                    <label>Grado de estudio *</label>
                    <input type="text" name="grado_estudio" value="{{ old('grado_estudio') }}" placeholder="Ej. 2do de Primaria" oninput="validarCampoRealTime(this); controlarEspacios(this)" onblur="limpiarEspacios(this)">
                    <span id="error_grado_estudio" class="error-msg" style="color: #ef4444; font-size: 0.8rem; display: none;"></span>
                </div>
                <div class="form-group">
                    <label>CURP</label>
                    <input type="text" name="curp" maxlength="18" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" value="{{ old('curp') }}" required>
                </div>
                <div class="form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" min="1920-01-01" max="{{ date('Y-m-d') }}" value="{{ old('fecha_nacimiento') }}" oninput="controlarEspacios(this)" onblur="limpiarEspacios(this)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Sexo</label>
                    <select name="sexo" class="form-control" required>
                        <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>-- Seleccione --</option>
                        <option value="hombre" {{ old('sexo') == 'hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="mujer" {{ old('sexo') == 'mujer' ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>
                <div class="form-group" id="contenedor_ocupacion">
                    <label>Ocupación *</label>
                    <input type="text" name="ocupacion" value="{{ old('ocupacion') }}" placeholder="Ej. Empleado" maxlength="30">
                </div>
                <div class="form-group">
                    <label>Peso (kg)</label>
                    <input type="number" step="0.01" name="peso" value="{{ old('peso') }}" required>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">¿Presenta alergias? *</label>
                    <div style="display: flex; gap: 20px; align-items: center; height: 40px;">
                        <label><input type="radio" name="tiene_alergias" value="no" onclick="toggleAlergias(false)" checked required> No</label>
                        <label><input type="radio" name="tiene_alergias" value="si" onclick="toggleAlergias(true)"> Sí</label>
                    </div>
                </div>
                <div class="form-group" id="contenedor_alergias_detalle" style="display: none; flex: 2;">
                    <label>Especifique las alergias *</label>
                    <input type="text" name="alergias" id="input_alergias" placeholder="Ej: Penicilina..." value="{{ old('alergias') }}">
                </div>
            </div>

            <h4 style="margin-top:15px; border-bottom: 1px solid #eee;">Dirección</h4>
            <div class="form-row">
                <div class="form-group"><label>C.P. *</label><input type="text" name="codigo_postal" maxlength="5" required value="{{ old('codigo_postal') }}"></div>
                <div class="form-group"><label>Colonia *</label><input type="text" name="colonia" required value="{{ old('colonia') }}"></div>
                <div class="form-group"><label>Ciudad *</label><input type="text" name="ciudad" required value="{{ old('ciudad') }}"></div>
                <div class="form-group"><label>Estado *</label><input type="text" name="estado" required value="{{ old('estado') }}"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>No. Ext *</label><input type="text" name="num_ext" required value="{{ old('num_ext') }}"></div>
                <div class="form-group"><label>No. Int</label><input type="text" name="num_int" value="{{ old('num_int') }}"></div>
                <div class="form-group" style="flex: 2;"><label>Calle *</label><input type="text" name="calle" required value="{{ old('calle') }}"></div>
            </div>

            <div id="seccion_tutor" style="display: none; background: #fffcf0; border: 1px dashed #eab308; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #854d0e; font-size: 1rem;">
                    <i class="fa-solid fa-person-breastfeeding"></i> Datos del Padre o Tutor
                </h4>
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label>Nombre Completo *</label>
                        <input type="text" name="nombre_tutor" value="{{ old('nombre_tutor') }}">
                    </div>
                    <div class="form-group">
                        <label>Parentesco *</label>
                        <select name="parentesco_tutor">
                            <option value="">-- Seleccione --</option>
                            <option value="Madre">Madre</option>
                            <option value="Padre">Padre</option>
                            <option value="Tutor Legal">Tutor Legal</option>
                            <option value="Otro">Otro familiar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Teléfono Tutor *</label>
                        <input type="text" name="telefono_tutor" value="{{ old('telefono_tutor') }}">
                    </div>
                    <div class="form-group">
                        <label>Email Tutor *</label>
                        <input type="email" name="email_tutor" value="{{ old('email_tutor') }}">
                    </div>
                </div>
            </div> <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">¿A qué viene hoy? *</label>
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label><input type="radio" name="tipo_atencion" value="tratamiento" onclick="mostrarOpcionesAtencion('tratamiento')" required {{ old('tipo_atencion') == 'tratamiento' ? 'checked' : '' }}> Nuevo Plan</label>
                    <label><input type="radio" name="tipo_atencion" value="servicio" onclick="mostrarOpcionesAtencion('servicio')" {{ old('tipo_atencion') == 'servicio' ? 'checked' : '' }}> Servicio Rápido</label>
                </div>

                <div id="contenedor_tratamiento" style="display: none;">
                    <label>Seleccione el Tratamiento:</label>
                    <select name="id_cat_tratamiento" id="select_tratamiento" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
                        <option value="">-- Seleccionar --</option>
                        @foreach($catTratamientos as $t)
                            <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="contenedor_servicio" style="display: none;">
                    <label>Seleccione el Servicio:</label>
                    <select name="id_cat_servicio" id="select_servicio" class="form-control" onchange="consultarDuracionDB('servicio', this.value)">
                        <option value="">-- Seleccionar --</option>
                        @foreach($catServicios as $s)
                            <option value="{{ $s->id_cat_servicio }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Duración sugerida (min)</label>
                    <input type="number" id="duracion_sugerida" value="0" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Duración real de la cita (min) *</label>
                    <input type="number" name="duracion" id="duracion_real" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label>Motivo de la cita *</label>
                    <input type="text" name="motivo_consulta" required value="{{ old('motivo_consulta') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label style="font-weight: bold;">Seleccionar fecha y hora de la cita *</label>
                    <div id="calendarNuevo" style="min-height: 400px; border: 1px solid #ccc; margin-top: 10px; background: white;"></div>
                    <input type="hidden" name="fecha_cita" id="fechaCitaNuevo" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">Guardar Paciente y Cita</button>
        </form>
    </div>
</div>

<div id="modalExistente" class="custom-modal">
    <div class="custom-modal-content large">
        <span class="close-btn" onclick="cerrarModal('modalExistente')">&times;</span>
        <h3>Registrar cita – Paciente existente</h3>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f87171;">
                <p style="margin: 0; font-weight: bold;">Por favor, corrige los siguientes errores:</p>
                <ul style="margin: 5px 0 0 20px; font-size: 0.9em;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pacientes.store_cita_existente') }}" method="POST" id="formExistente">
            @csrf
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label>Buscar paciente *</label>
                    <select name="id_paciente" class="form-control" required style="width: 100%" onchange="actualizarTratamientosAlCambiarPaciente()">
                        <option value="">-- Seleccione --</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id_paciente }}" {{ old('id_paciente') == $p->id_paciente ? 'selected' : '' }}>
                                {{ $p->nombre }} {{ $p->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f0fdf4; border-radius: 8px; border: {{ $errors->has('tipo_atencion_ex') ? '2px solid #dc3545' : '1px solid #bbf7d0' }};">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">Tipo de atención *</label>
                <div style="display: flex; gap: 20px;">
                    <label><input type="radio" name="tipo_atencion_ex" value="seguimiento" onclick="mostrarOpcionesExistente('seguimiento')" {{ old('tipo_atencion_ex') == 'seguimiento' ? 'checked' : '' }} required> Seguimiento</label>
                    <label><input type="radio" name="tipo_atencion_ex" value="nuevo_tratamiento" onclick="mostrarOpcionesExistente('nuevo_tratamiento')" {{ old('tipo_atencion_ex') == 'nuevo_tratamiento' ? 'checked' : '' }}> Nuevo Tratamiento</label>
                    <label><input type="radio" name="tipo_atencion_ex" value="servicio" onclick="mostrarOpcionesExistente('servicio')" {{ old('tipo_atencion_ex') == 'servicio' ? 'checked' : '' }}> Servicio Rápido</label>
                </div>
            </div>

            <div id="contenedor_seguimiento_ex" style="display: none; margin-bottom: 15px;">
                <label>Seleccione el tratamiento actual:</label>
                <select name="id_tratamiento_existente" id="select_tratamiento_ex" onchange="consultarDuracionDB('tratamiento_seguimiento', this.value)" class="form-control">
                    <option value="">-- Seleccionar --</option>
                </select>
            </div>

            <div id="contenedor_nuevo_plan_ex" style="display: none; margin-bottom: 15px;">
                <label>Seleccione el Nuevo Tratamiento:</label>
                <select name="id_cat_tratamiento_nuevo" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
                    <option value="">-- Seleccionar del catálogo --</option>
                    @foreach($catTratamientos as $t)
                        <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div id="contenedor_servicio_ex" style="display: none; margin-bottom: 15px;">
                <label>Seleccione el Servicio:</label>
                <select name="id_cat_servicio" class="form-control" onchange="consultarDuracionDB('servicio', this.value)">
                    <option value="">-- Seleccionar Servicio --</option>
                    @foreach($catServicios as $s) 
                        <option value="{{ $s->id_cat_servicio }}">{{ $s->nombre }}</option> 
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Duración sugerida (minutos)</label>
                    <input type="number" name="duracion_sugerida" id="duracion_sugerida_ex" readonly style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                </div>
                <div class="form-group">
                    <label>Duración real (min) *</label>
                    <input type="number" name="duracion" id="duracion_real_ex" required>
                </div>
                <div class="form-group">
                    <label>Motivo *</label>
                    <input type="text" name="motivo_consulta" required oninput="validarMotivo(this)">
                </div>
            </div>

            <div class="form-row" style="display: block; clear: both; margin-top: 20px;">
                <div class="form-group full-width">
                    <label style="font-weight: bold;">Seleccionar fecha y hora de la cita *</label>
                    <div id="calendarExistente" style="min-height: 450px; border: 1px solid #ccc; background: white; width: 100%;"></div>
                    <input type="hidden" name="fecha_cita" id="fechaCitaExistente" required>
                    <div id="info-fecha-calendarExistente" style="margin-top: 10px; font-weight: bold; color: #0d6efd;"></div>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">Guardar Cita</button>
        </form>
    </div>
</div>

<script>
// --- LÓGICA DE VALIDACIÓN EN TIEMPO REAL ---
const validaciones = {
    nombre: (v) => {
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(v)) return "Solo se permiten letras";
        if (v.trim().length < 2 || v.trim().length > 15) return "Debe tener entre 2 y 15 caracteres";
        return true;
    },
    apellido_paterno: (v) => {
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(v)) return "Solo se permiten letras";
        if (v.trim().length < 2 || v.trim().length > 15) return "Debe tener entre 2 y 15 caracteres";
        return true;
    },
    apellido_materno: (v) => {
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/.test(v)) return "Solo se permiten letras";
        if (v.trim().length < 2 || v.trim().length > 15) return "Debe tener entre 2 y 15 caracteres";
        return true;
    },
    curp: (v) => v === "" || /^[A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[0-9,A-Z][0-9]$/.test(v) || "Formato de CURP inválido (18 caracteres)",
    codigo_postal: (v) => v === "" || /^[0-9]{5}$/.test(v) || "El CP debe tener 5 dígitos",
    peso: (v) => {
        if (v === "" || v === null) return true;
        const p = parseFloat(v);
        return (p >= 0.5 && p <= 500) || "Ingrese un peso válido (0.5 - 500 kg)";
    },
    duracion: (v) => {
        const d = parseInt(v);
        if (isNaN(d)) return "Ingrese un número válido";
        return (d >= 5 && d <= 480) || "La duración debe ser entre 5 y 480 min";
    },
    num_ext: (v) => v.trim() !== "" || "El número exterior es obligatorio",
    colonia: (v) => v.trim() !== "" || "Campo obligatorio",
    ciudad: (v) => v.trim() !== "" || "Campo obligatorio",
    estado: (v) => v.trim() !== "" || "Campo obligatorio",
    calle: (v) => v.trim() !== "" || "Campo obligatorio",
    alergias: (v) =>  {
        const tieneAlergiasSi = document.querySelector('input[name="tiene_alergias"][value="si"]');
        const esRequerido = tieneAlergiasSi && tieneAlergiasSi.checked;

        if (esRequerido) {
            if (!v || v.trim().length < 3) return "Especifique qué alergias presenta";
            if (v.toLowerCase() === "ninguna") return "Si marcó 'Sí', debe detallar la alergia";
            
            const regexValida = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ,]+$/;
            if (!regexValida.test(v)) return "Solo letras y comas";
        }
        return true;
    },
    motivo_consulta: (v) => {
        if (v.trim().length < 4) return "Especifique el motivo";
        const regexValida = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ .,]+$/;
        if (!regexValida.test(v)) return "No se permiten caracteres especiales";
        return true;
    },
    precio_estimado: (v) => v === "" || parseFloat(v) >= 0 || "El precio no puede ser negativo",
    diagnostico_inicial: (v) => true,
    nombre_tutor: (v) => {
        const seccionTutor = document.getElementById('seccion_tutor');
        const isVisible = seccionTutor && seccionTutor.style.display === 'block';
        if (isVisible && v.trim().length < 3) return "El nombre del tutor es obligatorio";
        return true;
    },
    telefono: (v) => {
        const seccionTutor = document.getElementById('seccion_tutor');
        const esMenor = seccionTutor && seccionTutor.style.display === 'block';
        if (v.trim() === "") {
            if (esMenor) return true;
            return "El teléfono es obligatorio para adultos";
        }
        return /^[0-9]{10}$/.test(v) || "Ingrese 10 dígitos numéricos";
    },
    telefono_tutor: (v) => {
        const seccionTutor = document.getElementById('seccion_tutor');
        const esMenor = seccionTutor && seccionTutor.style.display === 'block';
        if (esMenor) {
            return /^[0-9]{10}$/.test(v) || "El teléfono del tutor es obligatorio (10 dígitos)";
        }
        return true; 
    },email_tutor: (v) => {
        if (v.trim() === "") return true; 
        // Esta regex es más estricta para asegurar el punto y la extensión (min 2 letras)
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return regex.test(v) || "Formato inválido (ej: usuario@dominio.com)";
    },
    email: (v) => {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (v.trim() === "") return "El correo es obligatorio";
        if (!regex.test(v)) return "Formato de correo inválido";
        return true;
    }, grado_estudio: (v) => {
        const seccionTutor = document.getElementById('seccion_tutor');
        const esMenor = seccionTutor && seccionTutor.style.display === 'block';
        
        if (esMenor) {
            if (!v || v.trim().length < 3) return "Especifique el grado (ej. 2do Primaria)";
            const regexValida = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ. ]+$/;
            if (!regexValida.test(v)) return "No se permiten caracteres especiales";
        }
        return true;
    },
    ocupacion: (v) => {
        const seccionTutor = document.getElementById('seccion_tutor');
        const esMenor = seccionTutor && seccionTutor.style.display === 'block';

        // Si NO es menor (es adulto), la ocupación es obligatoria
        if (!esMenor) {
            if (!v || v.trim().length < 3) return "La ocupación es obligatoria para adultos";
            
            const regexValida = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/;
            if (!regexValida.test(v)) return "Solo se permiten letras";
        }
        return true;
    },
    fecha_nacimiento: (v) => {
        if (!v) return "La fecha de nacimiento es obligatoria";
        
        const fechaSeleccionada = new Date(v + "T00:00:00");
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaSeleccionada > hoy) {
            return "La fecha de nacimiento no puede ser futura";
        }

        // Calcular edad (Ahora sí está dentro de la función)
        let edad = hoy.getFullYear() - fechaSeleccionada.getFullYear();
        const m = hoy.getMonth() - fechaSeleccionada.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < fechaSeleccionada.getDate())) {
            edad--;
        }

        if (edad < 1) {
            console.warn("Paciente menor de 1 año detectado.");
        }

        return true;
    },
};

function initRealTimeValidation() {
    const forms = ['#formNuevo', '#formExistente'];
    forms.forEach(selector => {
        const form = document.querySelector(selector);
        if (!form) return;

        form.querySelectorAll('input, select').forEach(input => {
            // Crear span de error si no existe
            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                const span = document.createElement('span');
                span.className = 'error-message';
                span.style.display = 'none';
                input.parentNode.appendChild(span);
            }

            input.addEventListener('input', function() {
                const validador = validaciones[this.name];
                const errorSpan = this.parentNode.querySelector('.error-message');
                
                if (validador) {
                    if (this.value.toString().trim() === "") {
                        errorSpan.style.display = 'none';
                        this.classList.remove('input-error', 'input-success'); // Quitamos ambos colores
                        return; // Detenemos la validación aquí
                    }

                    const mensaje = validador(this.value);
                    if (mensaje !== true) {
                        this.classList.add('input-error');
                        this.classList.remove('input-success');
                        errorSpan.textContent = mensaje;
                        errorSpan.style.display = 'block';
                    } else {
                        this.classList.remove('input-error');
                        this.classList.add('input-success');
                        errorSpan.style.display = 'none';
                    }
                }
            });
            // Marcar el campo como "tocado" cuando el usuario sale de él
            input.addEventListener('blur', function() {
                if (this.value.trim() !== "") {
                    this.classList.add('touched');
                }
            });
        });
    });
}

// --- LÓGICA DE MODALES Y CALENDARIO ---
function abrirModal(id) {
    const modal = document.getElementById(id);
    if(modal) {
        modal.style.display = 'flex';
        
        // Si es el existente, esperamos 350ms para que el modal y el buscador se estabilicen
        const delay = (id === 'modalExistente') ? 350 : 150;

        setTimeout(() => {
            if (id === 'modalNuevo') {
                inicializarCalendario('calendarNuevo', 'fechaCitaNuevo');
            }
            
            if (id === 'modalExistente') {
                // Limpiar contenido previo para evitar duplicados
                const elEx = document.getElementById('calendarExistente');
                elEx.innerHTML = ''; 

                // Inicializar
                inicializarCalendario('calendarExistente', 'fechaCitaExistente');
                
                // REFRESCAR EL TAMAÑO (Esto quita lo amontonado)
                const calInst = FullCalendar.getCalendar(elEx);
                if (calInst) {
                    calInst.updateSize(); 
                    // Un segundo ajuste rápido para asegurar el 100%
                    setTimeout(() => calInst.updateSize(), 100);
                }
            }
        }, delay);
    }
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.style.display = 'none';

    // 1. Limpiar el formulario y errores de Laravel (Observación 1 y 2)
    const formulario = modal.querySelector('form');
    if (formulario) {
        prepararFormulario(formulario);
    }

    // 2. Limpiar rastros de calendario e información visual
    const infoFechas = [
        'info-fecha-calendarNuevo', 
        'info-fecha-calendarExistente',
        'info-fecha-calendarNuevo_ex' // por si acaso
    ];
    infoFechas.forEach(idInfo => {
        const el = document.getElementById(idInfo);
        if (el) el.innerHTML = '';
    });

    // 3. Limpiar inputs específicos que a veces quedan fuera del reset
    const camposExtra = [
        'fechaCitaNuevo', 'fechaCitaExistente', 
        'duracion_sugerida', 'duracion_real', 
        'duracion_sugerida_ex', 'duracion_real_ex'
    ];
    camposExtra.forEach(idField => {
        const el = document.getElementById(idField);
        if (el) {
            el.value = "";
            el.classList.remove('input-error', 'input-success');
        }
    });

    // 4. Resetear visibilidad de contenedores dinámicos
    const contenedores = [
        'contenedor_tratamiento', 'contenedor_servicio', 
        'contenedor_seguimiento_ex', 'contenedor_servicio_ex',
        'contenedor_nuevo_plan_ex', 'precio_estimado', 'seccion_tutor'
    ];
    contenedores.forEach(c => {
        const el = document.getElementById(c);
        if (el) el.style.display = 'none';
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        cerrarModal('modalNuevo');
        cerrarModal('modalExistente');
        cerrarModal('modalSeleccion');
    }
});

function cambiarModal(c, a) { cerrarModal(c); setTimeout(() => abrirModal(a), 50); }

function inicializarCalendario(idDiv, idInput) {
    const el = document.getElementById(idDiv);
    const input = document.getElementById(idInput);
    if (!el) return;
    el.innerHTML = ''; 

    let cal = new FullCalendar.Calendar(el, {
        locale: 'es',
        timeZone: 'local',   
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            day: 'Día'
        },
        // Carga de citas ocupadas desde el controlador
        events: {
            url: '/api/citas-ocupadas',
            method: 'GET',
            failure: function() {
                console.error("Error al cargar las citas ocupadas.");
            }
        },
        // Configuración de horario de oficina
        eventDisplay: 'block',
        slotMinTime: "08:00:00",
        slotMaxTime: "21:00:00",
        allDaySlot: false,
        slotDuration: '00:15:00', // Franjas de 15 min para mejor visualización
        
        // No permitir fechas pasadas
        validRange: { 
            start: new Date().toISOString().split('T')[0] 
        },
        
        dateClick: function(info) {
            const ahora = new Date();
            
            // 1. Si estamos en vista de MES, al hacer clic saltamos al DÍA
            if (info.view.type === 'dayGridMonth') {
                if (info.date < ahora.setHours(0,0,0,0)) return;
                cal.changeView('timeGridDay', info.dateStr);
            } 
            // 2. Si estamos en vista de DÍA, seleccionamos la hora
            else {
                // Validar que no sea una hora pasada del mismo día
                if (info.date < ahora) {
        alert("No puedes seleccionar una hora que ya pasó.");
        return;
    }

    // --- NUEVO: VALIDAR CHOQUE POR DURACIÓN ---
    // Buscamos la duración según el modal (Nuevo o Existente)
    const inputDuracion = idDiv === 'calendarNuevo' 
        ? document.querySelector('input[name="duracion"]') 
        : document.getElementById('duracion_real_ex');
    
    const duracion = parseInt(inputDuracion?.value) || 30;

    if (verificarChoqueHorario(info.dateStr, duracion, cal)) {
        alert("⚠️ Conflicto de horario: La duración de esta cita ( " + duracion + " min) invade el tiempo de otra cita ya programada.");
        input.value = ""; // Vaciamos el input
        return; // Detenemos la ejecución
    }

    // --- NUEVA LÓGICA DE VALIDACIÓN DE DUPLICADOS ---
    const idPaciente = document.querySelector('select[name="id_paciente"]')?.value;
    const fechaSeleccionada = info.dateStr.split('T')[0]; // Obtiene YYYY-MM-DD

    if (idPaciente && fechaSeleccionada) {
        fetch(`/validar-cita-duplicada?id_paciente=${idPaciente}&fecha=${fechaSeleccionada}`)
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    // Si ya tiene cita, avisamos y pintamos el mensaje en rojo
                    alert("⚠️ ¡Atención! Este paciente ya tiene una cita agendada para este día.");
                    
                    const label = document.getElementById('info-fecha-' + idDiv);
                    if (label) {
                        label.style.color = "#dc3545"; // Rojo de error
                        label.innerHTML = "❌ El paciente ya tiene cita este día.";
                    }
                    input.value = ""; // Vaciamos el input para evitar el envío
                } else {
                    // Si NO tiene cita, procedemos normal
                    input.value = info.dateStr;
                    marcarSlotSeleccionado(info, idDiv, el);
                }
            });
    } else {
        // Si es un paciente nuevo (no hay id_paciente aún), procedemos normal
        input.value = info.dateStr;
        marcarSlotSeleccionado(info, idDiv, el);
    }
            }
        },
        // Estilo para las citas ocupadas
        eventDidMount: function(info) {
            info.el.title = "Horario ocupado";
            info.el.style.cursor = 'not-allowed';
        }
    });

    cal.render();
    
    // Ajustar el tamaño del calendario (importante para modales)
    setTimeout(() => { cal.updateSize(); }, 200);
}

// Función auxiliar para no repetir el feedback visual
function marcarSlotSeleccionado(info, idDiv, el) {
    document.querySelectorAll('.fc-timegrid-slot').forEach(s => s.style.background = "");
    info.dayEl.style.background = "#d1e7ff";
    
    let label = document.getElementById('info-fecha-' + idDiv);
    if(!label){
        label = document.createElement('div'); 
        label.id = 'info-fecha-' + idDiv;
        label.style.marginTop = "10px";
        label.style.fontWeight = "bold";
        el.after(label);
    }
    label.style.color = "#0d6efd"; // Azul normal
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    label.innerHTML = "📅 Seleccionado: " + info.date.toLocaleString('es-MX', opciones);
}
// --- FUNCIONES AUXILIARES (AJAX Y FILTROS) ---
function mostrarOpcionesAtencion(tipo) {
    const divTratamiento = document.getElementById('contenedor_tratamiento');
    const divServicio = document.getElementById('contenedor_servicio');

    // 1. Mostrar/Ocultar contenedores
    divTratamiento.style.display = tipo === 'tratamiento' ? 'block' : 'none';
    divServicio.style.display = tipo === 'servicio' ? 'block' : 'none';

    // 2. LIMPIEZA DE VALORES
    // Buscamos los selects dentro de cada contenedor y reseteamos su valor
    const selectTrat = divTratamiento.querySelector('select');
    const selectServ = divServicio.querySelector('select');

    if (tipo === 'tratamiento') {
        // Si eligió tratamiento, limpiamos el de servicio
        if (selectServ) selectServ.value = "";
    } else {
        // Si eligió servicio, limpiamos el de tratamiento
        if (selectTrat) selectTrat.value = "";
    }

    // 3. Opcional: Limpiar la duración sugerida al cambiar de tipo
    const sugNuevo = document.getElementById('duracion_sugerida');
    if (sugNuevo) sugNuevo.value = "";
}

function consultarDuracionDB(tipo, id) {
    if (!id) return;

    fetch(`/obtener-duracion?tipo=${tipo}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const minutos = data.duracion || 0;
            
            // Llenar sugerida en Modal Nuevo (si existe el campo)
            const sugNuevo = document.getElementById('duracion_sugerida');
            if (sugNuevo) sugNuevo.value = minutos;

            // Llenar sugerida en Modal Existente (el que nos interesa ahora)
            const sugEx = document.getElementById('duracion_sugerida_ex');
            if (sugEx) {
                sugEx.value = minutos;
                // Si tienes una función que calcula la hora fin basada en la duración, llámala aquí
                if (typeof calcularHoraFinEx === 'function') {
                    calcularHoraFinEx();
                }
            }
        })
        .catch(error => console.error('Error al obtener duración:', error));
}

function soloLetras(e) {
    let key = e.keyCode || e.which;
    let tecla = String.fromCharCode(key).toLowerCase();
    let letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    //if (letras.indexOf(tecla) == -1 && key != 8 && key != 46) e.preventDefault();
    if (letras.indexOf(tecla) == -1 && key != 8 && key != 46 && key != 13) {
        e.preventDefault();
        return false;
    }
    return true;
}


// Función para cargar tratamientos (tu original)
function mostrarOpcionesExistente(tipo) {
    const divSeguimiento = document.getElementById('contenedor_seguimiento_ex');
    const divNuevoPlan = document.getElementById('contenedor_nuevo_plan_ex');
    const divServicio = document.getElementById('contenedor_servicio_ex');
    
    const selectPac = document.querySelector('#modalExistente select[name="id_paciente"]');
    const idP = selectPac ? selectPac.value : '';

    divSeguimiento.style.display = 'none';
    divNuevoPlan.style.display = 'none';
    divServicio.style.display = 'none';

    // LIMPIAR VALORES para que no se envíen datos cruzados
    document.getElementById('select_tratamiento_ex').value = "";
    document.querySelector('select[name=\"id_cat_tratamiento_nuevo\"]').value = "";
    document.querySelector('select[name=\"id_cat_servicio\"]').value = "";
    document.getElementById('duracion_sugerida_ex').value = ""; // Limpiar duración al cambiar tipo

    if (tipo === 'seguimiento') {
        if (!idP) { alert("Seleccione un paciente primero."); return; }
        divSeguimiento.style.display = 'block';
        const sel = document.getElementById('select_tratamiento_ex');
        sel.innerHTML = '<option value="">-- Cargando tratamientos... --</option>';

        fetch(`/pacientes/${idP}/tratamientos-activos`)
            .then(r => r.json())
            .then(data => {
                sel.innerHTML = data.length === 0 ? '<option value="">-- Sin tratamientos activos --</option>' : '<option value="">-- Seleccionar Tratamiento en Curso --</option>';
                data.forEach(t => {
                    sel.innerHTML += `<option value="${t.id_tratamiento}">${t.nombre}</option>`;
                });
            });

    } else if (tipo === 'nuevo_tratamiento') {
        if (!idP) { alert("Seleccione un paciente primero."); return; }
        divNuevoPlan.style.display = 'block';
        
        // --- LÓGICA DE FILTRADO PARA NO REPETIR ---
        // Buscamos el select por el nombre que me pasaste
        const selectCat = document.querySelector('select[name="id_cat_tratamiento_nuevo"]');
        if (!selectCat) return;

        // --- PASO 1: RESETEAR EL SELECT ---
        // Esto quita los bloqueos y textos del paciente anterior
        Array.from(selectCat.options).forEach(opt => {
            opt.disabled = false;
            opt.style.color = '';
            opt.text = opt.text.replace(' (YA ESTÁ EN CURSO)', '');
        });

        // --- PASO 2: APLICAR EL FILTRO DEL NUEVO PACIENTE ---
        fetch(`/pacientes/${idP}/tratamientos-activos`)
            .then(r => r.json())
            .then(data => {
                const idsActivos = data.map(t => parseInt(t.id_cat_tratamiento));

                Array.from(selectCat.options).forEach(opt => {
                    if (opt.value === "") return;

                    if (idsActivos.includes(parseInt(opt.value))) {
                        opt.disabled = true;
                        opt.style.color = '#a0aec0';
                        // Solo agregamos el texto si no lo tiene (evita duplicados)
                        if (!opt.text.includes('(YA ESTÁ EN CURSO)')) {
                            opt.text += ' (YA ESTÁ EN CURSO)';
                        }
                    }
                });
            });

    } else if (tipo === 'servicio') {
        divServicio.style.display = 'block';
    }
}

function actualizarTratamientosAlCambiarPaciente() {
    const modalEx = document.getElementById('modalExistente');
    if (!modalEx) return;

    // Buscamos cuál de los radios está seleccionado actualmente
    const radioSeleccionado = modalEx.querySelector('input[name="tipo_atencion_ex"]:checked');
    
    // Si hay un radio seleccionado, disparamos la lógica de mostrarOpcionesExistente
    // Esto refrescará tanto la lista de seguimientos como el bloqueo del catálogo según sea el caso
    if (radioSeleccionado) {
        mostrarOpcionesExistente(radioSeleccionado.value);
    }
}

document.addEventListener('input', function (event) {
    if (event.target.tagName.toLowerCase() !== 'textarea') return;
    
    // Auto-ajuste de altura
    event.target.style.height = 'auto';
    event.target.style.height = (event.target.scrollHeight) + 'px';
}, false);


// --- FUNCIONES DE APERTURA DE REGISTRO ---

// --- FUNCIONES DE APERTURA DE REGISTRO CORREGIDAS ---
function abrirRegistroAdulto(esError = false) {
    document.querySelectorAll('.alert-danger').forEach(a => a.remove());
    const form = document.getElementById('formNuevo');

    // Solo limpiar y mover modales si NO es un error de validación
    if (esError === false) {
        prepararFormulario(form);
        cambiarModal('modalSeleccion', 'modalNuevo');
        toggleAlergias(false);
    }

    const hoy = new Date();
    const hace18Anios = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate()).toISOString().split('T')[0];
    const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
    if (inputFecha) {
        inputFecha.max = hace18Anios;
        inputFecha.min = "1920-01-01";
    }

    const seccionTutor = document.getElementById('seccion_tutor');
    if (seccionTutor) {
        seccionTutor.style.display = 'none';
        gestionarAtributosTutor(false);
    }
}

function abrirRegistroMenor(esError = false) {
    document.querySelectorAll('.alert-danger').forEach(a => a.remove());
    const form = document.getElementById('formNuevo');

    if (esError === false) {
        prepararFormulario(form);
        cambiarModal('modalSeleccion', 'modalNuevo');
        toggleAlergias(false);
    }

    const hoy = new Date();
    const hace18Anios = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate()).toISOString().split('T')[0];
    const fechaHoy = hoy.toISOString().split('T')[0];

    const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
    if (inputFecha) {
        inputFecha.min = hace18Anios;
        inputFecha.max = fechaHoy;
    }

    const seccionTutor = document.getElementById('seccion_tutor');
    if (seccionTutor) {
        seccionTutor.style.display = 'block';
        gestionarAtributosTutor(true);
    }
}

// --- FUNCIONES AUXILIARES PARA EVITAR REPETIR CÓDIGO ---

function prepararFormulario(form) {
    if (!form) return;
    // 1. El reset() ya limpia la mayoría de los campos a su estado original
    form.reset();

    // 2. Limpiar clases y valores, pero PROTEGIENDO el token
    form.querySelectorAll('input, select, textarea').forEach(i => {
        i.classList.remove('input-error', 'input-success', 'touched', 'is-invalid');
        
        // NO borramos el valor si es el token CSRF o el campo _method
        if (i.name !== '_token' && i.name !== '_method') {
            // Solo borramos valores de campos que no sean radios o checkboxes (el reset ya se encarga)
            if (i.type !== 'radio' && i.type !== 'checkbox') {
                i.value = "";
            }
        }
    });

    // 3. Ocultar mensajes de error de JS
    form.querySelectorAll('.error-message').forEach(m => {
        m.style.display = 'none';
        m.textContent = '';
    });

    // 4. Eliminar alertas de Laravel (tu lógica original)
    const erroresLaravel = form.querySelectorAll('.invalid-feedback, .text-danger, .alert-danger');
    erroresLaravel.forEach(e => e.remove());
    toggleAlergias(false);
}

function validarMotivo(input) {
    // Permite letras, números, espacios, puntos y comas.
    // Remueve todo lo demás instantáneamente.
    const regex = /[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ .,]/g;
    input.value = input.value.replace(regex, '');
    
    // Aprovechamos para usar tu función de controlar espacios que ya tienes
    controlarEspacios(input); 
}

function gestionarAtributosTutor(esRequerido) {
    // 1. Referencias a contenedores (para mostrar/ocultar)
    const seccionTutor = document.getElementById('seccion_tutor');
    const contEmail = document.getElementById('contenedor_email');
    const contTel = document.getElementById('contenedor_telefono');
    const contOcupacion = document.getElementById('contenedor_ocupacion');
    const contGrado = document.getElementById('contenedor_grado');

    // 2. Manejo de visibilidad (Display)
    if (seccionTutor) seccionTutor.style.display = esRequerido ? 'block' : 'none';
    if (contEmail)    contEmail.style.display    = esRequerido ? 'none'  : 'block';
    if (contTel)      contTel.style.display      = esRequerido ? 'none'  : 'block';
    if (contOcupacion) contOcupacion.style.display = esRequerido ? 'none'  : 'block';
    if (contGrado)     contGrado.style.display     = esRequerido ? 'block' : 'none';

    // 3. Campos del Tutor (Obligatorios si es menor, se limpian si es adulto)
    const camposTutor = [
        'input_nombre_tutor',
        'select_parentesco',
        'input_tel_tutor',
        'input_email_tutor'
    ];

    camposTutor.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.required = esRequerido;
            if (!esRequerido) el.value = ""; 
        }
    });

    // 4. Campo Grado de Estudio (Obligatorio si es menor, se limpia si es adulto)
    const inputGrado = document.getElementsByName('grado_estudio')[0];
    if (inputGrado) {
        inputGrado.required = esRequerido;
        if (!esRequerido) inputGrado.value = "";
    }

    // 5. Campos de Adulto (Obligatorios si NO es menor)
    // Usamos el "name" para asegurar coincidencia con el Controller
    const emailPaciente = document.getElementsByName('email')[0];
    const telPaciente = document.getElementsByName('telefono')[0];
    const ocupacionPaciente = document.getElementsByName('ocupacion')[0];

    if (emailPaciente)     emailPaciente.required = !esRequerido;
    if (telPaciente)       telPaciente.required   = !esRequerido;
    if (ocupacionPaciente) ocupacionPaciente.required = !esRequerido;

    if (esRequerido && ocupacionPaciente) {
        // Si ahora es MENOR, reseteamos el campo de ocupación (que es para adultos)
        ocupacionPaciente.value = "";
        ocupacionPaciente.classList.remove('input-error', 'input-success');
        const errOcup = ocupacionPaciente.parentNode.querySelector('.error-message');
        if (errOcup) errOcup.style.display = 'none';
    }
}


// 1. Evita que el usuario escriba dos espacios seguidos mientras teclea
function controlarEspacios(input) {
    // Reemplaza dos o más espacios consecutivos por uno solo
    input.value = input.value.replace(/\s{2,}/g, ' ');
    
    // Evita que el primer carácter sea un espacio
    if (input.value.startsWith(' ')) {
        input.value = input.value.trim();
    }
}

// 2. Limpia el texto cuando el usuario sale del campo (limpieza final)
function limpiarEspacios(input) {
    // Trim elimina espacios al inicio y al final
    // El regex limpia cualquier doble espacio que haya quedado
    input.value = input.value.trim().replace(/\s{2,}/g, ' ');
}

function validarEmailInput(input) {
    // 1. Elimina cualquier espacio en blanco en tiempo real (en cualquier posición)
    input.value = input.value.replace(/\s+/g, '');

    // 2. Opcional: Solo permite caracteres válidos para un correo
    // Esto bloquea letras con acentos, eñes y símbolos raros como #$%&
    input.value = input.value.replace(/[^a-zA-Z0-9@._-]/g, '');
}

function limpiarEmailFinal(input) {
    // 1. Asegura que no queden espacios (por si pegaron el texto)
    input.value = input.value.trim().toLowerCase(); // Los correos siempre se guardan mejor en minúsculas
}


function toggleAlergias(mostrar) {
    const contenedor = document.getElementById('contenedor_alergias_detalle');
    const input = document.getElementById('input_alergias');
    const errorSpan = contenedor.querySelector('.error-message');

    if (mostrar) {
        // --- CUANDO ELIGE SI ---
        contenedor.style.display = 'block';
        input.required = true;
        
        // Si el valor actual es "Ninguna", lo limpiamos para que el usuario escriba
        if (input.value === "Ninguna") {
            input.value = "";
        }
        input.focus(); 
    } else {
        // --- CUANDO ELIGE NO ---
        contenedor.style.display = 'none';
        input.required = false;
        
        // Asignamos "Ninguna" para que se envíe eso a la base de datos
        input.value = "Ninguna";
        input.classList.remove('input-error');
        input.classList.add('input-success');
        if (errorSpan) errorSpan.style.display = 'none';
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // 1. Inicializar validaciones básicas
    if (typeof initRealTimeValidation === 'function') initRealTimeValidation();

    // --- CORRECCIÓN PUNTO 6: Validación de Edad 0 o futura ---
    document.querySelectorAll('input[name="fecha_nacimiento"]').forEach(input => {
        input.addEventListener('change', function() {
            const fechaNac = new Date(this.value + "T00:00:00");
            const hoy = new Date();
            hoy.setHours(0,0,0,0);

            if (fechaNac > hoy) {
                alert("⚠️ Error: La fecha de nacimiento no puede ser futura.");
                this.value = "";
            } else if (fechaNac.toDateString() === hoy.toDateString()) {
                alert("⚠️ Nota: Registrando paciente con fecha de nacimiento de hoy (menor de 1 año).");
            }
        });
    });

    @if ($errors->any())
        // DETERMINAR A QUÉ MODAL REGRESAR
        @if (old('id_paciente'))
            /* --- MODAL PACIENTE EXISTENTE --- */
            abrirModal('modalExistente');
            
            @if(old('tipo_atencion_ex'))
                setTimeout(() => {
                    mostrarOpcionesExistente('{{ old("tipo_atencion_ex") }}');
                    if(document.getElementById('select_tratamiento_ex')) 
                        document.getElementById('select_tratamiento_ex').value = "{{ old('id_tratamiento_existente') }}";
                    if(document.getElementById('duracion_real_ex'))
                        document.getElementById('duracion_real_ex').value = "{{ old('duracion') }}";
                }, 400);
            @endif
        @else
            /* --- MODAL PACIENTE NUEVO --- */
            abrirModal('modalNuevo');
            @if (old('nombre_tutor'))
                abrirRegistroMenor(true);
                document.getElementById('seccion_tutor').style.display = 'block';
            @elseif(old('grado_estudio'))
                abrirRegistroAdulto(true);
            @endif
        @endif

        // --- CORRECCIÓN PUNTO 4: Persistencia de Alergias ---
        @if(old('tiene_alergias') == 'si')
            toggleAlergias(true);
            const inputAlergias = document.getElementById('input_alergias');
            if(inputAlergias) {
                inputAlergias.value = "{!! addslashes(old('alergias')) !!}";
            }
        @endif
    @endif

    // --- CORRECCIÓN PUNTOS 1, 2 Y AUDITORÍA: Cambio en tiempo real ---
    const camposCita = '#duracion_real, #duracion_real_ex, #fechaCitaNuevo, #fechaCitaExistente';
    
    document.querySelectorAll(camposCita).forEach(input => {
        input.addEventListener('change', function() {
            const esNuevo = (this.id.includes('Nuevo') || this.id === 'duracion_real');
            const idInputFecha = esNuevo ? 'fechaCitaNuevo' : 'fechaCitaExistente';
            const idInputDur = esNuevo ? 'duracion_real' : 'duracion_real_ex';
            const idCal = esNuevo ? 'calendarNuevo' : 'calendarExistente';
            
            const fechaVal = document.getElementById(idInputFecha).value;
            const duracionVal = parseInt(document.getElementById(idInputDur).value);
            const elCal = document.getElementById(idCal);
            
            if (fechaVal && duracionVal > 0 && elCal) {
                const calendarInstance = FullCalendar.getCalendar(elCal);
                if (calendarInstance && verificarChoqueHorario(fechaVal, duracionVal, calendarInstance)) {
                    alert("⚠️ ERROR DE HORARIO: La cita se empalma con otra ya programada.");
                    const inputError = document.getElementById(idInputDur);
                    inputError.style.border = "2px solid red";
                    inputError.style.backgroundColor = "#fee2e2";
                } else {
                    const inputOk = document.getElementById(idInputDur);
                    inputOk.style.border = "";
                    inputOk.style.backgroundColor = "";
                }
            }
        });
    });

    // --- BLOQUEO DEFINITIVO EN EL SUBMIT ---
    const formNuevo = document.getElementById('formNuevo');
    if (formNuevo) {
        formNuevo.addEventListener('submit', function(e) {
            // 1. Validar correo tutor
            const emailTutor = document.querySelector('input[name="email_tutor"]');
            if (emailTutor && emailTutor.value.trim() !== "") {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
                if (!regex.test(emailTutor.value)) {
                    e.preventDefault();
                    alert("❌ Formato de correo del tutor inválido.");
                    return false;
                }
            }
            
            // 2. Validar choque horario
            const fecha = document.getElementById('fechaCitaNuevo').value;
            const dur = parseInt(document.getElementById('duracion_real')?.value) || 30;
            const elCal = document.getElementById('calendarNuevo');
            if (fecha && elCal) {
                const cal = FullCalendar.getCalendar(elCal);
                if (cal && verificarChoqueHorario(fecha, dur, cal)) {
                    e.preventDefault();
                    alert("⚠️ Error: No se puede guardar, la cita se empalma.");
                    return false;
                }
            }
        });
    }

    const formExistente = document.getElementById('formExistente');
    if (formExistente) {
        formExistente.addEventListener('submit', function(e) {
            const fecha = document.getElementById('fechaCitaExistente').value;
            const dur = parseInt(document.getElementById('duracion_real_ex')?.value) || 30;
            const calElement = document.getElementById('calendarExistente');
            
            if (fecha && calElement) {
                const cal = FullCalendar.getCalendar(calElement);
                if (cal && verificarChoqueHorario(fecha, dur, cal)) {
                    e.preventDefault();
                    alert("⚠️ Error: Esta cita se empalma con otra.");
                    return false;
                }
            }
        });
    }
}); // AQUÍ termina correctamente el DOMContentLoaded

function verificarChoqueHorario(fechaHoraInicio, duracionMinutos, calendar, idCitaActual = null) {
    // 1. Convertir la nueva cita a tiempos (milisegundos)
    const inicioNueva = new Date(fechaHoraInicio).getTime();
    const finNueva = inicioNueva + (duracionMinutos * 60000);

    // 2. Obtener todos los eventos cargados en el calendario
    const citasExistentes = calendar.getEvents();

    for (let cita of citasExistentes) {
        // Ignorar la misma cita si estamos editando
        if (idCitaActual && String(cita.id) === String(idCitaActual)) continue;

        // 3. Obtener tiempos de la cita que ya está en el calendario
        const inicioExistente = new Date(cita.start).getTime();
        
        // Si la cita no tiene 'end' definido, usamos su duración guardada o 30 min por defecto
        let finExistente;
        if (cita.end) {
            finExistente = new Date(cita.end).getTime();
        } else {
            const dur = cita.extendedProps.duracion || 30;
            finExistente = inicioExistente + (dur * 60000);
        }

        // 4. LÓGICA DE TRASLAPE (EL CORAZÓN DEL PROBLEMA)
        // Hay choque si: El inicio de la nueva es antes del fin de la existente
        // Y el fin de la nueva es después del inicio de la existente.
        if (inicioNueva < finExistente && finNueva > inicioExistente) {
            console.warn("¡CHOQUE DETECTADO!");
            return true; 
        }
    }
    return false; 
}

function filterPatients() {
    const input = document.getElementById("patientSearch");
    const filter = input.value.toLowerCase();
    const table = document.querySelector(".data-table");
    const tr = table.getElementsByTagName("tr");
    const noResultsRow = document.getElementById("noResultsRow");
    
    let visibleCount = 0;

    // Empezamos en 1 para saltar el encabezado, y terminamos antes de la fila de "No hay resultados"
    // (tr.length - 1) porque la última fila es la de 'noResultsRow'
    for (let i = 1; i < tr.length - 1; i++) {
        let row = tr[i];
        let textContent = row.innerText.toLowerCase();

        if (textContent.indexOf(filter) > -1) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    }

    // Mostrar u ocultar el mensaje de "No se encontraron resultados"
    if (visibleCount === 0) {
        noResultsRow.style.display = "";
    } else {
        noResultsRow.style.display = "none";
    }
}

function verPaciente(id) {
    // Redirige al historial usando el ID del paciente
    // La URL quedará algo como: /asistente/historial/5
    window.location.href = "{{ url('/asistente/historial') }}/" + id;
}
</script>
@endsection