@extends('layouts.admin')

@section('title', 'Registrar Clínica | DentControl')

@section('content')
    <h1>Registrar clínica</h1>
    <p class="subtitle">Datos generales de la clínica dental</p>
    @if ($errors->any())
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <strong>¡Ups! Revisa los siguientes errores:</strong>
        <ul style="margin-top: 0.5rem; list-of-style: disc; margin-left: 1.5rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="form-card">
      <form action="{{ route('clinicas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- Token de seguridad obligatorio en Laravel --}}
        
        <div class="form-row">
          <div class="form-group">
            <label>Nombre de la clínica</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required>
          </div>
          <div class="form-group">
            <label>RFC (12 o 13 caracteres)</label>
            <input type="text" name="rfc" value="{{ old('rfc') }}" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Calle</label>
            <input type="text" name="calle" value="{{ old('calle') }}">
          </div>
          <div class="form-group">
            <label>Número exterior</label>
            <input type="text" name="numero_ext" value="{{ old('numero_ext') }}">
          </div>
          <div class="form-group">
            <label>Número interior (Opcional)</label>
            <input type="text" name="numero_int" value="{{ old('numero_int') }}">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Colonia</label>
            <input type="text" name="colonia" value="{{ old('colonia') }}">
          </div>
          <div class="form-group">
            <label>Código postal (5 dígitos)</label>
            <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Ciudad</label>
            <input type="text" name="ciudad" value="{{ old('ciudad') }}" required>
          </div>
          <div class="form-group">
            <label>Estado</label>
            <input type="text" name="estado" value="{{ old('estado') }}" required>
          </div>
          <div class="form-group">
            <label>Teléfono (10 dígitos)</label>
            <input type="tel" name="telefono" value="{{ old('telefono') }}" required>
          </div>
        </div>

        <form action="{{ route('clinicas.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="form-row">
      <div class="form-group">
        <label>Logo de la clínica</label>
        <input type="file" name="logo_ruta" accept="image/*" class="form-control">
        <small>Formatos permitidos: JPG, PNG. Máx 2MB.</small>
      </div>
    </div>

    </form>

        <div style="display:flex; gap:15px; margin-top:25px; flex-wrap:wrap;">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Guardar clínica
          </button>
          <a href="{{ route('dashboard') }}" class="btn btn-cancel">
            <i class="fa-solid fa-xmark"></i> Cancelar
          </a>
        </div>
      </form>
    </div>
@endsection