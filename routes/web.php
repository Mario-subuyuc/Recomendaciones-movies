<?php

use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PerfilController;
use App\Services\ResumenConsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bienvenida');
});

Route::get('/dashboard', function (Request $request, ResumenConsumo $resumen) {
    return view('panel', ['consumo' => $resumen->paraUsuario($request->user()->id), 'sinMedicion' => $resumen->llamadasSinMedicion($request->user()->id)]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [PerfilController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [PerfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [PerfilController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('dashboard')->middleware(['auth', 'permission:peliculas.ver|videojuegos.ver'])->group(function () {
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('chat', [ChatController::class, 'preguntar'])->middleware('throttle:chat')->name('chat.preguntar');
    Route::get('historial', [ChatController::class, 'historial'])->name('chat.historial');
    Route::get('historial/{conversacion}', [ChatController::class, 'detalle'])->name('chat.detalle');
    Route::get('consumo', [ChatController::class, 'consumo'])->name('chat.consumo');
});

Route::prefix('dashboard')->middleware('auth')->group(function () {
    foreach (config('modulos') as $module => $definition) {
        if (! isset($definition['controlador'])) {
            continue;
        }
        $controller = $definition['controlador'];
        if (in_array('ver', $definition['acciones'], true)) {
            Route::get($module, [$controller, 'index'])->middleware('permission:'.$module.'.ver')->name($module.'.index');
        }
        if (in_array('crear', $definition['acciones'], true)) {
            Route::get($module.'/create', [$controller, 'create'])->middleware('permission:'.$module.'.crear')->name($module.'.create');
        }
        if (in_array('crear', $definition['acciones'], true)) {
            Route::post($module, [$controller, 'store'])->middleware('permission:'.$module.'.crear')->name($module.'.store');
        }
        if (in_array('editar', $definition['acciones'], true)) {
            Route::get($module.'/{record}/edit', [$controller, 'edit'])->middleware('permission:'.$module.'.editar')->name($module.'.edit');
        }
        if (in_array('ver', $definition['acciones'], true)) {
            Route::get($module.'/{record}', [$controller, 'show'])->middleware('permission:'.$module.'.ver')->name($module.'.show');
        }
        if (in_array('editar', $definition['acciones'], true)) {
            Route::match(['put', 'patch'], $module.'/{record}', [$controller, 'update'])->middleware('permission:'.$module.'.editar')->name($module.'.update');
        }
        if (in_array('eliminar', $definition['acciones'], true)) {
            Route::delete($module.'/{record}', [$controller, 'destroy'])->middleware('permission:'.$module.'.eliminar')->name($module.'.destroy');
        }
    }
});

Route::prefix('dashboard')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'user'])->except('show')
        ->middlewareFor('index', 'permission:usuarios.ver')
        ->middlewareFor(['create', 'store'], 'permission:usuarios.crear')
        ->middlewareFor(['edit', 'update'], 'permission:usuarios.editar')
        ->middlewareFor('destroy', 'permission:usuarios.eliminar');
    Route::resource('roles', RolController::class)->except('show')->middleware('role:Administrador');
    Route::get('permisos', [PermisoController::class, 'index'])->middleware('role:Administrador')->name('permisos.index');
    Route::post('permisos/sincronizar', [PermisoController::class, 'sincronizar'])->middleware('role:Administrador')->name('permisos.sincronizar');
});
