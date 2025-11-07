<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UPController;
use App\Http\Controllers\UserController;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [UserController::class, 'show'])->name('usuarios.show');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
});

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'role:root'])->group(function(){
    
    Route::get('/root', function(){
        return view('root/InicioRoot');
    })->name('root');

    Route::get('/root/mapa', function () {
        return view('root/MapaRoot');
    })->name('mapa-root');

    Route::get('/root/registro-usuarios', function(){
        return view('root/RegistrarUsuario');
    })->name('registrar-usuarios-root');

    Route::get('/root/usuarios', function(){
        return view('root/AdministrarUsuariosRoot');
    })->name('administrar-usuarios-root');

    Route::get('/root/up', function(){
        return view('root/UnidadesProduccion');
    })->name('unidades-produccion-root');

    Route::get('/root/crear-up', [UPController::class, 'create'])->name('crear-up-root');

    Route::get('/root/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos-root');
    Route::get('/root/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up-root');
    Route::put('/root/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.root');
    Route::get('/root/modificar-usuario/{id}', [UserController::class, 'edit'])->name('root.modificar-usuario');
    Route::put('/root/usuarios/{id}', [UserController::class, 'update'])->name('actualizar.usuario.root');
});

Route::middleware(['auth', 'role:administrador'])->group(function(){
    
    Route::get('/admin', function(){
        return view('admin/InicioAdmin');
    })->name('admin');

    Route::get('/admin/mapa', function () {
        return view('admin/MapaAdmin');
    })->name('mapa-admin');

    Route::get('/admin/registro-usuarios', function(){
        return view('admin/RegistrarUsuario');
    })->name('registrar-usuarios-admin');

    Route::get('/admin/usuarios', function(){
        return view('admin/AdministrarUsuarios');
    })->name('administrar-usuarios-admin');

    Route::get('/admin/up', function(){
        return view('admin/UnidadesProduccion');
    })->name('unidades-produccion-admin');

    Route::get('/admin/crear-up', function(){
        return view('admin/CrearUP');
    })->name('crear-up-admin');

    Route::get('/admin/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos-admin');
    Route::get('/admin/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::put('/admin/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.admin');
    Route::get('/admin/modificar-usuario/{id}', [UserController::class, 'edit'])->name('admin.modificar-usuario');
});

// Rutas para usuario técnico
Route::middleware(['auth', 'role:tecnico'])->group(function(){
    Route::get('/tecnico', function(){
        return view('tecnico/InicioTecnico');
    })->name('tecnico');

    Route::get('/tecnico/mapa', function () {
        return view('tecnico/MapaTecnico');
    })->name('mapa-tecnico');

    Route::get('/tecnico/up', function(){
        return view('tecnico/TecnicoUP');
    })->name('tecnico-up');

    Route::get('/tecnico/registro-usuarios', function(){
        return view('tecnico/RegistrarUsuarioTecnico');
    })->name('registrar-usuarios-tecnico');

    Route::get('/tecnico/usuarios', function(){
        return view('tecnico/TecnicoUsuarios');
    })->name('usuarios-tecnico');

    Route::get('/tecnico/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos');
    Route::get('/tecnico/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::put('/tecnico/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.tecnico');
});

// Rutas para usuario productor
Route::middleware(['auth', 'role:productor'])->group(function(){
    Route::get('/productor', function(){
        return view('productor/InicioProductor');
    })->name('productor');

    Route::get('/productor/mapa', function () {
        return view('MapaGOB');
    })->name('mapa-gob');

    Route::get('/productor/up', function(){
        return view('productor/ProductorUP');
    })->name('productor-up');

    Route::get('/productor/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::put('/productor/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.productor');

});