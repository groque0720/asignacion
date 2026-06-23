# Módulo: Encuesta de Satisfacción · 0km

Encuesta de satisfacción post-entrega para clientes que retiraron su unidad **0km**.
Link/QR único por entrega que el cliente responde **sin login** desde el celular; al
responder, el link queda obsoleto. Scoring ponderado + dashboard de resultados.

Versión **moderna** (sobre `/comun/` + estilo `control_pagos`: Tailwind + Alpine + entrypoints
finos). Reemplazó al módulo viejo procedural+jQuery (preservado en git / `encuesta_legacy/` hasta validar).
Es el **espejo 0km** de `encuesta_usados/` — misma arquitectura, distinta fuente de datos.

## Diferencias con `encuesta_usados/` (mismo patrón)
- **Fuente de unidades:** tabla **`asignaciones`** (entregadas: `entregada=1 AND borrar=0 AND
  guardado=1 AND fec_entrega >= ENCUESTA_FECHA_DESDE`) con JOIN `usuarios`/`grupos`/`modelos`/`sucursales`.
- **Tablas `enc_*`** (NO `encu_*`): comparte el esquema pero con los datos del 0km
  (encuesta activa id=1, 13 preguntas). **No se crean tablas** — ya existen en la base.
- **Estado por unidad:** se CONSERVA en **`asignaciones.con_encuesta`** (0=sin generar,
  1=pendiente, 2=completada). El token lo pone en 1 (`actions/token.php`); el guardado de
  respuestas en 2 (`publico/responder_guardar.php`). En usados el estado se derivaba por JOIN; acá NO.
- **Columnas propias del 0km** (no en usados): `chasis`, `nro_orden`, `grupo` (grupos.grupo),
  `modelo`/versión (modelos.modelo) — aparecen en grilla, detalle, PDF y filtros del dashboard.

## ⚠️ Auditoría de `asignaciones` (trigger)
`con_encuesta` es un campo auditado por `trg_asignaciones_audit_update`. El `comun/func_mysql.php`
moderno NO setea las `@vars` que lee el trigger (`@id_usuario`/`@usuario_nombre`/`@origen`), así
que **antes de cada `UPDATE asignaciones SET con_encuesta`** se llama `enc_set_audit($con)`
(en `funciones/consulta.php`). Lado público sin sesión → cae a `0`/`'sistema'` (correcto).
Ver [asignacion/sql/CLAUDE.md](../asignacion/sql/CLAUDE.md).

## Arquitectura (sobre `/comun/`)
- `config.php` — constantes `BASE_URL_ENCUESTA`, `ENCUESTA_FECHA_DESDE`, `ENCUESTA_PERFILES`,
  `ENCUESTA_USUARIOS_CONFIG`. Override en `config.local.php` (no se commitea).
- `config/config_app.php` — `require ../../comun/bootstrap.php` + acceso (`ENCUESTA_PERFILES`)
  y `$puedeConfigurar` (perfil 1/14 o usuario en `ENCUESTA_USUARIOS_CONFIG`).
- `funciones/consulta.php` — helpers `enc_*`: `enc_utf8`, `enc_nivel`, `enc_generar_token`,
  `enc_evaluar_condicion`, `enc_set_audit`, y builders `enc_from`/`enc_where_base`/`enc_where_estado`/`enc_order`.
- `actions/*.php` — dejan el resultado en `$salida` (el endpoint hace `json_encode`).
- `views/components/*.php` + `views/js/*.js` — UI (Alpine). **Sin** CSS propio (usa `/comun/base.css`).

### Entrypoints (todos a nivel raíz del módulo, para que `../comun` resuelva)
- **Entregas (admin):** `index.php` → `data.php` (grilla JSON) + `token.php` (link+QR, `con_encuesta=1`).
- **Configurador** (`$puedeConfigurar`): `encuestas.php`, `preguntas.php`, `areas.php`, `niveles.php`
  → leen `cfg_data.php?res=…`, escriben `cfg_guardar.php` (POST `res`+`accion`).
- **Resultados:** `dashboard.php` → `dashboard_data.php` (KPIs/charts/tabla + filtros propios del
  0km: Año/Mes/Grupo/Modelo, además de sucursal/fecha/asesor; tarjetas por nivel; Chart.js vía
  `$extraHead`); `detalle.php?id=` (server-rendered, +chasis/nro_orden, badge de nivel por área)
  + `pdf.php?id=` (FPDF en `../asignacion/fpdf/`, +resumen por área, chasis, nro_orden).
- **Público (sin login):** `publico/` — `bootstrap_publico.php` (sólo conecta), `responder.php` +
  `responder.js` (mobile, 1 pregunta por slide, condicionales), `responder_guardar.php`
  (scoring + token `completada=1` + `con_encuesta=2`), `gracias.php`, `expirada.php`.

## Tipos de pregunta y ponderación
1 Escala 1-10 (valor directo) · 2 Sí/No (sí=10, no=0) · 3 Múltiple ((sel/total)·10) ·
4 Lista Sí/No (no pondera) · 5 Texto (no pondera). Omitidas por condición → `mostrada=0`,
fuera del promedio. **Promedio = Σ(ponderadas mostradas) / n**.

## Permisos (en `config.php`)
- `ENCUESTA_PERFILES = [1,2,5,7,14]` — acceso al panel (entregas, resultados).
- `ENCUESTA_USUARIOS_CONFIG = [11]` — además, configurar encuesta. `$puedeConfigurar` = perfil 1/14
  o usuario en esa lista.

## Acceso / encoding
- Por **URL directa** `http://asignacion.oo/encuesta/` (sin enlace en menú, igual que usados).
- `enc_utf8()` normaliza datos legacy latin1 antes de `json_encode` (que usa
  `JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE`). Apache corre PHP 7.2.24 (tiene el flag).
