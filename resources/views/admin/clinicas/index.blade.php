@extends('layouts.admin')

@section('title', 'Registrar Clínica | DentControl SaaS')

@section('content')
    <h1>Clínicas Registradas</h1>
    <p class="subtitle">Gestión de clínicas del sistema</p>

    {{-- Botón para abrir modal (Modo Crear) --}}
    <button class="btn btn-primary" style="margin:20px 0;" onclick="openClinicModal()">
        <i class="fa-solid fa-plus"></i> Agregar clínica
    </button>

    <div class="search-wrapper">
    <i class="fa-solid fa-magnifying-glass search-icon"></i>
    <input type="text" 
           id="clinicSearch" 
           class="search-input" 
           placeholder="Buscar por nombre o RFC..." 
           onkeyup="filterClinics()">
    </div>

    {{-- Alertas de éxito--}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-card">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>RFC</th>
                    <th>Ciudad</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clinicas as $clinica)
                <tr>
                    <td>{{ $clinica->nombre }}</td>
                    <td>{{ $clinica->rfc }}</td> {{-- Laravel lo desencripta solo por el Cast --}}
                    <td>{{ $clinica->ciudad }}</td>
                    <td>{{ $clinica->telefono }}</td>
                    <td>
                    <span class="badge {{ $clinica->estatus == 'activa' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($clinica->estatus) }}
                    </span>
                    </td>
                    <td class="actions">
                        {{-- Botón Editar --}}
                        <!--<button class="icon-btn edit" onclick="editClinic('{{ $clinica->id_clinica }}')">
                        <i class="fa-solid fa-pen"></i>
                        </button>-->
                        <button class="icon-btn edit" 
        onclick="editClinic('{{ $clinica->id_clinica }}')" 
        {{ $clinica->estatus != 'activo' ? 'disabled' : '' }}
        title="{{ $clinica->estatus != 'activo' ? 'No se puede editar una clínica dada de baja' : 'Editar clínica' }}"
        style="{{ $clinica->estatus != 'activo' ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
    <i class="fa-solid fa-pen"></i>
</button>

                        {{-- Botón Eliminar --}}
                        <form action="{{ route('clinicas.toggle', $clinica->id_clinica) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH') {{-- Usamos PATCH porque solo actualizamos un campo --}}
    
                        @if($clinica->estatus == 'activo')
                        {{-- BOTÓN PARA DAR DE BAJA --}}
                        <button type="submit" class="icon-btn delete" style="background-color: #f59e0b;" title="Dar de baja"
                            onclick="return confirm('¿Dar de baja esta clínica?')">
                            <i class="fa-solid fa-ban"></i>
                        </button>
                        @else
                        {{-- BOTÓN PARA REACTIVAR --}}
                        <button type="submit" class="icon-btn edit" style="background-color: #10b981;" title="Reactivar clínica"
                            onclick="return confirm('¿Deseas reactivar esta clínica?')">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                        </button>
                        @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL ADAPTADO --}}
    <div class="modal" id="clinicModal" style="display: none;">
        <div class="modal-content">
            <button class="close-btn" onclick="closeClinicModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 id="modalTitle">Registrar Clínica</h2>

            <form action="{{ route('clinicas.store') }}" method="POST" id="clinicForm" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div> {{-- Para PUT en edición --}}

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre de la clínica</label>
                        <input type="text" 
                        name="nombre" 
                        id="nombre" 
                        maxlength="50"
                        {{-- Permitimos letras, números, espacios, ñ y acentos --}}
                        oninput="this.value = this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s&'-]/g, '')"
                        value="{{ old('nombre') }}" 
                        class="@error('nombre') is-invalid @enderror"
                        title="Solo se permiten letras, números y espacios">
                        @error('nombre') <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>RFC</label>
                        <input type="text" 
                        name="rfc" 
                        id="rfc" 
                        maxlength="13" 
                        minlength="12"
                        style="text-transform: uppercase;" {{-- Visualmente en mayúsculas --}}
                        oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" {{-- Solo letras y números --}}
                        value="{{ old('rfc') }}" 
                        class="@error('rfc') is-invalid @enderror"
                        placeholder="ABC123456789">
                        @error('rfc') <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Calle</label>
                        <input type="text" name="calle" id="calle" value="{{ old('calle') }}">
                    </div>
                    <div class="form-group">
                        <label>Núm. Ext</label>
                        <input type="text" name="numero_ext" id="numero_ext" value="{{ old('numero_ext') }}">
                    </div>
                    <div class="form-group">
                        <label>Núm. Int</label>
                        <input type="text" name="numero_int" id="numero_int" value="{{ old('numero_int') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" name="codigo_postal" id="codigo_postal" 
                        maxlength="5" 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        value="{{ old('codigo_postal') }}">
                        @error('codigo_postal') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <input type="text" name="estado" id="estado" readonly value="{{ old('estado') }}" class="form-control-disabled">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ciudad / Municipio</label>
                        <input type="text" name="ciudad" id="ciudad" readonly value="{{ old('ciudad') }}" class="form-control-disabled">
                    </div>
                    <div class="form-group">
                        <label>Colonia</label>
                        <select name="colonia" id="colonia_select" class="form-control">
                            @if(old('colonia'))
                                <option value="{{ old('colonia') }}" selected>{{ old('colonia') }}</option>
                            @else
                                <option value="">Ingresa el CP primero...</option>
                            @endif
                        </select>
                        @error('colonia') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" id="telefono" 
                    maxlength="10" 
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    value="{{ old('telefono') }}">
                    @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

                <div style="margin-top:20px;">
                    <div id="logoPreviewContainer" style="display:none; margin-bottom:15px; text-align:center;">
                    <p style="font-size: 0.8rem; color: #666; margin-bottom: 5px;">Logo:</p>
                    <img id="logoPreviewImg" src="" alt="Logo Clínica" style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                    </div>
                    <label class="btn btn-secondary" style="cursor:pointer; display:inline-block;">
                <i class="fa-solid fa-image"></i> 
                <span id="logoButtonText">Agregar logo</span> 
                <input type="file" name="logo_ruta" id="logo_input" 
                style="display:none;" 
                accept="image/*">
                </label>
                    <small id="logoName" style="margin-left:10px; color:#666;"></small>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:15px; margin-top:30px;">
                    <button type="submit" class="btn btn-primary" >
                        <i class="fa-solid fa-floppy-disk"></i> Guardar clínica
                    </button>
                    <!--<button type="button" class="btn btn-cancel" onclick="closeClinicModal()">
                        Cancelar
                    </button>-->
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const clinicModal = document.getElementById("clinicModal");
    const clinicForm = document.getElementById("clinicForm");
    const methodField = document.getElementById("methodField");

    function openClinicModal() {
        //clinicForm.reset();
        clinicModal.style.display = "flex";
       // clinicForm.action = "{{ route('clinicas.store') }}";
        //methodField.innerHTML = "";
        
    }

    function closeClinicModal() {
    clinicModal.style.display = "none";
    clinicForm.reset(); 

    const inputs = clinicForm.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.value = ""; // Forzamos vaciar el valor que dejó el old()
        input.classList.remove('is-invalid'); // Quitamos el borde rojo de error
    });

    const errorMessages = clinicForm.querySelectorAll('.text-danger');
    errorMessages.forEach(msg => {
        msg.innerText = "";
    });
    
    // 1. Resetear ruta y método (Volver a modo creación)
    clinicForm.action = "{{ route('clinicas.store') }}";
    methodField.innerHTML = "";
    document.getElementById('modalTitle').innerText = "Registrar Clínica";

    // 2. Limpiar visualización del LOGO (Crucial para el requerimiento)
    document.getElementById('logoPreviewContainer').style.display = "none";
    document.getElementById('logoPreviewImg').src = "";
    document.getElementById('logoButtonText').innerText = "Agregar logo";
    document.getElementById('logoName').innerText = "";
    
    // 3. Limpiar mensajes de error de Laravel
    document.querySelectorAll('.text-danger').forEach(el => el.innerText = '');
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    // 4. Limpiar datos geográficos
    document.getElementById('colonia_select').innerHTML = '<option value="">Ingresa el CP primero...</option>';
    }

    // Detectar si hay errores de validación de Laravel y reabrir el modal correctamente
@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Intentamos obtener el ID de la clínica que se estaba editando
        const editingId = "{{ session('editing_clinic_id') }}";
        const form = document.getElementById('clinicForm');
        const methodField = document.getElementById('methodField');

        if (editingId) {
            // MODO EDICIÓN: Forzamos la ruta de update y el método PUT
            document.getElementById('modalTitle').innerText = "Editar Clínica";
            form.action = `/clinicas/${editingId}`; 
            methodField.innerHTML = `@method('PUT')`;

            // --- NUEVA LÓGICA PARA RECUPERAR EL LOGO TRAS ERROR ---
            fetch(`/clinicas/${editingId}/edit`)
                .then(res => res.json())
                .then(data => {
                    if (data.logo_ruta) {
                        const previewImg = document.getElementById('logoPreviewImg');
                        const previewContainer = document.getElementById('logoPreviewContainer');
                        previewImg.src = window.location.origin + '/' + data.logo_ruta;
                        previewContainer.style.display = "block";
                        document.getElementById('logoButtonText').innerText = "Cambiar logo";
                    }
                });

            // Si falló la validación, mantenemos la colonia que el usuario seleccionó
            @if(old('colonia'))
                const coloniaSelect = document.getElementById('colonia_select');
                coloniaSelect.innerHTML = `<option value="{{ old('colonia') }}" selected>{{ old('colonia') }}</option>`;
            @endif
        } else {
            // MODO REGISTRO: Aseguramos que el título sea el correcto
            document.getElementById('modalTitle').innerText = "Registrar Clínica";
        }

        // Abrimos el modal para mostrar los errores (usamos tu función existente)
        openClinicModal(); 
    });
@endif
    function editClinic(id) {

        // Si el estatus no es activo, detenemos la función
    if (estatus !== 'activo') {
        alert("Esta clínica está dada de baja y no se puede editar.");
        return;
    }
        document.getElementById('modalTitle').innerText = "Editar Clínica";
        clinicForm.action = `/clinicas/${id}`;
        methodField.innerHTML = `@method('PUT')`;

        // Definimos las variables del logo que usaremos abajo
    const previewContainer = document.getElementById('logoPreviewContainer');
    const previewImg = document.getElementById('logoPreviewImg');
    // id="logoButtonText", vamos a obtenerlo por su contenedor
    const logoButton = document.querySelector('.btn-secondary');

        fetch(`/clinicas/${id}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Error al obtener datos');
                return res.json();
            })
            .then(data => {
                // Función auxiliar para evitar que el script se rompa si un ID no existe
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val || '';
                };

                setVal('nombre', data.nombre);
                setVal('rfc', data.rfc);
                setVal('calle', data.calle);
                setVal('numero_ext', data.numero_ext);
                setVal('numero_int', data.numero_int);
                setVal('codigo_postal', data.codigo_postal);
                setVal('ciudad', data.ciudad);
                setVal('estado', data.estado);
                setVal('telefono', data.telefono);

                // Manejo de la colonia: agregamos la guardada al select
                const coloniaSelect = document.getElementById('colonia_select');
                if (coloniaSelect) {
                    coloniaSelect.innerHTML = `<option value="${data.colonia}" selected>${data.colonia}</option>`;
                }

                if (data.logo_ruta) {
                previewImg.src = window.location.origin + '/' + data.logo_ruta;
                previewContainer.style.display = "block";
            } else {
                previewContainer.style.display = "none";
                previewImg.src = "";
            }

                // IMPORTANTE: Llamar a abrir el modal al final
                openClinicModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert("No se pudo cargar la información de la clínica.");
            });
    }

    // Lógica de Copomex
    document.getElementById('codigo_postal').addEventListener('blur', function() {
        const cp = this.value;
        const token = "d1730311-71cf-4809-99d5-e6b2bdb2b08c";

        if (cp.length === 5) {
            const estadoInput = document.getElementById('estado');
            const ciudadInput = document.getElementById('ciudad');
            const coloniaSelect = document.getElementById('colonia_select');

            estadoInput.value = "Cargando...";
            
            fetch(`https://api.copomex.com/query/info_cp/${cp}?token=${token}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const info = data[0].response;
                        estadoInput.value = info.estado;
                        ciudadInput.value = info.municipio;

                        coloniaSelect.innerHTML = '<option value="">Selecciona colonia...</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.response.asentamiento;
                            option.textContent = item.response.asentamiento;
                            coloniaSelect.appendChild(option);
                        });
                    } else {
                        estadoInput.value = "";
                        alert("CP no encontrado.");
                    }
                })
                .catch(err => {
                    console.error('API Error:', err);
                    estadoInput.value = "";
                });
        }
    });

    // NUEVA LÓGICA DE VISTA PREVIA LOGO 
document.getElementById('logo_input').addEventListener('change', function(e) {
    const reader = new FileReader();
    const previewImg = document.getElementById('logoPreviewImg');
    const previewContainer = document.getElementById('logoPreviewContainer');
    const logoName = document.getElementById('logoName');

    if (this.files && this.files[0]) {
        // Validar que sea imagen (Requerimiento de seguridad)
        if (!this.files[0].type.startsWith('image/')) {
            alert('Por favor, selecciona solo archivos de imagen.');
            this.value = '';
            return;
        }

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = "block";
            document.getElementById('logoButtonText').innerText = "Cambiar logo";
        }
        reader.readAsDataURL(this.files[0]);
        logoName.innerText = this.files[0].name;
    }
});

function filterClinics() {
    const input = document.getElementById('clinicSearch');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.dashboard-table tbody tr');

    rows.forEach(row => {
        const nombre = row.cells[0].textContent.toLowerCase();
        const rfc = row.cells[1].textContent.toLowerCase();
        row.style.display = (nombre.includes(filter) || rfc.includes(filter)) ? "" : "none";
    });
}
</script>
@endpush