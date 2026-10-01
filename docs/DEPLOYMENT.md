# Despliegue

Configura PostgreSQL y crea un Blob Store **público** en Vercel. Define `DATABASE_URL` y `DYNAMIC_GALERY_READ_WRITE_TOKEN` en el proyecto. Ejecuta `nueva_galeria_dinamica.sql` en la base antes del primer uso.

Para migrar imágenes heredadas desde `public/img` al Blob Store, ejecuta `php scripts/migrate-images-to-blob.php` con las variables de entorno configuradas. El proceso es idempotente: solo procesa registros que todavía contienen un nombre local.

Las imágenes nuevas se cargan mediante la API HTTP de Vercel Blob y la URL pública se guarda en `fotos.imagen`. Los registros heredados con nombre local siguen apuntando a `public/img`. Si Blob sube pero PostgreSQL falla, la aplicación muestra la URL para recuperación manual.

Despliega desde esta carpeta como proyecto independiente. No subas `.env` ni tokens.
