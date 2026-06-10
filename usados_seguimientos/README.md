# Seguimiento Documentación — Usados (módulo moderno)

Versión del módulo `usados_docs/` migrada al patrón de los módulos nuevos
(`/comun/` + `/_plantilla/`, como `control_pagos`): Alpine + Tailwind + endpoints JSON.

## Convivencia con `usados_docs/`
- **Comparte las MISMAS tablas**: `usados_docs_items`, `usados_docs_seguimiento`,
  `usados_docs_historial`, `usados_docs_archivos`. Lo que se cambia en un módulo se
  ve en el otro (data en vivo). Pensado para validar este módulo y luego deprecar el viejo.
- **Comparte el almacén físico de archivos**: `usados_docs/uploads/`. Las rutas se
  definen en `config/config_app.php` (`$UPLOADS_DIR` / `$UPLOADS_URL`). Al deprecar el
  módulo viejo, mover esa carpeta acá y actualizar esas 2 líneas.

## Permisos  ⚠️
Se definen en `config/config_app.php` (placeholders, **ajustar**):
- `$EDIT_PERFILES` / `$EDIT_USUARIOS` → `$puedeEditar` (editar celdas: estado, obs, adjuntos).
- `$ADMIN_PERFILES` / `$ADMIN_USUARIOS` → `$esAdmin` (alta/edición de ítems/columnas).
Las acciones de escritura validan estos permisos también en el servidor.

## Estructura
```
usados_seguimientos/
├── index.php                     controlador (componentes + comun/layout.php)
├── data.php  celda.php  historial.php  items.php          endpoints JSON (GET)
├── guardar_celda.php  eliminar_archivo.php  guardar_item.php  endpoints JSON (POST)
├── config/config_app.php         comun/bootstrap + permisos + rutas uploads
├── funciones/
│   ├── schema.php                CREATE TABLE IF NOT EXISTS de las 4 tablas
│   └── consulta.php              queries + estados + helpers (prefijo us_)
├── actions/*.php                 lógica de cada endpoint (dejan $salida)
├── views/
│   ├── components/  header, toolbar, tabla, modal_celda, modal_admin (HTML + Alpine)
│   └── js/usados_seguimientos.js componente Alpine
└── css/usados_seguimientos.css   sólo lo que Tailwind no resuelve (matriz sticky, badges, toast)
```

## Notas
- El grid carga todo el conjunto (usados no entregados) en una sola pasada (`data.php`);
  el buscador de texto filtra en pantalla. Los filtros sucursal/estado_usado/estado_doc
  van al servidor (re-fetch).
- Subida de **múltiples archivos** por celda (input `archivo[]`), igual que el módulo viejo
  ya actualizado. Límite 5 MB por archivo.
