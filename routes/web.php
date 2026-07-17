<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UPController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PoligonoController;
use App\Http\Controllers\CultivoController;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [UserController::class, 'show'])->name('usuarios.show');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    Route::get('/poligonos', [PoligonoController::class, 'index']);
    Route::get('/poligonos/hectareas-totales', [PoligonoController::class, 'hectareasTotales']);
    Route::get('/poligonos/hectareas-por-cultivo', [PoligonoController::class, 'hectareasPorCultivo']);
    Route::get('/poligonos/hectareas-totales-usuario', [PoligonoController::class, 'hectareasTotalesUsuario']);
});

Route::get('/', function () {
    return view('welcome');
})->name('inicio');

Route::middleware(['auth'])->group(function () {

    // 📦 ENDPOINTS DE DATOS (AJAX)
    Route::get('/unidades-produccion', [UPController::class, 'index'])
        ->name('unidades-produccion.data');

    Route::post('/unidades-produccion', [UPController::class, 'store']);

    Route::delete('/unidades-produccion/{id}', [UPController::class, 'destroy']);

    });


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

    Route::get('/root/cultivos', function(){
        return view('root/AdministrarCultivos');
    })->name('administrar-cultivos-root');

    Route::get('/root/registro-cultivos', function(){
        return view('root/RegistrarCultivo');
    })->name('registrar-cultivos-root');

    Route::get('/root/crear-up', [UPController::class, 'create'])->name('crear-up-root');

    Route::get('/root/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos-root');
    Route::get('/root/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up-root');
    Route::put('/root/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.root');
    Route::get('/root/modificar-usuario/{id}', [UserController::class, 'edit'])->name('root.modificar-usuario');
    Route::put('/root/usuarios/{id}', [UserController::class, 'update'])->name('actualizar.usuario.root');

    // Datos de cultivos (solo root)
    Route::get('/root/cultivos/data', [CultivoController::class, 'index'])
    ->name('root.cultivos.data');

    Route::post('/root/cultivos', [CultivoController::class, 'store'])
    ->name('root.cultivos.store');

    Route::delete('/root/cultivos/{id}', [CultivoController::class, 'destroy'])
    ->name('root.cultivos.destroy');

    Route::get('/root/modificar-cultivo/{id}', [CultivoController::class, 'edit'])->name('root.modificar-cultivo');
    Route::put('/root/cultivos/{id}', [CultivoController::class, 'update'])->name('actualizar.cultivo.root');
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

    Route::get('/admin/crear-up', [UPController::class, 'create'])->name('crear-up-admin');


    Route::get('/admin/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos-admin');
    Route::get('/admin/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::put('/admin/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.admin');
    Route::get('/admin/modificar-usuario/{id}', [UserController::class, 'edit'])->name('admin.modificar-usuario');
    Route::put('/admin/usuarios/{id}', [UserController::class, 'update'])->name('actualizar.usuario.admin');
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

    Route::get('/tecnico/crear-up', [UPController::class, 'create'])->name('crear-up-tecnico');

    Route::get('/tecnico/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos');
    Route::get('/tecnico/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::get('/tecnico/modificar-usuario/{id}', [UserController::class, 'edit'])->name('tecnico.modificar-usuario');
    Route::put('/tecnico/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.tecnico');
    Route::put('/tecnico/usuarios/{id}', [UserController::class, 'update'])->name('actualizar.usuario.tecnico');
});

// Rutas para usuario jefe operativo
Route::middleware(['auth', 'role:jefe_operativo'])->group(function(){
    Route::get('/jefe_operativo', function(){
        return view('jefe_operativo/InicioJefeOperativo');
    })->name('jefe_operativo');

    Route::get('/jefe_operativo/mapa', function () {
        return view('jefe_operativo/MapaJefeOperativo');
    })->name('mapa-jefe_operativo');

    Route::get('/jefe_operativo/up', function(){
        return view('jefe_operativo/JefeOperativoUP');
    })->name('jefe-operativo-up');

    Route::get('/jefe_operativo/registro-usuarios', function(){
        return view('jefe_operativo/RegistrarUsuarioJefeOP');
    })->name('registrar-usuarios-jefe_operativo');

    Route::get('/jefe_operativo/usuarios', function(){
        return view('jefe_operativo/UsuariosJefeOP');
    })->name('usuarios-jefe_operativo');


    Route::get('/jefe_operativo/crear-up', [UPController::class, 'create'])->name('crear-up-jefe_operativo');

    Route::get('/jefe_operativo/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos');
    Route::get('/jefe_operativo/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::get('/jefe_operativo/modificar-usuario/{id}', [UserController::class, 'edit'])->name('jefe_operativo.modificar-usuario');
    Route::put('/jefe_operativo/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.jefe_operativo');
    Route::put('/jefe_operativo/usuarios/{id}', [UserController::class, 'update'])->name('actualizar.usuario.jefe_operativo');
});

// Rutas para usuario capturista
Route::middleware(['auth', 'role:capturista'])->group(function(){
    Route::get('/capturista', function(){
        return view('capturista/InicioCapturista');
    })->name('capturista');

    Route::get('/capturista/mapa', function () {
        return view('capturista/MapaCapturista');
    })->name('mapa-capturista');

    Route::get('/capturista/up', function(){
        return view('capturista/CapturistaUP');
    })->name('capturista-up');

    Route::get('/capturista/crear-up', [UPController::class, 'create'])->name('crear-up-capturista');

    Route::get('/capturista/up/poligonos', [UPController::class, 'mapaUP'])->name('mapa-poligonos');
    Route::get('/capturista/modificar-up/{id}', [UPController::class, 'edit'])->name('modificar-up');
    Route::put('/capturista/unidades-produccion/{id}', [UPController::class, 'update'])->name('up.actualizar.capturista');

});