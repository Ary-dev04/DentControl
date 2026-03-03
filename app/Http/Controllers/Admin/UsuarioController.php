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

    // desactivar usuario
    public function toggleStatus($id)
    {
    $usuario = Usuario::findOrFail($id);

    //Si el usuario es admin, no se puede tocar
    if ($usuario->rol === 'admin') {
        return redirect()->back()->with('error', 'El Superadministrador no puede ser suspendido.');
    }
    
    // Lógica inversa: si es activo -> inactivo, si es inactivo -> activo
    $nuevoEstado = ($usuario->estatus == 'activo') ? 'baja' : 'activo';
    
    $usuario->update(['estatus' => $nuevoEstado]);

    $mensaje = ($nuevoEstado == 'activo') 
        ? "El usuario {$usuario->nom_usuario} ha sido reactivado." 
        : "El acceso de {$usuario->nom_usuario} ha sido suspendido.";
    
    return redirect()->back()->with('success', $mensaje);
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

    // REGLA: Si el usuario es admin, solo él puede editar sus datos 
    // y NUNCA puede cambiarse el rol a sí mismo a algo inferior.
    if ($usuario->rol === 'superadmin') {
        $request->merge(['rol' => 'superadmin']); // Forzamos que el rol siga siendo admin
        
        // Opcional: Impedir que otros editen al admin
        //if (auth()->user()->id_usuario !== $usuario->id_usuario) {
        //     return redirect()->route('usuarios.index')->with('error', 'No puedes editar al Superadministrador.');
        //}
    }
    
    $rules = [
        'id_clinica' => 'required',
        'nombre' => 'required|string',
        'apellido_paterno' => 'required|string',
        'apellido_materno' => 'required|string',
        'nom_usuario' => 'required|unique:usuario,nom_usuario,' . $id . ',id_usuario',
        'rol' => 'required|in:superadmin,dentista,asistente',
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
