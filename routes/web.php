<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/productos', [ProductoController::class, 'index'])
    ->name('productos.index');

Route::post('/productos', [ProductoController::class, 'guardar'])
    ->name('productos.guardar');

Route::put('/productos/{id}', [ProductoController::class, 'actualizar'])
    ->name('productos.actualizar');

Route::delete('/productos/{id}', [ProductoController::class, 'eliminar'])
    ->name('productos.eliminar');
    
    ////
    Route::get('/clientes', [ClienteController::class, 'index'])
    ->name('clientes.index');

Route::post('/clientes', [ClienteController::class, 'guardar'])
    ->name('clientes.guardar');

Route::put('/clientes/{id}', [ClienteController::class, 'actualizar'])
    ->name('clientes.actualizar');

Route::delete('/clientes/{id}', [ClienteController::class, 'eliminar'])
    ->name('clientes.eliminar');
    ////
    Route::get('/ventas', [VentaController::class, 'index'])
    ->name('ventas.index');

Route::post('/ventas', [VentaController::class, 'guardar'])
    ->name('ventas.guardar');

Route::put('/ventas/{id}/estado', [VentaController::class, 'actualizarEstado'])
    ->name('ventas.estado');

Route::delete('/ventas/{id}', [VentaController::class, 'eliminar'])
    ->name('ventas.eliminar');