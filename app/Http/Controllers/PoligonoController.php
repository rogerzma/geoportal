<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poligono;
use App\Models\UnidadProduccion;
use App\Models\User;
use App\Models\Cultivo;
use App\Models\VarianteCultivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PoligonoController extends Controller
{
    /**
     * Mostrar todos los polígonos.
     */
    public function index()
    {
        $auth = auth()->user();

        if (!$auth) {
            return response()->json([]);
        }

        /*
        * Root y administrador pueden consultar
        * todos los polígonos del sistema.
        */
        if (in_array(
            $auth->tipo_usuario,
            ['root', 'administrador'],
            true
        )) {
            return response()->json(
                Poligono::with(
                    $this->relacionesPoligono()
                )->get()
            );
        }

        /*
        * Técnico: polígonos propios, de sus jefes
        * operativos y de los capturistas asociados.
        */
        if ($auth->tipo_usuario === 'tecnico') {
            $jefesOperativos = User::where(
                    'tipo_usuario',
                    'jefe_operativo'
                )
                ->where('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->where(function ($query) use (
                    $auth,
                    $jefesOperativos
                ) {
                    $query
                        ->where(
                            'created_by',
                            $auth->id
                        )
                        ->orWhereIn(
                            'created_by',
                            $jefesOperativos
                        );
                })
                ->pluck('id');

            $usuariosPermitidos = collect([
                    $auth->id
                ])
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique()
                ->values();

            return response()->json(
                Poligono::with(
                        $this->relacionesPoligono()
                    )
                    ->whereIn(
                        'user_id',
                        $usuariosPermitidos
                    )
                    ->get()
            );
        }

        /*
        * Jefe operativo: polígonos propios y de
        * sus capturistas.
        */
        if (
            $auth->tipo_usuario ===
            'jefe_operativo'
        ) {
            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->where(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $usuariosPermitidos = collect([
                    $auth->id
                ])
                ->merge($capturistas)
                ->unique()
                ->values();

            return response()->json(
                Poligono::with(
                        $this->relacionesPoligono()
                    )
                    ->whereIn(
                        'user_id',
                        $usuariosPermitidos
                    )
                    ->get()
            );
        }

        /*
        * Capturista: únicamente sus propios polígonos.
        */
        if (
            $auth->tipo_usuario ===
            'capturista'
        ) {
            return response()->json(
                Poligono::with(
                        $this->relacionesPoligono()
                    )
                    ->where(
                        'user_id',
                        $auth->id
                    )
                    ->get()
            );
        }

        return response()->json([]);
    }

    /**
     * Mostrar los polígonos para la vista inicial.
     */
    public function mapaInicial()
    {
        return Poligono::with(
            $this->relacionesPoligono()
        )->get();
    }

    /**
     * Mostrar los polígonos de una unidad
     * de producción específica.
     */
    public function porUP($up_id)
    {
        $poligonos = Poligono::with(
                $this->relacionesPoligono()
            )
            ->where('up_id', $up_id)
            ->get();

        return response()->json($poligonos);
    }

    /**
     * Guardar un nuevo polígono.
     */
    public function store(Request $request)
    {
        /*
         * La validación queda fuera del try para que
         * Laravel regrese 422 en errores de validación.
         */
        $datosValidados = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255'
            ],

            'coordenadas' => [
                'required',
                'json'
            ],

            'cultivo_id' => [
                'required',
                'integer',
                'exists:cultivos,id'
            ],

            'variante_cultivo_id' => [
                'nullable',
                'integer',
                'exists:variantes_cultivo,id'
            ],

            'geom' => [
                'required',
                'string'
            ],

            'fecha_creacion' => [
                'required',
                'date'
            ],

            'up_id' => [
                'required',
                'exists:unidad_produccion,id'
            ],

            'user_id' => [
                'required',
                'exists:users,id'
            ]
        ]);

        try {
            /*
             * Obtener el cultivo desde la base de datos.
             * El nombre no se toma directamente del JS.
             */
            $cultivo = Cultivo::findOrFail(
                $datosValidados['cultivo_id']
            );

            /*
             * Comprobar que la variante seleccionada
             * pertenezca al cultivo seleccionado.
             */
            if (
                !empty(
                    $datosValidados[
                        'variante_cultivo_id'
                    ]
                )
            ) {
                $varianteValida =
                    VarianteCultivo::where(
                        'id',
                        $datosValidados[
                            'variante_cultivo_id'
                        ]
                    )
                    ->where(
                        'cultivo_id',
                        $cultivo->id
                    )
                    ->exists();

                if (!$varianteValida) {
                    return response()->json([
                        'message' =>
                            'La variante seleccionada no pertenece al cultivo.'
                    ], 422);
                }
            }

            $poligono = Poligono::create([
                'nombre' =>
                    $datosValidados['nombre'],

                'coordenadas' =>
                    $datosValidados['coordenadas'],

                /*
                 * Se conservan cultivo y cultivo_id
                 * durante la transición.
                 */
                'cultivo' =>
                    $cultivo->nombre,

                'cultivo_id' =>
                    $cultivo->id,

                'variante_cultivo_id' =>
                    $datosValidados[
                        'variante_cultivo_id'
                    ] ?? null,

                'geom' => DB::raw(
                    "ST_GeomFromText(" .
                    DB::getPdo()->quote(
                        $datosValidados['geom']
                    ) .
                    ", 4326)"
                ),

                'fecha_creacion' =>
                    $datosValidados[
                        'fecha_creacion'
                    ],

                'up_id' =>
                    $datosValidados['up_id'],

                'user_id' =>
                    $datosValidados['user_id']
            ]);

            $poligono->load(
                $this->relacionesPoligono()
            );

            return response()->json([
                'message' =>
                    'Polígono guardado correctamente.',

                'poligono' =>
                    $poligono,

                'refresh' =>
                    true
            ], 201);

        } catch (\Exception $e) {
            Log::error(
                'Error al guardar polígono: ' .
                $e->getMessage()
            );

            return response()->json([
                'message' =>
                    'Error interno del servidor.',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un polígono específico.
     */
    public function show($id)
    {
        $poligono = Poligono::with(
            $this->relacionesPoligono()
        )->find($id);

        if ($poligono) {
            return response()->json($poligono);
        } else {
            return response()->json([
                'error' =>
                    'Polígono no encontrado.'
            ], 404);
        }
    }

    /**
     * Actualizar un polígono existente.
     */
    public function update(
        Request $request,
        $id
    ) {
        $datosValidados = $request->validate([
            'nombre' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'coordenadas' => [
                'sometimes',
                'json'
            ],

            'cultivo_id' => [
                'sometimes',
                'integer',
                'exists:cultivos,id'
            ],

            'variante_cultivo_id' => [
                'nullable',
                'integer',
                'exists:variantes_cultivo,id'
            ],

            'geom' => [
                'sometimes',
                'string'
            ],

            'fecha_creacion' => [
                'sometimes',
                'date'
            ],

            'up_id' => [
                'sometimes',
                'exists:unidad_produccion,id'
            ],

            'user_id' => [
                'sometimes',
                'exists:users,id'
            ]
        ]);

        $poligono = Poligono::find($id);

        if (!$poligono) {
            return response()->json([
                'error' =>
                    'Polígono no encontrado.'
            ], 404);
        }

        /*
         * Si no llega cultivo_id, se conserva
         * el cultivo actual.
         */
        $cultivoId =
            $datosValidados['cultivo_id']
            ?? $poligono->cultivo_id;

        $cultivo = Cultivo::find($cultivoId);

        if (!$cultivo) {
            return response()->json([
                'message' =>
                    'El cultivo seleccionado no existe.'
            ], 422);
        }

        /*
         * Si cambia el cultivo y no se envía una
         * variante nueva, se elimina la variante
         * anterior para evitar inconsistencias.
         */
        if (
            array_key_exists(
                'variante_cultivo_id',
                $datosValidados
            )
        ) {
            $varianteId =
                $datosValidados[
                    'variante_cultivo_id'
                ];
        } elseif (
            isset($datosValidados['cultivo_id']) &&
            (int) $datosValidados['cultivo_id'] !==
            (int) $poligono->cultivo_id
        ) {
            $varianteId = null;
        } else {
            $varianteId =
                $poligono->variante_cultivo_id;
        }

        /*
         * Validar que la variante pertenezca
         * al cultivo seleccionado.
         */
        if ($varianteId) {
            $varianteValida =
                VarianteCultivo::where(
                    'id',
                    $varianteId
                )
                ->where(
                    'cultivo_id',
                    $cultivo->id
                )
                ->exists();

            if (!$varianteValida) {
                return response()->json([
                    'message' =>
                        'La variante seleccionada no pertenece al cultivo.'
                ], 422);
            }
        }

        $datosActualizacion = [
            'nombre' =>
                $datosValidados['nombre']
                ?? $poligono->nombre,

            'coordenadas' =>
                $datosValidados['coordenadas']
                ?? $poligono->coordenadas,

            /*
             * El texto cultivo se actualiza
             * desde el catálogo.
             */
            'cultivo' =>
                $cultivo->nombre,

            'cultivo_id' =>
                $cultivo->id,

            'variante_cultivo_id' =>
                $varianteId,

            'fecha_creacion' =>
                $datosValidados[
                    'fecha_creacion'
                ]
                ?? $poligono->fecha_creacion,

            'up_id' =>
                $datosValidados['up_id']
                ?? $poligono->up_id,

            'user_id' =>
                $datosValidados['user_id']
                ?? $poligono->user_id
        ];

        /*
         * La geometría solamente se modifica
         * cuando viene en la solicitud.
         */
        if (!empty($datosValidados['geom'])) {
            $datosActualizacion['geom'] =
                DB::raw(
                    "ST_GeomFromText(" .
                    DB::getPdo()->quote(
                        $datosValidados['geom']
                    ) .
                    ", 4326)"
                );
        }

        $poligono->update(
            $datosActualizacion
        );

        return response()->json([
            'message' =>
                'Polígono actualizado correctamente.',

            'poligono' =>
                $poligono
                    ->fresh()
                    ->load(
                        $this->relacionesPoligono()
                    )
        ]);
    }

    /**
     * Eliminar un polígono.
     */
    public function destroy($id)
    {
        $poligono = Poligono::find($id);

        if ($poligono) {
            $poligono->delete();

            return response()->json([
                'message' =>
                    'Polígono eliminado correctamente.'
            ]);
        } else {
            return response()->json([
                'error' =>
                    'Polígono no encontrado.'
            ], 404);
        }
    }

    /**
     * Obtener el total de hectáreas
     * de todos los polígonos.
     */
    public function hectareasTotales()
    {
        $total = DB::table('poligono')
            ->selectRaw(
                'SUM(ST_Area(geom::geography) / 10000) as hectareas'
            )
            ->value('hectareas');

        return response()->json([
            'hectareas_totales' =>
                round($total, 2)
        ]);
    }

    /**
     * Obtener hectáreas por cada cultivo.
     */
    public function hectareasPorCultivo()
    {
        $resultados = DB::table(
                'poligono as p'
            )
            ->leftJoin(
                'cultivos as c',
                'c.id',
                '=',
                'p.cultivo_id'
            )
            ->select(
                'p.cultivo_id',

                DB::raw(
                    'COALESCE(c.nombre, p.cultivo) as cultivo'
                ),

                DB::raw(
                    "COALESCE(c.color, '#000000') as color"
                ),

                DB::raw(
                    'SUM(ST_Area(p.geom::geography) / 10000) as hectareas'
                )
            )
            ->groupBy(
                'p.cultivo_id',
                'c.nombre',
                'c.color',
                'p.cultivo'
            )
            ->get();

        return response()->json($resultados);
    }

    /**
     * Obtener el total de hectáreas de los
     * polígonos creados por el usuario
     * autenticado y sus usuarios a cargo.
     */
    public function hectareasTotalesUsuario()
    {
        $auth = auth()->user();

        if (!$auth) {
            return response()->json([
                'hectareas_totales' => 0
            ]);
        }

        /* ===============================
        ROOT → TODO
        =============================== */
        if ($auth->tipo_usuario === 'root') {
            $total = DB::table('poligono')
                ->selectRaw(
                    'SUM(ST_Area(geom::geography) / 10000)'
                )
                ->value('sum');

            return response()->json([
                'hectareas_totales' =>
                    round($total ?? 0, 2)
            ]);
        }

        /* ===============================
        USUARIOS PERMITIDOS
        =============================== */
        $usuariosPermitidos = collect([
            $auth->id
        ]);

        if (
            $auth->tipo_usuario ===
            'administrador'
        ) {
            $tecnicos = User::where(
                    'tipo_usuario',
                    'tecnico'
                )
                ->where('created_by', $auth->id)
                ->pluck('id');

            $jefesOperativos = User::where(
                    'tipo_usuario',
                    'jefe_operativo'
                )
                ->whereIn(
                    'created_by',
                    $tecnicos
                )
                ->orWhere(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->whereIn(
                    'created_by',
                    $jefesOperativos
                )
                ->orWhereIn(
                    'created_by',
                    $tecnicos
                )
                ->orWhere(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $usuariosPermitidos =
                $usuariosPermitidos
                    ->merge($tecnicos)
                    ->merge($jefesOperativos)
                    ->merge($capturistas)
                    ->unique();
        }

        if ($auth->tipo_usuario === 'tecnico') {
            $jefesOperativos = User::where(
                    'tipo_usuario',
                    'jefe_operativo'
                )
                ->where(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->whereIn(
                    'created_by',
                    $jefesOperativos
                )
                ->pluck('id');

            $usuariosPermitidos =
                $usuariosPermitidos
                    ->merge($jefesOperativos)
                    ->merge($capturistas)
                    ->unique();
        }

        if (
            $auth->tipo_usuario ===
            'jefe_operativo'
        ) {
            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->where(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $usuariosPermitidos =
                $usuariosPermitidos
                    ->merge($capturistas)
                    ->unique();
        }

        /* ===============================
        SUMA REAL DE HECTÁREAS
        =============================== */
        $total = DB::table('poligono')
            ->whereIn(
                'user_id',
                $usuariosPermitidos
            )
            ->selectRaw(
                'SUM(ST_Area(geom::geography) / 10000)'
            )
            ->value('sum');

        return response()->json([
            'hectareas_totales' =>
                round($total ?? 0, 2)
        ]);
    }

    public function hectareasPorCultivoUsuario()
    {
        $auth = auth()->user();

        if (!$auth) {
            return response()->json([]);
        }

        $query = DB::table('poligono as p')
            ->leftJoin(
                'cultivos as c',
                'c.id',
                '=',
                'p.cultivo_id'
            );

        $usuariosPermitidos =
            $this->usuariosPermitidosPoligonos($auth);

        /*
        * Root y administrador reciben null, por lo que
        * no se aplica filtro y consultan todos los datos.
        */
        if ($usuariosPermitidos !== null) {
            $query->whereIn(
                'p.user_id',
                $usuariosPermitidos
            );
        }

        $resultados = $query
            ->select(
                'p.cultivo_id',

                DB::raw(
                    'COALESCE(c.nombre, p.cultivo) as cultivo'
                ),

                DB::raw(
                    "COALESCE(c.color, '#000000') as color"
                ),

                DB::raw(
                    'SUM(ST_Area(p.geom::geography) / 10000) as hectareas'
                ),

                DB::raw(
                    'COUNT(p.id) as total_poligonos'
                )
            )
            ->groupBy(
                'p.cultivo_id',
                'c.nombre',
                'c.color',
                'p.cultivo'
            )
            ->orderByRaw(
                'COALESCE(c.nombre, p.cultivo)'
            )
            ->get()
            ->map(function ($resultado) {
                $resultado->hectareas = round(
                    (float) $resultado->hectareas,
                    2
                );

                $resultado->total_poligonos =
                    (int) $resultado->total_poligonos;

                return $resultado;
            });

        return response()->json($resultados);
    }

    /**
     * Obtener el número total de polígonos.
     */
    public function poligonosTotales()
    {
        $total = DB::table(
            'poligono'
        )->count();

        return response()->json([
            'poligonos_totales' =>
                $total
        ]);
    }

    private function usuariosPermitidosPoligonos(User $auth): ?\Illuminate\Support\Collection
    {
        if (in_array(
            $auth->tipo_usuario,
            ['root', 'administrador'],
            true
        )) {
            return null;
        }

        $usuariosPermitidos = collect([
            (int) $auth->id
        ]);

        if ($auth->tipo_usuario === 'tecnico') {
            $jefesOperativos = User::where(
                    'tipo_usuario',
                    'jefe_operativo'
                )
                ->where(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->where(function ($query) use (
                    $auth,
                    $jefesOperativos
                ) {
                    $query
                        ->where(
                            'created_by',
                            $auth->id
                        )
                        ->orWhereIn(
                            'created_by',
                            $jefesOperativos
                        );
                })
                ->pluck('id');

            return $usuariosPermitidos
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();
        }

        if ($auth->tipo_usuario === 'jefe_operativo') {
            $capturistas = User::where(
                    'tipo_usuario',
                    'capturista'
                )
                ->where(
                    'created_by',
                    $auth->id
                )
                ->pluck('id');

            return $usuariosPermitidos
                ->merge($capturistas)
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();
        }

        if ($auth->tipo_usuario === 'capturista') {
            return $usuariosPermitidos;
        }

        return collect();
    }

    /**
     * Relaciones utilizadas al consultar polígonos.
     */
    private function relacionesPoligono(): array
    {
        return [
            'user',
            'unidadProduccion',
            'cultivoCatalogo',
            'varianteCultivo'
        ];
    }
}