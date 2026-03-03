@extends('layouts.admin')

@section('title', 'Registrar Clínica | DentControl SaaS')

@section('content')
    <h1>Clínicas Registradas</h1>
    <p class="subtitle">Gestión de clínicas del sistema</p>

    {{-- Botón para abrir modal (Modo Crear) --}}
    <button class="btn btn-primary" style="margin:20px 0;" onclick="openClinicModal()">
        <i class="fa-solid fa-plus"></i> Agregar clínica
    </button>

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
                        <button class="icon-btn edit" onclick="editClinic('{{ $clinica->id_clinica }}')">
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
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="@error('nombre') is-invalid @enderror">
                        @error('nombre') <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>RFC</label>
                        <input type="text" name="rfc" id="rfc" value="{{ old('rfc') }}" class="@error('rfc') is-invalid @enderror">
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
                        <input type="text" name="codigo_postal" id="codigo_postal" maxlength="5" value="{{ old('codigo_postal') }}" 
                        class="@error('codigo_postal') is-invalid @enderror">
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
                    <input type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}">
                    @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

                <div style="margin-top:20px;">
                    <div id="logoPreviewContainer" style="display:none; margin-bottom:15px; text-align:center;">
                    <p style="font-size: 0.8rem; color: #666; margin-bottom: 5px;">Logo actual:</p>
                    <img id="logoPreviewImg" src="" alt="Logo Clínica" style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 8px;">
                    </div>
                    <label class="btn btn-secondary" style="cursor:pointer; display:inline-block;">
    <i class="fa-solid fa-image"></i> 
    <span id="logoButtonText">Agregar logo</span> <input type="file" name="logo_ruta" style="display:none;" accept="image/*">
</label>
                    <small id="logoName" style="margin-left:10px; color:#666;"></small>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:15px; margin-top:30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar clínica
                    </button>
                    <button type="button" class="btn btn-cancel" onclick="closeClinicModal()">
                        Cancelar
                    </button>
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
        clinicModal.style.display = "flex";
    }

    function closeClinicModal() {
        clinicModal.style.display = "none";
        clinicForm.reset();
        clinicForm.action = "{{ route('clinicas.store') }}";
        methodField.innerHTML = "";
        document.getElementById('modalTitle').innerText = "Registrar Clínica";
        document.getElementById('logoName').innerText = "";

        // Limpiar mensajes de error rojos
        document.querySelectorAll('.text-danger').forEach(el => el.innerText = '');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        // Limpiamos el select de colonias al cerrar
        document.getElementById('colonia_select').innerHTML = '<option value="">Ingresa el CP primero...</option>';
        document.getElementById('logoPreviewContainer').style.display = "none";
        document.getElementById('logoPreviewImg').src = "";
        document.getElementById('logoButtonText').innerText = "Agregar logo";
    }

    // Detectar si hay errores de validación de Laravel y reabrir el modal
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
        openClinicModal();
        
        // Si hay una colonia previa, nos aseguramos que el select no se limpie
        @if(old('colonia'))
            const coloniaSelect = document.getElementById('colonia_select');
            coloniaSelect.innerHTML = `<option value="{{ old('colonia') }}" selected>{{ old('colonia') }}</option>`;
        @endif
    });
    @endif

    function editClinic(id) {
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
        const token = "e3c2fe1d-ae86-40a5-86ef-9dac0715d9df";

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

    // Mostrar nombre del archivo seleccionado
    document.querySelector('input[name="logo_ruta"]').onchange = function() {
        if(this.files.length > 0) {
            document.getElementById('logoName').innerText = this.files[0].name;
        }
    };
</script>
@endpush