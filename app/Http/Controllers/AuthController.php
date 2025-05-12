<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tecnico;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar las credenciales
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'contraseña' => 'required|string',
        ]);

        // Buscar al técnico por el campo 'usuario'
        $tecnico = Tecnico::where('usuario', $credentials['usuario'])->first();

        // Verificar si el técnico existe y la contraseña es correcta
        if ($tecnico && Hash::check($credentials['contraseña'], $tecnico->contraseña)) {
            // Generar una sesión o token si es necesario
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'tecnico' => $tecnico, // Puedes devolver información del técnico si es necesario
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas',
        ], 401);
    }
}