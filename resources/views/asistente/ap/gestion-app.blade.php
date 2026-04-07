@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/gestion-app.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Control de usuarios con acceso a la app móvil</h1>

<!-- FILTROS -->
<section class="card">
  <h3>Filtrar usuarios</h3>

  <div class="form-row">
    <div class="form-group">
      <label>Paciente</label>
      <select>
        <option>Todos</option>
        <option>Juan Pérez</option>
        <option>Ana López</option>
      </select>
    </div>

    <div class="form-group">
      <label>Estado</label>
      <select>
        <option>Todos</option>
        <option>Activo</option>
        <option>Inactivo</option>
      </select>
    </div>

    <div class="form-group">
      <button class="btn-primary">
        <i class="fa-solid fa-filter"></i> Aplicar filtros
      </button>
    </div>
  </div>
</section>

<!-- TABLA -->
<section class="card">
  <h3>Usuarios registrados</h3>

  <table class="data-table">
    <thead>
      <tr>
        <th>Paciente</th>
        <th>Usuario</th>
        <th>Tratamiento</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>

    <tbody>

      <tr>
        <td>Juan Pérez</td>
        <td>juan_p</td>
        <td>Ortodoncia</td>
        <td><span class="status active">Activo</span></td>
        <td class="actions">

          <a href="#modalResend" class="btn-icon">
            <i class="fa-solid fa-envelope"></i>
          </a>

          <a href="#modalDeactivate" class="btn-icon danger">
            <i class="fa-solid fa-user-slash"></i>
          </a>

        </td>
      </tr>

      <tr>
        <td>Ana López</td>
        <td>ana_l</td>
        <td>Implantes</td>
        <td><span class="status inactive">Inactivo</span></td>
        <td class="actions">

          <a href="#modalReactivate" class="btn-icon success">
            <i class="fa-solid fa-user-check"></i>
          </a>

        </td>
      </tr>

    </tbody>
  </table>
</section>

<!-- MODAL REENVIAR -->
<div id="modalResend" class="modal">
  <div class="modal-content">
    <h3>Reenviar credenciales</h3>
    <p>Se generará una nueva contraseña y se enviará al correo del paciente.</p>

    <div class="form-actions">
      <button class="btn-primary">
        <i class="fa-solid fa-envelope"></i> Enviar
      </button>
      <a href="#" class="btn-secondary">Cancelar</a>
    </div>
  </div>
</div>

<!-- MODAL DESACTIVAR -->
<div id="modalDeactivate" class="modal">
  <div class="modal-content">
    <h3>Dar de baja acceso</h3>
    <p>¿Deseas desactivar el acceso a la aplicación móvil?</p>

    <div class="form-actions">
      <button class="btn-danger">
        <i class="fa-solid fa-user-slash"></i> Sí, desactivar
      </button>
      <a href="#" class="btn-secondary">Cancelar</a>
    </div>
  </div>
</div>

<!-- MODAL ACTIVAR -->
<div id="modalReactivate" class="modal">
  <div class="modal-content">
    <h3>Reactivar acceso</h3>
    <p>¿Deseas habilitar nuevamente el acceso del paciente?</p>

    <div class="form-actions">
      <button class="btn-success">
        <i class="fa-solid fa-user-check"></i> Sí, activar
      </button>
      <a href="#" class="btn-secondary">Cancelar</a>
    </div>
  </div>
</div>

@endsection