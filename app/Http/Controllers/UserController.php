<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Obtener todos los usuarios
    public function index()
    {
        $auth = auth()->user();

        // Si el autenticado es root, mostrar todos
        if ($auth && $auth->tipo_usuario === 'root') {
            return User::all();
        }

        // Si no, mostrar solo los que creó ese usuario (o ninguno si no autenticado)
        return User::where('created_by', $auth ? $auth->id : 0)->get();
    }

    // Obtener un usuario por ID
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user);
        }
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    // Crear un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string|max:255',
            'tipo_usuario' => 'required|string|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'tipo_usuario' => $request->tipo_usuario,
            'created_by' => auth()->id(), // Se guarda el usuario que lo creó
        ]);

        return response()->json(['message' => 'Usuario creado correctamente', 'user' => $user]);
    }

    // Actualizar un usuario existente
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->withErrors('Usuario no encontrado');
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'telefono' => 'nullable|string|max:255',
            'tipo_usuario' => 'string|max:255'
        ]);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->telefono = $request->telefono ?? $user->telefono;
        $user->tipo_usuario = $request->tipo_usuario ?? $user->tipo_usuario;
        $user->save();

        $authUser = auth()->user();

        // 🔁 Redirección según rol
        return match ($authUser->tipo_usuario) {
            'root'            => redirect()->route('administrar-usuarios-root')
                                    ->with('success', 'Usuario actualizado correctamente'),
            'administrador'   => redirect()->route('administrar-usuarios-admin')
                                    ->with('success', 'Usuario actualizado correctamente'),
            'tecnico'         => redirect()->route('usuarios-tecnico')
                                    ->with('success', 'Usuario actualizado correctamente'),
            'jefe_operativo'  => redirect()->route('usuarios-jefe_operativo')
                                    ->with('success', 'Usuario actualizado correctamente'),
            default           => redirect()->route('inicio')
                                    ->with('success', 'Usuario actualizado correctamente'),
        };
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $authUser = auth()->user();

        // Si el usuario autenticado es root, siempre muestra la vista de root
        if ($authUser && $authUser->tipo_usuario === 'root') {
            return view('root.ModificarUsuarioRoot', compact('user'));
        }
        // Si el usuario autenticado es admin, muestra la vista de admin
        elseif ($authUser && $authUser->tipo_usuario === 'administrador') {
            return view('admin.ModificarUsuariosAdmin', compact('user'));
        }
        // Si el usuario autenticado es tecnico, muestra la vista de tecnico
        elseif ($authUser && $authUser->tipo_usuario === 'tecnico') {
            return view('tecnico.ModificarUsuarioTecnico', compact('user'));
        }
        // Vista genérica o error
        else {
            return view('ModificarUsuarios', compact('user'));
        }
    }
}