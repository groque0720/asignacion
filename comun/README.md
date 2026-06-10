# Sistema de diseño — módulos modernos

**Fuente única de verdad del look & feel.** Todos los módulos nuevos (y refactorizados)
copian estas recetas para verse igual. El módulo de referencia es **`control_pagos`**.

> Regla de oro: **el look se hace con utilidades Tailwind + `comun/base.css`, NO con CSS propio.**
> Sólo se admite CSS de módulo para mecánica que Tailwind no cubre (ej. matriz con columnas
> sticky a ambos lados). Aun en ese caso, hay que **respetar estos tokens** (header claro, etc.).

## Base provista por `/comun/`
- `head.php`: Tailwind CDN + Alpine + FontAwesome + fuente **Inter**.
- `base.css`: `body` Inter · `[x-cloak]` · `.num` (números tabulares) · `.table-sticky` (header fijo claro).
- `layout.php`: shell `<html>/<body>` con `x-data`/`x-init` del componente Alpine.

## Tokens

| Token | Clase / valor |
|---|---|
| Fondo de app | `bg-gray-100` (lo pone `layout.php`) |
| Contenedor | `max-w-[1800px] mx-auto px-6 py-5 space-y-5` |
| Card | `bg-white rounded-xl shadow-sm border border-gray-200` |
| Título | `text-slate-900 font-bold` |
| Label | `text-xs font-medium text-slate-500` |
| Dato | `text-sm` / `text-xs` · `text-slate-700` |
| Acento primario | azul `blue-600` (hover `blue-700`) |
| Semánticos | `emerald` (ok) · `amber` (alerta) · `red` (error) · `violet`/`blue`/`slate` |
| Números | agregar clase `.num` |

## Recetas

### Header (barra superior) — `views/components/header.php`
```
header: bg-slate-900 text-white shadow-lg sticky top-0 z-30
  contenedor: max-w-[1800px] mx-auto px-6 py-3 flex items-center justify-between
  logo: w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center  (ícono FontAwesome)
  título: text-sm font-bold leading-tight   ·   subtítulo: text-slate-400 text-xs
  acciones (botones): bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium
```

### Toolbar / filtros
```
card (bg-white rounded-xl shadow-sm border border-gray-200 p-4)
  label:  block text-xs font-medium text-slate-500 mb-1
  select/input: text-sm border border-gray-300 rounded-lg px-3 py-2
                focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none
  buscador: input con ícono fa-magnifying-glass absolute left-3
```

### Tabla  ⭐ (lo más importante para la consistencia)
**Usá la clase compartida `.dv-table`** (definida en `comun/base.css`): es la ÚNICA
fuente del look de tabla. Te da base + encabezado claro fijo + separador de filas.
```
card: bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden
  contenedor scroll: overflow-x-auto + max-height
  <table class="dv-table">           ← encabezado claro slate + filas separadas
  THEAD: (lo da .dv-table → slate-100, texto slate-600 mayúsculas, fijo al scrollear)
  TBODY: (separadores los da .dv-table)
     fila: hover:bg-blue-50/40        ← el hover lo pone cada módulo
     celda: px-3 py-2   (números: text-right + .num)   ← padding por módulo
  Paginación (footer): bg-slate-50 border-t border-gray-200 px-4 py-3
```
> El header **siempre claro**. Nunca header oscuro. `.dv-table` lo garantiza.
> Ejemplos: `control_pagos` (tabla simple) y `usados_seguimientos` (matriz: usa
> `class="us-table dv-table"`, sumando su CSS sólo para las columnas sticky).

### Botones
```
primario:    bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm
secundario:  border border-gray-300 text-slate-600 hover:bg-gray-100 rounded-lg px-3 py-2 text-sm
```

### Badges / pills
```
inline-flex items-center rounded-full text-[10px] font-semibold px-2 py-0.5
paleta Tailwind: bg-<color>-100 text-<color>-700   (slate/blue/emerald/amber/red/violet)
íconos: FontAwesome (fa-*)
```

### Modal
```
overlay: fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4
panel:   bg-white rounded-2xl shadow-2xl  (header/footer: bg-slate-50 border)
acciones: botón primario/secundario (ver arriba)
```

### KPI cards (opcional, para dashboards/resúmenes)
```
grid grid-cols-2 md:grid-cols-4 gap-4
card: rounded-xl shadow-sm border border-<color>-100 p-4 flex items-center gap-3
      bg-gradient-to-br from-<color>-50 to-white
ícono: w-11 h-11 rounded-xl bg-<color>-100 text-<color>-600
```

## Checklist al crear/migrar un módulo
- [ ] ¿Header oscuro `slate-900` + tabla **clara** `slate-50`?
- [ ] ¿Cards `rounded-xl shadow-sm border-gray-200`?
- [ ] ¿Inputs/botones con las clases de arriba (radios `rounded-lg`, focus azul)?
- [ ] ¿Cero CSS propio para el look? (sólo mecánica que Tailwind no cubre)
- [ ] ¿Badges en paleta Tailwind, pill `rounded-full`?
