<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ClienteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta principal: Redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (requiere estar autenticado y verificado)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// GRUPO PRINCIPAL DE AUTENTICACIÓN
// Todas las rutas aquí dentro requieren que el usuario haya iniciado sesión
Route::middleware('auth')->group(function () {

    // --- Rutas de Perfil (Profile) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Módulo de Usuarios ---
    // 1. Ruta personalizada para restaurar (debe ir ANTES del resource)
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    
    // 2. Rutas estándar (resource)
    // Nota: Mantuve tu configuración de parámetros 'id'
    Route::resource('users', UserController::class)->parameters([
        'users' => 'id'
    ]);

    // --- Módulo de Categorías ---
    // Esto genera automáticamente las rutas: categorias.index, categorias.create, etc.
    Route::resource('categorias', CategoriaController::class);

    // --- Módulo de Marcas ---
    // Esto genera automáticamente las rutas: marcas.index, marcas.create, etc.
    Route::resource('marcas', MarcaController::class);

    // --- Módulo de Productos ---
    // Esto genera automáticamente las rutas: productos.index, productos.create, etc.
    Route::resource('productos', ProductoController::class);

    // --- Módulo de Proveedores ---
    // Esto genera automáticamente las rutas: productos.index, productos.create, etc.
    Route::resource('proveedores', ProveedorController::class);

    // --- Módulo de Compras ---
    // Esto genera automáticamente las rutas: compras.index, compras.create, etc.
    Route::resource('compras', CompraController::class)->except(['edit', 'update', 'destroy']);

    // --- Módulo de clientes ---
    // Aquí puedes agregar las rutas para el módulo de clientes cuando lo implementes
    Route::resource('clientes', ClienteController::class);

    // --- Módulo de Ventas ---
    // Aquí puedes agregar las rutas para el módulo de ventas cuando lo implementes
    Route::resource('ventas', VentaController::class)->except(['edit', 'update', 'destroy']);

});

require __DIR__.'/auth.php';