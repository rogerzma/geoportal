<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poligono;
use App\Models\UnidadProduccion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PoligonoController extends Controller
{
    /**
     * Mostrar todos los polígonos.
     */
    public function index()
    {
        $poligonos = Poligono::with(['user'])->get();
        return response()->json($poligonos);
    }

    /**
     * Guardar un nuevo polígono.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'coordenadas'    => 'required|string',
            'cultivo'        => 'required|string|max:255',
            'geom'           => 'required|string',
            'fecha_creacion' => 'required|date',
            'up_id'          => 'required|exists:unidad_produccions,id',
            'user_id'        => 'required|exists:users,id'
        ]);

        $poligono = Poligono::create([
            'nombre'         => $request->nombre,
            'coordenadas'    => $request->coordenadas,
            'cultivo'        => $request->cultivo,
            'geom'           => DB::raw("ST_GeomFromText('{$request->geom}', 4326)"),
            'fecha_creacion' => $request->fecha_creacion,
            'up_id'          => $request->up_id,
            'user_id'        => $request->user_id
        ]);

        return response()->json(['message' => 'Polígono guardado correctamente.', 'poligono' => $poligono], 201);
    }

    /**
     * Mostrar un polígono específico.
     */
    public function show($id)
    {
        $poligono = Poligono::with(['user'])->find($id);

        if ($poligono) {
            return response()->json($poligono);
        } else {
            return response()->json(['error' => 'Polígono no encontrado.'], 404);
        }
    }

    /**
     * Actualizar un polígono existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'         => 'string|max:255',
            'coordenadas'    => 'string',
            'cultivo'        => 'string|max:255',
            'geom'           => 'string',
            'fecha_creacion' => 'date',
            'up_id'          => 'exists:unidad_produccions,id',
            'user_id'        => 'exists:users,id'
        ]);

        $poligono = Poligono::find($id);

        if ($poligono) {
            $poligono->update([
                'nombre'         => $request->nombre ?? $poligono->nombre,
                'coordenadas'    => $request->coordenadas ?? $poligono->coordenadas,
                'cultivo'        => $request->cultivo ?? $poligono->cultivo,
                'geom'           => $request->geom ? DB::raw("ST_GeomFromText('{$request->geom}', 4326)") : $poligono->geom,
                'fecha_creacion' => $request->fecha_creacion ?? $poligono->fecha_creacion,
                'up_id'          => $request->up_id ?? $poligono->up_id,
                'user_id'        => $request->user_id ?? $poligono->user_id
            ]);

            return response()->json(['message' => 'Polígono actualizado correctamente.']);
        } else {
            return response()->json(['error' => 'Polígono no encontrado.'], 404);
        }
    }

    /**
     * Eliminar un polígono.
     */
    public function destroy($id)
    {
        $poligono = Poligono::find($id);

        if ($poligono) {
            $poligono->delete();
            return response()->json(['message' => 'Polígono eliminado correctamente.']);
        } else {
            return response()->json(['error' => 'Polígono no encontrado.'], 404);
        }
    }
}