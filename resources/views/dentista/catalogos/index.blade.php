@extends('layouts.clinica')

@section('title', 'Gestión de Catálogos | DentControl')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pacientes.css') }}">
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">

<div class="app-container">
    <main class="content">
        <h1>Gestión de Catálogos</h1>

        <p class="info-note">
            <i class="fa-solid fa-circle-info"></i> 
            Los precios registrados son solo sugerencias. El monto final se define al cerrar la cita según el diagnóstico del doctor.
        </p>

        @if(session('success'))
            <div class="alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="form-actions" style="margin-bottom: 30px;">
            <button type="button" class="btn-primary" onclick="mostrarModal('modalServicio')">
                <i class="fa-solid fa-stethoscope"></i> Registrar catálogo de servicios
            </button>
            <button type="button" class="btn-primary" onclick="mostrarModal('modalTratamiento')">
                <i class="fa-solid fa-notes-medical"></i> Registrar catálogo de tratamientos
            </button>
        </div>

        <section class="table-section">
            <h3 class="section-title"><i class="fa-solid fa-list-check"></i> Servicios registrados</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Duración</th>
                        <th>Precio sugerido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaServicios">
                    @forelse($servicios as $servicio)
                        <tr>
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ $servicio->descripcion ?? 'Sin descripción' }}</td>
                            <td>{{ $servicio->duracion }} min</td>
                            <td>${{ number_format($servicio->precio_sugerido, 2) }}</td>
                            <td>
                                <div class="acciones-wrapper">
                                    <button type="button" class="btn-primary" style="padding: 5px 10px;" 
                                        
                                        onclick="abrirEditarServicio('{{ $servicio->id_cat_servicio }}', '{{ addslashes($servicio->nombre) }}', '{{ addslashes($servicio->descripcion) }}', '{{ $servicio->duracion }}', '{{ $servicio->precio_sugerido }}')">
                                            <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('servicios.toggle', $servicio->id_cat_servicio) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-status {{ $servicio->estatus === 'activo' ? 'status-active' : 'status-inactive' }}">
                                            <i class="fa-solid {{ $servicio->estatus === 'activo' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            {{ ucfirst($servicio->estatus) }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;">No hay servicios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="table-section">
            <h3 class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Tratamientos registrados</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Duración sugerida/Sesión</th>
                        <th>Precio sugerido total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaTratamientos">
                    @forelse($tratamientos as $tratamiento)
                        <tr>
                            <td>{{ $tratamiento->nombre }}</td>
                            <td>{{ $tratamiento->descripcion ?? 'Sin descripción' }}</td>
                            <td>{{ $tratamiento->duracion_sugerido_sesion }} min</td>
                            <td>${{ number_format($tratamiento->precio_sugerido, 2) }}</td>
                            <td>
                                <div class="acciones-wrapper">
                                    <button type="button" class="btn-primary" style="padding: 5px 10px;" 
                                        
                                        onclick="abrirEditarTratamiento('{{ $tratamiento->id_cat_tratamientos }}', '{{ addslashes($tratamiento->nombre) }}', '{{ addslashes($tratamiento->descripcion) }}', '{{ $tratamiento->duracion_sugerido_sesion }}', '{{ $tratamiento->precio_sugerido }}')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('tratamientos.toggle', $tratamiento->id_cat_tratamientos) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-status {{ $tratamiento->estatus === 'activo' ? 'status-active' : 'status-inactive' }}">
                                            <i class="fa-solid {{ $tratamiento->estatus === 'activo' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            {{ ucfirst($tratamiento->estatus) }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;">No hay tratamientos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</div>

<div class="modal" id="modalServicio">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="cerrarModal('modalServicio')">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h3>Registrar Servicio</h3>
        <form action="{{ route('servicios.store') }}" method="POST" id="formServicio">
            @csrf
            <input type="hidden" name="tipo" value="nuevo_serv">
            <div class="form-group">
                <label>Nombre* (Máx 60 caracteres)</label>
                <input type="text" name="nombre" maxlength="60" value="{{ old('tipo') == 'nuevo_serv' ? old('nombre') : '' }}" oninput="limpiarTexto(this, true)" required>
                @if(old('tipo') == 'nuevo_serv') @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror @endif
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('tipo') == 'nuevo_serv' ? old('descripcion') : '' }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración (min)*</label>
                    <input type="number" name="duracion" min="1" max="480" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" value="{{ old('tipo') == 'nuevo_serv' ? old('duracion') : '' }}" placeholder="0" required>
                    @if(old('tipo') == 'nuevo_serv') @error('duracion') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Sugerido*</label>
                    <input type="number" step="0.01" min="1.00" max="999999.99" oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);" class="input-precio" name="precio_sugerido" value="{{ old('tipo') == 'nuevo_serv' ? old('precio_sugerido') : '' }}"
                           onkeypress="return isNumberKey(event)" placeholder="0.00" required>
                    @if(old('tipo') == 'nuevo_serv') @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Guardar Servicio</button>
        </form>
    </div>
</div>

<div class="modal" id="modalTratamiento">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="cerrarModal('modalTratamiento')">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h3>Registrar Tratamiento</h3>
        <form action="{{ route('tratamientos.store') }}" method="POST" id="formTratamiento">
            @csrf
            <input type="hidden" name="tipo" value="nuevo_trat">
            <div class="form-group">
                <label>Nombre del Plan de Tratamiento*</label>
                <input type="text" name="nombre" maxlength="60" value="{{ old('tipo') == 'nuevo_trat' ? old('nombre') : '' }}" oninput="limpiarTexto(this, true)" required>
                @if(old('tipo') == 'nuevo_trat') @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror @endif
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('tipo') == 'nuevo_trat' ? old('descripcion') : '' }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración Sesión (min)*</label>
                    <input type="number" name="duracion_sugerido_sesion" min="1" max="480" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" value="{{ old('tipo') == 'nuevo_trat' ? old('duracion_sugerido_sesion') : '' }}" placeholder="0" required>
                    @if(old('tipo') == 'nuevo_trat') @error('duracion_sugerido_sesion') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Total Aproximado*</label>
                    <input type="number" step="0.01" min="1.00" max="999999.99" oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);" class="input-precio" name="precio_sugerido" value="{{ old('tipo') == 'nuevo_trat' ? old('precio_sugerido') : '' }}"
                           onkeypress="return isNumberKey(event)" placeholder="0.00" required>
                    @if(old('tipo') == 'nuevo_trat') @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Guardar Tratamiento</button>
        </form>
    </div>
</div>

<div class="modal" id="modalEditarServicio">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="cerrarModal('modalEditarServicio')"><i class="fa-solid fa-xmark"></i></button>
        <h3>Editar Servicio</h3>
        <form action="{{ old('tipo') == 'edit_serv' ? '/catalogos/servicios/'.old('id_editado') : '' }}" method="POST" id="formEditarServicio">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_editado" id="id_serv_edit" value="{{ old('id_editado') }}">
            <input type="hidden" name="tipo" value="edit_serv">

            <div class="form-group">
                <label>Nombre*</label>
                <input type="text" name="nombre" id="edit_serv_nombre" value="{{ old('tipo') == 'edit_serv' ? old('nombre') : '' }}" maxlength="60" required oninput="limpiarTexto(this, true)">
                @if(old('tipo') == 'edit_serv') @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror @endif
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" id="edit_serv_descripcion" value="{{ old('tipo') == 'edit_serv' ? old('descripcion') : '' }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración (min)*</label>
                    <input type="number" name="duracion" id="edit_serv_duracion" value="{{ old('tipo') == 'edit_serv' ? old('duracion') : '' }}" min="1" max="480" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
                    @if(old('tipo') == 'edit_serv') @error('duracion') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Sugerido*</label>
                    <input type="number" step="0.01" min="1.00" max="999999.99" oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);" class="input-precio" name="precio_sugerido" id="edit_serv_precio" value="{{ old('tipo') == 'edit_serv' ? old('precio_sugerido') : '' }}" onkeypress="return isNumberKey(event)" required>
                    @if(old('tipo') == 'edit_serv') @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Guardar Cambios</button>
        </form>
    </div>
</div>

<div class="modal" id="modalEditarTratamiento">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="cerrarModal('modalEditarTratamiento')"><i class="fa-solid fa-xmark"></i></button>
        <h3>Editar Tratamiento</h3>
        <form action="{{ old('tipo') == 'edit_trat' ? '/catalogos/tratamientos/'.old('id_editado') : '' }}" method="POST" id="formEditarTratamiento">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_editado" id="id_trat_edit" value="{{ old('id_editado') }}">
            <input type="hidden" name="tipo" value="edit_trat">

            <div class="form-group">
                <label>Nombre del Plan Tratamiento*</label>
                <input type="text" name="nombre" id="edit_trat_nombre" value="{{ old('tipo') == 'edit_trat' ? old('nombre') : '' }}" maxlength="60" required oninput="limpiarTexto(this, true)">
                @if(old('tipo') == 'edit_trat') @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror @endif
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" id="edit_trat_descripcion" value="{{ old('tipo') == 'edit_trat' ? old('descripcion') : '' }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración Sesión (min)*</label>
                    <input type="number" name="duracion_sugerido_sesion" id="edit_trat_duracion" value="{{ old('tipo') == 'edit_trat' ? old('duracion_sugerido_sesion') : '' }}" min="1" max="480" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
                    @if(old('tipo') == 'edit_trat') @error('duracion_sugerido_sesion') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Total Aproximado*</label>
                    <input type="number" step="0.01" min="1.00" max="999999.99" class="input-precio" oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9);" name="precio_sugerido" id="edit_trat_precio" value="{{ old('tipo') == 'edit_trat' ? old('precio_sugerido') : '' }}" onkeypress="return isNumberKey(event)" required>
                    @if(old('tipo') == 'edit_trat') @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror @endif
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
// 1. Funciones de Apertura y Cierre
function mostrarModal(id, esEdicion = false) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('is-visible');
        // Solo resetear si NO es edición y NO hay errores de validación
        if (!esEdicion && !{{ $errors->any() ? 'true' : 'false' }}) {
            const form = modal.querySelector('form');
            if(form) form.reset();
        }
    }
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('is-visible');
}

// 2. Carga de datos para Edición
function abrirEditarServicio(id, nombre, descripcion, duracion, precio) {
    const form = document.getElementById('formEditarServicio');
    form.action = `/catalogos/servicios/${id}`;
    
    document.getElementById('id_serv_edit').value = id;
    document.getElementById('edit_serv_nombre').value = nombre;
    document.getElementById('edit_serv_descripcion').value = descripcion;
    document.getElementById('edit_serv_duracion').value = duracion;
    document.getElementById('edit_serv_precio').value = precio;
    
    mostrarModal('modalEditarServicio', true); // <--- AGREGAR EL TRUE
}

function abrirEditarTratamiento(id, nombre, descripcion, duracion, precio) {
    const form = document.getElementById('formEditarTratamiento');
    form.action = `/catalogos/tratamientos/${id}`;
    
    document.getElementById('id_trat_edit').value = id;
    document.getElementById('edit_trat_nombre').value = nombre;
    document.getElementById('edit_trat_descripcion').value = descripcion;
    document.getElementById('edit_trat_duracion').value = duracion;
    document.getElementById('edit_trat_precio').value = precio;
    
    mostrarModal('modalEditarTratamiento', true); // <--- AGREGAR EL TRUE
}

// 3. Manejo de Errores y Reapertura automática
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        const tipoError = "{{ old('tipo') }}";
        if (tipoError === 'edit_serv') {
            mostrarModal('modalEditarServicio');
        } else if (tipoError === 'edit_trat') {
            mostrarModal('modalEditarTratamiento');
        } else if (tipoError === 'nuevo_serv') {
            mostrarModal('modalServicio');
        } else if (tipoError === 'nuevo_trat') {
            mostrarModal('modalTratamiento');
        }
    @endif
});

// 4. Validaciones
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46);
}

function limpiarTexto(input, esNombre = false) {
    let value = input.value;
    // Evitar que peguen textos gigantes
    const maxLen = esNombre ? 60 : 255;
    
    if (esNombre) {
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');
    } else {
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\-]/g, '');
    }
    
    if (value.startsWith(' ')) value = value.trimStart();
    value = value.replace(/\s{2,}/g, ' ');
    input.value = value.slice(0, maxLen);
}

// Validación para montos mayores al límite
document.querySelectorAll('input[name="precio_sugerido"]').forEach(input => {
    input.addEventListener('change', function() {
        const valor = parseFloat(this.value);
        const limiteMaximo = 999999.99;
        const limiteMinimo = 1.00;

        if (valor > limiteMaximo) {
            alert('Error: La cantidad ingresada excede el límite permitido ($999,999.99). El campo se limpiará.');
            this.value = ''; // Deja el campo vacío
            this.focus();    // Devuelve el cursor al campo para reintentar
        } else if (valor <= limiteMinimo) {
            alert('Error: El precio debe ser mayor a $1.00.');
            this.value = ''; // También limpiamos si es 0 o negativo
            this.focus(); 
        }
    });
});
// 5. Prevenir doble envío
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if(btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
        }
    });
});

// Validación para Duración (Servicios y Tratamientos)
document.querySelectorAll('input[name="duracion"], input[name="duracion_sugerido_sesion"]').forEach(input => {
    input.addEventListener('change', function() {
        const valor = parseInt(this.value);
        const limiteMaximo = 480; // 8 horas máximo
        const limiteMinimo = 1;

        if (valor > limiteMaximo) {
            alert('Error: La duración no puede exceder los 480 minutos (8 horas). El campo se limpiará.');
            this.value = ''; 
            this.focus();
        } else if (valor < limiteMinimo) {
            alert('Error: La duración debe ser de al menos 1 minuto.');
            this.value = '';
            this.focus();
        }
    });
});
</script>
@endsection