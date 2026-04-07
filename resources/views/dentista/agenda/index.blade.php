@extends('layouts.clinica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/stylesBase.css') }}">
<link rel="stylesheet" href="{{ asset('css/agendaDentista.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="app-container">
    <main class="content">
        <h1>Agenda del Día</h1>
        <div class="fecha-hoy" id="fechaActual" style="font-weight: bold; color: #64748b; margin-bottom: 20px;"></div>

        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Tipo</th>
                        <th>Servicio / Tratamiento</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $hayPacienteEnAtencion = $citas->contains('estatus_cita', 'enproceso');
                    @endphp

                    @forelse($citas as $cita)
                        @php 
                            $esTrat = (bool)$cita->id_tratamiento; 
                        @endphp
                        <tr>
                            <td style="font-weight: bold;">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</td>

                            <td>{{ $cita->paciente->nombre }} {{ $cita->paciente->apellido_paterno }}</td>

                            <td>
                                <span class="tipo {{ $esTrat ? 'tratamiento' : 'servicio' }}">
                                    {{ $esTrat ? 'Tratamiento' : 'Servicio' }}
                                </span>
                            </td>

                            <td>
                                {{ $esTrat ? ($cita->tratamiento?->catalogoTratamiento?->nombre) : ($cita->servicio?->nombre ?? 'Consulta') }}
                            </td>

                            <td>{{ $cita->motivo_consulta }}</td>

                            <td>
                                @if($cita->estatus_cita == 'enproceso')
                                    <span class="estado enproceso">En atención</span>
                                    @if($cita->hora_inicio_real)
                                        <div style="font-size: 0.75rem; color: #3b82f6; margin-top: 4px;">
                                            Inicio: {{ \Carbon\Carbon::parse($cita->hora_inicio_real)->format('h:i A') }}
                                        </div>
                                    @endif
                                @elseif($cita->estatus_cita == 'programada')
                                    <span class="estado programada">Programada</span>
                                @else
                                    <span style="color: #16a34a; font-weight: bold;">
                                        <i class="fa-solid fa-circle-check"></i> Atendido
                                    </span>
                                    @if($cita->hora_final_real)
                                        <div style="font-size: 0.75rem; color: #202121;">
                                            Fin: {{ \Carbon\Carbon::parse($cita->hora_final_real)->format('h:i A') }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            <td class="acciones">
                                <div class="acciones-wrapper">
                                    <a href="{{ route('paciente.historial', $cita->id_paciente) }}" 
                                       class="btn-expediente" 
                                       title="Ver Expediente Clínico">
                                        <i class="fa-solid fa-folder-open"></i>
                                    </a>

                                    <div class="estado-accion-container">
                                        @if($cita->estatus_cita == 'programada')
                                            @if(!$hayPacienteEnAtencion)
                                                <button class="btn-atender" onclick="atenderCita({{ $cita->id_cita }})">
                                                    Atender <i class="fa-solid fa-stethoscope"></i>
                                                </button>
                                            @else
                                                <span class="texto-ocupado">
                                                    <i class="fa-solid fa-lock"></i> Médico Ocupado
                                                </span>
                                            @endif

                                        @elseif($cita->estatus_cita == 'enproceso')
                                            <span class="badge-atencion">
                                                <i class="fa-solid fa-user-doctor"></i> Atendiendo...
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 20px;">No hay citas agendadas para hoy</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    // Fecha Dinámica
    document.getElementById("fechaActual").innerText = new Date().toLocaleDateString('es-ES', {
        weekday:'long', year:'numeric', month:'long', day:'numeric'
    });

    // Función para que el dentista marque que ya está atendiendo
    function atenderCita(id) {
    if(confirm("¿Deseas marcar que el paciente ya está en atención?")) {
        fetch(`/agenda/iniciar/${id}`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json',
                'Accept': 'application/json' // Obliga a Laravel a responder en JSON
            }
        })
        .then(response => {
            if (!response.ok) {
                // Si el servidor responde con error (404, 500, etc)
                throw new Error('Error en el servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert("No se pudo actualizar: " + (data.error || "Error desconocido"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error de conexión o de sistema. Revisa la consola (F12).");
        });
    }
}
</script>
@endsection