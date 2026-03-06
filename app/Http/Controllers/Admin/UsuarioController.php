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
        $usuarios = Usuario::with('clinica')
        // 1. Superadmin primero, 2. Los demás por fecha (más reciente arriba)
        ->orderByRaw("CASE WHEN rol = 'superadmin' THEN 0 ELSE 1 END")
        ->orderBy('created_at', 'desc')
        ->get();
        //$usuarios = Usuario::with('clinica')->get(); // Carga usuarios con su clínica
        //$clinicas = Clinica::all(); // Para llenar el select del modal
        $clinicas = Clinica::where('estatus', 'activo')->get();
        
        return view('admin.usuarios.index', compact('usuarios', 'clinicas'));
    }

    // Guarda el nuevo usuario
    public function store(Request $request)
    {
        // 1. Validar y guardar en una variable
        $validated = $request->validate([
            'id_clinica' => 'required|exists:clinica,id_clinica',
            'nombre'   => 'required|string|min:3|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'apellido_paterno' => 'required|string|min:3|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'apellido_materno' => 'required|string|min:3|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            'email'    => 'required|email|unique:usuario,email',
            'telefono' => 'required|numeric|digits:10|unique:usuario,telefono',
            'cedula_profesional' => 'required_if:rol,dentista|nullable|unique:usuario,cedula_profesional|regex:/^[0-9]{7,8}$/',
            'nom_usuario' => 'required|alpha_num|min:4|max:20|unique:usuario,nom_usuario',
            'password'   => [
                'required', 'regex:/^[a-zA-Z0-9]+$/', //prohibir caracteres especiales
                Password::min(8)     // Mínimo 8 caracteres
                    ->letters()      // Al menos una letra
                    ->mixedCase()    // Mayúsculas y minúsculas
                    ->numbers(),     // Al menos un número
            ],
            'rol' => 'required|in:dentista,asistente',
            'cedula_profesional' => 'required_if:rol,dentista|nullable|digits_between:7,8',
        ],[ 
            // <--- AQUÍ EMPIEZAN LOS MENSAJES (Justo después de la coma)
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
            'nombre.min' => 'El nombre debe tener al menos 3 letras.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.mixed_case' => 'La contraseña debe tener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.regex' => 'La contraseña no debe contener espacios ni caracteres especiales.',
            'cedula_profesional.regex' => 'La cédula debe ser de 7 a 8 números.',
            'cedula_profesional.required_if' => 'La cédula es obligatoria para dentistas.'
        ]);

        // Corrección de la limpieza de datos en el método store
$validated['nombre'] = preg_replace('/\s+/', ' ', trim($request->nombre));
$validated['apellido_paterno'] = preg_replace('/\s+/', ' ', trim($request->apellido_paterno)); 
$validated['apellido_materno'] = preg_replace('/\s+/', ' ', trim($request->apellido_materno)); 

        // 2. Crear un solo registro. 
    // Nota: El modelo ya tiene el cast 'hashed', así que Laravel lo encriptará.
    Usuario::create($validated);
    
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
        'nombre' => 'required|string|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
        'apellido_paterno' => 'required|string|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
        'apellido_materno' => 'required|string|max:50|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
        'email' => 'required|email|unique:usuario,email,' . $id . ',id_usuario',
            'telefono' => 'required|numeric|digits:10|unique:usuario,telefono,' . $id . ',id_usuario',
        'nom_usuario' => 'required|unique:usuario,nom_usuario,' . $id . ',id_usuario',
        'rol' => 'required|in:superadmin,dentista,asistente',
        'cedula_profesional' => 'required_if:rol,dentista|nullable|unique:usuario,cedula_profesional,' . $id . ',id_usuario|regex:/^[0-9]{7,8}$/',
    ];

    // Solo validamos password si el usuario escribió algo nuevo
    if ($request->filled('password')) {
        $rules['password'] = [
        'required',    
        'regex:/^[a-zA-Z0-9]+$/', Password::min(8)->letters()->mixedCase()->numbers()];
    }
    $messages = [
    'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
    'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
    'nombre.min' => 'El nombre debe tener al menos 3 letras.',
    'password.min' => 'La contraseña es demasiado corta (mínimo 8 caracteres).',
    'password.letters' => 'La contraseña debe incluir al menos una letra.',
    'password.mixed_case' => 'La contraseña debe tener mayúsculas y minúsculas.',
    'password.numbers' => 'La contraseña debe incluir al menos un número.',
    'password.regex' => 'La contraseña no debe contener espacios ni caracteres especiales.',
];

    $validated = $request->validate($rules, $messages);

    $validated['nombre'] = preg_replace('/\s+/', ' ', trim($request->nombre));
    $validated['apellido_paterno'] = preg_replace('/\s+/', ' ', trim($request->apellido_paterno));
    $validated['apellido_materno'] = preg_replace('/\s+/', ' ', trim($request->apellido_materno));
    
    if (!$request->filled('password')) {
        unset($validated['password']); // No actualizamos password si está vacío
    }


    $usuario->update($validated);

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }
}
