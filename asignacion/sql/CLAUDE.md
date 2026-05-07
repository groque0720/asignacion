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

## Esquema

### `auditoria_unidades` — 1 fila = 1 Guardar

| Columna | Tipo | Notas |
|---------|------|-------|
| id_audit | INT AI PK | |
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
Estados: `estado_tasa`, `estado_reserva`, `reservada`, `reserva`, `cancelada`, `entregada`, `pagado`, `no_disponible`, `borrar`, `reventa`, `servicio_conectado`, `con_encuesta`
Fechas: `fec_playa`, `fec_despacho`, `fec_arribo`, `fec_reserva`, `fec_limite`, `fec_cancelacion`, `fec_entrega`, `fec_inscripcion`, `fec_pedido`
Otros: `costo`, `cliente`, `id_asesor`, `id_negocio`, `id_mes`, `año`, `nro_remito`, `observacion`, `hora`, `id_estado_entrega`, `hora_pedido`

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

**Programar** en el Programador de tareas de Windows, frecuencia diaria o semanal:
```
Programa: php.exe (de Laragon)
Argumentos: c:\laragon\www\asignacion\asignacion\sql\purgar_auditoria_unidades.php
```

Salida queda en `asignacion/api_log.txt` con cantidad de filas borradas y timestamp.

## Cosas a tener en cuenta

- **El trigger es transparente**: si falla la inserción en `auditoria_unidades`, falla TODO el UPDATE de la unidad. Por eso el SQL es minimalista, sin lógica que pueda romperse en runtime.
- **`<=>` es comparación NULL-safe**: `NULL <=> NULL` = 1 (TRUE), así que no se loggean falsos positivos cuando ambos están en NULL.
- **El historial empieza el día que se instala el trigger**. No hay forma de recuperar histórico anterior.
- **JSON nativo**: la columna `movimiento` es tipo `JSON`. MySQL la valida sintácticamente al insertar y permite consultarla con funciones JSON. Si en el futuro quisieran portarse a una DB vieja sin tipo JSON, se puede cambiar a `LONGTEXT` sin tocar nada más.
- **Cron / a_script_***: aparecen como `id_usuario=0`, `usuario='sistema'`, `origen='a_script_levantar.php'` (o el script que sea).
- **Otros módulos** (dashboard_recursos, ventas, etc.) que NO escriben hoy a `asignaciones` — si en el futuro escriben, hay que parchear su `func_mysql.php` igual que los de `asignacion/` y `encuesta/`. De lo contrario el cambio queda registrado pero como `usuario='sistema'`.

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
