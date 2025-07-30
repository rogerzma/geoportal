<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use Illuminate\Support\Facades\Auth;

class UPController extends Controller
{
    /**
     * Mostrar todas las unidades de producción.
     */
    public function index()
    {
        $unidades = UnidadProduccion::with('usuario')->get();
        return response()->json($unidades);
    }

    /**
     * Guardar una nueva unidad de producción.
     */
    public function store(Request $request)
        {
            $request->validate([
                'nombre_up' => 'required|string|max:255',
                'localidad' => 'required|string|max:255',
                'responsable' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'responsable_tecnico' => 'required|string|max:255',
                'user_id' => 'required|integer',
            ]);

            $unidad = UnidadProduccion::create([
                'nombre_up' => $request->nombre_up,
                'localidad' => $request->localidad,
                'responsable' => $request->responsable,
                'telefono' => $request->telefono,
                'responsable_tecnico' => $request->responsable_tecnico,
                'user_id' => $request->user_id,
            ]);

            return response()->json(['message' => 'Unidad de producción guardada correctamente.', 'unidad' => $unidad], 201);
        }

    /**
     * Mostrar una unidad de producción específica.
     */
    public function show($id)
    {
        $unidad = UnidadProduccion::with('usuario')->find($id);

        if ($unidad) {
            return response()->json($unidad);
        } else {
            return response()->json(['error' => 'Unidad de producción no encontrada.'], 404);
        }
    }

    /**
     * Actualizar una unidad de producción existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_up' => 'string|max:255',
            'localidad' => 'string|max:255',
            'responsable' => 'string|max:255',
            'telefono' => 'string|max:20',
        ]);

        $unidad = UnidadProduccion::find($id);

        if ($unidad) {
            $unidad->update([
                'nombre_up' => $request->nombre_up ?? $unidad->nombre_up,
                'localidad' => $request->localidad ?? $unidad->localidad,
                'responsable' => $request->responsable ?? $unidad->responsable,
                'telefono' => $request->telefono ?? $unidad->telefono,
                // responsable_tecnico y user_id normalmente no se actualizan aquí
            ]);
            return redirect()->route('unidades-produccion')->with('success', 'UP actualizada correctamente.');
        } else {
            return response()->json(['error' => 'Unidad de producción no encontrada.'], 404);
        }
    }

    // Funcion para retornar la vista de modificar UP
    public function edit($id)
    {
        $unidad = UnidadProduccion::findOrFail($id);
        return view('ModificarUP', compact('unidad'));
    }


    /**
     * Mostrar el mapa de unidades de producción.
     */
    public function mapaUP(Request $request)
    {
        $upId = $request->query('up_id');
        $unidadProduccion = null;
        if ($upId) {
            $unidadProduccion = \App\Models\UnidadProduccion::find($upId);
        }
        // Depuración
        // dd($upId, $unidadProduccion);
        return view('MapaUP', compact('unidadProduccion'));
    }

    /**
     * Eliminar una unidad de producción.
     */
    public function destroy($id)
    {
        $unidad = UnidadProduccion::find($id);

        if ($unidad) {
            $unidad->delete();
            return response()->json(['message' => 'Unidad de producción eliminada correctamente.']);
        } else {
            return response()->json(['error' => 'Unidad de producción no encontrada.'], 404);
        }
    }
}