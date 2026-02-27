<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    //
    // Muestra el formulario de creación
    public function create()
    {
        return view('admin.clinicas.create');
    }

    // Guarda la clínica en la BD
    public function store(Request $request)
    {
        $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'rfc'    => 'required|string|min:12|max:13|unique:clinica,rfc',
        'telefono' => 'required|digits:10',
        'codigo_postal' => 'required|digits:5',
        'ciudad'   => 'required|string',
        'estado'   => 'required|string',
        'logo_ruta'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación de imagen
        ]);

        // Procesar el Logo si se subió uno
        if ($request->hasFile('logo_ruta')) {
        $image = $request->file('logo_ruta');
        // Nombre único: logo_clinica_123456789.png
        $name = 'logo_' . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('/images/logos');
        $image->move($destinationPath, $name);
        
        // Guardamos la ruta relativa en el array validado
        $validated['logo_ruta'] = 'images/logos/' . $name;
        }

        Clinica::create($validated); // El modelo encripta el RFC por nosotros

        return redirect()->route('dashboard')->with('success', 'Clínica registrada correctamente.');
    }
}
