@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/acceso-movil.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="app-container">
    <main class="content">
        <h1>Acceso a aplicación móvil</h1>

        @if(session('success'))
    <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background:#fef2f2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px;">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif

        <section class="card description">
            <p>Este módulo habilita el acceso a la App Móvil para pacientes con <strong>tratamientos activos</strong>.</p>
        </section>

        <section class="card">
            <h3>Buscar paciente apto para acceso</h3>
            <div class="form-row" style="position: relative;">
                <div class="form-group full-width">
                    <label>Nombre del paciente o CURP</label>
                    <input type="text" id="inputBusqueda" placeholder="Escribe para buscar..." autocomplete="off">
                    <div id="sugerencias" style="display:none; position:absolute; width:100%; background:white; z-index:1000; box-shadow:0 4px 6px rgba(0,0,0,0.1); border-radius:8px; border:1px solid #e2e8f0; margin-top:5px;"></div>
                </div>
            </div>
        </section>

        @if($paciente)
        <section class="card">
            <h3>Confirmar y habilitar acceso</h3>

            <form action="{{ route('acceso.habilitar') }}" method="POST">
                @csrf
                <input type="hidden" name="id_paciente" value="{{ $paciente->id_paciente }}">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre del paciente</label>
                        <input type="text" value="{{ $paciente->nombre }} {{ $paciente->apellido_paterno }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Correo de recepción de claves</label>
                        @if($paciente->email)
                            <input type="text" value="{{ $paciente->email }}" disabled style="border: 1px solid #22c55e; background: #f0fdf4;">
                            <small style="color: #166534;"><i class="fa-solid fa-user-check"></i> Correo directo del paciente</small>
                        @elseif($paciente->email_tutor)
                            <input type="text" value="{{ $paciente->email_tutor }}" disabled style="border: 1px solid #f59e0b; background: #fffbeb;">
                            <small style="color: #92400e;"><i class="fa-solid fa-user-shield"></i> Correo del Tutor: {{ $paciente->nombre_tutor }}</small>
                        @else
                            <input type="text" value="Sin correo registrado" disabled style="border: 1px solid #ef4444; background: #fef2f2;">
                            <small style="color: #b91c1c;"><i class="fa-solid fa-circle-exclamation"></i> Falta correo del paciente o tutor</small>
                        @endif
                    </div>
                </div>

                <div class="form-group full-width" style="margin-top: 15px;">
                    <label>Tratamientos activos vinculados:</label>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        @php $activos = $paciente->tratamientos->where('estatus', 'curso'); @endphp
                        @if($activos->count() > 0)
                            <ul style="margin: 0; padding-left: 20px; color: #1e293b;">
                                @foreach($activos as $t)
                                    <li style="margin-bottom: 5px;">
                                        <i class="fa-solid fa-check-circle" style="color: #22c55e;"></i> 
                                        <strong>{{ $t->catalogoTratamiento->nombre ?? 'Tratamiento' }}</strong> 
                                        <span style="font-size: 0.85rem; color: #64748b;">(Iniciado: {{ $t->fecha_inicio }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> No hay tratamientos en curso.</span>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Estado de la cuenta móvil</label>
                        <input type="text" value="{{ $paciente->accesoMovil ? 'Cuenta: ' . ucfirst($paciente->accesoMovil->estatus) : 'Sin cuenta creada' }}" disabled 
                               style="color: {{ $paciente->accesoMovil ? '#166534' : '#64748b' }}; font-weight: bold;">
                    </div>
                </div>

                <div class="info-message" style="margin-top: 20px; background: #f1f5f9; padding: 15px; border-radius: 8px;">
                    <i class="fa-solid fa-paper-plane"></i>
                    @php $correoFinal = $paciente->email ?: $paciente->email_tutor; @endphp
                    @if($correoFinal)
                        Se enviará el <strong>Usuario</strong> y <strong>Contraseña</strong> a: <strong>{{ $correoFinal }}</strong> 
                        ({{ $paciente->email ? 'Paciente' : 'Tutor' }}).
                    @else
                        <span style="color: #b91c1c;">No se pueden generar credenciales sin un correo electrónico válido.</span>
                    @endif
                </div>

                <div class="form-actions" style="margin-top: 25px;">
                    @php $tieneCorreo = $paciente->email || $paciente->email_tutor; @endphp
                    <button type="submit" class="btn-primary" 
                            style="background: {{ $tieneCorreo ? '#2563eb' : '#94a3b8' }};" 
                            {{ !$tieneCorreo ? 'disabled' : '' }}>
                        <i class="fa-solid fa-envelope"></i> Enviar credenciales
                    </button>
                    <a href="{{ route('acceso.index') }}" class="btn-cancel" style="background: #64748b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
                        Cancelar
                    </a>
                </div>
            </form>
        </section>
        @else
        <div style="text-align: center; padding: 60px; color: #94a3b8;">
            <i class="fa-solid fa-mobile-button" style="font-size: 4rem; margin-bottom: 20px;"></i>
            <p>Busca un paciente para gestionar su acceso.</p>
        </div>
        @endif
    </main>
</div>

<script>
document.getElementById('inputBusqueda').addEventListener('input', function() {
    let q = this.value;
    let lista = document.getElementById('sugerencias');
    
    if (q.length > 2) {
        // IMPORTANTE: Cambiamos a la ruta específica que filtra por tratamiento
        fetch("{{ route('pacientes.buscar_acceso_ajax') }}?q=" + q)
            .then(res => res.json())
            .then(data => {
                lista.innerHTML = "";
                if (data.length > 0) {
                    lista.style.display = "block";
                    data.forEach(p => {
                        let div = document.createElement('div');
                        div.style.padding = "12px 15px";
                        div.style.cursor = "pointer";
                        div.style.borderBottom = "1px solid #f1f5f9";
                        div.innerHTML = `
                            <div style="font-weight: bold; color: #1e293b;">${p.nombre} ${p.apellido_paterno}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">CURP: ${p.curp} | <span style="color:#2563eb;">Tratamiento Activo</span></div>
                        `;
                        div.onclick = () => {
                            window.location.href = "{{ route('acceso.index') }}?id_paciente=" + p.id_paciente;
                        };
                        div.onmouseover = () => div.style.background = "#f8fafc";
                        div.onmouseout = () => div.style.background = "white";
                        lista.appendChild(div);
                    });
                } else {
                    lista.style.display = "none";
                }
            });
    } else {
        lista.style.display = "none";
    }
});

document.addEventListener('click', (e) => {
    if (e.target.id !== 'inputBusqueda') document.getElementById('sugerencias').style.display = "none";
});
</script>
@endsection