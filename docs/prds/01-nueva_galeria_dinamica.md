# PRD: 07-nueva_galeria_dinamica

## Problema y objetivo

La practica `05-galeria_dinamica` resuelve el flujo basico, pero mezcla acceso a datos, renderizado y rutas de forma poco mantenible. Este proyecto crea una nueva galeria dinamica con una base de datos propia, una interfaz renovada basada en los disenos de Stitch y una estructura PHP mas limpia.

## Alcance

- Usar una base dedicada para este proyecto.
- Crear un proyecto nuevo y autonomo con sus propios assets publicos.
- Implementar tres pantallas:
  - listado principal
  - visualizacion detallada con edicion de metadatos
  - carga de nuevas imagenes
- Incorporar buenas practicas minimas:
  - configuracion separada
  - repositorio para acceso a datos
  - inspeccion de metadatos de archivo
  - plantillas reutilizables

## Actores

- Visitante que explora la galeria.
- Editor que carga nuevas imagenes.
- Editor que ajusta titulo y descripcion de una imagen existente.

## Impacto en datos

- Se introduce la base `nueva_galeria_dinamica`.
- La tabla principal sigue siendo `fotos`, pero ahora vive en un esquema propio del proyecto.
- El proyecto se instala con su propio script `nueva_galeria_dinamica.sql`.

## Reglas y validaciones

- La subida acepta solo archivos que `getimagesize` reconoce como imagen.
- El titulo y la descripcion son obligatorios al crear o actualizar.
- Los filtros del listado se derivan de datos existentes, sin introducir nuevas tablas.

## Riesgos y notas

- El proyecto 07 ya no comparte registros con la practica 05.
- Los disenos de Stitch se usan como referencia visual y se adaptan al flujo real en PHP.
