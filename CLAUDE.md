# Proyecto Asignación – Notas para Claude

Sistema interno de **Derka y Vargas S.A.** (concesionaria) corriendo sobre Laragon (Windows + PHP + MySQL). Mezcla de scripts PHP procedurales clásicos con `mysqli`, sesiones (`$_SESSION["autentificado"]`), FPDF para reportes y Tailwind (CDN) + Chart.js para dashboards.

## Convenciones generales
- Cada módulo trae su propio `funciones/func_mysql.php` con `conectar()` que setea `$con` global y `SET NAMES 'utf8'`.
- Autenticación: redirect a `../login` si no hay `$_SESSION["autentificado"] === "SI"`.
- Fechas en DB en formato `YYYY-MM-DD`; UI en `d/m/Y`.
- Locale: `America/Argentina/Buenos_Aires`, formato monetario AR (`$ 1.234.567`).
- Las queries se construyen concatenando strings (no hay PDO/prepared statements en el código existente).

## Auditoría de cambios en `asignaciones`
- Trigger MySQL `trg_asignaciones_audit_update` graba en tabla `auditoria_unidades` un row por campo modificado.
- `conectar()` en `asignacion/funciones/func_mysql.php` y `encuesta/funciones/func_mysql.php` setea `@id_usuario`, `@usuario_nombre`, `@origen` (variables de sesión MySQL que el trigger lee).
- UI: botón "Historial" en `unidad.php` → abre `asignacion/historial_unidad.php?id_unidad=X` en nueva pestaña.
- Retención 6 meses post `fec_entrega`. Purga: `asignacion/sql/purgar_auditoria_unidades.php` (cron).
- Detalles, instalación SQL y lista de campos auditados: ver [asignacion/sql/CLAUDE.md](asignacion/sql/CLAUDE.md).

## Módulo `dashboard_recursos/`
Dashboard contable de exposición financiera por sucursal.

### Vistas MySQL usadas (definidas en la DB, **no en el repo**)
- `view_asignaciones_saldo_pendiente_corregida` – todas las unidades con saldo pendiente
- `view_asignaciones_saldo_pendiente_corregida_no_llegadas` – pendientes de pago TASA (sin arribo, sin TASA pagada)
- `view_asignaciones_saldo_pendiente_corregida_en_viaje` – TASA pagada, sin arribo
- `view_asignaciones_saldo_pendiente_corregida_llegadas` – con arribo (`Arribo IS NOT NULL`)

### Definición de `view_asignaciones_saldo_pendiente_corregida` (confirmada por el usuario)
JOIN entre `asignaciones`, `grupos`, `modelos`, `usuarios`, `meses`, `sucursales` + LEFT JOIN `reservas_suma_montos`, `reservas_suma_pagos`.
Filtros: `guardado=1`, `borrar=0`, `entregada=0`, `usuarios.id_negocio=1`, `estado_tasa=1`, `nro_orden NOT LIKE 'TPA%'`, año/mes ≤ hoy.
La sucursal de la vista **es la sucursal del ASESOR** (`usuarios.idsucursal`), no de la unidad.

Aliases expuestos: `NroUn.` (= `asignaciones.nro_unidad`), `Mes`, `Año`, `Modelo` (= `grupos.grupo`), `Versión` (= `modelos.modelo`), `NroOrden`, `Interno`, `Chasis`, `Cliente`, `Asesor`, `Sucursal`, `Reserva`, `Arribo`, `Despacho`, `Confirmada TASA` (= `asignaciones.estado_tasa`), `Cancelada`, `pagado_tasa` (= `asignaciones.pagado`), `Costo TASA`, `Operacion`, `Pagos`, `Saldo`, `idsucursal`.

> ⚠️ **`Costo TASA` está mal nombrado**: el SQL real es `\`modelos\`.\`costo\` AS \`Costo TASA\``. Es decir, **es el costo del MODELO** (`modelos.costo`), no un cargo TASA. Toda la app trata esa columna como si fuera "Costo TASA" pero financieramente es el costo de la unidad. Renombrarla rompería reportes en cascada — mejor dejarla y usar el alias actual con el entendimiento correcto.

> **Saldo = Operacion − Pagos** (con COALESCE 0).

### Vistas hijas (independientes de la maestra)
**Importante:** las tres vistas hijas no son `SELECT * FROM view_asignaciones_saldo_pendiente_corregida WHERE ...`. Tienen su propio FROM/JOIN y devuelven solo `IdSucursal`, `Sucursal`, `Saldo` agregado por sucursal. Modificar la maestra **no se propaga** a las hijas.

- **`_no_llegadas`** (KPI "Pendiente Pago TASA"): filtros `pagado = 0`. **No** filtra por `fec_arribo`. Sin JOIN a `modelos`/`grupos`.
- **`_en_viaje`** (KPI "En Viaje"): `pagado = 1` AND `fec_arribo IS NULL` AND `chasis` no vacío. Inicia desde `sucursales` con LEFT JOIN, así devuelve fila por sucursal aunque no tenga unidades. Sin JOIN a `modelos`.
- **`_llegadas`** (KPI "Con Arribo"): `pagado = 1` AND `fec_arribo IS NOT NULL` AND `fec_arribo <> ''`. Sin JOIN a `modelos`.

Para mostrar **costo** y **estado_reserva** por sucursal hay que agregarles JOIN con `modelos` y nuevas columnas agregadas.

### Tabla `asignaciones` (subyacente)
Columnas relevantes detectadas por uso: `id_unidad`, `id_modelo`, `chasis`, `reservada` (0/1), `estado_reserva` (0=No Confirmada, 1=Confirmada), `entregada`, `borrar`, `cancelada`, `fec_limite`, `fec_reserva`, `fec_arribo`, `fec_despacho`, `fec_playa`, `pagado`, `estado_tasa`, `cliente`, `id_asesor`, `id_color`.
Ref: [stock_real_sin_vender_pdf.php:155](asignacion/stock_real_sin_vender_pdf.php#L155), [a_transpaso.php:80](asignacion/a_transpaso.php#L80), [unidad.php:293-295](asignacion/unidad.php#L293).

### Tabla `unidades`
Tiene columna `costo` (campo `costo_z`/`costo` en [unidad.php:253-255](asignacion/unidad.php#L253)) — pero **NO** se usa como costo para reportes financieros del dashboard.

### Tabla `modelos` ← fuente real del costo de unidad
Columnas detectadas: `idmodelo` (PK), `idgrupo`, `idtipo`, `modelo`, `posicion`, `activo` ([precio_agregar.php:43](ventas/_admin/precio_agregar.php#L43)).
**El costo de la unidad vive en `modelos.costo` (confirmado por el usuario). Una unidad pertenece a un modelo vía `asignaciones.id_modelo` → `modelos.idmodelo`.** El código actual no referencia `modelos.costo` en ningún lado, así que el JOIN hay que agregarlo a mano.

### Tabla `listaprecio` (precio de venta, no costo)
Columnas: `idmodelo`, `pl` (precio lista), `flete`, `trans`, `activo` ([precios_actualizar_reservas.php:19](ventas/_admin/precios_actualizar_reservas.php#L19)). No confundir con `modelos.costo`.

### Estructura del dashboard `dashboard_recursos/index.php`
1. Métricas de gestión (3 cards) – tasas/porcentajes
2. KPI cards (4) – Pendiente / En Viaje / Con Arribo / Total
3. Charts (barras agrupadas + dona)
4. Tablas Asesor / Modelo
5. Card de tabs detallada (Pendiente / En Viaje / Con Arribo / Todas)

## Decisiones / hallazgos
- `dashboard_recursos/index copy.php` es backup/versión vieja; los cambios van en `index.php`.
- Para diferenciar unidades "reservadas confirmadas" hace falta `asignaciones.estado_reserva` que **no está expuesto en las vistas** – requiere JOIN o ampliar la vista.
