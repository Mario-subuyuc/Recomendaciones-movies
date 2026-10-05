# Guía del proyecto

## Chatbot y consumo de IA

| Parte | Ubicación |
| --- | --- |
| Conexión con Groq, endpoint y modelo | `config/services.php` y `app/Services/GroqService.php` |
| Validación de filtros y búsqueda del catálogo | `app/Services/ConsultaCatalogo.php` |
| Orquestación de las dos llamadas e historial | `app/Services/ChatCatalogo.php` |
| Contador central de palabras | `app/Services/ContadorPalabras.php` |
| Totales personales | `app/Services/ResumenConsumo.php` |
| Controlador del chat e historial | `app/Http/Controllers/ChatController.php` |
| Validación de la pregunta | `app/Http/Requests/PreguntarChatRequest.php` |
| Modelos de conversación, mensajes y consumo | `app/Models/Conversacion.php`, `Mensaje.php`, `ConsumoToken.php` |
| Pantallas del chat | `resources/views/chat/` |
| Envío seguro de preguntas | `resources/js/chat.js` |
| Gráfica de barras con Chart.js | `resources/js/consumo.js` |
| Nuevas tablas | `database/migrations/2026_10_06_000001_create_chat_tables.php` |
| SQL de referencia MySQL/MariaDB | `database/sql/chatbot.sql` |
| Configuración y demostración | `README.md` y `DEMOSTRACION.md` |

Los espacios de tokens del panel ahora muestran consumo real registrado por la aplicación. No hay cuota configurada ni saldo restante calculado.

## Dónde editar cada parte

| Parte | Archivo o carpeta |
| --- | --- |
| Página principal del dashboard | `resources/views/panel.blade.php` |
| Plantilla común: tema, cabecera y contenido | `resources/views/components/plantilla-panel.blade.php` |
| Menú lateral | `resources/views/plantillas/parciales/menu-lateral.blade.php` |
| Nombre, correo y roles de la barra superior | `resources/views/plantillas/parciales/usuario-barra-superior.blade.php` |
| Pie de página | `resources/views/plantillas/parciales/pie-pagina.blade.php` |
| Espacios para los futuros gráficos de tokens | `resources/views/panel/parciales/resumen-tokens.blade.php` |
| Formularios de acceso y recuperación de contraseña | `resources/views/autenticacion/` |
| Perfil y sus formularios | `resources/views/perfil/` |
| Listado y formulario de usuarios | `resources/views/administracion/usuarios/` |
| Vistas compartidas de películas y videojuegos | `resources/views/catalogo/` |
| Campos de formulario reutilizables del panel | `resources/views/components/campo-panel.blade.php` |
| Gestión de usuarios | `app/Http/Controllers/Admin/UsuarioController.php` |
| Gestión del perfil | `app/Http/Controllers/PerfilController.php` |
| Lógica compartida de los catálogos | `app/Http/Controllers/CatalogoController.php` |
| Campos específicos de películas y videojuegos | `PeliculaController.php` y `VideojuegoController.php` en la misma carpeta |
| Validación de formularios | `app/Http/Requests/`: `GuardarCatalogoRequest`, `ActualizarPerfilRequest` y `Admin/GuardarUsuarioRequest` |
| Tablas representadas en PHP | `app/Models/`: `User`, `Pelicula` y `Videojuego` |
| Creación de las tablas | `database/migrations/` |
| Cuentas, roles y permisos de prueba | `database/seeders/DatabaseSeeder.php` |
| Películas y videojuegos ficticios | `database/seeders/CatalogoSeeder.php` |
| Direcciones del panel y permisos de acceso | `routes/web.php` |
| Direcciones de autenticación | `routes/auth.php` |
| Pruebas automáticas | `tests/` |

## Nombres técnicos que se conservan

Las carpetas `Controllers` (controladores), `Requests` (validación de solicitudes), `Models` (modelos), `components` (componentes reutilizables) y `routes` (rutas) mantienen la estructura habitual de Laravel. Esto permite seguir su documentación y agregar paquetes sin cambiar sus convenciones.

`User` significa usuario. Se conserva junto con `UserFactory`, la tabla `users` y los nombres de campos de autenticación (`name`, `email`, `password`). Los controladores de `Auth` mantienen sus nombres originales de Breeze. `DatabaseSeeder` es el punto de entrada habitual de los datos de prueba. Las migraciones existentes conservan su nombre porque Laravel registra sus nombres en la base de datos.

Los métodos de los CRUD conservan su convención: `index` lista, `create` muestra la creación, `store` guarda, `show` muestra un detalle, `edit` muestra la edición, `update` actualiza y `destroy` elimina. Las URLs y los nombres de las rutas no cambiaron durante la limpieza.

No editar los archivos generados de `bootstrap/cache` ni `storage/framework/views`: Laravel los reconstruye. `vendor` contiene dependencias PHP y `node_modules` dependencias JavaScript; sus nombres y contenido pertenecen a los paquetes.

## Agregar una vista al panel

```blade
<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Título de la página</h2></x-slot>
    <div class="card card-body">Contenido de la página</div>
</x-plantilla-panel>
```

## Verificar cambios

```bash
php artisan test
php artisan route:list
php artisan view:clear
```

Para recrear la base de datos de prueba: `php artisan migrate:fresh --seed`. Este comando elimina los datos existentes. Las tres cuentas de demostración conservan la contraseña `12345678`.

## Archivos retirados

Se retiraron el componente `AppLayout`, su plantilla `layouts/app`, la navegación anterior de Breeze y sus componentes sin referencias: `nav-link`, `responsive-nav-link`, `dropdown`, `dropdown-link`, `modal`, `danger-button` y `secondary-button`. El panel y el perfil utilizan Mazer; las pantallas de autenticación siguen usando sus componentes activos de Breeze.
