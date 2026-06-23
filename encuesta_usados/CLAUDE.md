# Módulo: Encuesta de Satisfacción · USADOS

Co-módulo **independiente** del de 0km (`/encuesta/`). Misma idea (link/QR único por entrega que el cliente responde sin login, scoring ponderado, dashboard), pero:
- Construido con el **patrón moderno** (`/comun/` + estilo `control_pagos`: Tailwind + Alpine + entrypoints finos), no el jQuery/CSS viejo del 0km.
- Fuente de unidades: **`view_asignaciones_usados_entregadas`** (usados con `entregado=1`), no `asignaciones`.
- Tablas propias **`encu_*`** (NO comparte las `enc_*` del 0km — el `id_asignacion` de usados colisiona con el de 0km, por eso van separadas). Crearlas con `sql/encuesta_usados.sql` (importar con `--default-character-set=utf8`).

**No se modificó nada del módulo 0km ni de `asignaciones_usados`.** El estado de encuesta por unidad se **deriva** por LEFT JOIN a `encu_tokens`/`encu_respuestas` (no hay columna `con_encuesta` en usados).

## Arquitectura (sobre `/comun/`)
- `config.php` — constantes `EU_*` (URL pública, fecha desde, perfiles). Override en `config.local.php`.
- `config/config_app.php` — `require ../../comun/bootstrap.php` + chequea acceso (`EU_PERFILES`) y calcula `$puedeConfigurar` / `$puedeEditar`.
- `funciones/consulta.php` — helpers `eu_*`: `eu_utf8` (normaliza datos legacy latin1), `eu_nivel`, `eu_generar_token`, `eu_evaluar_condicion`, builders de WHERE/FROM/orden.
- `actions/*.php` — dejan resultado en `$salida` (el endpoint hace `json_encode`).
- `views/components/*.php` + `views/js/*.js` — UI (Alpine). **No** CSS propio (usa `/comun/base.css`).

### Entrypoints (todos a nivel raíz del módulo, para que `../comun` resuelva)
- **Entregas (admin):** `index.php` → `data.php` (grilla JSON) + `token.php` (genera/devuelve link+QR, modal).
- **Configurador** (`$puedeConfigurar`): `encuestas.php`, `preguntas.php`, `areas.php`, `niveles.php` → leen `cfg_data.php?res=…`, escriben `cfg_guardar.php` (POST `res`+`accion`).
- **Resultados:** `dashboard.php` → `dashboard_data.php` (KPIs/charts/tabla, Chart.js vía `$extraHead`); `detalle.php?id=` (server-rendered) + `pdf.php?id=` (FPDF en `../asignacion/fpdf/`).
- **Público (sin login):** `publico/` — `bootstrap_publico.php` (sólo conecta), `responder.php` + `responder.js` (mobile, 1 pregunta por slide, condicionales), `responder_guardar.php` (scoring), `gracias.php`, `expirada.php`.

## Tipos de pregunta y ponderación (idéntico al 0km)
1 Escala 1-10 (valor directo) · 2 Sí/No (sí=10, no=0) · 3 Múltiple ((sel/total)·10) · 4 Lista Sí/No (no pondera) · 5 Texto (no pondera). Omitidas por condición → `mostrada=0`, fuera del promedio. **Promedio = Σ(ponderadas mostradas) / n**.

## Notas de encoding
- `view_asignaciones_usados_entregadas.año` tiene la `ñ` en UTF-8 (`C3B1`): referenciarla como `` v.`año` `` bajo conexión utf8.
- Datos legacy de usados (cliente/asesor) pueden venir en latin1 → `eu_utf8()` los normaliza antes de `json_encode` (que además usa `JSON_INVALID_UTF8_SUBSTITUTE` como red de seguridad).
- Apache corre **PHP 7.2.24**; el CLI por defecto es 7.1.1 (sin `JSON_INVALID_UTF8_SUBSTITUTE`) — usar el de 7.2 para tests CLI.

## Permisos (en `config.php`)
- `EU_PERFILES = [1,2,5,7,14]` — acceso al panel (entregas, resultados).
- `EU_USUARIOS_CONFIG = [11]` — además, configurar encuesta. `$puedeConfigurar` = perfil 1/14 o usuario en esa lista.

## Acceso
- Por **URL directa** `http://asignacion.oo/encuesta_usados/` (decisión del usuario: sin enlace en menú, igual que el módulo 0km). No se modificó ningún archivo existente.
