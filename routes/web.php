<?php

use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\VideojuegoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bienvenida');
});

Route::get('/dashboard', function () {
    return view('panel');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [PerfilController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [PerfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [PerfilController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('dashboard')->middleware('auth')->group(function () {
    foreach (['peliculas' => PeliculaController::class, 'videojuegos' => VideojuegoController::class] as $module => $controller) {
        Route::get($module, [$controller, 'index'])->middleware('permission:'.$module.'.ver')->name($module.'.index');
        Route::get($module.'/create', [$controller, 'create'])->middleware('permission:'.$module.'.crear')->name($module.'.create');
        Route::post($module, [$controller, 'store'])->middleware('permission:'.$module.'.crear')->name($module.'.store');
        Route::get($module.'/{record}/edit', [$controller, 'edit'])->middleware('permission:'.$module.'.editar')->name($module.'.edit');
        Route::get($module.'/{record}', [$controller, 'show'])->middleware('permission:'.$module.'.ver')->name($module.'.show');
        Route::match(['put', 'patch'], $module.'/{record}', [$controller, 'update'])->middleware('permission:'.$module.'.editar')->name($module.'.update');
        Route::delete($module.'/{record}', [$controller, 'destroy'])->middleware('permission:'.$module.'.eliminar')->name($module.'.destroy');
    }
});

Route::prefix('dashboard')->name('admin.')->middleware(['auth', 'role:Administrador'])->group(function () {
    Route::resource('usuarios', UsuarioController::class)
        ->parameters(['usuarios' => 'user'])->except('show');
});
