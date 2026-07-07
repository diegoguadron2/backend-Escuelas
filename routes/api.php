<?php
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PadreController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\SchoolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
// Público
Route::post('/login', [AuthController::class, 'login']);

// Requiere token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('alumnos', AlumnoController::class);
    Route::apiResource('padres', PadreController::class);

    Route::get('/reportes/escuelas', [ReporteController::class, 'escuelas']);
    Route::get('/reportes/alumnos', [ReporteController::class, 'alumnos']);

    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('usuarios-simples', [UsuarioController::class, 'getUsuariosSimples']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/markers', [DashboardController::class, 'markers']);

});
