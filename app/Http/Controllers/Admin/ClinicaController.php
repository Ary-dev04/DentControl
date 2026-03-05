<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Importante para manejar archivos
use Illuminate\Support\Facades\Validator;

class ClinicaController extends Controller
{
    public function index()
    {
        //$clinicas = Clinica::all();
        // Obs 4: Mostrar las más recientes al inicio
        $clinicas = Clinica::orderBy('created_at', 'desc')->get();
        return view('admin.clinicas.index', compact('clinicas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s&\'\-]+$/u',
            'rfc'           => 'required|string|uppercase|min:12|max:13|unique:clinica,rfc',
            'calle'         => 'nullable|string|max:255',
            'numero_ext'    => 'nullable|string|max:10',
            'numero_int'    => 'nullable|string|max:10',
            'colonia'       => 'nullable|string|max:255',
            'codigo_postal' => 'required|digits:5',
            'ciudad'        => 'required|string',
            'estado'        => 'required|string',
            'telefono'      => 'required|numeric|digits:10|unique:clinica,telefono',
            'logo_ruta'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ],[
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres.',
            'nombre.regex' => 'El nombre solo permite letras, números y espacios.',
            'rfc.unique' => 'Este RFC ya está registrado.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'telefono.digits' => 'El teléfono debe ser de 10 dígitos.',
            'codigo_postal.digits' => 'El código postal debe ser de 5 dígitos.',
        ]);

        if ($request->hasFile('logo_ruta')) {
            $image = $request->file('logo_ruta');
            $name = 'logo_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/logos'), $name);
            $validated['logo_ruta'] = 'images/logos/' . $name;
        }

        Clinica::create($validated);

        // Cambiado a clinicas.index para que veas tu tabla actualizada
        return redirect()->route('clinicas.index')->with('success', 'Clínica registrada correctamente.');
    }

    // Para cargar los datos en el modal de edición
    public function edit($id) 
    {
        $clinica = Clinica::findOrFail($id);
        return response()->json($clinica);
    }

    // NUEVO: Para procesar la actualización de la clínica
    public function update(Request $request, $id)
{
    $clinica = Clinica::findOrFail($id);

    // 1. Creamos la instancia del validador manualmente para controlar el fallo
    $validator = Validator::make($request->all(), [
        'nombre'        => 'required|string|max:50|regex:/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s&\'\-]+$/u',
        'rfc'           => 'required|string|min:12|max:13|uppercase|unique:clinica,rfc,' . $id . ',id_clinica',
        'calle'         => 'nullable|string|max:255',
        'numero_ext'    => 'nullable|string|max:10',
        'numero_int'    => 'nullable|string|max:10',
        'colonia'       => 'nullable|string',
        'codigo_postal' => 'required|digits:5',
        'ciudad'        => 'required|string',
        'estado'        => 'nullable|string',
        'telefono'      => 'required|numeric|digits:10|unique:clinica,telefono,' . $id . ',id_clinica',
        'logo_ruta'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ], [
        'rfc.unique'      => 'Este RFC ya pertenece a otra clínica registrada.',
        'telefono.unique' => 'Este número telefónico ya está asociado a otra clínica.',
        'nombre.max'      => 'El nombre no debe exceder los 50 caracteres.',
        'nombre.regex'    => 'El nombre solo permite letras, números y espacios.',
    ]);

    // 2. Si falla, mandamos el editing_clinic_id a la sesión
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('editing_clinic_id', $id); // Esto le dirá al JS que abra el modal en modo edición
    }

    $validated = $validator->validated();

    // 3. Manejo del Logo
    if ($request->hasFile('logo_ruta')) {
        // Borrar logo anterior si existe
        if ($clinica->logo_ruta && File::exists(public_path($clinica->logo_ruta))) {
            File::delete(public_path($clinica->logo_ruta));
        }

        $image = $request->file('logo_ruta');
        $name = 'logo_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/logos'), $name);
        $validated['logo_ruta'] = 'images/logos/' . $name;
    }

    $clinica->update($validated);

    return redirect()->route('clinicas.index')->with('success', 'Clínica actualizada correctamente.');
}

    // NUEVO: Para desactivar clínicas
    public function toggleStatus($id)
{
    $clinica = Clinica::findOrFail($id);
    
    // Si está activa la pasamos a baja, si está en baja la pasamos a activa
    $nuevoEstado = ($clinica->estatus == 'activo') ? 'baja' : 'activo';
    
    $clinica->update(['estatus' => $nuevoEstado]);

    $mensaje = ($nuevoEstado == 'activo') ? 'Clínica reactivada con éxito.' : 'Clínica dada de baja.';
    
    return redirect()->back()->with('success', $mensaje);
}
}