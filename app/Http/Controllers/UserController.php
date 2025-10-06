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
        return User::all();
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
            'tipo_usuario' => $request->tipo_usuario
        ]);

        // Redirigir según el rol
        if ($user->tipo_usuario === 'administrador') {
            return redirect()->route('admin');
        } elseif ($user->tipo_usuario === 'tecnico') {
            return view('UsuarioTecnico');
        } elseif ($user->tipo_usuario === 'productor') {
            return redirect()->route('productor');
        }

        // Si no coincide con ningún rol
        abort(403, 'No tienes permisos para acceder aquí.');
    }

    // Actualizar un usuario existente
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
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

        return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $user]);
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
        return view('admin.ModificarUsuarioAdmin', compact('user'));
    }
}