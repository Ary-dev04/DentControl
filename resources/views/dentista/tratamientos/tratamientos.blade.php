@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/tratamientos.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<h1>Gestión de tratamientos</h1>
<p class="subtitle">Consulta y seguimiento de tratamientos por paciente</p>

<!-- BUSCAR -->
<section class="card-section">
  <h3>Buscar paciente</h3>

  <div class="form-row">
    <div class="form-group full-width">
      <label>Nombre del paciente</label>
      <input type="text" placeholder="Ej. Juan Pérez López">
    </div>

    <div class="form-group">
      <button class="btn-primary">
        <i class="fa-solid fa-magnifying-glass"></i> Buscar
      </button>
    </div>
  </div>
</section>

<!-- LISTA -->
<section class="card-section">
  <h3>Tratamientos del paciente</h3>

  <div class="form-group full-width">
    <label>Seleccione un tratamiento</label>
    <select>
      <option>Seleccione un tratamiento</option>
      <option>Limpieza dental — 12/01/2025</option>
      <option>Ortodoncia — 05/11/2024</option>
      <option>Extracción — 20/09/2024</option>
    </select>
  </div>
</section>

<!-- DETALLE -->
<section class="card-section">
  <h3>Detalle del tratamiento</h3>

  <div class="form-row">
    <div class="form-group full-width">
      <label>Diagnóstico clínico</label>
      <textarea rows="3"></textarea>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group full-width">
      <label>Plan de tratamiento</label>
      <textarea rows="3"></textarea>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label>Fecha inicio</label>
      <input type="date">
    </div>

    <div class="form-group">
      <label>Fecha fin</label>
      <input type="date">
    </div>

    <div class="form-group">
      <label>Precio estimado</label>
      <input type="number" placeholder="$">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group full-width">
      <label>Evolución del tratamiento</label>
      <textarea rows="3"></textarea>
    </div>
  </div>
</section>

<!-- BOTONES -->
<div class="form-actions">

  <button class="btn-primary">
    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
  </button>

  <button class="btn btn-edit">
    <i class="fa-solid fa-pen"></i> Editar
  </button>

  <a href="#" class="btn btn-cancel">
    <i class="fa-solid fa-xmark"></i> Cancelar
  </a>

</div>

@endsection