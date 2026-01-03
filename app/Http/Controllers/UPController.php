<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadProduccion;
use App\Models\User;

class UPController extends Controller
{
    /**
     * ============================
     * LISTAR UNIDADES DE PRODUCCIÓN
     * ============================
     */
    public function index()
    {
        $auth = auth()->user();
        if (!$auth) {
            return response()->json([]);
        }

        // ROOT → ve todas las UP
        if ($auth->tipo_usuario === 'root') {
            return UnidadProduccion::with(['capturista', 'creador'])->get();
        }

        // ADMINISTRADOR → UP de sus CAPTURISTAS (directos o vía técnicos)
        if ($auth->tipo_usuario === 'administrador') {

            // Técnicos creados por el admin
            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $auth->id)
                ->pluck('id');

            // Capturistas creados por el admin o por sus técnicos
            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($q) use ($auth, $tecnicos) {
                    $q->where('created_by', $auth->id)
                      ->orWhereIn('created_by', $tecnicos);
                })
                ->pluck('id');

            return UnidadProduccion::with(['capturista', 'creador'])
                ->whereIn('capturista_id', $capturistas)
                ->get();
        }

        // TÉCNICO → UP de sus capturistas
        if ($auth->tipo_usuario === 'tecnico') {

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where('created_by', $auth->id)
                ->pluck('id');

            return UnidadProduccion::with(['capturista', 'creador'])
                ->whereIn('capturista_id', $capturistas)
                ->get();
        }

        // CAPTURISTA → ve SUS UP aunque no las haya creado
        if ($auth->tipo_usuario === 'capturista') {
            return UnidadProduccion::with(['capturista', 'creador'])
                ->where('capturista_id', $auth->id)
                ->get();
        }

        return response()->json([]);
    }

    // Guardar nueva UP

    public function store(Request $request)
    {
        $request->validate([
            'nombre_up'    => 'required|string|max:255',
            'localidad'    => 'required|string|max:255',
            'responsable'  => 'required|string|max:255',
            'telefono'     => 'required|string|max:20',
            'capturista_id' => 'required|exists:users,id',
        ]);

        $unidad = UnidadProduccion::create([
            'nombre_up'    => $request->nombre_up,
            'localidad'    => $request->localidad,
            'responsable'  => $request->responsable,
            'telefono'     => $request->telefono,
            'capturista_id' => $request->capturista_id,
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

            return view('jefe_operativo.CrearUP', compact('capturistas'));
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

        return match ($user->tipo_usuario) {
            'root'          => view('root.ModificarUPRoot', compact('unidad')),
            'administrador' => view('admin.ModificarUPAdmin', compact('unidad')),
            'tecnico'       => view('tecnico.ModificarUPTecnico', compact('unidad')),
            'jefe_operativo' => view('jefe_operativo.ModificarUPJefeOperativo', compact('unidad')),
            'capturista'     => view('capturista.ModificarUPCapturista', compact('unidad')),
            default         => abort(403),
        };
    }

    // Mapa de Unidades de producción 
    public function mapaUP(Request $request)
    {
        $unidadProduccion = $request->up_id
            ? UnidadProduccion::find($request->up_id)
            : null;

        $user = auth()->user();

        return match ($user->tipo_usuario) {
            'root'          => view('root.MapaUPRoot', compact('unidadProduccion')),
            'administrador' => view('admin.MapaUPAdmin', compact('unidadProduccion')),
            'tecnico'       => view('tecnico.MapaUPTecnico', compact('unidadProduccion')),
            'jefe_operativo' => view('jefe_operativo.MapaUPJefeOperativo', compact('unidadProduccion')),
            'capturista'     => view('capturista.MapaUPCapturista', compact('unidadProduccion')),
            default         => abort(403),
        };
    }

    /**
     * ============================
     * ELIMINAR UP
     * ============================
     */
    public function destroy($id)
    {
        $unidad = UnidadProduccion::find($id);

        if (!$unidad) {
            return response()->json(['error' => 'Unidad no encontrada'], 404);
        }

        $unidad->delete();

        return response()->json(['message' => 'Unidad eliminada correctamente']);
    }
}
