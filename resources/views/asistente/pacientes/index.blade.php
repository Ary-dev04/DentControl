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
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <section class="table-section">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pacientes as $p)
                    <tr>
                        <td>{{ $p->nombre }} {{ $p->apellido_paterno }}</td>
                        <td>{{ $p->telefono ?? 'N/A' }}</td>
                        <td>{{ $p->email }}</td>
                        <td>
                            <button class="btn-secondary" onclick="verPaciente({{ $p->id_paciente }})">Ver</button>
                        </td>
                    </tr>
                    @endforeach
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
                    <input type="text" name="nombre" maxlength="50" required value="{{ old('nombre') }}">
                </div>
                <div class="form-group">
                    <label>Apellido Paterno *</label>
                    <input type="text" name="apellido_paterno" maxlength="50" required value="{{ old('apellido_paterno') }}">
                </div>
                <div class="form-group">
                    <label>Apellido Materno *</label>
                    <input type="text" name="apellido_materno" maxlength="50" value="{{ old('apellido_materno') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
    <label>Email *</label>
    <input type="email" name="email" maxlength="100" required 
           value="{{ old('email') }}"
           oninput="validarEmailInput(this)"
           onblur="limpiarEmailFinal(this)">
</div>
                <div class="form-group">
    <label>Teléfono (Paciente)</label>
    <input type="text" name="telefono" id="tel_paciente" maxlength="10" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
           value="{{ old('telefono') }}">
    <small style="color: #6c757d;">* Opcional para menores</small>
</div>
                <div class="form-group">
                    <label>CURP</label>
                    <input type="text" name="curp" maxlength="18" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" value="{{ old('curp') }}">
                </div>
                <div class="form-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" min="1920-01-01" max="{{ date('Y-m-d') }}" value="{{ old('fecha_nacimiento') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Sexo</label>
                    <select name="sexo">
                        <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>-- Seleccione --</option>
                        <option value="hombre" {{ old('sexo') == 'hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="mujer" {{ old('sexo') == 'mujer' ? 'selected' : '' }}>Mujer</option>
                    </select>
                </div>
                <div class="form-group"><label>Ocupación</label><input type="text" name="ocupacion" value="{{ old('ocupacion') }}"></div>
                <div class="form-group"><label>Peso (kg)</label><input type="number" step="0.01" name="peso" value="{{ old('peso') }}"></div>
                <div class="form-group">
                    <label>Alergias *</label>
                    <input type="text" name="alergias" placeholder="Ej: Penicilina o Ninguna" required value="{{ old('alergias') }}">
                </div>
            </div>

            <h4 style="margin-top:15px; border-bottom: 1px solid #eee;">Dirección</h4>
            <div class="form-row">
                <div class="form-group"><label>Código Postal *</label><input type="text" name="codigo_postal" maxlength="5" required value="{{ old('codigo_postal') }}"></div>
                <div class="form-group"><label>Colonia *</label><input type="text" name="colonia" required value="{{ old('colonia') }}"></div>
                <div class="form-group"><label>Ciudad *</label><input type="text" name="ciudad" required value="{{ old('ciudad') }}"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Estado *</label><input type="text" name="estado" required value="{{ old('estado') }}"></div>
                <div class="form-group">
        <label>No. Exterior *</label>
        <input type="text" name="num_ext" required 
               value="{{ old('num_ext') }}"
               oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, ''); controlarEspacios(this)"
               onblur="limpiarEspacios(this)">
    </div>
    <div class="form-group">
        <label>No. Interior</label>
        <input type="text" name="num_int" 
               value="{{ old('num_int') }}"
               oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, ''); controlarEspacios(this)"
               onblur="limpiarEspacios(this)">
    </div>
    <div class="form-group">
        <label>Calle *</label>
        <input type="text" name="calle" required 
               value="{{ old('calle') }}"
               oninput="this.value = this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]/g, ''); controlarEspacios(this)"
               onblur="limpiarEspacios(this)">
    </div>
            </div>
            
            <div id="seccion_tutor" style="display: none; background: #fffcf0; border: 1px dashed #eab308; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <h4 style="margin-top: 0; color: #854d0e; font-size: 1rem;">
        <i class="fa-solid fa-person-breastfeeding"></i> Datos del Padre o Tutor
    </h4>
    <div class="form-row">
        <div class="form-group" style="flex: 2;">
    <label>Nombre Completo *</label>
    <input type="text" name="nombre_tutor" id="input_nombre_tutor" 
        placeholder="Ej. María Pérez García"
        onkeypress="return soloLetras(event)"
        oninput="controlarEspacios(this); this.value = this.value.replace(/[0-9]/g, '')"
        onblur="limpiarEspacios(this)">
</div>
        <div class="form-group">
            <label>Parentesco *</label>
            <select name="parentesco_tutor" id="select_parentesco">
                <option value="">-- Seleccione --</option>
                <option value="Madre">Madre</option>
                <option value="Padre">Padre</option>
                <option value="Tutor Legal">Tutor Legal</option>
                <option value="Otro">Otro familiar</option>
            </select>
        </div>
        <div class="form-group">
            <label>Teléfono Tutor *</label>
            <input type="text" name="telefono_tutor" id="input_tel_tutor" 
                maxlength="10" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
    </div>
</div>

            <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">¿A qué viene hoy? *</label>
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label>
    <input type="radio" name="tipo_atencion" value="tratamiento" 
    onclick="mostrarOpcionesAtencion('tratamiento')" required
    {{ old('tipo_atencion') == 'tratamiento' ? 'checked' : '' }}> Nuevo Plan
</label>
<label>
    <input type="radio" name="tipo_atencion" value="servicio" 
    onclick="mostrarOpcionesAtencion('servicio')"
    {{ old('tipo_atencion') == 'servicio' ? 'checked' : '' }}> Servicio Rápido
</label>
                </div>

                <div id="contenedor_tratamiento" style="display: none;">
    <label>Seleccione el Tratamiento:</label>
    <select name="id_cat_tratamiento" id="select_tratamiento" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
        <option value="">-- Seleccionar --</option>
        @foreach($catTratamientos as $t)
            <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
        @endforeach
    </select>

    <div class="form-group" style="margin-top: 12px;">
        <label>Precio Estimado (Opcional)</label>
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="font-weight: bold;">$</span>
            <input type="number" name="precio_estimado" id="precio_estimado_input" step="0.01" class="form-control" placeholder="0.00">
        </div>
        <small style="color: #6c757d;">Presupuesto global aproximado.</small>
    </div>

    <div class="form-group" style="margin-top: 12px;">
        <label>Diagnóstico Inicial / Notas del Plan (Opcional)</label>
        <textarea name="diagnostico_inicial" class="form-control" rows="2" placeholder="Ej: Paciente requiere ortodoncia por apiñamiento severo..."></textarea>
    </div>
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
                <div class="form-group full-width">
                    <label style="font-weight: bold;">Seleccionar fecha y hora de la cita *</label>
                    <div id="calendarNuevo" style="min-height: 400px; border: 1px solid #ccc; margin-top: 10px; background: white;"></div>
                    <input type="hidden" name="fecha_cita" id="fechaCitaNuevo" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Duración sugerida (min)</label>
                    <input type="number" id="duracion_sugerida" value="0" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                    <small style="color: #6c757d;">* Según catálogo</small>
                </div>
                <div class="form-group">
                    <label>Duración real de la cita (min) *</label>
                    <input type="number" name="duracion" id="duracion_real" required>
                    <small style="color: #6c757d;">* Tiempo que se bloqueará en la agenda</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label>Motivo de la cita *</label>
                    <input type="text" name="motivo_consulta" required value="{{ old('motivo_consulta') }}" oninput="validarMotivo(this)">
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
            <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
    <label style="font-weight: bold; display: block; margin-bottom: 10px;">Tipo de atención *</label>
    <div style="display: flex; gap: 20px;">
        <label>
            <input type="radio" name="tipo_atencion_ex" value="seguimiento" onclick="mostrarOpcionesExistente('seguimiento')" required> Seguimiento
        </label>
        <label>
            <input type="radio" name="tipo_atencion_ex" value="nuevo_tratamiento" onclick="mostrarOpcionesExistente('nuevo_tratamiento')"> Nuevo Tratamiento
        </label>
        <label>
            <input type="radio" name="tipo_atencion_ex" value="servicio" onclick="mostrarOpcionesExistente('servicio')"> Servicio Rápido
        </label>
    </div>

    <div id="contenedor_seguimiento_ex" style="display: none; margin-top: 15px;">
        <label>Seleccione el tratamiento actual:</label>
        <select name="id_tratamiento_existente" id="select_tratamiento_ex" class="form-control">
            <option value="">-- Seleccionar --</option>
        </select>
    </div>

    <div id="contenedor_nuevo_plan_ex" style="display: none; margin-top: 15px;">
        <label>Seleccione el Nuevo Tratamiento:</label>
        <select name="id_cat_tratamiento_nuevo" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
            <option value="">-- Seleccionar del catálogo --</option>
            @foreach($catTratamientos as $t)
                <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
            @endforeach
        </select>
        
        <div class="form-group" style="margin-top: 10px;">
            <label>Precio Estimado (Opcional)</label>
            <input type="number" name="precio_estimado_nuevo" step="0.01" class="form-control" placeholder="0.00">
        </div>
        
        <div class="form-group" style="margin-top: 10px;">
            <label>Diagnóstico Inicial (Opcional)</label>
            <textarea name="diagnostico_nuevo" class="form-control" rows="2" placeholder="Notas sobre este nuevo problema..."></textarea>
        </div>
    </div>

    <div id="contenedor_servicio_ex" style="display: none; margin-top: 15px;">
        <select name="id_cat_servicio" class="form-control" onchange="consultarDuracionDB('servicio', this.value)">
            <option value="">-- Seleccionar Servicio --</option>
            @foreach($catServicios as $s) 
                <option value="{{ $s->id_cat_servicio }}">{{ $s->nombre }}</option> 
            @endforeach
        </select>
    </div>
</div>
            <div class="form-row">
                <div class="form-group full-width">
                    <div id="calendarExistente" style="min-height: 400px; border: 1px solid #ccc; background: white;"></div>
                    <input type="hidden" name="fecha_cita" id="fechaCitaExistente" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
        <label>Duración sugerida (minutos)</label>
        <input type="number" id="duracion_sugerida_ex" value="" readonly style="background-color: #f8f9fa; cursor: not-allowed; border: 1px solid #dee2e6;">
        <small style="color: #6c757d;">* Según catálogo</small>
                </div>
                <div class="form-group"><label>Duración real de la cita (min) *</label><input type="number" name="duracion" id="duracion_real_ex" required><small style="color: #6c757d;">* Tiempo que se bloqueará en la agenda</small></div>
                <div class="form-group"><label>Motivo *</label><input type="text" name="motivo_consulta" value="{{ old('motivo_consulta') }}" oninput="validarMotivo(this)" required></div>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Guardar Cita</button>
        </form>
    </div>
</div>

<script>
// --- LÓGICA DE VALIDACIÓN EN TIEMPO REAL ---
const validaciones = {
    nombre: (v) => v.trim().length >= 2 || "El nombre es obligatorio (min 2 letras)",
    apellido_paterno: (v) => v.trim().length >= 2 || "El apellido es obligatorio",
    apellido_materno: (v) => v.trim().length >= 2 || "El apellido es obligatorio",
    curp: (v) => v === "" || /^[A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[0-9,A-Z][0-9]$/.test(v) || "Formato de CURP inválido (18 caracteres)",
    codigo_postal: (v) => v === "" || /^[0-9]{5}$/.test(v) || "El CP debe tener 5 dígitos",
    peso: (v) => {
        if (v === "" || v === null) return true;
        const p = parseFloat(v);
        return (p >= 0.5 && p <= 500) || "Ingrese un peso válido (0.5 - 500 kg)";
    },
    duracion: (v) => (parseInt(v) >= 5 && parseInt(v) <= 480) || "La duración debe ser entre 5 y 480 min",
    num_ext: (v) => v.trim() !== "" || "El número exterior es obligatorio",
    colonia: (v) => v.trim() !== "" || "Campo obligatorio",
    ciudad: (v) => v.trim() !== "" || "Campo obligatorio",
    estado: (v) => v.trim() !== "" || "Campo obligatorio",
    calle: (v) => v.trim() !== "" || "Campo obligatorio",
    alergias: (v) => v.trim() !== "" || "Especifique alergias o escriba 'Ninguna'",
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
    },
    email: (v) => {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (v.trim() === "") return "El correo es obligatorio";
        if (!regex.test(v)) return "Formato de correo inválido";
        return true;
    }
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
        if (id === 'modalNuevo') setTimeout(() => inicializarCalendario('calendarNuevo', 'fechaCitaNuevo'), 150);
        if (id === 'modalExistente') setTimeout(() => inicializarCalendario('calendarExistente', 'fechaCitaExistente'), 150);
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
    document.getElementById('contenedor_tratamiento').style.display = tipo === 'tratamiento' ? 'block' : 'none';
    document.getElementById('contenedor_servicio').style.display = tipo === 'servicio' ? 'block' : 'none';
}

function consultarDuracionDB(tipo, id) {
    if (!id) return;

    fetch(`/obtener-duracion?tipo=${tipo}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const minutos = data.duracion || 0;
            
            // Llenar sugerida en Modal Nuevo
            const sugNuevo = document.getElementById('duracion_sugerida');
            if (sugNuevo) sugNuevo.value = minutos;

            // Llenar sugerida en Modal Existente
            const sugEx = document.getElementById('duracion_sugerida_ex');
            if (sugEx) sugEx.value = minutos;
            
            // Nota: Se eliminó la línea que rellenaba duracion_real y duracion_real_ex
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

// --- INICIALIZACIÓN FINAL ---
// --- INICIALIZACIÓN FINAL ACTUALIZADA ---
document.addEventListener('DOMContentLoaded', function() {
    initRealTimeValidation();
    
    // Filtro de solo letras
    ['nombre', 'apellido_paterno', 'apellido_materno'].forEach(n => {
        const input = document.getElementsByName(n)[0];
        if(input) input.onkeypress = soloLetras;
    });

    // --- Lógica para reabrir modal con errores de Laravel ---
    @if ($errors->any())
        @if(old('id_paciente')) 
            abrirModal('modalExistente');
            // Si el usuario ya había seleccionado un tipo, lo mostramos
            @if(old('tipo_atencion'))
                setTimeout(() => mostrarOpcionesExistente('{{ old("tipo_atencion") }}'), 300);
            @endif
        @else 
            abrirModal('modalNuevo');
            // Si el usuario ya había seleccionado un tipo, lo mostramos
            @if(old('tipo_atencion'))
                setTimeout(() => mostrarOpcionesAtencion('{{ old("tipo_atencion") }}'), 300);
            @endif
        @endif
        setTimeout(() => {
        document.querySelectorAll('.alert-danger').forEach(el => el.remove());
    }, 5000);
    @endif

    const camposTexto = ['nombre', 'apellido_paterno', 'apellido_materno', 'nombre_tutor'];
    
    camposTexto.forEach(nombreCampo => {
        const input = document.getElementsByName(nombreCampo)[0];
        if (input) {
            // Aplicar control de espacios en tiempo real
            input.addEventListener('input', function() {
                controlarEspacios(this);
            });
            // Aplicar limpieza final al salir
            input.addEventListener('blur', function() {
                limpiarEspacios(this);
            });
        }
    });

   // Seleccionamos específicamente el select que está dentro del modal de Paciente Existente
const selectPacienteEx = document.querySelector('#modalExistente select[name="id_paciente"]');
if (selectPacienteEx) {
    selectPacienteEx.addEventListener('change', function() {
        actualizarTratamientosAlCambiarPaciente();
    });
}
});

// Función para cargar tratamientos (tu original)
function mostrarOpcionesExistente(tipo) {
    const divSeguimiento = document.getElementById('contenedor_seguimiento_ex');
    const divNuevoPlan = document.getElementById('contenedor_nuevo_plan_ex');
    const divServicio = document.getElementById('contenedor_servicio_ex');
    
    // Usamos el ID del select específico para paciente existente
    const selectPac = document.querySelector('#modalExistente select[name="id_paciente"]');
    const idP = selectPac ? selectPac.value : '';

    divSeguimiento.style.display = 'none';
    divNuevoPlan.style.display = 'none';
    divServicio.style.display = 'none';

    if (tipo === 'seguimiento') {
        if (!idP) { 
            alert("Por favor, seleccione un paciente primero.");
            // Desmarcar radios si no hay paciente
            document.querySelectorAll('input[name="tipo_atencion"]').forEach(r => r.checked = false);
            return; 
        }

        divSeguimiento.style.display = 'block';
        const sel = document.getElementById('select_tratamiento_ex');
if (sel) sel.innerHTML = '<option value="">-- Cargando tratamientos... --</option>';

        fetch(`/pacientes/${idP}/tratamientos-activos`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    sel.innerHTML = '<option value="">-- Sin tratamientos activos --</option>';
                } else {
                    sel.innerHTML = '<option value="">-- Seleccionar Tratamiento en Curso --</option>';
                    data.forEach(t => {
                        sel.innerHTML += `<option value="${t.id_tratamiento}">${t.nombre}</option>`;
                    });
                }
            })
            .catch(err => {
                console.error("Error:", err);
                sel.innerHTML = '<option value="">-- Error al cargar --</option>';
            });

    } else if (tipo === 'nuevo_tratamiento') {
        divNuevoPlan.style.display = 'block';
    } else if (tipo === 'servicio') {
        divServicio.style.display = 'block';
    }
}

function actualizarTratamientosAlCambiarPaciente() {
    // 1. Buscamos el radio DENTRO del modal existente usando el nombre correcto: tipo_atencion_ex
    const modalEx = document.getElementById('modalExistente');
    if (!modalEx) return;

    // IMPORTANTE: Tu HTML usa "tipo_atencion_ex" para los radios de este modal
    const radioSeguimiento = modalEx.querySelector('input[name="tipo_atencion_ex"][value="seguimiento"]');
    
    // 2. Si el modo seguimiento está activo, refrescamos la lista
    if (radioSeguimiento && radioSeguimiento.checked) {
        mostrarOpcionesExistente('seguimiento');
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

function abrirRegistroAdulto() {
    document.querySelectorAll('.alert-danger').forEach(a => a.remove());
    const form = document.getElementById('formNuevo'); // 1. Primero obtener el elemento
    //if (!form) return;

    prepararFormulario(form); // 2. Ahora sí, limpiar
    cambiarModal('modalSeleccion', 'modalNuevo'); // 3. Cambiar el modal
    
    // 4. Configurar Fechas
    const hoy = new Date();
    const hace18Anios = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate()).toISOString().split('T')[0];
    
    const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
    if (inputFecha) {
        inputFecha.max = hace18Anios;
        inputFecha.min = "1920-01-01";
    }

    // 5. Tutor
    const seccionTutor = document.getElementById('seccion_tutor');
    if (seccionTutor) {
        seccionTutor.style.display = 'none';
        gestionarAtributosTutor(false);
    }
}

function abrirRegistroMenor() {
    document.querySelectorAll('.alert-danger').forEach(a => a.remove());
    const form = document.getElementById('formNuevo'); // 1. Primero obtener el elemento
    //if (!form) return;

    prepararFormulario(form); // 2. Ahora sí, limpiar
    cambiarModal('modalSeleccion', 'modalNuevo'); // 3. Cambiar el modal

    // 4. Configurar Fechas
    const hoy = new Date();
    const hace18Anios = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate()).toISOString().split('T')[0];
    const fechaHoy = hoy.toISOString().split('T')[0];

    const inputFecha = form.querySelector('input[name="fecha_nacimiento"]');
    if (inputFecha) {
        inputFecha.min = hace18Anios;
        inputFecha.max = fechaHoy;
    }

    // 5. Tutor
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
    // Asegúrate de que estos IDs coincidan EXACTAMENTE con los que pusiste en el HTML
    const camposTutor = [
        { id: 'input_nombre_tutor', name: 'nombre_tutor' },
        { id: 'select_parentesco', name: 'parentesco' },
        { id: 'input_tel_tutor', name: 'telefono_tutor' }
    ];

    camposTutor.forEach(campo => {
        const el = document.getElementById(campo.id);
        if (el) {
            el.required = esRequerido;
            // Si es adulto (no requerido), limpiamos el valor para que no mande basura
            if (!esRequerido) el.value = ""; 
        }
    });

    // El teléfono del paciente es obligatorio SOLO para adultos
    // Verifica si el ID es 'tel_paciente' o 'telefono'
    const telPaciente = document.querySelector('input[name="tel_paciente"]'); 
    if (telPaciente) {
        telPaciente.required = !esRequerido;
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

// Detectar cambio de paciente para actualizar tratamientos automáticamente
document.addEventListener('change', function(e) {
    // Verificamos si el cambio fue en el select de paciente
    if (e.target.name === 'id_paciente') {
        // Buscamos el radio con el nombre correcto: tipo_atencion_ex
        const radioSeguimiento = document.querySelector('input[name="tipo_atencion_ex"][value="seguimiento"]');
        
        if (radioSeguimiento && radioSeguimiento.checked) {
            mostrarOpcionesExistente('seguimiento');
        }
    }
});
</script>
@endsection