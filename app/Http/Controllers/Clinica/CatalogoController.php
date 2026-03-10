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
        $servicios = CatalogoServicio::where('id_clinica', $id_clinica)
            ->where('estatus', 'activo')
            ->get();

        $tratamientos = CatalogoTratamiento::where('id_clinica', $id_clinica)
            ->where('estatus', 'activo')
            ->get();

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
            'precio_sugerido' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'nombre.unique' => 'Ya tienes un servicio registrado con este nombre.',
            'precio_sugerido.numeric' => 'El precio debe ser un número válido.',
            'precio_sugerido.min' => 'El precio no puede ser menor a 0.01.',
            'duracion.min' => 'La duración debe ser al menos 1 minuto.',
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
            'precio_sugerido' => 'required|numeric|min:0',
        ], [
            'nombre.unique' => 'Ya tienes un tratamiento registrado con este nombre.',
            'nombre.required' => 'El nombre del tratamiento es obligatorio.',
            'precio_sugerido.numeric' => 'El precio debe ser un número válido.',
            'precio_sugerido.min' => 'El precio no puede ser menor a 0.01.',
            'duracion.min' => 'La duración debe ser al menos 1 minuto.',
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
    public function destroyServicio($id)
    {
        $servicio = CatalogoServicio::findOrFail($id);
        $servicio->update(['estatus' => 'baja']);
        return redirect()->back()->with('success', 'Servicio dado de baja.');
    }

    public function destroyTratamiento($id)
    {
        $tratamiento = CatalogoTratamiento::findOrFail($id);
        $tratamiento->update(['estatus' => 'baja']);
        return redirect()->back()->with('success', 'Tratamiento dado de baja.');
    }
}