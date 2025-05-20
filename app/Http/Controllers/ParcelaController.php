<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcela;
use App\Models\Tecnico;
use Illuminate\Support\Facades\DB;

class ParcelaController extends Controller
{
    /**
     * Mostrar todas las parcelas.
     */
    public function index()
    {
        $parcelas = Parcela::with('tecnico')->get(); // Obtener todas las parcelas con su técnico asociado
        return response()->json($parcelas);
    }

    /**
     * Guardar una nueva parcela.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'cultivo' => 'required|string|max:255',
            'coordenadas' => 'required|string',
            'geom' => 'required|string',
            'nombre_productor' => 'required|string|max:255',
            'tecnico_id' => 'required|exists:tecnicos,id'
        ]);

        // Crear la parcela
        $parcela = new Parcela([
            'cultivo' => $request->cultivo,
            'coordenadas' => $request->coordenadas,
            'geom' => DB::raw("ST_GeomFromText('{$request->geom}', 4326)"),
            'nombre_productor' => $request->nombre_productor
        ]);

        // Asociar la parcela al técnico
        $tecnico = Tecnico::find($request->tecnico_id);
        $tecnico->parcelas()->save($parcela);

        return response()->json(['message' => 'Parcela guardada correctamente.'], 201);
    }

    /**
     * Mostrar una parcela específica.
     */
    public function show($id)
    {
        $parcela = Parcela::with('tecnico')->find($id);

        if ($parcela) {
            return response()->json($parcela);
        } else {
            return response()->json(['error' => 'Parcela no encontrada.'], 404);
        }
    }

    /**
     * Actualizar una parcela existente.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'cultivo' => 'string|max:255',
            'coordenadas' => 'string',
            'geom' => 'string',
            'nombre_productor' => 'string|max:255',
            'tecnico_id' => 'exists:tecnicos,id'
        ]);

        $parcela = Parcela::find($id);

        if ($parcela) {
            $parcela->update([
                'cultivo' => $request->cultivo ?? $parcela->cultivo,
                'coordenadas' => $request->coordenadas ?? $parcela->coordenadas,
                'geom' => $request->geom ? DB::raw("ST_GeomFromText('{$request->geom}', 4326)") : $parcela->geom,
                'nombre_productor' => $request->nombre_productor ?? $parcela->nombre_productor,
                'tecnico_id' => $request->tecnico_id ?? $parcela->tecnico_id
            ]);

            return response()->json(['message' => 'Parcela actualizada correctamente.']);
        } else {
            return response()->json(['error' => 'Parcela no encontrada.'], 404);
        }
    }

    /**
     * Eliminar una parcela.
     */
    public function destroy($id)
    {
        $parcela = Parcela::find($id);

        if ($parcela) {
            $parcela->delete();
            return response()->json(['message' => 'Parcela eliminada correctamente.']);
        } else {
            return response()->json(['error' => 'Parcela no encontrada.'], 404);
        }
    }
}