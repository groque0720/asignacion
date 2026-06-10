# Plantilla de módulo moderno

Esqueleto para arrancar un módulo nuevo con el **mismo formato** que `control_pagos`,
`estado_cuenta`, etc. La base visual y de bootstrap vive en `/comun/` (una sola
fuente de verdad); este esqueleto sólo trae lo específico del módulo.

## Cómo crear un módulo nuevo

1. **Copiar** esta carpeta a la raíz del proyecto con el nombre del módulo:
   ```
   asignacion/_plantilla/  ->  asignacion/mi_modulo/
   ```
   (Debe quedar **un nivel** bajo `asignacion/`, igual que los demás módulos: de eso
   dependen las rutas `../comun/...` y `../login`.)

2. **Renombrar el componente Alpine** y su prefijo de funciones:
   - `views/js/plantilla.js` → `views/js/mi_modulo.js`
   - en el archivo: `function plantilla(` → `function miModulo(`
   - en `index.php`: `$jsFile = 'mi_modulo.js';` y `$bodyData = "miModulo(...)"`
   - prefijo `pl_` de `funciones/consulta.php` → el que prefieras (ej. `mm_`)

3. **Ajustar permisos** en `config/config_app.php` (perfiles / usuarios que editan).

4. **Reemplazar la query de ejemplo** en `funciones/consulta.php` (tabla, columnas,
   filtros reales) y los componentes de `views/components/` (columnas de la tabla,
   filtros del toolbar, ícono/título del header).

## Estructura

```
mi_modulo/
├── index.php                 controlador fino (arma componentes + incluye comun/layout.php)
├── data.php                  endpoint JSON fino (bootstrap + acción)
├── config/config_app.php     incluye comun/bootstrap.php + calcula $puedeEditar
├── funciones/consulta.php    lógica de datos (queries)
├── actions/lista.php         arma $salida para data.php
└── views/
    ├── components/           header.php, toolbar.php, tabla.php (HTML + Alpine)
    └── js/plantilla.js       componente Alpine del módulo
```

## Qué aporta `/comun/` (NO copiar, ya es compartido)

- `comun/bootstrap.php`  conexión + sesión + auth. Expone `$con`, `$userId`, `$userName`, `$perfil`.
- `comun/layout.php`     shell `<html>`/`<body>` parametrizado (`$title`, `$content`, `$bodyData`, `$bodyInit`, `$jsFile`, `$extraHead`).
- `comun/head.php`       `<head>` con Tailwind/Alpine/FontAwesome/Inter + `base.css`.
- `comun/base.css`       estilos base (fuente, `[x-cloak]`, `.num`, `.table-sticky`).
- `comun/func_mysql.php` `conectar()` global.

## Convenciones

- **Entrypoints finos**: cada `.php` de primer nivel sólo orquesta (bootstrap + include
  de acción/componentes). La lógica va en `actions/` y `funciones/`.
- **Endpoints JSON**: `$AUTH_FAIL = 'json';` antes de incluir `config_app.php`, y las
  actions dejan el resultado en `$salida` (el endpoint hace el `json_encode`).
- **Tabla con header fijo**: agregá la clase `table-sticky` a la `<table>` y poné el
  contenedor con `max-height` + `overflow`.
- **Estilo**: NO crear CSS/layout propio. Si falta algo global, agregalo a `comun/`.
