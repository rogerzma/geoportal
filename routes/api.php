<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UPController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/parcelas', [ParcelaController::class, 'index'])->name('parcelas.index');
Route::post('/parcelas', [ParcelaController::class, 'store']);
Route::get('/poligonos', [ParcelaController::class, 'index'])->name('parcelas.index');
Route::post('/poligonos', [ParcelaController::class, 'store']);
// Rutas para unidades de producción
    Route::post('/unidades-produccion', [UPController::class, 'store']);
    Route::get('/unidades-produccion', [UPController::class, 'index']);
    Route::get('/unidades-produccion/{id}', [UPController::class, 'show']);
    Route::put('/unidades-produccion/{id}', [UPController::class, 'update']);
    Route::delete('/unidades-produccion/{id}', [UPController::class, 'destroy']);
// Rutas para usuarios
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'store']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);