<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use App\Models\VarianteCultivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VarianteCultivoController extends Controller
{
    public function index($cultivoId)
    {
        $cultivo = Cultivo::findOrFail($cultivoId);

        return response()->json(
            $cultivo->variantes()
                ->orderBy('nombre')
                ->get()
        );
    }

        public function store(Request $request)
    {
        $request->validate([
            'cultivo_id' => [
                'required',
                'integer',
                'exists:cultivos,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('variantes_cultivo', 'nombre')
                    ->where(
                        fn ($query) => $query->where(
                            'cultivo_id',
                            $request->cultivo_id
                        )
                    ),
            ],
            'activo' => [
                'required',
                'boolean',
            ],
        ]);

        $variante = VarianteCultivo::create([
            'cultivo_id' => $request->cultivo_id,
            'nombre' => trim($request->nombre),
            'activo' => $request->boolean('activo'),
        ]);

        return response()->json([
            'message' => 'Variante registrada correctamente.',
            'variante' => $variante->load('cultivo'),
        ], 201);
    }

    
}
