# Demostración del chatbot (2–3 minutos)

Preparación: configurar Groq, ejecutar `php artisan config:clear`, `php artisan migrate`, `php artisan permisos:sincronizar`, `npm run build` y `php artisan serve`. Verificar antes del video una consulta real. Tener una película Drama y un videojuego Aventura; para comprobar el límite estricto, tener dos juegos Disparos con calificaciones 8.5 y 8.6. Crear estos datos por el CRUD, sin borrar registros existentes. Para demostrar accesos limitados, preparar una cuenta con un rol personalizado que solo tenga `usuarios.ver` y otra cuenta Cliente con los permisos iniciales de lectura de catálogos.

1. **0:00–0:20 · Acceso.** Iniciar sesión como Mario. Mostrar nombre, rol y menú. Explicar que el sistema integra Laravel, los catálogos y Groq.
2. **0:20–0:50 · Administración.** Crear o editar un registro de demostración en Películas. Mostrar Roles y permisos: Empleado y Cliente son editables, Administrador está protegido. Abrir Rutas del proyecto y señalar la sincronización de permisos. Mostrar el rol preparado con solo `usuarios.ver`. Cada cuenta tiene un único rol, sin permisos individuales.
3. **0:50–1:25 · Chat.** Abrir Chat del catálogo, seleccionar Películas y enviar «¿Cuáles son 3 películas de drama?». Mostrar la respuesta basada en la tabla y el aviso si se encontraron menos de tres.
4. **1:25–1:55 · Videojuegos.** Cambiar la categoría y preguntar «¿Cuáles son 2 videojuegos de aventura?». Luego «¿Qué juegos de disparos tienen una calificación mayor a 8.5?». Explicar que el registro con 8.5 no cumple y el de 8.6 sí. Cada pregunta es independiente.
5. **1:55–2:20 · Historial.** Mostrar fecha en hora de Guatemala, pregunta y respuesta; abrir un detalle. Explicar que cada usuario ve solo sus consultas completadas.
6. **2:20–2:45 · Consumo.** Abrir Mi consumo o Dashboard. Mostrar las barras Películas/Videojuegos y los totales. Explicar la regla académica: una palabra por token, incluyendo las dos llamadas. No equivale a facturación ni cuota de Groq.
7. **2:45–3:00 · Cierre.** Cerrar sesión. Si hay tiempo, entrar con la cuenta de consulta de usuarios: puede ver el listado pero no crear, editar ni eliminar. Explicar que Cliente accede según los permisos que configure el Administrador y que solo este último gestiona roles.

Si Groq falla durante la grabación, mostrar el mensaje comprensible y explicar que el historial no guarda errores técnicos como respuestas. Las pruebas con HTTP simulado verifican el código, pero no sustituyen una consulta real.
