<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function redirectTo()
    {
        $user = auth()->user();

        if ($user->tipo_usuario === 'administrador') {
            return '/admin';
        } elseif ($user->tipo_usuario === 'tecnico') {
            return '/tecnico';
        } elseif ($user->tipo_usuario === 'root') {
            return '/root';
        } elseif ($user->tipo_usuario === 'capturista') {
            return '/capturista';
        }

        // En caso de que no coincida con ninguno
        return '/home';
    }
}
