<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use App\Models\User;
use App\Models\Cultivo;

class UPController extends Controller
{
    // Función para obtener los IDs de los usuarios que pueden crear UP según el rol del usuario autenticado
    private function creadoresPermitidosParaGestionar(User $auth)
    {
        $usuariosPermitidos = collect([$auth->id]);

        if ($auth->tipo_usuario === 'root') {
            return User::pluck('id');
        }

        if ($auth->tipo_usuario === 'administrador') {
            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where(function ($query) use ($auth, $tecnicos) {
                    $query->where('created_by', $auth->id)
                        ->orWhereIn('created_by', $tecnicos);
                })
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($query) use ($auth, $tecnicos, $jefesOperativos) {
                    $query->where('created_by', $auth->id)
                        ->orWhereIn('created_by', $tecnicos)
                        ->orWhereIn('created_by', $jefesOperativos);
                })
                ->pluck('id');

            return $usuariosPermitidos
                ->merge($tecnicos)
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique()
                ->values();
        }

        if ($auth->tipo_usuario === 'tecnico') {
            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($query) use ($auth, $jefesOperativos) {
                    $query->where('created_by', $auth->id)
                        ->orWhereIn('created_by', $jefesOperativos);
                })
                ->pluck('id');

            return $usuariosPermitidos
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique()
                ->values();
        }

        if ($auth->tipo_usuario === 'jefe_operativo') {
            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where('created_by', $auth->id)
                ->pluck('id');

            return $usuariosPermitidos
                ->merge($capturistas)
                ->unique()
                ->values();
        }

        return $usuariosPermitidos;
    }
    /*
     Listar unidades de producción
     */
    public function index()
    {
        $auth = auth()->user();

        if (!$auth) {
            return response()->json([]);
        }

        $relaciones = ['capturista', 'creador'];

        /*
        * Root y administrador visualizan todas las UP.
        */
        if (in_array($auth->tipo_usuario, ['root', 'administrador'], true)) {
            $creadoresPermitidos = $this
                ->creadoresPermitidosParaGestionar($auth)
                ->map(fn ($id) => (int) $id);

            $unidades = UnidadProduccion::with($relaciones)
                ->orderBy('nombre_up')
                ->get();

            $unidades->each(function ($unidad) use ($auth, $creadoresPermitidos) {
                $unidad->puede_gestionar =
                    $auth->tipo_usuario === 'root'
                    || $creadoresPermitidos->contains((int) $unidad->created_by);
            });

            return response()->json($unidades);
        }

        /*
        * Técnico y jefe operativo conservan el acceso
        * a las UP de su jerarquía.
        */
        if (in_array($auth->tipo_usuario, ['tecnico', 'jefe_operativo'], true)) {
            $creadoresPermitidos = $this
                ->creadoresPermitidosParaGestionar($auth);

            $unidades = UnidadProduccion::with($relaciones)
                ->whereIn('created_by', $creadoresPermitidos)
                ->orderBy('nombre_up')
                ->get();

            $unidades->each(function ($unidad) {
                $unidad->puede_gestionar = true;
            });

            return response()->json($unidades);
        }

        /*
        * Capturista: visualiza las UP que tiene asignadas.
        */
        if ($auth->tipo_usuario === 'capturista') {
            $unidades = UnidadProduccion::with($relaciones)
                ->where('capturista_id', $auth->id)
                ->orderBy('nombre_up')
                ->get();

            $unidades->each(function ($unidad) use ($auth) {
                $unidad->puede_gestionar =
                    (int) $unidad->created_by === (int) $auth->id;
            });

            return response()->json($unidades);
        }

        return response()->json([]);
    }

    // Verifica si el usuario autenticado puede gestionar la unidad de producción
    private function puedeGestionarUnidad(User $auth, UnidadProduccion $unidad): bool
    {
        if ($auth->tipo_usuario === 'root') {
            return true;
        }

        $creadoresPermitidos = $this
            ->creadoresPermitidosParaGestionar($auth)
            ->map(fn ($id) => (int) $id);

        return $creadoresPermitidos->contains((int) $unidad->created_by);
    }

    // Guardar nueva UP

    public function store(Request $request)
    {
        $request->validate([
            'nombre_up'    => 'required|string|max:255',
            'localidad'    => 'required|string|max:255',
            'responsable'  => 'required|string|max:255',
            'telefono'     => 'required|string|max:20',
            'capturista_id' => 'nullable|exists:users,id',
        ]);

        $unidad = UnidadProduccion::create([
            'nombre_up'    => $request->nombre_up,
            'localidad'    => $request->localidad,
            'responsable'  => $request->responsable,
            'telefono'     => $request->telefono,
            'capturista_id' => $request->capturista_id ?: null,
            'created_by'   => auth()->id(), // 👈 siempre el usuario autenticado
        ]);

        return response()->json([
            'message' => 'Unidad de producción creada correctamente',
            'unidad'  => $unidad
        ], 201);
    }

    // Crear UP
    public function create()
    {
        $auth = auth()->user();

        // ROOT → todos los capturistas
        if ($auth->tipo_usuario === 'root') {
            $capturistas = User::where('tipo_usuario', 'capturista')
                ->get(['id', 'name']);

            return view('root.CrearUP', compact('capturistas'));
        }

        // ADMINISTRADOR → capturistas propios, de sus técnicos, o de los jefes operativos de sus técnicos
        if ($auth->tipo_usuario === 'administrador') {
            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->whereIn('created_by', $tecnicos)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($q) use ($auth, $tecnicos, $jefesOperativos) {
                    $q->where('created_by', $auth->id)
                    ->orWhereIn('created_by', $tecnicos)
                    ->orWhereIn('created_by', $jefesOperativos);
                })
                ->get(['id', 'name']);

            return view('admin.CrearUP', compact('capturistas'));
        }

        // TÉCNICO → capturistas propios, o de sus jefes operativos
        if ($auth->tipo_usuario === 'tecnico') {
            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($q) use ($auth, $jefesOperativos) {
                    $q->where('created_by', $auth->id)
                    ->orWhereIn('created_by', $jefesOperativos);
                })
                ->get(['id', 'name']);

            return view('tecnico.CrearUP', compact('capturistas'));
        }

        // JEFE OPERATIVO → capturistas creados por él
        if ($auth->tipo_usuario === 'jefe_operativo') {
            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where('created_by', $auth->id)
                ->get(['id', 'name']);

            return view('jefe_operativo.CrearUPJefeOP', compact('capturistas'));
        }

        // CAPTURISTA → no puede crear UP para otros
        if ($auth->tipo_usuario === 'capturista') {
            return view('capturista.CrearUPCapturista');
        }

        return abort(403, 'No autorizado');
    }
    //Mostrar UP

    public function show($id)
    {
        $unidad = UnidadProduccion::with(['capturista', 'creador'])->find($id);

        if (!$unidad) {
            return response()->json(['error' => 'Unidad no encontrada'], 404);
        }

        return response()->json($unidad);
    }

    //Actualizar UP

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_up'   => 'nullable|string|max:255',
            'localidad'   => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'telefono'    => 'nullable|string|max:20',
        ]);

        $unidad = UnidadProduccion::findOrFail($id);

        $user = auth()->user();
        if (!$this->puedeGestionarUnidad($user, $unidad)) {
            return response()->json([
                'message' => 'No tiene permiso para modificar esta unidad de producción.'
            ], 403);
        }

        $unidad->update($request->only([
            'nombre_up',
            'localidad',
            'responsable',
            'telefono'
        ]));

        $user = auth()->user();

        // 🔁 REDIRECCIÓN SEGÚN ROL
        return match ($user->tipo_usuario) {
            'root'          => redirect()->route('unidades-produccion-root')
                                ->with('success', 'UP actualizada correctamente'),
            'administrador' => redirect()->route('unidades-produccion-admin')
                                ->with('success', 'UP actualizada correctamente'),
            'tecnico'       => redirect()->route('tecnico-up')
                                ->with('success', 'UP actualizada correctamente'),
            'jefe_operativo' => redirect()->route('jefe-operativo-up')
                                ->with('success', 'UP actualizada correctamente'),
            'capturista'     => redirect()->route('capturista-up')
                                ->with('success', 'UP actualizada correctamente'),
            default         => redirect()->route('inicio'),
        };
    }

    //Editar UP
    public function edit($id)
    {
        $unidad = UnidadProduccion::findOrFail($id);
        $user = auth()->user();

        //  Verifica si el usuario autenticado puede gestionar la unidad de producción
        if(!$this->puedeGestionarUnidad($user, $unidad)) {
            abort(403, 'No autorizado para modificar esta unidad de producción');
        }

        // Redirige a la vista de superusuario
        if ($user->tipo_usuario === 'root') {
            $capturistas = User::where('tipo_usuario', 'capturista')
                ->get(['id', 'name']);

            return view('root.ModificarUPRoot', compact('unidad', 'capturistas'));
        }

        // Redirige a la vista de administrador
        if ($user->tipo_usuario === 'administrador') {

            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $user->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($q) use ($user, $tecnicos) {
                    $q->where('created_by', $user->id)
                    ->orWhereIn('created_by', $tecnicos);
                })
                ->get(['id', 'name']);

            return view('admin.ModificarUPAdmin', compact('unidad', 'capturistas'));
        }

        // Redirige a la vista de técnico
        if ($user->tipo_usuario === 'tecnico') {
            return view('tecnico.ModificarUPTecnico', compact('unidad'));
        }

        // Redirige a la vista de jefe operativo
        if ($user->tipo_usuario === 'jefe_operativo') {
            return view('jefe_operativo.ModificarUPJefeOperativo', compact('unidad'));
        }

        // Redirige a la vista de capturista
        if ($user->tipo_usuario === 'capturista') {
            return view('capturista.ModificarUPCapturista', compact('unidad'));
        }

        abort(403);
    }


    // Mapa de Unidades de producción 
    public function mapaUP(Request $request)
    {
        $unidadProduccion = $request->up_id
            ? UnidadProduccion::find($request->up_id)
            : null;

        $cultivos = Cultivo::with([
            'variantes' => function ($query) {
                $query->orderBy('nombre');
            }
        ])
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();

        $user = auth()->user();

        return match ($user->tipo_usuario) {
            'root'          => view('root.MapaUPRoot', compact('unidadProduccion', 'cultivos')),
            'administrador' => view('admin.MapaUPAdmin', compact('unidadProduccion', 'cultivos')),
            'tecnico'       => view('tecnico.MapaUPTecnico', compact('unidadProduccion', 'cultivos')),
            'jefe_operativo' => view('jefe_operativo.MapaUPJefeOperativo', compact('unidadProduccion', 'cultivos')),
            'capturista'     => view('capturista.MapaUPCapturista', compact('unidadProduccion', 'cultivos')),
            default         => abort(403),
        };
    }

    // Eliminar UP 
    public function destroy($id)
    {
        $unidad = UnidadProduccion::find($id);

        if (!$unidad) {
            return response()->json([
                'error' => 'Unidad no encontrada'
            ], 404);
        }

        $auth = auth()->user();

        if (!$auth || !$this->puedeGestionarUnidad($auth, $unidad)) {
            return response()->json([
                'message' => 'No tiene permiso para eliminar esta unidad de producción.'
            ], 403);
        }

        $unidad->delete();

        return response()->json([
            'message' => 'Unidad eliminada correctamente'
        ]);
    }
}
