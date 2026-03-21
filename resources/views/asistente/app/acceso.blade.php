@extends('layouts.clinica')

@section('content')

<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/acceso-movil.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="app-container">

<main class="content">

<h1>Acceso a aplicación móvil</h1>

<!-- DESCRIPCIÓN -->

<section class="card description">

<p>
Este módulo permite habilitar el acceso a la aplicación móvil únicamente a
pacientes con tratamientos de larga duración, garantizando la confidencialidad
de la información clínica conforme a la NOM-004-SSA3-2012.
</p>

<p>
Cuando se habilita el acceso, el sistema genera automáticamente las credenciales
del paciente y las envía a su correo electrónico registrado.
</p>

</section>


<!-- BUSCAR PACIENTE -->

<section class="card">

<h3>Buscar paciente</h3>

<div class="form-row">

<div class="form-group full-width">
<label>Nombre del paciente</label>
<input type="text" placeholder="Escribe el nombre del paciente">
</div>

<div class="form-group">
<button class="btn-primary">
<i class="fa-solid fa-magnifying-glass"></i> Buscar
</button>
</div>

</div>

</section>


<!-- HABILITAR ACCESO -->

<section class="card">

<h3>Habilitar acceso</h3>

<div class="form-row">

<div class="form-group">
<label>Nombre del paciente</label>
<input type="text" value="Juan Pérez López" disabled>
</div>

<div class="form-group">
<label>Correo electrónico</label>
<input type="email" value="juanperez@email.com" disabled>
</div>

</div>


<div class="form-row">

<div class="form-group">
<label>Tratamiento</label>

<select>

<option>Seleccionar tratamiento</option>
<option>Ortodoncia</option>
<option>Implantes</option>
<option>Endodoncia</option>

</select>

</div>


<div class="form-group">
<label>Estado de acceso</label>
<input type="text" value="Sin acceso habilitado" disabled>
</div>

</div>


<div class="info-message">

<i class="fa-solid fa-circle-info"></i>

Al habilitar el acceso, el sistema generará automáticamente el usuario y contraseña
del paciente y los enviará a su correo electrónico registrado.

</div>


<div class="form-actions">

<button class="btn-primary">

<i class="fa-solid fa-envelope"></i>
Enviar acceso al paciente

</button>


<button class="btn btn-edit">

<i class="fa-solid fa-pen"></i>
Editar

</button>


<button class="btn btn-cancel">

<i class="fa-solid fa-xmark"></i>
Cancelar

</button>

</div>

</section>

</main>

</div>

@endsection