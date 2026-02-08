<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* Rutas para el módulo de Usuarios */
Route::middleware(['auth'])->group(function () {
    // 1. Ruta de restaurar (tipo POST) y colocada ANTES del resource
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    
    // 2. Rutas estándar (resource)
    Route::resource('users', UserController::class)->parameters([
    'users' => 'id'
]);
});

require __DIR__.'/auth.php';
