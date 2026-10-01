# Documentacion

Esta carpeta concentra la documentacion funcional y tecnica del proyecto.

## Estructura

- `prds/`: documentos de producto y definicion funcional.

## Referencias

- `prds/01-nueva_galeria_dinamica.md`: alcance, reglas e impacto del proyecto 07.
- `prds/02-eliminacion_imagenes.md`: reglas e impacto de retirar imagenes.

## Eliminacion de imagenes

El listado y el detalle permiten retirar una imagen mediante una solicitud
`POST` confirmada por el usuario. Primero se elimina el registro de `fotos` para
que la imagen deje de aparecer. Si `imagen` contiene una URL administrada por
Vercel Blob, la aplicacion intenta eliminar tambien el objeto usando
`BLOB_READ_WRITE_TOKEN` (o `DYNAMIC_GALERY_READ_WRITE_TOKEN`). Las imagenes
locales heredadas solo se retiran de la galeria y permanecen en `public/img/`.

## Tests

Ejecuta las pruebas nativas del flujo de eliminacion con:

```bash
php tests/eliminacion_test.php
```
