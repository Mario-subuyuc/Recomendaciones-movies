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

El seeder crea las tres cuentas de demostración indicadas más abajo y 12 películas y 12 videojuegos ficticios. No es necesario importar el archivo SQL si ya ejecutaste las migraciones.

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
npm run build
php artisan serve
```

En PowerShell puedes usar `npm.cmd` si la política de ejecución bloquea `npm.ps1`. En una instalación nueva, copiar `.env.example` a `.env`, configurar la base de datos y ejecutar `php artisan key:generate` antes de migrar. No reemplazar `.env` ni regenerar APP_KEY en una instalación existente.

La actualización del chatbot crea únicamente `conversaciones`, `mensajes` y `consumo_tokens`. No necesita `migrate:fresh` ni volver a sembrar el catálogo. No ejecutar comandos de reconstrucción para conservar tus datos.

Solo en una base de prueba nueva y vacía, `php artisan db:seed` carga las cuentas y 12 registros ficticios por catálogo. El seeder existente actualiza las cuentas de demostración y sus contraseñas y puede actualizar registros ficticios: no es necesario ejecutarlo para activar el chatbot.

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
- `/dashboard/usuarios`: CRUD exclusivo del Administrador.
- `/dashboard/peliculas` y `/dashboard/videojuegos`: catálogos con detalles, creación, edición y eliminación.
- `/dashboard/chat`: asistente del catálogo.
- `/dashboard/historial`: historial personal paginado.
- `/dashboard/historial/{id}`: detalle propio; un ID de otra persona devuelve 404, incluso para un Administrador.
- `/dashboard/consumo`: totales propios y barras por categoría.
- `/profile`: datos personales, contraseña y eliminación de cuenta.

El chatbot reutiliza `peliculas.ver` y `videojuegos.ver`. Para consultar una categoría se exige su permiso; el selector solo ofrece categorías autorizadas. Los usuarios sin permisos de lectura no tienen acceso al chat. No se modifica el registro público de Breeze ni los permisos existentes.

Las cuentas del seeder de prueba mantienen contraseña `12345678`:

| Nombre | Correo | Rol |
| --- | --- | --- |
| mario | mariosubuyucfb@gmail.com | Administrador |
| laureano | msubuyuct@miumg.edu.gt | Empleado |
| user1 | holamariost@gmail.com | Cliente |

Administrador administra todo. Empleado ve, crea y edita catálogos. Cliente consulta catálogos. Los permisos directos se suman a los de los roles. El módulo Usuarios sigue siendo exclusivo del Administrador.

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

Las pruebas usan SQLite en memoria y respuestas HTTP simuladas: no tocan la base configurada para la aplicación ni consumen Groq. Cubren acceso, aislamiento, filtros estrictos, ausencia de resultados, entradas inválidas, errores del proveedor, conteo Unicode, consumo parcial y permisos por categoría. La migración adicional se comprobó sobre la base MySQL/MariaDB local sin recrear las tablas existentes.

Pendiente con API real: introducir una clave válida y modelo autorizado, comprobar interpretación y redacción en español, latencia, límites de la cuenta y consultas en ambas categorías. Los mensajes de error evitan mostrar cuerpos internos de Groq o credenciales.
