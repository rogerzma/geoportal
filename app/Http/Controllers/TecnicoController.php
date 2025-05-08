<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tecnico;

class TecnicoController extends Controller
{
    /**
     * Mostrar todos los técnicos.
     */
    public function index()
    {
        $tecnicos = Tecnico::with('parcelas')->get(); // Obtener todos los técnicos con sus parcelas asociadas
        return response()->json($tecnicos);
    }

    /**
     * Guardar un nuevo técnico.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:tecnicos,usuario',
            'contraseña' => 'required|string|min:6'
        ]);

        // Crear el técnico
        $tecnico = Tecnico::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'contraseña' => bcrypt($request->contraseña) // Encriptar la contraseña
        ]);

        return response()->json(['message' => 'Técnico creado correctamente.', 'tecnico' => $tecnico], 201);
    }

    /**
     * Mostrar un técnico específico.
     */
    public function show($id)
    {
        $tecnico = Tecnico::with('parcelas')->find($id);

        if ($tecnico) {
            return response()->json($tecnico);
        } else {
            return response()->json(['error' => 'Técnico no encontrado.'], 404);
        }
    }

    /**
     * Actualizar un técnico existente.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'string|max:255',
            'usuario' => 'string|max:255|unique:tecnicos,usuario,' . $id,
            'contraseña' => 'string|min:6'
        ]);

        $tecnico = Tecnico::find($id);

        if ($tecnico) {
            $tecnico->update([
                'nombre' => $request->nombre ?? $tecnico->nombre,
                'usuario' => $request->usuario ?? $tecnico->usuario,
                'contraseña' => $request->contraseña ? bcrypt($request->contraseña) : $tecnico->contraseña
            ]);

            return response()->json(['message' => 'Técnico actualizado correctamente.', 'tecnico' => $tecnico]);
        } else {
            return response()->json(['error' => 'Técnico no encontrado.'], 404);
        }
    }

    /**
     * Eliminar un técnico.
     */
    public function destroy($id)
    {
        $tecnico = Tecnico::find($id);

        if ($tecnico) {
            $tecnico->delete();
            return response()->json(['message' => 'Técnico eliminado correctamente.']);
        } else {
            return response()->json(['error' => 'Técnico no encontrado.'], 404);
        }
    }
}