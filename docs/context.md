# Preparación de los tres proyectos PHP para PostgreSQL y Vercel

## Resumen

Preparar cada repositorio de forma independiente, manteniendo su historial y documentación:

- `dynamic-galery-php`: imágenes estáticas incluidas en el repositorio; las nuevas subidas se rechazarán explícitamente en Vercel.
- `best-dynamic-galery-php`: imágenes nuevas almacenadas en Vercel Blob y URLs públicas persistidas en PostgreSQL.
- `paging-php`: migración de la consulta de paginación desde MySQL a PostgreSQL.
- Los tres proyectos recibirán `.gitignore`, `.env.example`, configuración de Vercel y documentación específica.

Vercel ejecuta PHP mediante el runtime comunitario `vercel-php` y sus funciones utilizan un filesystem de solo lectura, salvo `/tmp`; por eso no se usará el filesystem local como almacenamiento permanente. [Runtime PHP de Vercel](https://vercel.com/docs/functions/runtimes)

## Cambios principales

### `dynamic-galery-php`

- Reemplazar la conexión PDO MySQL por PostgreSQL usando `DATABASE_URL`, con fallback documentado a las variables `PG*`.
- Convertir consultas incompatibles:
  - eliminar `SQL_CALC_FOUND_ROWS`;
  - eliminar `FOUND_ROWS()`;
  - usar `LIMIT` y `OFFSET` parametrizados;
  - corregir cualquier sintaxis o palabra reservada incompatible con PostgreSQL.
- Mantener las imágenes existentes en `img/` como assets estáticos del repositorio.
- Generar URLs de imagen correctamente desde la ruta pública de cada despliegue, codificando nombres con espacios y caracteres especiales.
- Desactivar las subidas nuevas en el entorno Vercel con un mensaje explícito:
  - “Las imágenes de esta versión son estáticas y no se pueden subir en Vercel.”
  - no guardar registros en PostgreSQL si el archivo no puede persistirse.
- Mantener el flujo local documentado, dejando claro que el guardado local solo es válido para desarrollo.
- Mejorar el manejo visible de errores de conexión, consulta y archivo inexistente.

### `best-dynamic-galery-php`

- Migrar `Database.php` y el esquema SQL a PostgreSQL.
- Adaptar filtros de formato actualmente basados en `SUBSTRING_INDEX` a expresiones compatibles con PostgreSQL.
- Usar Vercel Blob como almacenamiento persistente público para las imágenes subidas. Vercel Blob está pensado para archivos públicos como imágenes y devuelve URLs directas persistentes. [Vercel Blob](https://vercel.com/docs/vercel-blob)
- Implementar el upload mediante la API HTTP de Blob desde PHP, usando `BLOB_READ_WRITE_TOKEN`, sin introducir una dependencia Node innecesaria.
- Validar:
  - error de upload HTTP;
  - respuesta inválida o sin URL;
  - archivo que no sea imagen;
  - extensión/MIME no permitido;
  - tamaño máximo;
  - error al insertar los metadatos en PostgreSQL.
- Insertar en PostgreSQL la URL pública retornada por Blob, conservando compatibilidad con registros existentes que solo tengan nombre de archivo.
- Centralizar la resolución de `image_url`:
  - URL absoluta de Blob: usar directamente;
  - imagen heredada local: construir ruta pública;
  - imagen ausente: mostrar placeholder y diagnóstico visible.
- Evitar inconsistencias: si Blob sube correctamente pero PostgreSQL falla, informar el error y registrar la URL para permitir recuperación; si PostgreSQL falla antes de guardar, no presentar la imagen como publicada.
- Documentar creación del Blob Store, variable requerida y configuración de acceso público. El store debe crearse como público porque las imágenes deben poder mostrarse directamente. [Configuración de acceso de Vercel Blob](https://vercel.com/docs/vercel-blob/using-blob-sdk)

### `paging-php`

- Reemplazar la conexión MySQL embebida por PDO PostgreSQL basado en `DATABASE_URL`/`PG*`.
- Reescribir paginación con `LIMIT` y `OFFSET` parametrizados.
- Sustituir `FOUND_ROWS()` por una consulta separada `COUNT(*)`.
- Eliminar la redirección fija a `localhost` y generar rutas relativas o basadas en la URL actual.
- Mostrar errores de conexión y consulta de manera clara sin exponer credenciales.
- Documentar que este proyecto no administra imágenes.

### Configuración común de cada repositorio

- Crear un `.gitignore` independiente que ignore como mínimo:
  - `.env`;
  - archivos de logs;
  - temporales;
  - archivos de IDE;
  - dependencias generadas;
  - uploads locales temporales;
  - secretos y archivos de runtime.
- Crear `.env.example` sin valores sensibles, conservando todas las variables necesarias:
  - `DATABASE_URL`;
  - `DATABASE_URL_UNPOOLED`;
  - variables `PG*` relevantes;
  - `BLOB_READ_WRITE_TOKEN` únicamente en `best-dynamic-galery-php`.
- Revisar que los `.env` actuales no se incorporen al historial.
- Crear `vercel.json` independiente con el runtime PHP, rutas de entrada y exposición correcta de archivos públicos.
- Mantener cada proyecto desplegable como unidad independiente, sin asumir un monorepo.
- Actualizar README y, cuando corresponda, la documentación existente con:
  - instalación local;
  - configuración PostgreSQL;
  - variables de entorno;
  - diferencias entre desarrollo y Vercel;
  - comportamiento de imágenes;
  - errores esperados y diagnóstico;
  - proceso de despliegue.
- Añadir un documento de planificación/PRD en los repositorios que no tengan uno, especialmente para el cambio de almacenamiento de imágenes y PostgreSQL.

## Verificación

Para cada repositorio:

- `php -l` sobre todos los archivos PHP modificados.
- Comprobación estática de que no quedan DSN MySQL, credenciales hardcodeadas ni `localhost` usado como destino de producción.
- Verificación de conexión a PostgreSQL con las variables del `.env`.
- Ejecución de los scripts SQL sobre PostgreSQL.
- Prueba de listado, detalle, filtros y paginación.
- Prueba de errores de conexión y tabla inexistente.
- Prueba de rutas de imágenes existentes con nombres que contengan espacios y caracteres especiales.
- `dynamic-galery-php`:
  - comprobar que la imagen estática se muestra;
  - comprobar que una nueva subida devuelve el mensaje explícito de no disponibilidad;
  - comprobar que no se crea un registro incompleto.
- `best-dynamic-galery-php`:
  - upload válido a Blob;
  - archivo inválido;
  - error HTTP de Blob;
  - fallo de PostgreSQL después del upload;
  - renderizado con URL pública de Blob;
  - renderizado de registros heredados.
- Validación local de cada `vercel.json` y build/deploy de prueba si las credenciales de Vercel están disponibles.
- No se crearán commits automáticamente salvo que se solicite; si luego se piden, se separarán en commits atómicos con la convención indicada.

## Supuestos

- `best-dynamic-galery-php` se refiere al repositorio existente `best-dynamic-galery-php`.
- Las imágenes ya presentes en `dynamic-galery-php/img/` son parte del producto y permanecerán versionadas.
- Las imágenes públicas de `best-dynamic-galery-php` no requieren autenticación para lectura.
- Las credenciales reales continuarán fuera del repositorio; `.env.example` solo documentará nombres y valores de ejemplo.
- Las migraciones SQL conservarán las tablas y columnas actuales siempre que PostgreSQL permita mantenerlas sin cambios funcionales.
