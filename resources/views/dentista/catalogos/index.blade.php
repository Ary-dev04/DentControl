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
                                <div style="display: flex; gap: 5px;">
                                    <button type="button" class="btn-primary" style="padding: 5px 10px;" 
                                        onclick="abrirEditarServicio('{{ $servicio->id_cat_servicio }}', '{{ $servicio->nombre }}', '{{ $servicio->descripcion }}', '{{ $servicio->duracion }}', '{{ $servicio->precio_sugerido }}')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('servicios.destroy', $servicio->id_cat_servicio) }}" method="POST" onsubmit="return confirm('¿Deseas dar de baja este servicio?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary" style="padding: 5px 10px; background-color: #dc3545;" title="Dar de baja">
                                            <i class="fa-solid fa-arrow-down"></i>
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
                                <div style="display: flex; gap: 5px;">
                                    <button type="button" class="btn-primary" style="padding: 5px 10px;" 
                                        onclick="abrirEditarTratamiento('{{ $tratamiento->id_cat_tratamientos }}', '{{ $tratamiento->nombre }}', '{{ $tratamiento->descripcion }}', '{{ $tratamiento->duracion_sugerido_sesion }}', '{{ $tratamiento->precio_sugerido }}')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                <form action="{{ route('tratamientos.destroy', $tratamiento->id_cat_tratamientos) }}" method="POST" onsubmit="return confirm('¿Dar de baja?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary" style="padding: 5px 10px; background-color: #dc3545;">
                                        <i class="fa-solid fa-arrow-down"></i>
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
            <div class="form-group">
                <label>Nombre* (Máx 60 caracteres)</label>
                <input type="text" name="nombre" maxlength="60" value="{{ old('nombre') }}" oninput="limpiarTexto(this, true)" required>
                @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración (min)*</label>
                    <input type="number" name="duracion" min="1"  value="{{ old('duracion') }}" placeholder="0" required>
                    @error('duracion') <small style="color:red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Sugerido*</label>
                    <input type="number" step="0.01" min="0" name="precio_sugerido" value="{{ old('precio_sugerido') }}"
                           onkeypress="return isNumberKey(event)" placeholder="0.00" required>
                    @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror
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
            <div class="form-group">
                <label>Nombre del Plan de Tratamiento*</label>
                <input type="text" name="nombre" maxlength="60" value="{{ old('nombre') }}" oninput="limpiarTexto(this, true)" required>
                @error('nombre') <small style="color:red;">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración Sesión (min)*</label>
                    <input type="number" name="duracion_sugerido_sesion"  min="1"value="{{ old('duracion_sugerido_sesion') }}" placeholder="0" required>
                    @error('duracion_sugerido_sesion') <small style="color:red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Total Aproximado*</label>
                    <input type="number" step="0.01" min="0" name="precio_sugerido" value="{{ old('precio_sugerido') }}"
                           onkeypress="return isNumberKey(event)" placeholder="0.00" required>
                    @error('precio_sugerido') <small style="color:red;">{{ $message }}</small> @enderror
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
        <form action="" method="POST" id="formEditarServicio">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre*</label>
                <input type="text" name="nombre" id="edit_serv_nombre" maxlength="60" required oninput="limpiarTexto(this, true)">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" id="edit_serv_descripcion" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración (min)*</label>
                    <input type="number" name="duracion" id="edit_serv_duracion" min="1" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Sugerido*</label>
                    <input type="number" step="0.01" min="0.01" name="precio_sugerido" id="edit_serv_precio" onkeypress="return isNumberKey(event)" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Actualizar Servicio</button>
        </form>
    </div>
</div>

<div class="modal" id="modalEditarTratamiento">
    <div class="modal-content">
        <button type="button" class="close-btn" onclick="cerrarModal('modalEditarTratamiento')"><i class="fa-solid fa-xmark"></i></button>
        <h3>Editar Tratamiento</h3>
        <form action="" method="POST" id="formEditarTratamiento">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nombre del Plan*</label>
                <input type="text" name="nombre" id="edit_trat_nombre" maxlength="60" required oninput="limpiarTexto(this, true)">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" id="edit_trat_descripcion" oninput="limpiarTexto(this, false)">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Duración Sesión (min)*</label>
                    <input type="number" name="duracion_sugerido_sesion" id="edit_trat_duracion" min="1" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Precio Total*</label>
                    <input type="number" step="0.01" min="0.01" name="precio_sugerido" id="edit_trat_precio" onkeypress="return isNumberKey(event)" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%; margin-top:15px;">Actualizar Tratamiento</button>
        </form>
    </div>
</div>
<script>
// 1. Funciones de apertura y cierre
function mostrarModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('is-visible');
        const form = modal.querySelector('form');
        // Solo resetear si no hay errores de validación activos
        if (!{{ $errors->any() ? 'true' : 'false' }}) {
            form.reset();
        }
    }
}

function cerrarModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('is-visible');
    }
}



// 3. Validaciones de entrada (Importantes para tus reglas de negocio)
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46);
}

function limpiarTexto(input, esNombre = false) {
    let value = input.value;
    if (esNombre) {
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');
    } else {
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\-]/g, '');
    }
    if (value.startsWith(' ')) value = value.trimStart();
    value = value.replace(/\s{2,}/g, ' ');
    let limite = esNombre ? 60 : 255;
    input.value = value.slice(0, limite);
}

// 4. Manejo de errores de Laravel al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        @if($errors->has('duracion') || $errors->has('nombre') && old('duracion'))
            mostrarModal('modalServicio');
        @else
            mostrarModal('modalTratamiento');
        @endif
    @endif
});

// 5. Prevenir doble envío y error 419 por clics repetidos
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if(btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
        }
    });
});


// Funciones para Editar
function abrirEditarServicio(id, nombre, descripcion, duracion, precio) {
    const form = document.getElementById('formEditarServicio');
    form.action = `/catalogos/servicios/${id}`; // Asegúrate que esta ruta coincida con web.php
    
    document.getElementById('edit_serv_nombre').value = nombre;
    document.getElementById('edit_serv_descripcion').value = descripcion;
    document.getElementById('edit_serv_duracion').value = duracion;
    document.getElementById('edit_serv_precio').value = precio;
    
    mostrarModal('modalEditarServicio');
}

function abrirEditarTratamiento(id, nombre, descripcion, duracion, precio) {
    const form = document.getElementById('formEditarTratamiento');
    form.action = `/catalogos/tratamientos/${id}`;
    
    document.getElementById('edit_trat_nombre').value = nombre;
    document.getElementById('edit_trat_descripcion').value = descripcion;
    document.getElementById('edit_trat_duracion').value = duracion;
    document.getElementById('edit_trat_precio').value = precio;
    
    mostrarModal('modalEditarTratamiento');
}
</script>
@endsection