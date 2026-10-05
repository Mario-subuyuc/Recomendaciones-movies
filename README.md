# ProyectIA — Chatbot de películas y videojuegos

Proyecto académico en Laravel 12 / PHP 8.2+, MySQL o MariaDB, Breeze, Spatie Laravel Permission y Mazer. Incluye usuarios, roles, catálogos, chatbot con Groq, historial privado y consumo académico con Chart.js.

Consulta [ESTRUCTURA.md](ESTRUCTURA.md) para orientarte por las carpetas y [DEMOSTRACION.md](DEMOSTRACION.md) para presentar el proyecto.

## Clonar y ejecutar por primera vez

### Requisitos

- Git y Composer.
- PHP 8.2 o superior con las extensiones necesarias para Laravel, incluyendo `pdo_mysql`, `mbstring`, `openssl` y `curl`.
- Node.js 22.12 o superior y npm para compilar los estilos y scripts.
- MySQL o MariaDB. En XAMPP, iniciar MySQL desde su panel de control.
- Conexión a Internet para instalar dependencias, cargar los estilos Mazer y consultar Groq.

### 1. Clonar el repositorio

Abre una terminal en la carpeta donde quieras guardar el proyecto. Si usas XAMPP, puedes usar `C:\xampp\htdocs`:

```bash
git clone https://github.com/Mario-subuyuc/Recomendaciones-movies.git
cd Recomendaciones-movies
composer install
npm install
```

### 2. Crear el archivo de configuración

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

En Linux o macOS:

```bash
cp .env.example .env
```

Estos comandos son para una instalación nueva que todavía no tiene `.env`. Luego genera la clave de Laravel:

```bash
php artisan key:generate
```

### 3. Configurar la base de datos y Groq

Crea una base de datos vacía llamada `proyectia` desde phpMyAdmin o tu cliente MySQL. Abre `.env` y configura tus datos locales. Ejemplo para una instalación habitual de XAMPP:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyectia
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=database
GROQ_API_KEY=
GROQ_MODEL=llama-3.3-70b-versatile
```

Ajusta el usuario, contraseña y puerto según tu servidor. Introduce tu propia clave en `GROQ_API_KEY` para usar el chatbot; los CRUD y el login pueden funcionar sin ella. Consulta la sección **Configurar Groq** para más detalles.

### 4. Crear las tablas y los datos de demostración

Solo para esta instalación nueva con base de datos vacía:

```bash
php artisan config:clear
php artisan migrate --seed
npm run build
```

El seeder crea las tres cuentas de demostración indicadas más abajo y carga 50 películas y 69 videojuegos del material proporcionado. Los datos están en `database/data/peliculas.json` y `database/data/videojuegos.json`; no necesitas ejecutar los INSERT de PostgreSQL.

### 5. Iniciar el proyecto

```bash
php artisan serve
```

Mantén la terminal abierta y visita **http://127.0.0.1:8000/login**. Para entrar como Administrador usa `mariosubuyucfb@gmail.com` y contraseña `12345678`. Si Laravel indica otro puerto porque el 8000 está ocupado, usa la dirección que muestra la terminal.

Para desarrollar y ver cambios de estilos o JavaScript al instante, abre otra terminal en el proyecto y ejecuta `npm run dev`, manteniendo también `php artisan serve` activo. Para detener cada servidor, pulsa `Ctrl+C` en su terminal.

En PowerShell usa `npm.cmd install`, `npm.cmd run build` o `npm.cmd run dev` si la política de ejecución bloquea `npm.ps1`. Si no se reconoce `php` o `composer`, agrega sus ejecutables al PATH; en XAMPP, PHP suele estar en `C:\xampp\php`.

## Actualizar una instalación existente sin borrar datos

Desde la carpeta del proyecto:

```bash
composer install
npm install
php artisan migrate
php artisan permisos:sincronizar
npm run build
php artisan serve
```

En PowerShell puedes usar `npm.cmd` si la política de ejecución bloquea `npm.ps1`. En una instalación nueva, copiar `.env.example` a `.env`, configurar la base de datos y ejecutar `php artisan key:generate` antes de migrar. No reemplazar `.env` ni regenerar APP_KEY en una instalación existente.

Las migraciones pendientes crean las tablas del chatbot y aplican la normalización de un rol por usuario cuando corresponda. Esa normalización retira permisos individuales antiguos y conserva un solo rol (prioridad: Administrador, Empleado, Cliente y después un personalizado); las cuentas sin rol reciben Cliente. No cambia contraseñas ni elimina usuarios, catálogos o historial. No necesita `migrate:fresh` ni volver a sembrar el catálogo.

Solo en una base de prueba nueva y vacía, `php artisan db:seed` carga las cuentas y el catálogo. Este comando actualiza las cuentas de demostración y sus contraseñas; no es necesario ejecutarlo para activar el chatbot.

Para importar únicamente el catálogo a una instalación existente de MySQL o MariaDB:

```bash
php artisan migrate
php artisan db:seed --class=CatalogoSeeder
```

La carga agrega títulos ausentes y conserva los registros existentes y sus ediciones. Puedes repetirla sin duplicar títulos. `jugadores` admite texto como `1-8+` o `4-15`. Las películas conservan vacíos los campos plataforma, productora y clasificación porque el material no los incluye. La fecha de registro se asigna al importar, según la hora de Guatemala. Los datos anteriores permanecen en la base; una instalación vacía tendrá 50 películas y 69 videojuegos.

## Configurar Groq

Introduce personalmente tu clave en `.env`:

```dotenv
GROQ_API_KEY=
GROQ_MODEL=llama-3.3-70b-versatile
```

Escribe tu nueva clave a la derecha de `GROQ_API_KEY=`. Nunca la pongas en una vista, JavaScript, un archivo SQL o el repositorio. `.env` está excluido por `.gitignore`. No se copiaron valores existentes ni se modificaron claves previas. Las entradas se agregan vacías cuando faltan.

Después de editar el entorno:

```bash
php artisan config:clear
```

La integración lee `config('services.groq.*')`; las variables solo se leen en `config/services.php`. El endpoint es `https://api.groq.com/openai/v1/chat/completions`, configurado en ese archivo. Hay timeout de conexión de 5 segundos y de petición de 25 segundos. No hay reintentos automáticos que dupliquen llamadas.

El modelo es configurable. La [lista oficial de modelos](https://console.groq.com/docs/models) consultada no incluye `allam-2-7b`, aunque conserva una [página de documentación de ese modelo](https://console.groq.com/docs/model/allam-2-7b). Por ello, esa página no confirma su disponibilidad actual. `llama-3.3-70b-versatile` figura en la lista, pero la disponibilidad y permisos de tu cuenta deben comprobarse con tu nueva clave. No se usan herramientas, funciones ni JSON mode obligatorio: se pide JSON en texto y se valida estrictamente en Laravel. Referencia del protocolo: [API oficial de Groq](https://console.groq.com/docs/api-reference).

No se ha realizado una llamada real con credenciales. Las pruebas usan HTTP simulado.

## Pantallas y permisos

- `/login`: acceso.
- `/dashboard`: resumen y gráfica de consumo personal.
- `/dashboard/usuarios`: CRUD protegido por permisos para cada acción.
- `/dashboard/roles`: gestión de roles y sus permisos, exclusiva del Administrador.
- `/dashboard/permisos`: catálogo de rutas protegidas y sincronización, exclusivo del Administrador.
- `/dashboard/peliculas` y `/dashboard/videojuegos`: catálogos con detalles, creación, edición y eliminación.
- `/dashboard/chat`: asistente del catálogo.
- `/dashboard/historial`: historial personal paginado.
- `/dashboard/historial/{id}`: detalle propio; un ID de otra persona devuelve 404, incluso para un Administrador.
- `/dashboard/consumo`: totales propios y barras por categoría.
- `/profile`: datos personales, contraseña y eliminación de cuenta.

El chatbot reutiliza `peliculas.ver` y `videojuegos.ver`. Para consultar una categoría se exige su permiso; el selector solo ofrece categorías autorizadas. Los usuarios sin permisos de lectura no tienen acceso al chat. El registro público asigna automáticamente el rol Cliente.

Las cuentas del seeder de prueba mantienen contraseña `12345678`:

| Nombre | Correo | Rol |
| --- | --- | --- |
| mario | mariosubuyucfb@gmail.com | Administrador |
| laureano | msubuyuct@miumg.edu.gt | Empleado |
| user1 | holamariost@gmail.com | Cliente |

Los accesos iniciales son: Administrador administra todo, Empleado ve, crea y edita catálogos, y Cliente consulta catálogos. Los permisos de Empleado y Cliente pueden modificarse desde Roles, por lo que sus accesos efectivos dependen de esa configuración. Cada usuario tiene un único rol y no admite permisos individuales. Los roles personalizados permiten acceso a Usuarios según las acciones seleccionadas. Solo el Administrador gestiona roles y asignaciones.

## Funcionamiento del chat

1. Seleccionar Películas o Videojuegos y escribir una pregunta de hasta 1000 caracteres.
2. Groq interpreta únicamente la pregunta actual y la categoría elegida. No se le envía el catálogo en esta etapa.
3. Laravel valida el JSON, las claves exactas, tipos, campos y operadores contra listas cerradas. Solo admite hasta 8 filtros AND y entre 1 y 10 resultados (5 por defecto).
4. Eloquent consulta exclusivamente la tabla del catálogo seleccionado, con parámetros enlazados. La IA nunca genera SQL ejecutable.
5. Se envían la pregunta actual y los registros recuperados para redactar la respuesta en español. No se envían usuarios, contraseñas ni conversaciones anteriores.
6. Una transacción breve guarda una conversación con dos mensajes y vincula el consumo. No hay transacciones abiertas durante las llamadas a Groq.

Los filtros cubren título, género, plataforma, año, calificación, fecha y los campos particulares de cada categoría. Los operadores numéricos son `=`, `>`, `>=`, `<`, `<=`; los textos admiten `=` o `contiene`. «Mayor a 8.5» se valida como `> 8.5`, distinto de `>= 8.5`. Los comodines de búsquedas de texto se escapan para tratarlos como texto literal.

Se permiten preguntas como:

- Categoría Películas: «¿Cuáles son 3 películas de drama?»
- Categoría Videojuegos: «¿Cuáles son 2 videojuegos de aventura?»
- Categoría Videojuegos: «¿Qué juegos de disparos tienen una calificación mayor a 8.5?»

Si no hay coincidencias, la aplicación muestra un mensaje determinista. Si se recuperaron menos resultados, añade el aviso de cantidad. Los prompts prohíben inventar títulos y obedecer instrucciones del catálogo, pero la calidad de interpretación y redacción del modelo debe evaluarse con la API real. Solo se presenta texto plano escapado: no se interpreta HTML ni Markdown.

Las cadenas del contexto se limitan a 150 caracteres (títulos hasta 255), y la entrada completa de cada llamada a 24000 caracteres. Si la pregunta requiere más detalle o filtros incompatibles, se solicita reformularla. Cada consulta es independiente aunque los mensajes se vean juntos en pantalla.

El botón se desactiva durante el procesamiento. Se limita a 6 solicitudes por minuto por usuario y se usa un bloqueo de 75 segundos para impedir procesamiento concurrente del mismo usuario. Si hay un corte de conexión, consulta el historial antes de repetir: el servidor podría haber terminado aunque el navegador no recibiera la respuesta.

## Tokens académicos

Las horas del historial se muestran en `America/Guatemala` (UTC−6), definida en `config/app.php` como `display_timezone`. La aplicación conserva UTC para almacenar los timestamps y convierte la hora al presentarla; así los registros anteriores también se muestran correctamente. Las fechas de lanzamiento o registro del catálogo son fechas sin hora y no se convierten.

`ContadorPalabras` divide por espacios en blanco Unicode (`\s` y separadores `\p{Z}`), descarta elementos vacíos y cuenta cada elemento como una palabra. La puntuación pegada a una palabra no añade otro token. No equivale al tokenizador, cuota ni facturación real de Groq.

Cada llamada con contenido textual verificable registra:

- Entrada: contenido textual de todos los mensajes enviados, incluyendo instrucciones, pregunta y contexto.
- Salida: contenido textual recibido de Groq, incluido el JSON de interpretación.
- Total: suma de entrada y salida.

No se cuentan nombres de roles, cabeceras, campos del protocolo HTTP ni tokens reales de `usage`. La pregunta se cuenta en cada llamada porque se procesa dos veces. No se cuenta dos veces una misma llamada: `consulta_uuid + etapa` es único. Los avisos locales de ausencia/cantidad no se envían al modelo y no añaden consumo.

Si la interpretación devuelve texto, pero su JSON es inválido o la respuesta posterior falla, su consumo permanece registrado sin conversación completada. Una salida truncada con texto verificable también conserva su consumo. Un timeout, un error HTTP o una respuesta sin texto verificable no inventan consumo.

Las barras y totales incluyen esos consumos parciales, siempre limitados al usuario autenticado. No se ha definido una cuota académica, por lo que no se calcula un saldo restante ficticio. La gráfica usa [Chart.js con Vite](https://www.chartjs.org/docs/latest/getting-started/integration.html) y se adapta al tema del panel.

## Organización y entrega

- `app/Services/GroqService.php`: HTTP, errores y registro por llamada.
- `app/Services/ConsultaCatalogo.php`: interpretación, validación de filtros y consulta SQL segura.
- `app/Services/ChatCatalogo.php`: orquestación e historial transaccional.
- `app/Services/ContadorPalabras.php` y `ResumenConsumo.php`: conteo y agregación personal.
- `app/Http/Controllers/ChatController.php` y `Requests/PreguntarChatRequest.php`: rutas, identidad de sesión y validación.
- `app/Models/Conversacion.php`, `Mensaje.php`, `ConsumoToken.php`: relaciones de historial y consumo.
- `resources/views/chat/`, `resources/js/chat.js`, `resources/js/consumo.js`: interfaz y gráficos.
- `database/sql/chatbot.sql`: esquema MySQL/MariaDB equivalente a la nueva migración. Es una alternativa documental; no ejecutarlo después de migrar.
- `DEMOSTRACION.md`: guion de 2–3 minutos.

La interfaz Mazer usa estilos CDN y requiere Internet. Los componentes compartidos están documentados en ESTRUCTURA.md.

## Verificaciones

```bash
php artisan test
php artisan route:list --path=dashboard
php artisan view:cache
php artisan view:clear
npm run build
```

Las pruebas usan SQLite en memoria y respuestas HTTP simuladas: no tocan la base configurada para la aplicación ni consumen Groq. Cubren acceso, aislamiento, filtros estrictos, ausencia de resultados, entradas inválidas, errores del proveedor, conteo Unicode, consumo parcial, roles editables, un rol por usuario, sincronización repetible y protección contra ampliación de privilegios. Las migraciones adicionales se comprobaron sobre la base MySQL/MariaDB local sin recrear las tablas existentes.

Pendiente con API real: introducir una clave válida y modelo autorizado, comprobar interpretación y redacción en español, latencia, límites de la cuenta y consultas en ambas categorías. Los mensajes de error evitan mostrar cuerpos internos de Groq o credenciales.

## Módulos, rutas y permisos sincronizables

El Administrador puede consultar `/dashboard/permisos` desde **Administración → Rutas del proyecto**. La tabla muestra los permisos declarados y las rutas que los usan (método HTTP, URL y nombre). Login, logout y otras rutas especiales no se convierten automáticamente en permisos administrables.

`config/modulos.php` es el catálogo central. Para un nuevo CRUD, crea su controlador, modelos y vistas y agrega una entrada como:

```php
'reportes' => [
    'nombre' => 'Reportes',
    'ruta' => 'reportes.index',
    'controlador' => \App\Http\Controllers\ReporteController::class,
    'acciones' => ['ver', 'crear', 'editar', 'eliminar'],
],
```

La sección de catálogos de `routes/web.php` registra únicamente las rutas CRUD de las acciones declaradas: `ver` habilita listado y detalle; `crear`, formulario y guardado; `editar`, formulario y actualización; `eliminar`, borrado. El controlador debe implementar los métodos correspondientes (`index`, `show`, `create`, `store`, `edit`, `update`, `destroy`), usando `{record}` como parámetro de sus acciones con ID. El menú y las tarjetas del panel leen el mismo catálogo y muestran el enlace si el usuario tiene `modulo.ver`.

Si un módulo no es CRUD, omite `controlador`, registra sus rutas manualmente y protégelas, por ejemplo:

```php
Route::get('/dashboard/reportes', [ReporteController::class, 'index'])
    ->middleware(['auth', 'permission:reportes.ver'])
    ->name('reportes.index');
```

Declara las acciones adicionales en el catálogo (`exportar`, por ejemplo) y usa `permission:reportes.exportar` en su ruta. Los nombres de módulo y acción admiten letras minúsculas, números y guion bajo. Debe existir la ruta de entrada antes de agregar el enlace al catálogo.

Tras cambiar el catálogo, ejecuta:

```bash
php artisan config:clear
php artisan route:clear
php artisan permisos:sincronizar
```

También puedes pulsar **Sincronizar permisos** en la pantalla de rutas. La operación es repetible: crea los permisos faltantes, conserva los existentes y no sobrescribe los permisos de Empleado, Cliente ni roles personalizados. Las nuevas acciones quedan disponibles en el formulario de roles. Los permisos antiguos no se eliminan automáticamente: se conservan sus asignaciones para que revises los cambios del proyecto.

Administrador conserva acceso a todos los permisos del catálogo y al mantenimiento de usuarios y roles; no puede renombrarse, eliminarse ni editarse desde la interfaz. Empleado y Cliente mantienen sus nombres y no pueden eliminarse, pero el Administrador puede modificar sus permisos. `config/roles.php` define los permisos iniciales de una instalación nueva, no reemplaza las asignaciones guardadas de estos roles durante la sincronización.

Cada usuario sigue teniendo un único rol; los permisos individuales no conceden acceso. Solo el Administrador crea roles y asigna el rol a una cuenta. El registro público asigna Cliente, por lo que los cambios de ese rol afectan también a las cuentas nuevas registradas.

Los roles delegados con permisos de usuarios pueden gestionar cuentas Cliente solo si estas no tienen accesos superiores a los suyos. Tampoco pueden crear una cuenta Cliente con accesos superiores a los propios. Así no pueden ampliar sus privilegios cambiando contraseñas o creando cuentas. Las cuentas de otros roles siguen protegidas y solo Administrador cambia sus asignaciones.

No se necesita una migración nueva para este catálogo: en una instalación actualizada basta sincronizar los permisos. La restricción de un rol por usuario sigue protegida por el índice de la migración anterior.
