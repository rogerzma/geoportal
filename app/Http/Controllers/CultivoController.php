<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

            // Puede venir vacío si el cultivo no tiene variantes.
            'variantes' => [
                'nullable',
                'array',
            ],

            'variantes.*' => [
                'required',
                'string',
                'max:150',
                'distinct',
            ],
        ]);

        $nombre = trim($datos['nombre']);

        $nombreCientifico = !empty($datos['nombre_cientifico'])
            ? trim($datos['nombre_cientifico'])
            : null;

        $existe = Cultivo::whereRaw(
            'LOWER(nombre) = ?',
            [mb_strtolower($nombre)]
        )
        ->when(
            $nombreCientifico !== null,
            function ($query) use ($nombreCientifico) {
                $query->whereRaw(
                    'LOWER(nombre_cientifico) = ?',
                    [mb_strtolower($nombreCientifico)]
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

        // Limpiar variantes y quitar duplicados sin distinguir mayúsculas.
        $variantes = $this->normalizarVariantes(
            $datos['variantes'] ?? []
        );

        $cultivo = DB::transaction(function () use (
            $datos,
            $usuario,
            $nombre,
            $nombreCientifico,
            $variantes
        ) {
            $cultivo = Cultivo::create([
                'nombre' => $nombre,
                'nombre_cientifico' => $nombreCientifico,
                'categoria' => $datos['categoria'],
                'color' => strtoupper($datos['color']),
                'activo' => filter_var(
                    $datos['activo'],
                    FILTER_VALIDATE_BOOLEAN
                ),
                'created_by' => $usuario->id,
            ]);

            if (!empty($variantes)) {
                $cultivo->variantes()->createMany(
                    array_map(
                        fn ($nombreVariante) => [
                            'nombre' => $nombreVariante,
                        ],
                        $variantes
                    )
                );
            }

            return $cultivo;
        });

        return response()->json([
            'message' => 'Cultivo registrado correctamente.',
            'cultivo' => $cultivo->load([
                'creador:id,name',
                'variantes',
            ]),
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

        $cultivo = Cultivo::with('variantes')->find($id);

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

            /*
            * Si se envía:
            * variantes: [] → elimina todas.
            *
            * Si no se envía el campo:
            * conserva las variantes actuales.
            */
            'variantes' => [
                'sometimes',
                'array',
            ],

            'variantes.*' => [
                'required',
                'string',
                'max:150',
                'distinct',
            ],
        ]);

        $nombreObjetivo = array_key_exists('nombre', $datos)
            ? trim($datos['nombre'])
            : $cultivo->nombre;

        $nombreCientificoObjetivo =
            array_key_exists('nombre_cientifico', $datos)
                ? (
                    !empty($datos['nombre_cientifico'])
                        ? trim($datos['nombre_cientifico'])
                        : null
                )
                : $cultivo->nombre_cientifico;

        $existe = Cultivo::where('id', '!=', $cultivo->id)
            ->whereRaw(
                'LOWER(nombre) = ?',
                [mb_strtolower($nombreObjetivo)]
            )
            ->when(
                $nombreCientificoObjetivo !== null,
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

        DB::transaction(function () use (
            $request,
            $datos,
            $cultivo
        ) {
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

            if ($request->exists('activo')) {
                $cultivo->activo = $request->boolean('activo');
            }

            $cultivo->save();

            /*
            * Solo modificar variantes cuando el frontend
            * haya enviado explícitamente el campo.
            */
            if (array_key_exists('variantes', $datos)) {
                $variantes = $this->normalizarVariantes(
                    $datos['variantes']
                );

                /*
                * Elimina las actuales y crea las nuevas.
                * Un arreglo vacío deja el cultivo sin variantes.
                */
                $cultivo->variantes()->delete();

                if (!empty($variantes)) {
                    $cultivo->variantes()->createMany(
                        array_map(
                            fn ($nombreVariante) => [
                                'nombre' => $nombreVariante,
                            ],
                            $variantes
                        )
                    );
                }
            }
        });

        return response()->json([
            'message' => 'Cultivo actualizado correctamente.',
            'cultivo' => $cultivo->fresh()->load([
                'creador:id,name',
                'variantes',
            ]),
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

    private function normalizarVariantes(array $variantes): array
    {
        $resultado = [];
        $nombresRegistrados = [];

        foreach ($variantes as $variante) {
            $nombre = trim((string) $variante);

            // Ignorar entradas vacías.
            if ($nombre === '') {
                continue;
            }

            // Comparar sin distinguir mayúsculas ni minúsculas.
            $clave = mb_strtolower($nombre);

            if (in_array($clave, $nombresRegistrados, true)) {
                continue;
            }

            $nombresRegistrados[] = $clave;
            $resultado[] = $nombre;
        }

        return $resultado;
    }
}
