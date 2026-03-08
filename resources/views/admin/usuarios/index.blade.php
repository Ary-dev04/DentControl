@extends('layouts.admin')

@section('title', 'Registrar Usuarios | DentControl SaaS')

@section('content')
    <style>
        /* Estilos mejorados para el modal y formulario */
        .modal-body-custom {
            background-color: white; 
            padding: 25px; 
            border-radius: 12px; 
            width: 850px; /* Ancho ampliado para 3 columnas */
            max-width: 95%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columnas iguales */
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group-full {
            grid-column: span 3; /* Ocupa todo el ancho */
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
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
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
        .text-danger { color: #ef4444; font-size: 0.8rem; margin-top: 5px; }
    </style>

    <h1>Registrar usuarios</h1>
    <p class="subtitle">Gestión de accesos al sistema por clínica</p>

    <button class="btn-primary" onclick="prepareCreateModal()">
      <i class="fa-solid fa-user-plus"></i> Agregar usuario
    </button>

    <div class="search-wrapper" style="margin-top: 20px; position: relative; width: 400px;">
    <i class="fa-solid fa-magnifying-glass search-icon" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
    <input type="text" 
           id="userSearch" 
           class="search-input" 
           style="padding: 10px 10px 10px 35px; border: 1px solid #d1d5db; border-radius: 6px; width: 100%;"
           placeholder="Buscar por nombre, clínica, rol o estado..." 
           onkeyup="filterUsers()">
</div>

    @if(session('success'))
        <div style="padding: 15px; background-color: #d4edda; color: #155724; margin-top: 20px; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <table id="userTable" style="margin-top:25px; width: 100%; border-collapse: collapse;">
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
        {{ $user->estatus == 'activo' ? 'Activo' : 'Baja' }}
    </span>
</td>
          <td>
            <button class="btn btn-edit" 
        onclick="editUser('{{ $user->id_usuario }}')"
        {{ $user->estatus == 'baja' ? 'disabled' : '' }}
        style="{{ $user->estatus == 'baja' ? 'opacity: 0.5; cursor: not-allowed; background-color: #94a3b8;' : '' }}"
        title="{{ $user->estatus == 'baja' ? 'Reactivar usuario para editar' : 'Editar Usuario' }}">
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
        <h3 id="modalTitle" on>Agregar usuario</h3>
        <button onclick="closeUserModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
    </div>

    <form action="{{ route('usuarios.store') }}" method="POST" id="userForm" autocomplete="off">
        @csrf
        
        <div class="form-grid">
            <div class="form-group-full">
                <label>Clínica</label>
                <select name="id_clinica" id="id_clinica" required class="@error('id_clinica') is-invalid @enderror">
                    <option value="">Seleccionar clínica</option>
                    @foreach($clinicas as $clinica)
                        <option value="{{ $clinica->id_clinica }}" {{ old('id_clinica') == $clinica->id_clinica ? 'selected' : '' }}>{{ $clinica->nombre }}</option>
                    @endforeach
                </select>
                @error('id_clinica') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group-custom">
                <label>Nombre(s)</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                       maxlength="50" required class="@error('nombre') is-invalid @enderror"
                       oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')"
                       onblur="this.value = this.value.trim().replace(/\s+/g, ' ')">
                @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Apellido Paterno</label>
                <input type="text" name="apellido_paterno" id="apellido_paterno" value="{{ old('apellido_paterno') }}" 
                       maxlength="50" required class="@error('apellido_paterno') is-invalid @enderror"
                       oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')"
                       onblur="this.value = this.value.trim().replace(/\s+/g, ' ')">
                @error('apellido_paterno') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" id="apellido_materno" value="{{ old('apellido_materno') }}" 
                       maxlength="50" class="@error('apellido_materno') is-invalid @enderror"
                       oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')"
                       onblur="this.value = this.value.trim().replace(/\s+/g, ' ')">
                @error('apellido_materno') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group-custom">
                <label>Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="@error('email') is-invalid @enderror"
                       onblur="this.value = this.value.trim().toLowerCase()" 
                       placeholder="ejemplo@correo.com" required
                       oninput="validateEmailInput(this)">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Teléfono (10 dígitos)</label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" 
                       maxlength="10" required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="@error('telefono') is-invalid @enderror" 
                       placeholder="5512345678">
                @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Usuario (Login)</label>
                <input type="text" name="nom_usuario" id="nom_usuario" value="{{ old('nom_usuario') }}" required
                       maxlength="15" class="@error('nom_usuario') is-invalid @enderror" oninput="validateLoginInput(this)"
                       placeholder="Ejemplo: hola123">
                @error('nom_usuario') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group-custom">
                <label>Contraseña</label>
                <input type="password" name="password" id="password_input"
                       maxlength="8" class="@error('password') is-invalid @enderror"
                       placeholder="8 caracteres (letras, Mayús. y núm.)"
                       oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom">
                <label>Rol</label>
                <select name="rol" id="rol" onchange="toggleCedula()" required class="@error('rol') is-invalid @enderror">
                    <option value="">Seleccionar</option>
                    <option value="dentista" {{ old('rol') == 'dentista' ? 'selected' : '' }}>Dentista / Dueño</option>
                    <option value="asistente" {{ old('rol') == 'asistente' ? 'selected' : '' }}>Asistente</option>
                </select>
                <input type="hidden" name="rol_hidden" id="rol_hidden" disabled>
                @error('rol') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group-custom" id="cedulaGroup" style="display:none;">
                <label>Cédula profesional</label>
                <input type="text" name="cedula_profesional" id="cedula_profesional"
                       value="{{ old('cedula_profesional') }}" maxlength="8"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="@error('cedula_profesional') is-invalid @enderror"
                       placeholder="8 dígitos">
                @error('cedula_profesional') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-actions" style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
          <button type="submit" class="btn-primary" style="padding:10px 25px;">Guardar Usuario</button>
        </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>

    // Esta es la función que debes agregar/reemplazar
function prepareCreateModal() {
    const form = document.getElementById('userForm');
    
    // 1. Limpiar el formulario de forma nativa
    form.reset(); 

    form.querySelectorAll('.text-danger').forEach(el => el.remove());

    // 2. Limpiar manualmente los campos
    const fields = ['nombre', 'apellido_paterno', 'apellido_materno', 'email', 'telefono', 'nom_usuario', 'password_input', 'cedula_profesional', 'rol'];
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if(element) {
            element.value = ''; 
            element.classList.remove('is-invalid'); 
        }
    });

    // --- NUEVA LÓGICA PARA EL ROL ---
    const rolSelect = document.getElementById('rol');
    if (rolSelect) {
        // A. Habilitar el select (por si veníamos de una edición donde se bloqueó)
        rolSelect.disabled = false;

        // B. Quitar la opción de superadmin si se agregó dinámicamente en una edición
        const superOpt = rolSelect.querySelector('option[value="superadmin"]');
        if (superOpt) {
            superOpt.remove();
        }
    }

    // C. Desactivar el input hidden de rol para que no interfiera en la creación
    const rolHidden = document.getElementById('rol_hidden');
    if (rolHidden) {
        rolHidden.disabled = true;
        rolHidden.value = '';
    }
    // --------------------------------

    // 3. Resetear el Select de Clínica
    const clinicaSelect = document.getElementById('id_clinica');
    if(clinicaSelect) clinicaSelect.value = '';

    const passInput = document.getElementById('password_input');
    passInput.placeholder = "8 carac. (letras, Mayús. y núm.)"; 
    passInput.required = true;

    // 4. Configurar el modal para modo "Crear"
    form.action = "{{ route('usuarios.store') }}";
    const methodInput = form.querySelector('input[name="_method"]');
    if(methodInput) methodInput.remove(); 

    document.getElementById('modalTitle').innerText = 'Agregar usuario';
    document.getElementById('cedulaGroup').style.display = 'none';

    // 5. Finalmente, abrir el modal
    openUserModal();
}
    @if($errors->any())
    window.onload = function() { 
        openUserModal(); 

        const form = document.getElementById('userForm');
        // 1. Detectamos si venimos de una edición fallida
        const editingId = "{{ session('editing_user_id') }}";

        if (editingId || "{{ old('_method') }}" === 'PUT') {
            const id = editingId || "{{ basename(url()->previous()) }}"; // Intenta recuperar el ID
            
            document.getElementById('modalTitle').innerText = 'Editar usuario';
            
            // 2. ACTUALIZAMOS LA RUTA: Esto evita que te mande a "Registrar"
            form.action = `/usuarios/${id}`;
            
            // 3. ASEGURAMOS EL MÉTODO PUT: Para que Laravel sepa que es actualización
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // 4. Password no es obligatorio al fallar edición
            const passInput = document.getElementById('password_input');
            passInput.required = false;
            passInput.placeholder = "Dejar en blanco para no cambiar";
        }

        // Lógica de la cédula (se mantiene igual)
        const rolActual = document.getElementById('rol').value;
        if(rolActual === 'dentista') {
            document.getElementById('cedulaGroup').style.display = 'flex';
        }
        
        toggleCedula();
    };
@endif

    function editUser(id) {
    document.getElementById('modalTitle').innerText = 'Editar usuario';
    const form = document.getElementById('userForm');
    form.action = `/usuarios/${id}`;
    
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    fetch(`/usuarios/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            form.id_clinica.value = data.id_clinica;
            form.nombre.value = data.nombre;
            form.apellido_paterno.value = data.apellido_paterno;
            form.apellido_materno.value = data.apellido_materno || '';
            form.nom_usuario.value = data.nom_usuario;
            form.email.value = data.email;
            form.telefono.value = data.telefono;
            
            // --- LÓGICA DE ROL BLOQUEADO PARA TODOS ---
            const rolSelect = document.getElementById('rol');
            const rolHidden = document.getElementById('rol_hidden');

            // NUEVO: Verificar si el rol es superadmin y añadir la opción si falta
            if (data.rol === 'superadmin') {
                let superOpt = rolSelect.querySelector('option[value="superadmin"]');
                if (!superOpt) {
                    const newOpt = document.createElement('option');
                    newOpt.value = 'superadmin';
                    newOpt.text = 'Superadministrador';
                    rolSelect.add(newOpt);
                }
            }

            rolSelect.value = data.rol; 
            rolSelect.disabled = true; 

            if (rolHidden) {
                rolHidden.value = data.rol;
                rolHidden.disabled = false; 
            }
            // ------------------------------------------

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
    
    // Limpiar clases de error y mensajes de texto de Laravel
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Resetear el formulario al estado original
    form.reset();
    
    // IMPORTANTE: Limpiar los campos manualmente para borrar los valores de old()
    const fields = ['id_clinica', 'nombre', 'apellido_paterno', 'apellido_materno', 'email', 'telefono', 'nom_usuario', 'password_input', 'cedula_profesional', 'rol'];
    fields.forEach(id => {
        const el = document.getElementById(id);
        if(el) el.value = '';
    });

    // Resetear configuración de edición
    form.action = "{{ route('usuarios.store') }}";
    const methodInput = form.querySelector('input[name="_method"]');
    if(methodInput) methodInput.remove();
    
    document.getElementById('modalTitle').innerText = 'Agregar usuario';
    const passInput = document.getElementById('password_input');
    passInput.required = true;
    passInput.placeholder = "Mínimo 8 caracteres";
    document.getElementById('cedulaGroup').style.display = 'none';
    
    // Resetear el select de rol
    const rolSelect = document.getElementById('rol');
    rolSelect.disabled = false;
    rolSelect.innerHTML = `
        <option value="">Seleccionar</option>
        <option value="dentista">Dentista / Dueño</option>
        <option value="asistente">Asistente</option>
    `;
}

    function toggleCedula() {
        const rol = document.getElementById('rol').value;
        const cedulaGroup = document.getElementById('cedulaGroup');
        cedulaGroup.style.display = (rol === 'dentista') ? 'flex' : 'none';
        if(rol !== 'dentista') document.getElementById('cedula_profesional').value = '';
    }

    function validateEmailInput(input) {
    // 1. Limpieza básica en tiempo real (minúsculas y sin espacios)
    let value = input.value.toLowerCase().replace(/\s/g, '');

    // 1.1 FILTRO DE CARACTERES ESPECIALES (NUEVO)
    // Solo permite: letras, números, @, punto, guion y guion bajo. 
    // Borra todo lo demás (*, #, $, etc.)
    value = value.replace(/[^a-z0-9@._-]/g, '');

    // 1.2 EVITAR DOBLE @ (NUEVO)
    const parts = value.split('@');
    if (parts.length > 2) {
        value = parts[0] + '@' + parts.slice(1).join('');
    }

    // Actualizamos el valor visual del input con el texto limpio
    input.value = value;

    // 2. Expresión regular para formato de email (mantenemos la tuya)
    const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
    const errorSpan = document.getElementById('emailError');

    // 3. Validación visual (mantenemos tu lógica de colores)
    if (value.length > 0) {
        if (!emailRegex.test(value)) {
            input.style.border = "1px solid #ef4444"; // Borde rojo
            if(errorSpan) errorSpan.style.display = 'block';
        } else {
            input.style.border = "1px solid #10b981"; // Borde verde
            if(errorSpan) errorSpan.style.display = 'none';
        }
    } else {
        input.style.border = "1px solid #d1d5db"; // Reset color si está vacío
        if(errorSpan) errorSpan.style.display = 'none';
    }
}

function filterUsers() {
    const input = document.getElementById("userSearch");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("userTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let visible = false;
        const td = tr[i].getElementsByTagName("td");
        
        // Revisamos las primeras 4 columnas: Nombre, Clínica, Rol y ESTADO
        for (let j = 0; j < 4; j++) { 
            if (td[j]) {
                const textValue = td[j].textContent || td[j].innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    visible = true;
                    break; 
                }
            }
        }
        tr[i].style.display = visible ? "" : "none";
    }
}
    
function validateLoginInput(input) {
    // 1. Convertir a minúsculas y eliminar espacios en blanco de inmediato
    let value = input.value.toLowerCase().replace(/\s/g, '');

    // 2. FILTRO ESTRICTO: Solo permite letras de la 'a' a la 'z' y números del 0 al 9
    // Elimina caracteres especiales, puntos, guiones y tildes
    value = value.replace(/[^a-z0-9]/g, '');

    // 3. Asignar el valor limpio de vuelta al input
    input.value = value;
}
</script>
@endpush