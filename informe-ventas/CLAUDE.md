# Módulo `informe-ventas/` — Notas para Claude

Dashboard BI de **ventas/reservas** de Derka y Vargas. SPA-light: shell PHP + JS que consume API JSON. Distinto al resto del proyecto — leer esta nota antes de tocar código aquí.

## Stack (rompe con el resto del repo)
- **Bootstrap 5** + DataTables 1.13 + Chart.js 4.4 + `chartjs-plugin-datalabels` (NO Tailwind).
- Tema claro/oscuro con `data-theme` en `<body>`, persistido en `localStorage` (clave `dv-theme`).
- jQuery 3.7 (lo trae DataTables).

## Arquitectura
- [index.php](index.php) — shell HTML con filtros, KPI cards, charts y tabla.
- [js/dashboard.js](js/dashboard.js) — orquesta todo: filtros, fetch a `api.php`, Chart.js, DataTables, exportaciones.
- [api.php](api.php) — único endpoint JSON, dispatch por `?action=…` ([api.php:23](api.php#L23)).
- [export_excel.php](export_excel.php), [export_pdf.php](export_pdf.php), [reporte.php](reporte.php) — salidas (reciben los mismos filtros + imágenes base64 de los charts vía POST en el caso de `reporte.php`).
- [funciones/func_mysql.php](funciones/func_mysql.php) — wrapper que incluye `/asignacion/funciones/func_mysql.php` (no duplicar lógica acá).
- [css/style.css](css/style.css) — estilos del dashboard, variables CSS por tema.

## Convención CRÍTICA: prepared statements
A diferencia del resto del proyecto (que concatena strings con `mysqli_query`), **acá se usan `mysqli_prepare` + `bind_param`**. Ningún valor que venga del cliente debe ir concatenado en la query.

Patrón establecido en [api.php:49](api.php#L49):
```php
function build_where_clause(array &$params, string &$types, bool $exclude_anio = false, bool $exclude_mes_fechas = false): string
```
- `$exclude_anio = true` → no aplica el filtro `YEAR(r.fecres) = ?` (usado por `chart_anio_comp` y `comp_grupo`).
- `$exclude_mes_fechas = true` → además ignora `mes`, `fecha_desde`, `fecha_hasta` (usado por `comp_dia_mes`, que define su propia ventana temporal).

Cada filtro agrega `" AND col = ?"` al WHERE y empuja a `$params` + `$types`. La query se ejecuta con `exec_prepared()` / `exec_prepared_one()` ([api.php:200](api.php#L200)).

> Si agregás un nuevo filtro: extender `build_where_clause()` Y replicar el mismo bloque en `build_where_rep()` de [reporte.php:16](reporte.php#L16) y `build_where_clause_ex()` de [export_excel.php:15](export_excel.php#L15) y la versión equivalente en [export_pdf.php](export_pdf.php). Los cuatro builders deben mantenerse sincronizados — no hay helper compartido todavía.

`ORDER BY` y `LIMIT/OFFSET` en [api.php:738-739](api.php#L738) sí se concatenan, pero solo después de validar contra whitelist (col_map) y de castear a int.

## Modelo de datos
Este módulo opera sobre **`reservas`**, no `asignaciones`. No mezclar las dos.

### Base SQL ([api.php:181](api.php#L181))
```sql
FROM reservas r
LEFT JOIN grupos g    ON r.idgrupo  = g.idgrupo
LEFT JOIN modelos m   ON r.idmodelo = m.idmodelo
INNER JOIN usuarios u ON r.idusuario = u.idusuario
INNER JOIN sucursales s ON u.idsucursal = s.idsucursal
LEFT JOIN ( ... ) ld ON r.idreserva = ld.idreserva
WHERE r.fecres >= '2020-01-01' AND r.enviada != 0
```

Filtros fijos de la base:
- `r.fecres >= '2020-01-01'` — corte histórico.
- `r.enviada != 0` — excluye reservas no enviadas (≈ borradores).
- **Sucursal = sucursal del vendedor** (`u.idsucursal`), no de la unidad. Mismo criterio que `dashboard_recursos`.

### Subquery `ld` — derivación de flags por reserva
Hace un `GROUP BY ld.idreserva` sobre `lineas_detalle` JOIN `codigos`:
- `toma_usado` = 1 si la reserva tiene alguna línea con **`ld.idcodigo = 51`** (código mágico).
- `credito` = 1 si la reserva tiene alguna línea con **`codigos.credito = 1`**.

> Si te piden un nuevo flag derivado de líneas (ej. accesorios, plan canje), seguir este patrón en el mismo subquery — no hacer un `EXISTS` aparte por cada uno.

### Tabla `reservas` — campos en uso
`idreserva` (PK), `fecres` (fecha reserva, usada para todos los filtros temporales), `idgrupo`, `idmodelo`, `idusuario`, `marca` (texto libre), `compra` (`'nuevo'` / `'usado'` / null), `anulada` (0/1), `enviada` (0=borrador), `detalleu` (texto del usado tomado).

### Tablas de catálogo
`grupos` (`idgrupo`, `grupo`, `posicion`), `modelos` (`idmodelo`, `idgrupo`, `modelo`, `posicion`), `sucursales`, `usuarios` (`activo`, `idsucursal`), `codigos` (con flag `credito`).

## Endpoints API existentes
Switch en [api.php:23](api.php#L23). Todos respetan los filtros de `build_where_clause()`:

| action | retorna |
|---|---|
| `filters` | dropdowns: sucursales, vendedores, grupos, modelos, marcas, años |
| `kpis` | total, anuladas, con_credito, con_toma, nuevas, usadas + porcentajes |
| `chart_mes` | reservas/mes del año filtrado (default = año actual) |
| `chart_anio_comp` | comparación 3 años × 12 meses |
| `chart_sucursal` / `chart_vendedor` (top 10) / `chart_credito` / `chart_toma` / `chart_anuladas` / `chart_compra` | distribuciones |
| `comp_grupo` | matriz grupo × 3 años |
| `comp_modelo_mes` | matriz grupo>modelo × 12 meses (año filtrado) |
| `comp_dia_mes` | día a día por grupo/modelo: para el AÑO filtrado, devuelve `meses_activos[]` + `grupos[].modelos[].daily_por_mes[mes][día]`. Cliente acumula hasta el día N elegido por el slider. Ignora filtros mes/fecha; respeta año |
| `modelos_by_grupo` | versiones dependientes del grupo (cascading select) |
| `table` | DataTables server-side, con `draw`/`start`/`length`/`search`/`order` |

Convención de naming: `get_*()` para los handlers, `chart_*` para los que alimentan canvases.

## Convenciones JS ([js/dashboard.js](js/dashboard.js))
- Estado global en `DashboardState` (filters, charts, dtTable, comparisonMode).
- `applyFilters()` dispara el refresh; cada chart tiene su propio `loadChartX()`.
- Charts persisten como `DashboardState.charts.<key>` — antes de re-render destruir con `.destroy()`.
- Paleta en constante `COLORS` (líneas 41-50). Reusar; no inventar colores ad-hoc.
- Labels meses en español: `MONTH_LABELS` / `MONTH_FULL`.

## Reglas para extender
- **Nuevo filtro UI**: agregar `<select>`/`<input>` en [index.php](index.php), agregar key en `DashboardState.filters`, leer en `applyFilters()`, y propagar a los 4 builders PHP de WHERE.
- **Nuevo KPI**: agregar campo al `SELECT` de `get_kpis()`, una `<div class="kpi-card">` en index, y un `setKpi()` en `loadKpis()`.
- **Nuevo chart**: nuevo handler `get_chart_*()` en api.php (usar `build_where_clause()`), `<canvas>` + `chart-card` en index, función `loadChartX()` + `Chart.register`/`new Chart()` en dashboard.js, y agregarlo al snapshot de `reporte.php` si va al reporte gerencial.
- **Costo / margen**: el costo de unidad vive en `modelos.costo` (ver CLAUDE.md general). `reservas` no lo trae — hay que JOIN con `modelos` y agregar.

## Decisiones / hallazgos
- `build_where_*()` está duplicado en 4 archivos a propósito (api, reporte, export_excel, export_pdf). Si el costo de mantenerlos se vuelve molesto, considerar mover a un `funciones/where_builder.php`.
- `r.enviada != 0` filtra borradores — confirmar antes de quitarlo si alguna métrica pide "todo".
- El "Reporte Gerencia" ([reporte.php](reporte.php)) recibe los PNG de los charts en POST como `chart_img_*` (data URI base64). El sanitizador `safe_img()` solo acepta `data:image/...`.
