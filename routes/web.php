<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function(){
    Route::get('/administrador', function () {
        return view('administrador');
    });

    Route::get('/admin/mapa', function () {
        return view('MapaGOB');
    })->name('mapa-gob');

    Route::get('/admin', function(){
        return view('InicioAdmin');
    })->name('admin');

    Route::get('/admin/registro-usuarios', function(){
        return view('RegistrarUsuario');
    })->name('registrar-usuarios');

    Route::get('/admin/usuarios', function(){
        return view('AdministrarUsuarios');
    })->name('administrar-usuarios');

    Route::get('/admin/up', function(){
        return view('UnidadesProduccion');
    })->name('unidades-produccion');