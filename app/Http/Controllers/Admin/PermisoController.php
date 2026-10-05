<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogoPermisos;
use Illuminate\Support\Facades\Route;

class PermisoController extends Controller
{
    public function index(CatalogoPermisos $catalogo)
    {
        $names = $catalogo->nombres();
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                if (! is_string($middleware) || ! str_starts_with($middleware, 'permission:')) {
                    continue;
                }
                $permissions = explode('|', explode(',', substr($middleware, 11))[0]);
                foreach ($permissions as $permission) {
                    if (in_array($permission, $names, true)) {
                        $routes[] = ['permiso' => $permission, 'metodos' => implode(', ', $route->methods()), 'ruta' => '/'.$route->uri(), 'nombre' => $route->getName()];
                    }
                }
            }
        }

        return view('administracion.roles.permisos', ['rutas' => $routes, 'permisos' => $names]);
    }

    public function sincronizar(CatalogoPermisos $catalogo)
    {
        $count = $catalogo->sincronizar();

        return back()->with('status', "Permisos sincronizados: {$count} nuevos. Los roles editables conservan sus accesos.");
    }
}
