<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

class CultivoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json([], 401);
        }

        $cultivos = Cultivo::with([
            'creador:id,name'
        ])
        ->orderBy('nombre')
        ->get();

        return response()->json($cultivos);
    }

    public function create()
    {
        $usuario = auth()->user();

        $this->autorizarGestion($usuario);

        return match ($usuario->tipo_usuario) {
            'root' => view('root.CrearCultivo'),
            'administrador' => view('admin.CrearCultivo'),
            default => abort(403, 'No autorizado'),
        };
    }

    public function store(Request $request)
    {
        $usuario = auth()->user();

        $this->autorizarGestion($usuario);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
            'nombre_cientifico' => [
                'nullable',
                'string',
                'max:150',
            ],
            'categoria' => [
                'required',
                'string',
                'max:50',
                Rule::in([
                    'Cereal',
                    'Leguminosa',
                    'Hortaliza',
                    'Frutal',
                    'Oleaginosa',
                    'Tuberculo',
                    'Forrajero',
                    'Forestal',
                    'Industrial',
                    'Otra',
                ]),
            ],
            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
        ]);

        $existe = Cultivo::whereRaw(
            'LOWER(nombre) = ?',
            [mb_strtolower(trim($datos['nombre']))]
        )
        ->when(
            !empty($datos['nombre_cientifico']),
            function ($query) use ($datos) {
                $query->whereRaw(
                    'LOWER(nombre_cientifico) = ?',
                    [mb_strtolower(trim($datos['nombre_cientifico']))]
                );
            },
            function ($query) {
                $query->whereNull('nombre_cientifico');
            }
        )
        ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya existe un cultivo con ese nombre y nombre científico.',
            ], 422);
        }

        $cultivo = Cultivo::create([
            'nombre' => trim($datos['nombre']),
            'nombre_cientifico' => !empty($datos['nombre_cientifico'])
                ? trim($datos['nombre_cientifico'])
                : null,
            'categoria' => $datos['categoria'],
            'color' => strtoupper($datos['color']),
            'activo' => $request->boolean('activo'),
            'created_by' => $usuario->id,
        ]);

        return response()->json([
            'message' => 'Cultivo registrado correctamente.',
            'cultivo' => $cultivo->load('creador:id,name'),
        ], 201);
    }

    public function show($id)
    {
        $cultivo = Cultivo::with([
            'creador:id,name',
            'variantes',
        ])->find($id);

        if (!$cultivo) {
            return response()->json([
                'message' => 'Cultivo no encontrado.',
            ], 404);
        }

        return response()->json($cultivo);
    }

    public function edit($id)
    {
        $usuario = auth()->user();

        $this->autorizarGestion($usuario);

        $cultivo = Cultivo::with('variantes')->findOrFail($id);

        return match ($usuario->tipo_usuario) {
            'root' => view(
                'root.ModificarCultivo',
                compact('cultivo')
            ),
            'administrador' => view(
                'admin.ModificarCultivo',
                compact('cultivo')
            ),
            default => abort(403, 'No autorizado'),
        };
    }

    public function update(Request $request, $id)
    {
        $usuario = auth()->user();

        $this->autorizarGestion($usuario);

        $cultivo = Cultivo::find($id);

        if (!$cultivo) {
            return response()->json([
                'message' => 'Cultivo no encontrado.',
            ], 404);
        }

        $datos = $request->validate([
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'nombre_cientifico' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],
            'categoria' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::in([
                    'Cereal',
                    'Leguminosa',
                    'Hortaliza',
                    'Frutal',
                    'Oleaginosa',
                    'Tuberculo',
                    'Forrajero',
                    'Forestal',
                    'Industrial',
                    'Otra',
                ]),
            ],
            'color' => [
                'sometimes',
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'activo' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $nombreObjetivo = array_key_exists('nombre', $datos)
            ? trim($datos['nombre'])
            : $cultivo->nombre;

        $nombreCientificoObjetivo = array_key_exists('nombre_cientifico', $datos)
            ? (!empty($datos['nombre_cientifico'])
                ? trim($datos['nombre_cientifico'])
                : null)
            : $cultivo->nombre_cientifico;

        $existe = Cultivo::where('id', '!=', $cultivo->id)
            ->whereRaw(
                'LOWER(nombre) = ?',
                [mb_strtolower($nombreObjetivo)]
            )
            ->when(
                !empty($nombreCientificoObjetivo),
                function ($query) use ($nombreCientificoObjetivo) {
                    $query->whereRaw(
                        'LOWER(nombre_cientifico) = ?',
                        [mb_strtolower($nombreCientificoObjetivo)]
                    );
                },
                function ($query) {
                    $query->whereNull('nombre_cientifico');
                }
            )
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya existe otro cultivo con ese nombre y nombre científico.',
            ], 422);
        }

        if (array_key_exists('nombre', $datos)) {
            $cultivo->nombre = trim($datos['nombre']);
        }

        if (array_key_exists('nombre_cientifico', $datos)) {
            $cultivo->nombre_cientifico =
                !empty($datos['nombre_cientifico'])
                    ? trim($datos['nombre_cientifico'])
                    : null;
        }

        if (array_key_exists('categoria', $datos)) {
            $cultivo->categoria = $datos['categoria'];
        }

        if (array_key_exists('color', $datos)) {
            $cultivo->color = strtoupper($datos['color']);
        }

        if ($request->has('activo')) {
            $cultivo->activo = $request->boolean('activo');
        }

        $cultivo->save();

        return response()->json([
            'message' => 'Cultivo actualizado correctamente.',
            'cultivo' => $cultivo->load('creador:id,name'),
        ]);
    }

    public function destroy($id)
    {
        $usuario = auth()->user();

        $this->autorizarGestion($usuario);

        $cultivo = Cultivo::find($id);

        if (!$cultivo) {
            return response()->json([
                'message' => 'Cultivo no encontrado.',
            ], 404);
        }

        $cultivo->activo = false;
        $cultivo->save();

        return response()->json([
            'message' => 'Cultivo desactivado correctamente.',
        ]);
    }

    private function autorizarGestion($usuario): void
    {
        if (
            !$usuario ||
            !in_array(
                $usuario->tipo_usuario,
                ['root', 'administrador'],
                true
            )
        ) {
            abort(403, 'No autorizado para gestionar cultivos.');
        }
    }
}
