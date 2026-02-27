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
        // Validamos que el usuario envíe los datos
    $credentials = $request->validate([
        'nom_usuario' => 'required|string',
        'password' => 'required|string',
    ]);

    // Intentamos autenticar usando 'nom_usuario' en lugar de 'email'
    if (Auth::attempt([
        'nom_usuario' => $credentials['nom_usuario'], 
        'password' => $credentials['password']
    ])) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    // Si falla, regresamos con error
    return back()->withErrors([
        'nom_usuario' => 'El nombre de usuario o la contraseña no son correctos.',
    ])->onlyInput('nom_usuario');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
