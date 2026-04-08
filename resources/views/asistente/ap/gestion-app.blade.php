@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/gestion-app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Control de usuarios con acceso a la app móvil</h1>

@if(session('success'))
    <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<section class="card">
    <h3>Filtrar usuarios</h3>
    <form action="{{ route('asistente.gestion-app') }}" method="GET">
        <div class="form-row">
            <div class="form-group">
                <label>Paciente</label>
                <select name="id_paciente">
                    <option value="">Todos los pacientes</option>
                    @foreach($listaPacientes as $lp)
                        <option value="{{ $lp->id_paciente }}" {{ request('id_paciente') == $lp->id_paciente ? 'selected' : '' }}>
                            {{ $lp->nombre }} {{ $lp->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estatus">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="expirado" {{ request('estatus') == 'expirado' ? 'selected' : '' }}>Inactivo (Expirado)</option>
                    <option value="temporal" {{ request('estatus') == 'temporal' ? 'selected' : '' }}>Temporal</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary" style="margin-top: 25px;">
                    <i class="fa-solid fa-filter"></i> Aplicar filtros
                </button>
            </div>
        </div>
    </form>
</section>

<section class="card">
    <h3>Usuarios registrados</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Usuario App</th>
                <th>Tratamiento Activo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $u)
            <tr>
                <td>{{ $u->paciente->nombre }} {{ $u->paciente->apellido_paterno }}</td>
                <td><code>{{ $u->usuario_movil }}</code></td>
                <td>
                    @php $trat = $u->paciente->tratamientos->where('estatus', 'curso')->first(); @endphp
                    {{ $trat ? $trat->catalogoTratamiento->nombre : 'Sin tratamiento activo' }}
                </td>
                <td>
                    <span class="status {{ $u->estatus == 'activo' ? 'active' : ($u->estatus == 'temporal' ? 'warning' : 'inactive') }}">
                        {{ ucfirst($u->estatus) }}
                    </span>
                </td>
                <td class="actions">
    {{-- Solo mostramos el botón funcional si NO está expirado --}}
    @if($u->estatus !== 'expirado')
        <button class="btn-icon" title="Reenviar Credenciales" 
                onclick="abrirModal('modalResend', '{{ $u->id_paciente }}', '{{ $u->paciente->nombre }}')">
            <i class="fa-solid fa-envelope"></i>
        </button>
    @else
        {{-- Botón visualmente deshabilitado cuando está desactivado --}}
        <button class="btn-icon" title="Debe reactivar el acceso primero" 
                style="background: #ccc; cursor: not-allowed;" disabled>
            <i class="fa-solid fa-envelope"></i>
        </button>
    @endif

    {{-- Botones de Activar/Desactivar (Se mantienen igual) --}}
    @if($u->estatus == 'activo' || $u->estatus == 'temporal')
        <button class="btn-icon danger" title="Desactivar Acceso" 
                onclick="abrirModal('modalDeactivate', '{{ $u->id_paciente }}', '{{ $u->paciente->nombre }}')">
            <i class="fa-solid fa-user-slash"></i>
        </button>
    @else
        <button class="btn-icon success" title="Reactivar Acceso" 
                onclick="abrirModal('modalReactivate', '{{ $u->id_paciente }}', '{{ $u->paciente->nombre }}')">
            <i class="fa-solid fa-user-check"></i>
        </button>
    @endif
</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">No se encontraron usuarios con acceso móvil.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</section>

<div id="modalResend" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Reenviar credenciales</h3>
        <p>¿Generar nueva contraseña y enviarla a <strong class="nombre-paciente"></strong>?</p>
        <form id="formResend" method="POST">
            @csrf
            <div class="form-actions">
                <button type="submit" class="btn-primary">Sí, enviar nuevo acceso</button>
                <button type="button" class="btn-secondary" onclick="cerrarModales()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDeactivate" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Dar de baja acceso</h3>
        <p>¿Deseas desactivar el acceso móvil de <strong class="nombre-paciente"></strong>?</p>
        <form id="formDeactivate" method="POST">
            @csrf
            <input type="hidden" name="accion" value="desactivar">
            <div class="form-actions">
                <button type="submit" class="btn-danger">Sí, desactivar</button>
                <button type="button" class="btn-secondary" onclick="cerrarModales()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalReactivate" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Reactivar acceso</h3>
        <p>¿Habilitar nuevamente el acceso para <strong class="nombre-paciente"></strong>?</p>
        <form id="formReactivate" method="POST">
            @csrf
            <input type="hidden" name="accion" value="activar">
            <div class="form-actions">
                <button type="submit" class="btn-success">Sí, activar</button>
                <button type="button" class="btn-secondary" onclick="cerrarModales()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(idModal, idPaciente, nombre) {
    const modal = document.getElementById(idModal);
    
    // Insertar nombre del paciente en el texto del modal
    modal.querySelectorAll('.nombre-paciente').forEach(el => el.innerText = nombre);
    
    // Configurar la URL del formulario
    const form = modal.querySelector('form');
    if(idModal === 'modalResend') {
        form.action = `/asistente/gestion-app/reenviar/${idPaciente}`;
    } else {
        form.action = `/asistente/gestion-app/estatus/${idPaciente}`;
    }
    
    modal.style.display = 'flex'; // Usar flex para centrar si tu CSS lo permite
}

function cerrarModales() {
    document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
}

// Cerrar si hacen clic fuera del modal
window.onclick = function(event) {
    if (event.target.className === 'modal') cerrarModales();
}
</script>
@endsection