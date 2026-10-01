# Desarrollo con Docker

1. Copia `.env.example` a `.env` y, si necesitas cargas nuevas, define `BLOB_READ_WRITE_TOKEN`.
2. Inicia los servicios: `docker compose up --build`.
3. Abre `http://localhost:8081`.

PostgreSQL queda disponible en `localhost:5433` para herramientas locales. La base usa las credenciales de desarrollo `gallery/gallery` y el esquema se ejecuta automáticamente al crear el volumen por primera vez.

Para reinicializar la base después de modificar el SQL, ejecuta `docker compose down -v` y vuelve a levantar los servicios.
