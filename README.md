# ProyectIA

Consulta [la guía de estructura en español](ESTRUCTURA.md) para saber dónde editar cada módulo y qué significan los nombres técnicos de Laravel.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Administración de usuarios

### Catálogos de películas y videojuegos

Ejecutar `php artisan migrate:fresh --seed` para reconstruir la base de datos con las nuevas tablas y 12 registros ficticios por catálogo (el comando elimina los datos existentes). Para conservar los datos, usar `php artisan migrate` y luego `php artisan db:seed`; el seeder actualiza las cuentas de demostración y reemplaza los permisos antiguos de productos/ventas.

Rutas: `/dashboard/peliculas` y `/dashboard/videojuegos`. En ambos módulos: `GET /create` muestra el formulario, `POST` crea un registro, `GET /{id}` muestra todos sus datos, `GET /{id}/edit` abre la edición, `PUT/PATCH /{id}` actualiza y `DELETE /{id}` elimina. Los IDs son `id_pelicula` e `id_videojuego`. Todos los campos solicitados están incluidos; `fecha_registro` es una fecha editable, la calificación va de 0 a 10 (un decimal) y `jugadores` representa una cantidad positiva.

Administrador tiene todos los permisos. Empleado puede ver, crear y editar ambos catálogos. Cliente puede ver ambos. Los permisos individuales asignados desde Usuarios se suman a los del rol; se validan en cada ruta y los botones se muestran según el acceso. Los permisos se llaman `peliculas.ver/crear/editar/eliminar` y `videojuegos.ver/crear/editar/eliminar`.

Los modelos son `Pelicula` y `Videojuego`, cada módulo tiene su controlador, y comparten `CatalogoController`, `GuardarCatalogoRequest` y las vistas de `resources/views/catalogo` para reducir duplicación. Los datos ficticios están en `database/seeders/CatalogoSeeder.php`.

### Interfaz Mazer

El dashboard y el CRUD usan Mazer (Bootstrap 5), con un menú lateral adaptable y un botón para alternar modo claro/oscuro. La preferencia se guarda en `localStorage`; la primera visita respeta el tema del sistema. Los estilos de Mazer se cargan desde el CDN oficial indicado por el proyecto y requieren conexión a Internet. El perfil también usa Mazer, incluidos sus formularios y la confirmación de eliminación. Las pantallas de autenticación conservan Breeze. El menú lateral funciona como acordeón: al abrir una sección se cierran las otras, y la sección de la ruta actual comienza abierta.

Para agregar módulos, usar `<x-plantilla-panel>` con un slot `header` y el contenido de la página. Este componente centraliza el tema, la navegación y los mensajes de éxito y validación. El menú está en `resources/views/plantillas/parciales/menu-lateral.blade.php`; los campos reutilizables están en `<x-campo-panel>`. No mezclar clases Tailwind con este layout: usar clases Bootstrap. Hay slots de extensión `@stack('styles')` y `@stack('scripts')`.

Preparar la base de datos local:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

`migrate:fresh` elimina las tablas existentes y vuelve a crearlas.

Ingresar en `/login` con cualquiera de estas cuentas (contraseña: `12345678`):

| Nombre | Correo | Rol |
| --- | --- | --- |
| mario | mariosubuyucfb@gmail.com | Administrador |
| laureano | msubuyuct@miumg.edu.gt | Empleado |
| user1 | holamariost@gmail.com | Cliente |

El Administrador encontrará el enlace **Administrar usuarios** en `/dashboard` y **Usuarios** en la navegación.

| Método | Ruta | Acción |
| --- | --- | --- |
| GET | /dashboard/usuarios | Listar usuarios |
| GET | /dashboard/usuarios/create | Formulario de creación |
| POST | /dashboard/usuarios | Crear usuario |
| GET | /dashboard/usuarios/{user}/edit | Formulario de edición |
| PUT/PATCH | /dashboard/usuarios/{user} | Actualizar usuario |
| DELETE | /dashboard/usuarios/{user} | Eliminar usuario |

Todas las rutas del CRUD requieren autenticación y el rol `Administrador`. Empleados y clientes reciben HTTP 403 aunque tengan permisos individuales de usuarios. El administrador puede seleccionar varios roles y permisos adicionales existentes. Los permisos adicionales se suman a los heredados; desmarcarlos no revoca permisos del rol. Al editar, dejar la contraseña vacía conserva la actual. Desde este módulo no se permite eliminar la propia cuenta ni quitarse el rol Administrador.

La validación está en `app/Http/Requests/Admin/GuardarUsuarioRequest.php`, el controlador en `app/Http/Controllers/Admin/UsuarioController.php` y las vistas en `resources/views/administracion/usuarios`. Las escrituras de usuarios y sus asignaciones se ejecutan en transacciones. Para futuros módulos, agregar sus controladores y rutas y definir permisos `modulo.accion` en el seeder; el formulario agrupa automáticamente los permisos por módulo. Los alias `role` y `permission` están registrados en `bootstrap/app.php` para proteger futuras rutas. El registro público de Breeze sigue sin asignar roles automáticamente.

Verificar:

```bash
php artisan route:list --path=dashboard
php artisan test
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
