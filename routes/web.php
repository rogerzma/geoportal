<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/administrador', function () {
    return view('administrador');
});

Route::get('/inicio-gob', function () {
    return view('InicioGob');
});

Route::view('/inicio-gob', 'InicioGob')->name('inicio');


Route::get('/admin-gob', function () {
    return view('AdminGob');
});

Route::get('/admin', function(){
    return view('InicioAdmin');
})->name('admin');

Route::get('/admin/registro-usuarios', function(){
    return view('RegistrarUsuario');
});

Route::get('/admin/ranchos', function(){
    return view('Ranchos');
})->name('ranchos');

Route::get('/admin/crear-rancho', function(){
    return view('CrearRancho');
})->name('crear-rancho');