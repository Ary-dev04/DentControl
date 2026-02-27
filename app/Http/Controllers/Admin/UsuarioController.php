<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Clinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    //
    // Muestra la lista de usuarios y el formulario (modal)
    public function index()
    {
        $usuarios = Usuario::with('clinica')->get(); // Carga usuarios con su clínica
        $clinicas = Clinica::all(); // Para llenar el select del modal
        
        return view('admin.usuarios.index', compact('usuarios', 'clinicas'));
    }

    // Guarda el nuevo usuario
    public function store(Request $request)
    {
        // 1. Validar y guardar en una variable
        $validated = $request->validate([
            'id_clinica' => 'required|exists:clinica,id_clinica',
            'nombre' => 'required|string',
            'apellido_materno' => 'required|string',
            'apellido_paterno' => 'required|string', 
            'nom_usuario' => 'required|alpha_num|min:4|max:20|unique:usuario,nom_usuario',
            'password'   => [
                'required',
                Password::min(8)     // Mínimo 8 caracteres
                    ->letters()      // Al menos una letra
                    ->mixedCase()    // Mayúsculas y minúsculas
                    ->numbers(),     // Al menos un número
            ],
            'rol' => 'required|in:dentista,asistente',
            'cedula_profesional' => 'required_if:rol,dentista|nullable|digits_between:7,10',
        ]);

        // 2. Crear un solo registro. 
    // Nota: El modelo ya tiene el cast 'hashed', así que Laravel lo encriptará.
    Usuario::create([
        'id_clinica' => $validated['id_clinica'],
        'nombre' => $validated['nombre'],
        'apellido_paterno' => $validated['apellido_paterno'],
        'apellido_materno' => $validated['apellido_materno'],
        'nom_usuario' => $validated['nom_usuario'],
        'password' => $validated['password'], // El Cast del modelo hará el Hash automáticamente
        'rol' => $validated['rol'],
        'cedula_profesional' => $request->cedula_profesional,
        'estatus' => 'activo',
    ]);
    
    return redirect()->route('usuarios.index')->with('success', 'Usuario creado con éxito.');
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }

    //Editar usuario
    public function edit($id)
{
    $usuario = Usuario::findOrFail($id);
    return response()->json($usuario); // Devolvemos JSON para que JavaScript lo use
}

public function update(Request $request, $id)
{
    $usuario = Usuario::findOrFail($id);
    
    $rules = [
        'id_clinica' => 'required',
        'nombre' => 'required|string',
        'apellido_paterno' => 'required|string',
        'nom_usuario' => 'required|unique:usuario,nom_usuario,' . $id . ',id_usuario',
        'rol' => 'required',
    ];

    // Solo validamos password si el usuario escribió algo nuevo
    if ($request->filled('password')) {
        $rules['password'] = [\Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()];
    }

    $validated = $request->validate($rules);
    
    if (!$request->filled('password')) {
        unset($validated['password']); // No actualizamos password si está vacío
    }

    $usuario->update($validated);

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }
}
