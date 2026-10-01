# PRD: eliminacion de imagenes de la galeria dinamica

## Problema y objetivo

La galeria permite subir y editar imagenes, pero no retirarlas. El objetivo es
permitir que un editor elimine una imagen desde el listado o su detalle,
manteniendo sincronizados los metadatos de PostgreSQL y el objeto almacenado en
Vercel Blob cuando la imagen sea remota.

## Alcance

- Agregar una accion de eliminacion en las tarjetas y en el detalle.
- Requerir confirmacion del navegador antes de enviar la accion.
- Procesar la eliminacion exclusivamente mediante `POST`.
- Eliminar la fila de `fotos` por `id`.
- Limpiar en Vercel Blob las URLs remotas administradas por el proyecto.
- Mantener los assets locales heredados cuando no exista un objeto remoto que
  borrar.

## Actores

- Editor que administra el contenido de la galeria.

## Impacto en datos, contratos e integraciones

- `GalleryRepository` incorpora una operacion de borrado por identificador.
- Se usa el endpoint oficial de eliminacion de Vercel Blob con
  `BLOB_READ_WRITE_TOKEN` para borrar una URL remota.
- Las imagenes locales heredadas no se eliminan del disco; solo se retira su
  metadato de la galeria.

## Validaciones y reglas

- El `id` debe ser entero positivo y la accion solo acepta `POST`.
- El servidor vuelve al listado si la imagen no existe.
- La fila de PostgreSQL se elimina aunque la limpieza remota falle; el fallo se
  registra y se informa como advertencia, porque la imagen ya no debe seguir
  visible en la galeria.
- Si PostgreSQL falla, no se intenta borrar el Blob y se informa el error.
- La confirmacion del navegador no sustituye las validaciones del servidor.

## Riesgos y casos limite

- Una falla de Blob puede dejar un objeto huérfano, que debe limpiarse desde el
  panel o CLI de Vercel Blob usando la URL registrada en los logs.
- Un fallo posterior al `DELETE` de PostgreSQL no debe presentar la accion como
  fallida si la fila ya fue retirada; por eso se registra el resultado de la
  limpieza remota por separado.
- El borrado no agrega autenticacion nueva; conserva el modelo actual de
  administracion de la aplicacion.
