<?php

namespace App\Http\Controllers\Asistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccesoMovil;
use App\Models\Paciente;
use App\Mail\AccesoMovilMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GestionAppController extends Controller
{
    // Muestra la tabla con todos los usuarios que YA tienen acceso
    public function index(Request $request)
    {
        $paciente_id = $request->get('id_paciente');
        $estado = $request->get('estatus');

        $usuarios = AccesoMovil::with(['paciente.tratamientos.catalogoTratamiento'])
            ->when($paciente_id, function($query) use ($paciente_id) {
                return $query->where('id_paciente', $paciente_id);
            })
            ->when($estado, function($query) use ($estado) {
                return $query->where('estatus', $estado);
            })
            ->get();

        // Lista para el select del filtro
        $listaPacientes = Paciente::has('accesoMovil')->get();

        return view('asistente.ap.gestion-app', compact('usuarios', 'listaPacientes'));
    }

    // Acción: Reenviar (POST)
    public function reenviar(Request $request, $id)
{
    $acceso = AccesoMovil::where('id_paciente', $id)->firstOrFail();
    $paciente = $acceso->paciente;
    
    // 1. Limpiar tokens antiguos de la tabla personal_access_tokens
    // Esto desloguea al paciente de cualquier teléfono donde esté iniciada la sesión
    $acceso->tokens()->delete(); 

    // 2. Generar nueva contraseña
    $nuevaPassword = Str::random(8);

    // 3. Mantener estatus temporal si nunca ha entrado, o reactivar si estaba expirado
    $nuevoEstatus = ($acceso->estatus == 'expirado') ? 'activo' : $acceso->estatus;

    $acceso->update([
        'password' => Hash::make($nuevaPassword),
        'estatus'  => $nuevoEstatus
    ]);

    $correo = $paciente->email ?: $paciente->email_tutor;

    try {
        Mail::to($correo)->send(new AccesoMovilMail(
            $acceso->usuario_movil, 
            $nuevaPassword, 
            $paciente->nombre
        ));
        return back()->with('success', "Nueva contraseña enviada y sesiones anteriores cerradas.");
    } catch (\Exception $e) {
        return back()->with('error', 'Error al enviar el correo.');
    }
}

    // Acción: Cambiar Estatus (POST)
   public function cambiarEstatus(Request $request, $id)
{
    // 1. Buscamos el registro del paciente en la tabla de acceso
    $acceso = \App\Models\AccesoMovil::where('id_paciente', $id)->firstOrFail();

    // 2. Determinamos el nuevo estado basado en el botón presionado
    $nuevoEstado = ($request->accion == 'activar') ? 'activo' : 'expirado';
    
    // 3. SEGURIDAD: Si estamos desactivando (expirado), cerramos todas las sesiones activas.
    // Esto borra los registros en la tabla 'personal_access_tokens' que viste antes.
    if ($nuevoEstado === 'expirado') {
        $acceso->tokens()->delete(); 
    }
    
    // 4. Actualizamos el estatus en la tabla acceso_movil
    $acceso->update(['estatus' => $nuevoEstado]);

    // 5. Mensaje personalizado para el asistente
    $mensaje = ($nuevoEstado == 'activo') 
        ? "Acceso reactivado. Ahora puedes volver a enviar credenciales si es necesario." 
        : "Acceso desactivado. Se han cerrado todas las sesiones en dispositivos móviles.";

    return back()->with('success', $mensaje);
}
}