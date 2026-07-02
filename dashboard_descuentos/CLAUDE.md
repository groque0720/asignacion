# Módulo: Dashboard · Descuentos (0km entregados)

Tablero interactivo de descuentos sobre **unidades 0km entregadas**. Versión moderna
(sobre `/comun/` + Tailwind + Alpine + Chart.js, entrypoints finos). Reemplaza al módulo
viejo procedural, preservado en **`dashboard_descuentos_old/`** hasta validar (borrar luego).

## Modelo de datos (confirmado analizando la base — NO es obvio)
- **Unidad entregada = `asignaciones`** con `entregada=1 AND borrar=0 AND guardado=1`.
  `asignaciones` es **stock 0km** (los usados casi no están: 2 de 1.682). `fec_entrega`
  es la **fecha real** de entrega → es el eje temporal del tablero.
- **Vínculo venta ↔ unidad:** `reservas.nrounidad = asignaciones.nro_unidad` (relación
  **~1:1**), con `reservas.anulada = 0`.
- **`reservas.entregada` está MUERTO** (0 en las 39.671 filas). No usar. La entrega vive
  en `asignaciones`.
- **El descuento vive en `lineas_detalle`** (1 fila por concepto de la reserva):
  - `movimiento = 1` → desglose de la **operación** (Precio de Lista + Flete − Descuento).
    Su suma = precio neto. `movimiento = 2` → forma de pago (Seña, Saldo); balancea.
  - **Definición de descuento = AMPLIA** (elegida por el usuario): **toda** línea de
    operación con `monto < 0` (no sólo las de `codigos.descuento=1`). Incluye, entonces,
    cualquier negativo de la operación. ⚠️ Esto infla la penetración respecto del flag
    estricto (ej. 2026 ≈ 78% amplio vs ~18% estricto). Si algún día se quiere lo estricto,
    filtrar además por `codigos.descuento = 1` en la subconsulta de `dd_filas`.
  - Por reserva: `operacion = SUM(monto)`, `descuento = SUM(-monto WHERE monto<0)`,
    `bruto = operacion + descuento` (todo con `movimiento=1 AND idcodigo>0`).
- **Dimensiones:** Sucursal = del **vendedor** (`usuarios.idsucursal`), Vendedor
  (`reservas.idusuario`→`usuarios.nombre`), Modelo (`reservas.idgrupo`→`grupos.grupo`),
  Versión (`reservas.idmodelo`→`modelos.modelo`), Cliente (`reservas.idcliente`→`clientes.nombre`).
- Datos útiles **desde 2024** (las `lineas_detalle` arrancan ahí). Tablas en **latin1** →
  `dd_utf8()` normaliza nombres antes del `json_encode` (espejo de `enc_utf8` de encuesta).

## Arquitectura (sobre `/comun/`)
- `config/config_app.php` → `require ../../comun/bootstrap.php` (auth genérico; SOLO LECTURA,
  cualquier usuario autenticado).
- `funciones/consulta.php` → `dd_*`: `dd_filas` (la espina, 1 fila por unidad entregada,
  aplica filtros estructurales), `dd_opciones` (años/sucursales/grupos/vendedores para los
  selects), `dd_agrupar` (agrega por dimensión), `dd_utf8`, `dd_mes_nombre`, `dd_es_fecha`.
  > `dd_filas` **no** aplica "sólo con descuento" (las filas sin descuento se necesitan para
  > la penetración). Ese filtro es de la vista de tabla, en el cliente.
- `actions/datos.php` → arma `$salida` (kpis, porSucursal/Modelo/Vendedor, tendencia, tabla, opciones).
- `data.php` → endpoint JSON fino (`$AUTH_FAIL='json'`).
- `index.php` → controlador fino: header + filtros + kpis + charts + tabla → `comun/layout.php`
  (Chart.js vía `$extraHead`). `views/js/dashboard_descuentos.js` = componente Alpine
  (`fetch` + render de 4 gráficos). **Sin CSS propio** (usa `/comun/base.css`, clases `.num`/`.dv-table`).

## Filtros
Año (default = año actual), Desde/Hasta (por `fec_entrega`, **tienen prioridad sobre el Año**),
Sucursal, Modelo, Vendedor (todos server-side, recargan vía `load()`). La tabla además filtra
en el cliente por "sólo con descuento" (default ON) y búsqueda de texto.

## Acceso
Por URL directa `http://asignacion.oo/dashboard_descuentos/`.
