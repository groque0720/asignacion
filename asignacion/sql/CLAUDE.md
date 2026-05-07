# Módulo: Auditoría de cambios en `asignaciones`

Sistema para dejar asentado qué cambios se hacen sobre cada unidad: día, hora, ejecutor (usuario PHP), y todos los campos modificados con valor anterior y valor actual.

## Decisiones tomadas (2026-05-06)

| Punto | Decisión |
|-------|----------|
| Alcance | **Solo tabla `asignaciones`**. No se auditan otras tablas. |
| Operaciones | **Solo UPDATE**. INSERTs y DELETEs físicos NO se loggean. El borrado lógico (`borrar=1`) sí queda registrado porque es un UPDATE del campo `borrar`. |
| Granularidad | **1 fila por cada Guardar** que cambia ≥ 1 campo. Si el Guardar no cambió nada → no se crea fila. |
| Estructura | **1 sola tabla** `auditoria_unidades`. Todos los campos modificados quedan dentro de la columna `movimiento` (tipo `JSON`) como un array de `{campo, antes, despues}`. |
| Estrategia | **Trigger MySQL `BEFORE UPDATE`** sobre `asignaciones`. Cuenta cambios; si > 0, arma el JSON con `JSON_OBJECT` + `JSON_ARRAY_APPEND` y hace 1 INSERT. |
| Usuario | El trigger lee variables de sesión MySQL `@id_usuario`, `@usuario_nombre`, `@origen` que setea `conectar()` en `func_mysql.php` desde `$_SESSION`. Si no hay sesión PHP (cron / a_script_*) cae a `0` / `'sistema'`. |
| Retención | **6 meses después de `fec_entrega`**. Script de purga manual: `sql/purgar_auditoria_unidades.php`. |
| UI | Botón "Historial" dentro de `unidad.php` (visible para todos los que pueden abrir la unidad). Abre `historial_unidad.php?id_unidad=X` en nueva pestaña. PHP decodea el JSON y muestra un card por evento con los campos adentro. |
| Módulos parcheados | `asignacion/funciones/func_mysql.php` y `encuesta/funciones/func_mysql.php` son los únicos que escriben hoy a `asignaciones`. |
| Requisito DB | **MySQL 5.7+ o MariaDB 10.2.7+** (uso del tipo `JSON` y de `JSON_OBJECT`/`JSON_ARRAY_APPEND` en el trigger). |

## Archivos del módulo

```
asignacion/
├── sql/
│   ├── 01_auditoria_unidades_tabla.sql      # CREATE TABLE auditoria_unidades (con columna JSON)
│   ├── 02_auditoria_unidades_trigger.sql    # CREATE TRIGGER trg_asignaciones_audit_update
│   ├── purgar_auditoria_unidades.php        # Script de purga (cron)
│   └── CLAUDE.md                            # Este archivo
├── funciones/func_mysql.php                 # MODIFICADO: setea @id_usuario, @usuario_nombre, @origen
├── unidad.php                               # MODIFICADO: agregado botón "Historial"
└── historial_unidad.php                     # NUEVO: UI de consulta (decodea JSON)
```

## Instalación (una sola vez)

1. **Ejecutar tabla** en phpMyAdmin sobre la base `asignacion`:
   ```
   asignacion/sql/01_auditoria_unidades_tabla.sql
   ```
2. **Ejecutar trigger** (mismo lugar). El archivo usa `DELIMITER`, así que phpMyAdmin lo procesa bien:
   ```
   asignacion/sql/02_auditoria_unidades_trigger.sql
   ```
3. **Verificar** abriendo cualquier unidad y guardándola: debería aparecer 1 fila en `auditoria_unidades` con la columna `movimiento` poblada.

### Verificación rápida desde SQL

```sql
-- ¿Está el trigger instalado?
SHOW TRIGGERS LIKE 'asignaciones';
-- Debe listar trg_asignaciones_audit_update con Event=UPDATE, Timing=BEFORE.

-- Últimas 10 entradas
SELECT id_audit, fecha, hora, usuario, origen, cant_campos
FROM auditoria_unidades ORDER BY id_audit DESC LIMIT 10;

-- ¿Las variables de sesión llegan? (después de un guardado real)
SELECT usuario, origen, COUNT(*) AS n
FROM auditoria_unidades
WHERE fecha = CURDATE()
GROUP BY usuario, origen;
-- Si todo aparece como usuario='sistema' / origen='', conectar() no está
-- seteando las @vars o el script que escribe no llamó a conectar() (ver advertencia abajo).
```

## Esquema

### `auditoria_unidades` — 1 fila = 1 Guardar

| Columna | Tipo | Notas |
|---------|------|-------|
| id_audit | INT UNSIGNED AI PK | |
| id_unidad | INT | FK lógica a `asignaciones.id_unidad` |
| nro_unidad | INT | Denormalizado (NEW.nro_unidad) — sobrevive si se borra la unidad |
| fecha | DATE | CURDATE() |
| hora | TIME | CURTIME() |
| id_usuario | INT | `$_SESSION['id']` o 0 si cron |
| usuario | VARCHAR(64) | `$_SESSION['usuario']` o 'sistema' |
| origen | VARCHAR(96) | basename(SCRIPT_NAME) |
| cant_campos | SMALLINT | Cantidad de campos cambiados (denormalizado para mostrar en UI) |
| **movimiento** | **JSON** | Array de objetos `{campo, antes, despues}` con todos los deltas |

Índices: `id_unidad`, `fecha`, `id_usuario`.

### Estructura del JSON `movimiento`

```json
[
  {"campo": "cliente",      "antes": "Juan Pérez",   "despues": "Juan A. Pérez"},
  {"campo": "fec_reserva",  "antes": null,           "despues": "2026-05-06"},
  {"campo": "estado_tasa",  "antes": 0,              "despues": 1}
]
```

- `null` significa NULL real en la base; `""` significa string vacío. Son cosas distintas.
- El orden del array es el orden en que el trigger evalúa los campos (ver código del trigger).
- Para consultas SQL avanzadas se puede usar `JSON_EXTRACT(movimiento, '$[*].campo')` o `JSON_TABLE` en MySQL 8+.

## Campos auditados

Identificación: `nro_unidad`, `chasis`, `nro_orden`, `interno`, `patente`
Modelo/colores: `id_grupo`, `id_modelo`, `id_color`, `color_uno`, `color_dos`, `color_tres`
Sucursal/ubicación: `id_sucursal`, `id_ubicacion`, `id_ubicacion_entrega`
Estados: `estado_tasa`, `estado_reserva`, `reservada`, `reserva`, `cancelada`, `entregada`, `pagado`, `no_disponible`, `borrar`, `reventa`, `servicio_conectado`
Fechas: `fec_playa`, `fec_despacho`, `fec_arribo`, `fec_reserva`, `fec_limite`, `fec_cancelacion`, `fec_entrega`, `fec_inscripcion`, `fec_pedido`
Otros: `costo`, `cliente`, `id_asesor`, `id_negocio`, `id_mes`, `año`, `nro_remito`, `observacion`, `hora`, `id_estado_entrega`, `hora_pedido`, `con_encuesta`

**NO** se audita: `guardado` (uso interno cache), `fecha_borrado`/`hora_borrado`/`usuario_borrado` (datos del borrado lógico, ya quedan en otra parte).

### Para agregar/quitar un campo del trigger

Editar `02_auditoria_unidades_trigger.sql`:
1. **Agregar/quitar la línea** correspondiente en el cálculo de `v_count` (suma de `(1 - (OLD.x <=> NEW.x))`).
2. **Agregar/quitar el bloque** `IF NOT (OLD.x <=> NEW.x) THEN SET v_json = JSON_ARRAY_APPEND(...); END IF;`.
3. Reejecutar el archivo entero. El `DROP TRIGGER IF EXISTS` lo recrea sin tocar datos existentes.

> Las dos secciones (count + IFs) tienen que estar en sincronía. Si el count está pero falta el IF, `cant_campos` queda mayor al tamaño del array JSON real (inconsistencia visual, no rompe).

## Cómo funciona el flujo

1. Usuario abre `unidad.php`, hace cambios y presiona Guardar.
2. Browser → `guardar_unidad.php` (POST).
3. `guardar_unidad.php` llama `conectar()` → setea `@id_usuario = X`, `@usuario_nombre = '...'`, `@origen = 'guardar_unidad.php'` en la sesión MySQL.
4. `guardar_unidad.php` arma `UPDATE asignaciones SET ... WHERE id_unidad = X`.
5. Trigger `BEFORE UPDATE` se dispara:
   - Cuenta cuántos campos auditados cambian respecto a OLD.
   - Si > 0: construye un JSON array iterando los campos cambiados con `JSON_ARRAY_APPEND(JSON_OBJECT('campo','x','antes',OLD.x,'despues',NEW.x))` y hace 1 `INSERT INTO auditoria_unidades`.
   - Si = 0: no hace nada.
6. Usuario abre `historial_unidad.php?id_unidad=X` (botón "Historial"); el PHP hace `json_decode(movimiento)` y renderiza un card por fila con la tabla de cambios adentro.

## Purga (retención 6 meses post-entrega)

Script: `asignacion/sql/purgar_auditoria_unidades.php`

```sql
DELETE au FROM auditoria_unidades au
JOIN asignaciones a ON a.id_unidad = au.id_unidad
WHERE a.fec_entrega IS NOT NULL
  AND a.fec_entrega <> '0000-00-00'
  AND a.fec_entrega < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
```

**Modo CLI (recomendado)** — programar en el Programador de tareas de Windows, frecuencia diaria o semanal:
```
Programa: php.exe (de Laragon)
Argumentos: c:\laragon\www\asignacion\asignacion\sql\purgar_auditoria_unidades.php
```

**Modo web (para ejecución manual)** — `/asignacion/sql/purgar_auditoria_unidades.php?web=1`
- Requiere sesión PHP activa (`$_SESSION["autentificado"]="SI"`).
- Requiere `idperfil === 14` (admin). Cualquier otro perfil recibe `403 Forbidden`.
- Sin `?web=1` el script aborta con "Falta parámetro web=1.".

**Salida**: en ambos modos imprime una línea con previstas/borradas/OK|ERROR y la appendea a `asignacion/api_log.txt` con timestamp. Útil para auditar la purga.

## Cosas a tener en cuenta

- **El trigger NO bloquea el UPDATE si la auditoría falla**: tiene un `DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN END` que silencia cualquier error (UTF-8 inválido en TEXT, versión rara de MySQL, etc.). El UPDATE de la unidad se completa igual. Decisión deliberada: preferimos perder un registro de auditoría antes que bloquear un guardado del usuario. Si se sospecha que la auditoría está perdiendo filas, hay que revisar el log de errores de MySQL — el trigger no devuelve nada al PHP.
- **`<=>` es comparación NULL-safe**: `NULL <=> NULL` = 1 (TRUE), así que no se loggean falsos positivos cuando ambos están en NULL.
- **El historial empieza el día que se instala el trigger**. No hay forma de recuperar histórico anterior.
- **JSON nativo**: la columna `movimiento` es tipo `JSON`. MySQL la valida sintácticamente al insertar y permite consultarla con funciones JSON. Si en el futuro quisieran portarse a una DB vieja sin tipo JSON, se puede cambiar a `LONGTEXT` sin tocar nada más.
- **Cron / a_script_***: aparecen como `id_usuario=0`, `usuario='sistema'`, `origen='a_script_levantar.php'` (o el script que sea) — cualquier script CLI hereda eso porque no hay `$_SESSION`.
- **Otros módulos** (dashboard_recursos, ventas, etc.) que NO escriben hoy a `asignaciones` — si en el futuro escriben, hay que parchear su `func_mysql.php` igual que los de `asignacion/` y `encuesta/`. De lo contrario el cambio queda registrado pero como `usuario='sistema'`.
- **UPDATEs por fuera del PHP**: ediciones manuales desde phpMyAdmin, MySQL Workbench o cualquier conexión que no llame a `conectar()` también disparan el trigger, pero con `id_usuario=0`, `usuario='sistema'`, `origen=''`. Útil saberlo cuando se investigue una fila huérfana.
- **Sincronización con `historial_unidad.php`**: si se agrega un campo nuevo al trigger, también hay que sumarlo a `$etiquetas` en [historial_unidad.php:84](../historial_unidad.php#L84) para que se renderice con nombre legible (si no, sale el nombre crudo del campo). Si el campo es FK a otra tabla, agregar la entrada correspondiente en `$caches` para mostrar el valor humano. Booleanos/enum-like: agregar al array `$booleanos`.

## Consultas útiles sobre el JSON

```sql
-- Cambios recientes a un campo específico (ej: cliente)
SELECT au.id_audit, au.fecha, au.hora, au.usuario, au.id_unidad,
       JSON_UNQUOTE(JSON_EXTRACT(c.v, '$.antes'))   AS antes,
       JSON_UNQUOTE(JSON_EXTRACT(c.v, '$.despues')) AS despues
FROM auditoria_unidades au
JOIN JSON_TABLE(au.movimiento, '$[*]'
       COLUMNS (v JSON PATH '$')) c
WHERE JSON_UNQUOTE(JSON_EXTRACT(c.v, '$.campo')) = 'cliente'
ORDER BY au.id_audit DESC
LIMIT 50;
-- Requiere MySQL 8+. En 5.7 usar JSON_EXTRACT y un WHERE con JSON_SEARCH.

-- Filas que tocaron un campo dado (5.7+ compatible)
SELECT id_audit, fecha, usuario, id_unidad
FROM auditoria_unidades
WHERE JSON_SEARCH(movimiento, 'one', 'estado_tasa', NULL, '$[*].campo') IS NOT NULL
ORDER BY id_audit DESC LIMIT 50;

-- Top usuarios por actividad en un rango
SELECT usuario, COUNT(*) AS guardados, SUM(cant_campos) AS campos_tocados
FROM auditoria_unidades
WHERE fecha BETWEEN '2026-04-01' AND '2026-04-30'
GROUP BY usuario ORDER BY guardados DESC;

-- Tamaño actual de la auditoría
SELECT COUNT(*) AS filas,
       ROUND(SUM(LENGTH(movimiento))/1024/1024, 2) AS mb_json
FROM auditoria_unidades;
```

## Rollback

Si se quiere desactivar la auditoría sin perder los datos:
```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
```
La tabla queda intacta. Para desinstalar completo:
```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
DROP TABLE IF EXISTS auditoria_unidades;
```
Y revertir los `func_mysql.php` (los `SET @id_usuario = ...` no rompen nada aunque queden — las variables de sesión MySQL son inocuas si no hay trigger que las consuma).
