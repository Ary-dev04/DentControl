<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatalogoServicio;
use App\Models\CatalogoTratamiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CatalogoController extends Controller
{
    /**
     * Muestra la lista de servicios y tratamientos de la clínica.
     */
    public function index()
    {
        $id_clinica = Auth::user()->id_clinica;

        // Obtenemos solo los que están activos para la tabla principal
        $servicios = CatalogoServicio::where('id_clinica', $id_clinica)->get();

        $tratamientos = CatalogoTratamiento::where('id_clinica', $id_clinica)->get();

        return view('dentista.catalogos.index', compact('servicios', 'tratamientos'));
    }

    /**
     * Guarda un nuevo servicio en el catálogo.
     */
    public function storeServicio(Request $request)
    {
        $id_clinica = Auth::user()->id_clinica;

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                // Regla para evitar duplicados en la misma clínica y estatus activo
                Rule::unique('catalogo_servicios')->where(function ($query) use ($id_clinica) {
                    return $query->where('id_clinica', $id_clinica)
                                 ->where('estatus', 'activo');
                })
            ],
            'duracion' => 'required|integer|min:1|max:480',
            'precio_sugerido' => 'required|numeric|min:1.00|max:999999.99',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'nombre.unique' => 'Ya tienes un servicio registrado con este nombre.',
            'precio_sugerido.numeric' => 'El precio debe ser un número válido.',
            'precio_sugerido.min' => 'El precio no puede ser menor a 1.00.',
            'duracion.min' => 'La duración debe ser al menos 1 minuto.',
            'precio_sugerido.max' => 'El precio ingresado es demasiado alto.',
        ]);

        CatalogoServicio::create([
            'id_clinica' => $id_clinica,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion' => $request->duracion,
            'precio_sugerido' => $request->precio_sugerido,
            'estatus' => 'activo'
        ]);

        //return redirect()->back()->with('success', 'Servicio agregado correctamente.');
        return redirect()->route('catalogos.index')->with('success', 'Servicio agregado correctamente');
    }

    /**
     * Guarda un nuevo tratamiento en el catálogo.
     */
    public function storeTratamiento(Request $request)
    {
        $id_clinica = Auth::user()->id_clinica;

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                // Regla para evitar duplicados en la misma clínica y estatus activo
                Rule::unique('catalogo_tratamientos')->where(function ($query) use ($id_clinica) {
                    return $query->where('id_clinica', $id_clinica)
                                 ->where('estatus', 'activo');
                })
            ],
            'duracion_sugerido_sesion' => 'required|integer|min:1|max:480',
            'descripcion' => 'nullable|string|max:255',
            'precio_sugerido' => 'required|numeric|min:1.00|max:999999.99',
        ], [
            'nombre.unique' => 'Ya tienes un tratamiento registrado con este nombre.',
            'nombre.required' => 'El nombre del tratamiento es obligatorio.',
            'precio_sugerido.numeric' => 'El precio debe ser un número válido.',
            'precio_sugerido.min' => 'El precio no puede ser menor a 1.00.',
            'duracion.min' => 'La duración debe ser al menos 1 minuto.',
            'precio_sugerido.max' => 'El precio ingresado es demasiado alto.',
        ]);

        CatalogoTratamiento::create([
            'id_clinica' => $id_clinica,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'duracion_sugerido_sesion' => $request->duracion_sugerido_sesion,
            'precio_sugerido' => $request->precio_sugerido,
            'estatus' => 'activo'
        ]);

        //return redirect()->back()->with('success', 'Tratamiento agregado correctamente.');
        return redirect()->route('catalogos.index')->with('success', 'Tratamiento agregado correctamente');
    }

    /**
     * "Dar de baja" (Cambio de estatus)
     */
    public function toggleServicio($id)
{
    $servicio = CatalogoServicio::findOrFail($id);
    $nuevoEstado = ($servicio->estatus === 'activo') ? 'baja' : 'activo';
    
    $servicio->update(['estatus' => $nuevoEstado]);

    return redirect()->back()->with('success', "Servicio marcado como $nuevoEstado.");
}

public function toggleTratamiento($id)
{
    $tratamiento = CatalogoTratamiento::findOrFail($id);
    $nuevoEstado = ($tratamiento->estatus === 'activo') ? 'baja' : 'activo';

    $tratamiento->update(['estatus' => $nuevoEstado]);

    return redirect()->back()->with('success', "Tratamiento marcado como $nuevoEstado.");
}

    /**
 * Actualiza un servicio existente.
 */
public function updateServicio(Request $request, $id)
{
    $id_clinica = Auth::user()->id_clinica;
    $request->validate([
        'nombre' => [
            'required', 'string', 'max:100',
            Rule::unique('catalogo_servicios')->where(fn ($q) => 
                $q->where('id_clinica', $id_clinica)->where('estatus', 'activo')
            )->ignore($id, 'id_cat_servicio')
        ],
        'duracion' => 'required|integer|min:1',
        'precio_sugerido' => 'required|numeric|min:1.00|max:999999.99',
    ], $this->mensajesError());

    CatalogoServicio::findOrFail($id)->update($request->all());
    return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
}

/**
 * Actualiza un tratamiento existente.
 */
public function updateTratamiento(Request $request, $id)
{
    $id_clinica = Auth::user()->id_clinica;
    $request->validate([
        'nombre' => [
            'required', 'string', 'max:100',
            Rule::unique('catalogo_tratamientos')->where(fn ($q) => 
                $q->where('id_clinica', $id_clinica)->where('estatus', 'activo')
            )->ignore($id, 'id_cat_tratamientos')
        ],
        'duracion_sugerido_sesion' => 'required|integer|min:1',
        'precio_sugerido' => 'required|numeric|min:1.00|max:999999.99',
    ], $this->mensajesError());

    CatalogoTratamiento::findOrFail($id)->update($request->all());
    return redirect()->back()->with('success', 'Tratamiento actualizado correctamente.');
}

private function mensajesError() {
    return [
        'nombre.unique' => 'Este nombre ya está en uso en otro registro activo.',
        'nombre.required' => 'El nombre es obligatorio.',
        'precio_sugerido.min' => 'El precio debe ser al menos $1.',
        'duracion.min' => 'La duración debe ser de al menos 1 minuto.',
        'precio_sugerido.max' => 'El precio ingresado es demasiado alto.',
    ];
}
}