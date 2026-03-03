@extends('layouts.admin')

@section('title', 'Registrar Usuarios | DentControl SaaS')

@section('content')
    <style>
        /* Estilos mejorados para el modal y formulario */
        .modal-body-custom {
            background-color: white; 
            padding: 25px; 
            border-radius: 12px; 
            width: 600px; 
            max-width: 95%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group-full {
            grid-column: span 2;
            display: flex;
            flex-direction: column;
        }
        .form-group-custom {
            display: flex;
            flex-direction: column;
        }
        .form-group-custom label {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #374151;
        }
        .form-group-custom input, .form-group-custom select {
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group-custom input:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px rgba(59, 130, 246, 0.5);
        }
        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .modal-header-custom h3 { margin: 0; color: #111827; }

        .is-invalid {
    border: 1px solid #ef4444 !important;
    background-color: #fef2f2;
}
    </style>

    <h1>Registrar usuarios</h1>
    <p class="subtitle">Gestión de accesos al sistema por clínica</p>

    <button class="btn-primary" onclick="openUserModal()">
      <i class="fa-solid fa-user-plus"></i> Agregar usuario
    </button>

    @if(session('success'))
        <div style="padding: 15px; background-color: #d4edda; color: #155724; margin-top: 20px; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <table style="margin-top:25px; width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th>Nombre Completo</th>
          <th>Clínica</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $user)
        <tr>
          <td>{{ $user->nombre }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}</td>
          <td>{{ $user->clinica->nombre ?? 'Sin clínica' }}</td>
          <td>{{ ucfirst($user->rol) }}</td>
          <td>
            <span class="badge {{ $user->estatus == 'activo' ? 'bg-success' : 'bg-danger' }}">
                {{ ucfirst($user->estatus) }}
            </span>
          </td>
          <td>
            <button class="btn btn-edit" onclick="editUser('{{ $user->id_usuario }}')">
              <i class="fa-solid fa-pen"></i>
            </button>
            @if($user->rol !== 'superadmin')
            <form action="{{ route('usuarios.toggle', $user->id_usuario) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')
            
            @if($user->estatus == 'activo')
                <button type="submit" class="btn btn-cancel" style="background-color: #64748b;" 
                    title="Desactivar Usuario" onclick="return confirm('¿Suspender el acceso a este usuario?')">
                    <i class="fa-solid fa-user-slash"></i>
                </button>
                @else
                <button type="submit" class="btn btn-primary" style="background-color: #10b981;" 
                    title="Reactivar Usuario" onclick="return confirm('¿Reestablecer acceso para este usuario?')">
                    <i class="fa-solid fa-user-check"></i>
                </button>
                @endif
                </form>
                @else
                {{-- Si es admin, mostramos un ícono de escudo o candado deshabilitado --}}
                <button class="btn" style="background-color: #e2e8f0; cursor: not-allowed;" title="Usuario del Sistema" disabled>
                    <i class="fa-solid fa-shield-halved"></i>
                </button>
                 @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

<div class="modal" id="userModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
  <div class="modal-body-custom">
    <div class="modal-header-custom">
        <h3 id="modalTitle">Agregar usuario</h3>
        <button onclick="closeUserModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
    </div>

    <form action="{{ route('usuarios.store') }}" method="POST" id="userForm" autocomplete="off">
        @csrf
        
        <div class="form-grid">
    <div class="form-group-full">
        <label>Clínica</label>
        <select name="id_clinica" id="clinica" required class="@error('id_clinica') is-invalid @enderror">
            <option value="">Seleccionar clínica</option>
            @foreach($clinicas as $clinica)
                <option value="{{ $clinica->id_clinica }}" {{ old('id_clinica') == $clinica->id_clinica ? 'selected' : '' }}>{{ $clinica->nombre }}</option>
            @endforeach
        </select>
        @error('id_clinica') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
    </div>
</div>

        <div class="form-grid">
    <div class="form-group-custom">
        <label>Nombre(s)</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" class="@error('nombre') is-invalid @enderror">
        @error('nombre') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
    </div>
    <div class="form-group-custom">
        <label>Apellido Paterno</label>
        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="@error('apellido_paterno') is-invalid @enderror">
        @error('apellido_paterno') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
    </div>
</div>

        <div class="form-grid">
            <div class="form-group-custom">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" class="@error('apellido_materno') is-invalid @enderror">
                @error('apellido_materno') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Usuario (Login)</label>
                <input type="text" name="nom_usuario" value="{{ old('nom_usuario') }}" class="@error('nom_usuario') is-invalid @enderror">
                @error('nom_usuario') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group-custom">
                <label>Contraseña</label>
    <input type="password" 
           name="password" 
           id="password_input" 
           class="@error('password') is-invalid @enderror"
           placeholder="Mínimo 8 caracteres, letras y números">
    
    @error('password')
        <span style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
        </span>
    @enderror
            </div>
            <div class="form-group-custom">
                <label>Rol</label>
        <select name="rol" id="rol" onchange="toggleCedula()" class="@error('rol') is-invalid @enderror">
            <option value="">Seleccionar</option>
            <option value="dentista" {{ old('rol') == 'dentista' ? 'selected' : '' }}>Dentista / Dueño</option>
            <option value="asistente" {{ old('rol') == 'asistente' ? 'selected' : '' }}>Asistente</option>
        </select>
        {{-- Campo oculto para cuando el select esté disabled --}}
    <input type="hidden" name="rol_hidden" id="rol_hidden">
        @error('rol') <span style="color:red; font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid" id="cedulaGroup" style="display:none;">
            <div class="form-group-full">
                <label>Cédula profesional</label>
                <input type="text" name="cedula_profesional" placeholder="Ingrese los 7-10 dígitos">
            </div>
        </div>

        <div class="form-actions" style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
          <button type="button" class="btn btn-cancel" onclick="closeUserModal()" style="background:#6b7280; color:white; padding:10px 20px; border-radius:6px; border:none; cursor:pointer;">Cancelar</button>
          <button type="submit" class="btn-primary" style="padding:10px 25px;">Guardar Usuario</button>
        </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
    @if($errors->any())
        window.onload = function() { openUserModal(); };
    @endif

    function editUser(id) {
        document.getElementById('modalTitle').innerText = 'Editar usuario';
        const form = document.getElementById('userForm');
        form.action = `/usuarios/${id}`;
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT'; // Forzamos que sea PUT para editar

       fetch(`/usuarios/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                form.id_clinica.value = data.id_clinica;
                form.nombre.value = data.nombre;
                form.apellido_paterno.value = data.apellido_paterno;
                form.apellido_materno.value = data.apellido_materno || ''; // Carga el materno
                form.nom_usuario.value = data.nom_usuario;
                const rolSelect = document.getElementById('rol');
            if (data.rol === 'superadmin') {
                // Añadimos temporalmente la opción admin para que se visualice
                rolSelect.innerHTML += `<option value="superadmin">Superadmin</option>`;
                rolSelect.value = 'superadmin';
                rolSelect.disabled = true; // No se puede cambiar el rol al admin

                // ASIGNA EL VALOR AL CAMPO OCULTO
                document.getElementById('rol_hidden').value = 'superadmin';
                document.getElementById('rol_hidden').disabled = false; // Activamos el oculto
            } else {
                rolSelect.disabled = false;
                document.getElementById('rol_hidden').disabled = true; // Desactivamos el oculto
                // Limpiamos y dejamos solo las opciones normales
                rolSelect.innerHTML = `
                    <option value="">Seleccionar</option>
                    <option value="dentista">Dentista / Dueño</option>
                    <option value="asistente">Asistente</option>
                `;
                rolSelect.value = data.rol;
            }
                form.cedula_profesional.value = data.cedula_profesional || '';
                
                const passInput = document.getElementById('password_input');
                passInput.placeholder = "Dejar en blanco para no cambiar";
                passInput.required = false;

                toggleCedula();
                openUserModal();
            });
    }

    function openUserModal() {
        document.getElementById('userModal').style.display = 'flex';
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');
        modal.style.display = 'none';
        const form = document.getElementById('userForm');
        form.reset();

        // NUEVO: Limpiar clases de error y mensajes de texto rojos
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('span[style*="color:red"]').forEach(el => el.remove());
        const rolSelect = document.getElementById('rol');
    rolSelect.disabled = false;
    rolSelect.innerHTML = `
        <option value="">Seleccionar</option>
        <option value="dentista">Dentista / Dueño</option>
        <option value="asistente">Asistente</option>
    `;
        form.action = "{{ route('usuarios.store') }}";
        const methodInput = document.querySelector('input[name="_method"]');
        if(methodInput) methodInput.remove();
        document.getElementById('modalTitle').innerText = 'Agregar usuario';
        const passInput = document.getElementById('password_input');
        passInput.required = true;
        passInput.placeholder = "";
        document.getElementById('cedulaGroup').style.display = 'none';
        document.getElementById('rol_hidden').disabled = true;
document.getElementById('rol_hidden').value = '';
    }

    function toggleCedula() {
        const rol = document.getElementById('rol').value;
        document.getElementById('cedulaGroup').style.display = (rol === 'dentista') ? 'grid' : 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('userModal')) {
            closeUserModal();
        }
    }
</script>
@endpush