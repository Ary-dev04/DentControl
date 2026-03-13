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

@if ($errors->any())
    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
    <div class="custom-modal-content">
        <span class="close-btn" onclick="cerrarModal('modalSeleccion')">&times;</span>
        <h3>¿El paciente ya existe?</h3>
        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
            <button class="btn-primary" onclick="cambiarModal('modalSeleccion', 'modalExistente')">
                Paciente existente
            </button>
            <button class="btn-primary" onclick="cambiarModal('modalSeleccion', 'modalNuevo')">
                Paciente nuevo
            </button>
        </div>
    </div>
</div>

<div id="modalNuevo" class="custom-modal">
    <div class="custom-modal-content large">
        <span class="close-btn" onclick="cerrarModal('modalNuevo')">&times;</span>
        <h3>Registrar paciente y cita</h3>
        
        <form action="{{ route('pacientes.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Nombre(s) *</label><input type="text" name="nombre" maxlength="50" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios" required></div>
                <div class="form-group"><label>Apellido Paterno *</label><input type="text" name="apellido_paterno" maxlength="50" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios" required></div>
                <div class="form-group"><label>Apellido Materno</label><input type="text" maxlength="50" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios" name="apellido_materno"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div class="form-group"><label>CURP</label><input type="text" name="curp" maxlength="18" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()"></div>
                <div class="form-group"><label>Fecha de Nacimiento</label><input type="date" name="fecha_nacimiento"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Sexo</label>
                    <select name="sexo">
                        <option value="hombre">Hombre</option>
                        <option value="mujer">Mujer</option>
                    </select>
                </div>
                <div class="form-group"><label>Ocupación</label><input type="text" name="ocupacion"></div>
                <div class="form-group"><label>Peso (kg)</label><input type="number" step="0.01" name="peso"></div>
            </div>

            <h4 style="margin-top:15px; border-bottom: 1px solid #eee;">Dirección</h4>
            <div class="form-row">
                <div class="form-group"><label>Código Postal</label><input type="text" name="codigo_postal" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div class="form-group"><label>Colonia</label><input type="text" name="colonia"></div>
                <div class="form-group"><label>Ciudad</label><input type="text" name="ciudad"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Estado</label><input type="text" name="estado"></div>
                <div class="form-group"><label>No. Exterior</label><input type="text" name="num_ext"></div>
                <div class="form-group"><label>No. Interior</label><input type="text" name="num_int"></div>
                <div class="form-group"><label>Calle</label><input type="text" name="calle"></div>
                
            </div>

            <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">¿A qué viene hoy? *</label>
                
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="radio" name="tipo_atencion" value="tratamiento" onclick="mostrarOpcionesAtencion('tratamiento')" required> 
                        Nuevo Plan (Tratamiento)
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="radio" name="tipo_atencion" value="servicio" onclick="mostrarOpcionesAtencion('servicio')"> 
                        Servicio Rápido
                    </label>
                </div>

                <div id="contenedor_tratamiento" style="display: none;">
    <label>Seleccione el Tratamiento:</label>
    <select name="id_cat_tratamiento" id="select_tratamiento" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
    <option value="">-- Seleccionar Tratamiento --</option>
    @foreach($catTratamientos as $t)
        <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
    @endforeach
</select>
</div>

<div id="contenedor_servicio" style="display: none;">
    <label>Seleccione el Servicio:</label>
    <select name="id_cat_servicio" id="select_servicio" class="form-control" onchange="consultarDuracionDB('servicio', this.value)">
    <option value="">-- Seleccionar Servicio --</option>
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
        <label>Duración sugerida (minutos)</label>
        <input type="number" id="duracion_sugerida" value="0" readonly style="background-color: #f8f9fa; cursor: not-allowed; border: 1px solid #dee2e6;">
        <small style="color: #6c757d;">* Basado en el catálogo</small>
    </div>

    <div class="form-group">
        <label>Duración real de la cita (minutos) *</label>
        <input type="number" name="duracion" id="duracion_real" value="30" required>
        <small style="color: #6c757d;">* Tiempo que bloqueará la agenda</small>
    </div>
</div>

<div class="form-row">
    <div class="form-group full-width">
        <label>Motivo de la cita</label>
        <input type="text" name="motivo_consulta" placeholder="Ej: Revisión general por dolor en encía" required>
    </div>
</div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 15px;">
                Guardar Paciente y Cita
            </button>
        </form>
    </div>
</div>

<div id="modalExistente" class="custom-modal">
    <div class="custom-modal-content large">
        <span class="close-btn" onclick="cerrarModal('modalExistente')">&times;</span>
        <h3>Registrar cita – Paciente existente</h3>

        <form action="{{ route('pacientes.store_cita_existente') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group full-width">
                    <label>Buscar paciente *</label>
                    <select name="id_paciente" class="form-control" required style="width: 100%">
                        <option value="">-- Seleccione un paciente --</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id_paciente }}">{{ $p->nombre }} {{ $p->apellido_paterno }} {{ $p->apellido_materno }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="atencion-selector" style="margin: 20px 0; padding: 15px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">Tipo de atención *</label>
                <div style="display: flex; gap: 20px;">
                    <label><input type="radio" name="tipo_atencion" value="tratamiento" onclick="mostrarOpcionesExistente('tratamiento')" required> Seguimiento de Plan</label>
                    <label><input type="radio" name="tipo_atencion" value="servicio" onclick="mostrarOpcionesExistente('servicio')"> Servicio Rápido</label>
                </div>

                <div id="contenedor_tratamiento_ex" style="display: none; margin-top: 15px;">
                    <label>Seleccione el Tratamiento en curso:</label>
                    <select name="id_cat_tratamiento" id="select_tratamiento" class="form-control" onchange="consultarDuracionDB('tratamiento', this.value)">
    <option value="">-- Seleccionar Tratamiento --</option>
    @foreach($catTratamientos as $t)
        <option value="{{ $t->id_cat_tratamientos }}">{{ $t->nombre }}</option>
    @endforeach
</select>
                </div>

                <div id="contenedor_servicio_ex" style="display: none; margin-top: 15px;">
                    <label>Seleccione el Servicio:</label>
                    <select name="id_cat_servicio" id="select_servicio" class="form-control" onchange="consultarDuracionDB('servicio', this.value)">
    <option value="">-- Seleccionar Servicio --</option>
    @foreach($catServicios as $s)
        <option value="{{ $s->id_cat_servicio }}">{{ $s->nombre }}</option>
    @endforeach
</select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label style="font-weight: bold;">Seleccionar fecha y hora *</label>
                    <div id="calendarExistente" style="min-height: 400px; border: 1px solid #ccc; background: white;"></div>
                    <input type="hidden" name="fecha_cita" id="fechaCitaExistente" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Duración real (minutos) *</label>
                    <input type="number" name="duracion" id="duracion_real_ex" value="30" required>
                </div>
                <div class="form-group">
                    <label>Motivo</label>
                    <input type="text" name="motivo_consulta" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Guardar Cita</button>
        </form>
    </div>
</div>

<script>
let calendar; // Variable global para la instancia

function abrirModal(id) {
    const modal = document.getElementById(id);
    if(modal) {
        modal.style.display = 'flex';
        // Inicializar el calendario si el modal lo requiere
        if (id === 'modalNuevo') {
            setTimeout(() => { inicializarCalendario('calendarNuevo', 'fechaCitaNuevo'); }, 150);
        } else if (id === 'modalExistente') {
            setTimeout(() => { inicializarCalendario('calendarExistente', 'fechaCitaExistente'); }, 150);
        }
    }
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if(modal) {
        modal.style.display = 'none';
    }
}

function cambiarModal(cerrarId, abrirId) {
    cerrarModal(cerrarId);
    // Un pequeño delay ayuda a que la transición sea más suave
    setTimeout(() => {
        abrirModal(abrirId);
    }, 50);
}

function inicializarCalendario(idDiv, idInput) {
    const calendarEl = document.getElementById(idDiv);
    const inputFecha = document.getElementById(idInput);

    // Limpiar contenido previo para evitar duplicados visuales
    calendarEl.innerHTML = ''; 

    let calendarInstance = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridDay'
        },
        selectable: true,
        allDaySlot: false, // Quita la fila de "todo el día"
        height: '400px',
        dateClick: function(info) {
            if (info.view.type === 'dayGridMonth') {
                calendarInstance.changeView('timeGridDay', info.dateStr);
            } else {
                inputFecha.value = info.dateStr; // Formato: 2026-03-12T10:30:00
                
                // Limpiar selección previa
                document.querySelectorAll('.fc-timegrid-slot').forEach(el => el.style.background = "");
                // Marcar la hora seleccionada
                info.dayEl.style.background = "#d1e7ff";
                
                // Mostrar al usuario una confirmación amigable
                const fechaLegible = new Date(info.dateStr).toLocaleString();
                console.log("Cita marcada para: " + fechaLegible);
            }
        },
        slotMinTime: "08:00:00",
        slotMaxTime: "20:00:00",
    });

    calendarInstance.render();
    setTimeout(() => { calendarInstance.updateSize(); }, 100);
}

    // Integrar esto en tu función mostrarOpcionesAtencion existente
function mostrarOpcionesAtencion(tipo) {
    const divTratamiento = document.getElementById('contenedor_tratamiento');
    const divServicio = document.getElementById('contenedor_servicio');
    
    document.getElementById('duracion_sugerida').value = 0;
    document.getElementById('duracion_real').value = 30;

    if (tipo === 'tratamiento') {
        divTratamiento.style.display = 'block';
        divServicio.style.display = 'none';
        document.getElementById('select_servicio').value = ""; // Limpia el otro select
    } else {
        divTratamiento.style.display = 'none';
        divServicio.style.display = 'block';
        document.getElementById('select_tratamiento').value = ""; // Limpia el otro select
    }
}

    function consultarDuracionDB(tipo, id) {
    if (!id) return;

    fetch(`/obtener-duracion?tipo=${tipo}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const minutos = data.duracion || 0;
            
            // Lógica para el Modal Nuevo
            const sug = document.getElementById('duracion_sugerida');
            const real = document.getElementById('duracion_real');
            
            if (sug && real) {
                sug.value = minutos;
                real.value = minutos;
            }

            // Lógica para el Modal Existente (por si acaso)
            const realEx = document.getElementById('duracion_real_ex');
            if (realEx) {
                realEx.value = minutos;
            }
        })
        .catch(error => console.error('Error al obtener duración:', error));
}

function mostrarOpcionesExistente(tipo) {
    const divTratamiento = document.getElementById('contenedor_tratamiento_ex');
    const divServicio = document.getElementById('contenedor_servicio_ex');
    const idPaciente = document.querySelector('select[name="id_paciente"]').value;

    if (tipo === 'tratamiento') {
        if (!idPaciente) {
            alert("Seleccione un paciente primero");
            return;
        }
        divTratamiento.style.display = 'block';
        divServicio.style.display = 'none';
        
        // Cargar tratamientos activos del paciente vía AJAX
        fetch(`/pacientes/${idPaciente}/tratamientos-activos`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('select_tratamiento_ex');
                select.innerHTML = '<option value="">-- Seleccionar --</option>';
                data.forEach(t => {
                    select.innerHTML += `<option value="${t.id_tratamiento}">${t.catalogo_tratamiento.nombre}</option>`;
                });
            });
    } else {
        divTratamiento.style.display = 'none';
        divServicio.style.display = 'block';
    }
}

// Función para permitir solo letras y espacios en tiempo real
function soloLetras(e) {
    let key = e.keyCode || e.which;
    let tecla = String.fromCharCode(key).toLowerCase();
    let letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    let especiales = [8, 37, 39, 46]; // Backspace, flechas, delete

    let tecla_especial = false;
    for (let i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if (letras.indexOf(tecla) == -1 && !tecla_especial) {
        e.preventDefault();
        return false;
    }
}

// Aplicar a los inputs del modal
document.getElementsByName('nombre')[0].onkeypress = soloLetras;
document.getElementsByName('apellido_paterno')[0].onkeypress = soloLetras;
document.getElementsByName('apellido_materno')[0].onkeypress = soloLetras;
</script>
@endsection