<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nom_usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Capturamos si el usuario marcó la casilla "Recordarme"
        // Esto devuelve true si el checkbox fue marcado, o false si no.
        $remember = $request->has('remember');

        // 1. Intentamos el login
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // 2. VERIFICACIÓN DE ESTATUS (Bloquear si no está activo)
            // Nota: 'activa' es para la clínica, 'activo' para el usuario. 
            // Si el usuario es 'inactivo', lo sacamos de inmediato.
            if ($user->estatus !== 'activo') {
                Auth::logout();
                return back()->withErrors([
                    'nom_usuario' => 'Tu cuenta está suspendida o inactiva. Contacta al administrador.',
                ]);
            }
            // 2. Verificar si su CLÍNICA está activa (Solo si el usuario tiene una clínica asociada)
            // El Superadmin no tiene clínica, por eso usamos el check optional o null safe
            if ($user->id_clinica && $user->clinica->estatus === 'baja') {
                Auth::logout();
                return back()->withErrors([
                    'nom_usuario' => 'La clínica asociada a esta cuenta ha sido dada de baja.',
                ]);
            }   

            $request->session()->regenerate();

            // 3. REDIRECCIÓN SEGÚN ROL
            return $this->redirectByUserRole($user);
        }

        return back()->withErrors([
            'nom_usuario' => 'El nombre de usuario o la contraseña no son correctos.',
        ])->onlyInput('nom_usuario');
    }

    /**
     * Función auxiliar para decidir a qué dashboard enviar a cada quien
     */
    protected function redirectByUserRole($user)
    {
        switch ($user->rol) {
            case 'superadmin':
                return redirect()->route('admin.dashboard'); // Tu dashboard actual (SaaS)
            case 'dentista':
                return redirect()->route('dentista.dashboard'); // Dashboard de clínica
            case 'asistente':
                return redirect()->route('asistente.dashboard'); // Dashboard limitado
            default:
                return redirect()->intended('dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}