<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = auth()->user();
        if ($user) {
            switch ($user->role) {
                case 'root':
                    return redirect()->route('root');
                case 'admin':
                    return redirect()->route('admin');
                case 'tecnico':
                    return redirect()->route('tecnico');
                case 'jefe_operativo':
                    return redirect()->route('jefe_operativo');
                case 'capturista':
                    return redirect()->route('capturista');
                default:
                    return view('home');
            }
        }
        return view('home');
    }
}
