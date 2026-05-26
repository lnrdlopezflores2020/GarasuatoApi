<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\Api\PersonalizadoController;
use App\Http\Controllers\CPController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;

//login
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register',[AuthController::class, 'apiRegister']);

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {

    
    Route::get('/usuario', function (Request $request) {return $request->user();});

    // Rutas de favoritos
    Route::get('/favoritos', [FavoritoController::class, 'apiFavoritos']);
    Route::post('/favoritos/{id}', [FavoritoController::class, 'apiAgregar']);
    Route::delete('/favoritos/{id}', [FavoritoController::class, 'apiQuitar']);

    // Rutas de carrito
    Route::get('/carrito', [CarritoController::class, 'apiIndex']);
    Route::post('/carrito/agregar', [CarritoController::class, 'apiAgregar']);
    Route::put('/carrito/actualizar', [CarritoController::class, 'apiActualizar']);
    Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'apiEliminar']);
    Route::middleware('auth:sanctum')->post('/personalizados',[PersonalizadoController::class, 'guardar']);Route::post('/personalizados',[PersonalizadoController::class, 'guardar']);

    // Rutas de perfil
    Route::get('/perfil',[PerfilController::class, 'apiPerfil']);
    Route::put('/perfil',[PerfilController::class, 'apiActualizarPerfil']);
    Route::post('/perfil/foto',[PerfilController::class, 'apiActualizarFoto']);

    Route::put( '/perfil/telefono', [PerfilController::class, 'actualizarTelefono'] );

    // Rutas de comentarios
    Route::post('/comentarios',[ComentarioController::class, 'store']);
    Route::delete('/comentarios/{id}',[ComentarioController::class, 'destroy']);

    // Rutas admin usuarios
    Route::middleware('admin')->group(function () {

        Route::get('/admin/usuarios', [UsuariosController::class, 'apiIndex']);
        Route::get('/admin/usuarios/{id}', [UsuariosController::class, 'apiShow']);
        Route::post('/admin/usuarios', [UsuariosController::class, 'apiStore']);
        Route::put('/admin/usuarios/{id}', [UsuariosController::class, 'apiUpdate']);
        Route::delete('/admin/usuarios/{id}', [UsuariosController::class, 'apiDestroy']);


        // Rutas admin productos
        Route::get('/admin/productos', [ProductoController::class, 'apiAdminIndex']);
        Route::get('/admin/productos/categorias', [ProductoController::class, 'apiCategorias']);
        Route::get('/admin/productos/{id}', [ProductoController::class, 'apiAdminShow']);
        Route::post('/admin/productos', [ProductoController::class, 'apiAdminStore']);
        Route::post('/admin/productos/{id}', [ProductoController::class, 'apiAdminUpdate']);
        Route::delete('/admin/productos/{id}', [ProductoController::class, 'apiAdminDestroy']);

        // Rutas admin dashboard
        Route::get('/admin/dashboard/usuarios-mes', [DashboardController::class, 'usuariosPorMes']);
        Route::get('/admin/dashboard/ventas-mes', [DashboardController::class, 'ventasPorMes']);
        Route::get('/admin/dashboard/prediccion-compras', [DashboardController::class, 'prediccionCompras']);
       
        // Rutas admin backup
        Route::post('/admin/backup/generar', [BackupController::class, 'generar']);
        Route::get('/admin/backup/descargar/{archivo}', [BackupController::class, 'descargar']);
        
        });
});

// Rutas de productos
Route::get('/productos', [ProductoController::class, 'apiIndex']);
Route::get('/productos/{id}', [ProductoController::class, 'apiShow']);

// Ruta para verificar 2FA
Route::post('/verificar-2fa',[AuthController::class, 'apiVerificar2FA']);
Route::post( '/reenviar-2fa',[AuthController::class, 'apiReenviar2FA']);

// Ruta para buscar por código postal
Route::get('/cp/{cp}',[CPController::class, 'buscarCP']);

// RUTAS PÚBLICAS PARA DEMOSTRACIÓN API
Route::get('/demo/productos', [ProductoController::class, 'apiIndex']);
Route::get('/demo/productos/{id}', [ProductoController::class, 'apiShow']);

Route::get('/demo/dashboard/usuarios-mes', [DashboardController::class, 'usuariosPorMes']);
Route::get('/demo/dashboard/ventas-mes', [DashboardController::class, 'ventasPorMes']);
Route::get('/demo/dashboard/prediccion-compras', [DashboardController::class, 'prediccionCompras']);
