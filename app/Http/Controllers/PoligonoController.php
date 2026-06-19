<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poligono;
use App\Models\UnidadProduccion;
use App\Models\User;
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

        // ROOT
        if ($auth->tipo_usuario === 'root') {
            return Poligono::with(['user', 'unidadProduccion'])->get();
        }

        // ADMINISTRADOR
        if ($auth->tipo_usuario === 'administrador') {

            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where(function ($q) use ($auth, $tecnicos) {
                    $q->where('created_by', $auth->id)
                    ->orWhereIn('created_by', $tecnicos);
                })
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where(function ($q) use ($auth, $tecnicos, $jefesOperativos) {
                    $q->where('created_by', $auth->id)
                    ->orWhereIn('created_by', $tecnicos)
                    ->orWhereIn('created_by', $jefesOperativos);
                })
                ->pluck('id');

            $usuariosPermitidos = collect([$auth->id])
                ->merge($tecnicos)
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique();

            return Poligono::with(['user', 'unidadProduccion'])
                ->whereIn('user_id', $usuariosPermitidos)
                ->get();
        }

        // TÉCNICO
        if ($auth->tipo_usuario === 'tecnico') {

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->whereIn('created_by', $jefesOperativos)
                ->pluck('id');

            return Poligono::with(['user', 'unidadProduccion'])
                ->whereIn('user_id', collect([$auth->id])->merge($jefesOperativos)->merge($capturistas))
                ->get();
        }

        // JEFE OPERATIVO
        if ($auth->tipo_usuario === 'jefe_operativo') {

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where('created_by', $auth->id)
                ->pluck('id');

            return Poligono::with(['user', 'unidadProduccion'])
                ->whereIn('user_id', collect([$auth->id])->merge($capturistas))
                ->get();
        }

        // CAPTURISTA
        if ($auth->tipo_usuario === 'capturista') {
            return Poligono::with(['user', 'unidadProduccion'])
                ->where('user_id', $auth->id)
                ->get();
        }

        return response()->json([]);
    }



    /** 
     * Mostrar los polígonos para la vista inicial.
     */

    public function mapaInicial(){
        return Poligono::with(['user', 'unidadProduccion'])->get();
    }


    /**
     * Mostrar los polígonos de una unidad de producción específica.
     */
    public function porUP($up_id)
    {
        $poligonos = Poligono::with(['user'])->where('up_id', $up_id)->get();
        return response()->json($poligonos);
    }

    /**
     * Guardar un nuevo polígono.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'         => 'required|string|max:255',
                'coordenadas'    => 'required|json',
                'cultivo'        => 'required|string|max:255',
                'geom'           => 'required|string',
                'fecha_creacion' => 'required|date',
                'up_id'          => 'required|exists:unidad_produccion,id',
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

            return response()->json([
                'message' => 'Polígono guardado correctamente.',
                'refresh' => true
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar polígono: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'coordenadas'    => 'json',
            'cultivo'        => 'string|max:255',
            'geom'           => 'string',
            'fecha_creacion' => 'date',
            'up_id'          => 'exists:unidad_produccion,id',
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

    /**
     * Obtener el total de hectáreas de todos los polígonos.
     */
    public function hectareasTotales()
    {
        // Calcula el área total en hectáreas (ST_Area en metros cuadrados / 10,000)
        $total = DB::table('poligono')
            ->selectRaw('SUM(ST_Area(geom::geography) / 10000) as hectareas')
            ->value('hectareas');

        return response()->json([
            'hectareas_totales' => round($total, 2)
        ]);
    }

    // Obtener hectareas por cada cultivo
    public function hectareasPorCultivo()
    {
        $resultados = DB::table('poligono')
        ->select('cultivo', DB::raw('SUM(ST_Area(geom::geography) / 10000) as hectareas'))
        ->groupBy('cultivo')
        ->get();

        return response()->json($resultados);
    }

        /**
     * Obtener el total de hectáreas de los polígonos creados por el usuario autenticado y sus usuarios a cargo.
     */
    public function hectareasTotalesUsuario()
    {
        $auth = auth()->user();

        if (!$auth) {
            return response()->json(['hectareas_totales' => 0]);
        }

        /* ===============================
        ROOT → TODO
        =============================== */
        if ($auth->tipo_usuario === 'root') {
            $total = DB::table('poligono')
                ->selectRaw('SUM(ST_Area(geom::geography) / 10000)')
                ->value('sum');

            return response()->json([
                'hectareas_totales' => round($total ?? 0, 2)
            ]);
        }

        /* ===============================
        USUARIOS PERMITIDOS
        =============================== */
        $usuariosPermitidos = collect([$auth->id]);

        if ($auth->tipo_usuario === 'administrador') {

            $tecnicos = User::where('tipo_usuario', 'tecnico')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->whereIn('created_by', $tecnicos)
                ->orWhere('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->whereIn('created_by', $jefesOperativos)
                ->orWhereIn('created_by', $tecnicos)
                ->orWhere('created_by', $auth->id)
                ->pluck('id');

            $usuariosPermitidos = $usuariosPermitidos
                ->merge($tecnicos)
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique();
        }

        if ($auth->tipo_usuario === 'tecnico') {

            $jefesOperativos = User::where('tipo_usuario', 'jefe_operativo')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->whereIn('created_by', $jefesOperativos)
                ->pluck('id');

            $usuariosPermitidos = $usuariosPermitidos
                ->merge($jefesOperativos)
                ->merge($capturistas)
                ->unique();
        }

        if ($auth->tipo_usuario === 'jefe_operativo') {

            $capturistas = User::where('tipo_usuario', 'capturista')
                ->where('created_by', $auth->id)
                ->pluck('id');

            $usuariosPermitidos = $usuariosPermitidos
                ->merge($capturistas)
                ->unique();
        }

        /* ===============================
        SUMA REAL DE HECTÁREAS
        =============================== */
        $total = DB::table('poligono')
            ->whereIn('user_id', $usuariosPermitidos)
            ->selectRaw('SUM(ST_Area(geom::geography) / 10000)')
            ->value('sum');

        return response()->json([
            'hectareas_totales' => round($total ?? 0, 2)
        ]);
    }

    /**
     * Obtener el numero total de polígonos
     */

    public function poligonosTotales(){
        $total = DB::table('poligono')->count();
        return response()->json([
            'poligonos_totales' => $total
        ]);
    }

}