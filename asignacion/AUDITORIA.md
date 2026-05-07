# Auditoría de cambios en Unidades

Manual de operación, instalación y mantenimiento del módulo de auditoría sobre la tabla `asignaciones`.

---

## Tabla de contenidos

1. [Resumen](#resumen)
2. [Cómo lo ve el usuario final](#cómo-lo-ve-el-usuario-final)
3. [Arquitectura](#arquitectura)
4. [Instalación en un servidor nuevo](#instalación-en-un-servidor-nuevo)
5. [Verificación post-instalación](#verificación-post-instalación)
6. [Mantenimiento: purga automática](#mantenimiento-purga-automática)
7. [Modificar qué campos se auditan](#modificar-qué-campos-se-auditan)
8. [Estructura técnica](#estructura-técnica)
9. [Diagnóstico de problemas](#diagnóstico-de-problemas)
10. [Rollback](#rollback)
11. [Preguntas frecuentes](#preguntas-frecuentes)

---

## Resumen

Sistema para dejar asentado qué cambios se hacen sobre cada unidad: **día, hora, usuario, todos los campos modificados** con su valor anterior y nuevo.

| Concepto | Decisión |
|----------|----------|
| Alcance | Solo tabla `asignaciones` |
| Operaciones | Solo UPDATE (los INSERTs/DELETEs físicos no se loggean) |
| Granularidad | 1 fila por cada Guardar que cambia ≥ 1 campo |
| Almacenamiento | 1 sola tabla, todos los cambios en un campo `JSON` |
| Quién puede ver | Todos los que pueden abrir la unidad (botón "Historial") |
| Retención | 6 meses después de la fecha de entrega |
| Requisito DB | MySQL 5.7+ o MariaDB 10.2.7+ |

---

## Cómo lo ve el usuario final

1. Abre cualquier unidad en la planilla.
2. Aparece un botón gris **"Historial"** entre Cancelar y Guardar.
3. Al hacer clic, se abre una nueva pestaña con el historial:

```
🟦 07/05/2026 10:29 · Admin · guardar_unidad.php · [3 campos]
   Color 1     Blanco (#1)   →   Negro (#2)
   Color 2     Blanco (#1)   →   Rojo (#3)
   Color 3     Blanco (#1)   →   Azul (#7)

🟦 06/05/2026 19:58 · Admin · guardar_unidad.php · [5 campos]
   Cliente         (vacío)        →  Roque Gomez
   Asesor          Sistema (#1)   →  Juan Pérez (#96)
   Fec. Reserva    (vacío)        →  06/05/2026
   Fec. Lim. Canc. (vacío)        →  11/05/2026
   Hora Reserva    (vacío)        →  19:57:00
```

Cada **card** = 1 Guardar. Adentro, cada fila = 1 campo modificado, con valor anterior (rojo) y valor actual (verde).

---

## Arquitectura

### Componentes

| Componente | Tipo | Ubicación |
|------------|------|-----------|
| Tabla `auditoria_unidades` | MySQL | DB `asignacion` |
| Trigger `trg_asignaciones_audit_update` | MySQL | DB `asignacion` |
| Función `conectar()` modificada | PHP | `asignacion/funciones/func_mysql.php` y `encuesta/funciones/func_mysql.php` |
| Botón "Historial" | PHP | `asignacion/unidad.php` |
| UI de consulta | PHP | `asignacion/historial_unidad.php` |
| Script de purga | PHP | `asignacion/sql/purgar_auditoria_unidades.php` |
| SQL de instalación | SQL | `asignacion/sql/01_auditoria_unidades_tabla.sql` y `02_auditoria_unidades_trigger.sql` |

### Flujo de un Guardar

```
Usuario edita unidad y presiona Guardar
            ↓
   guardar_unidad.php recibe el POST
            ↓
   conectar() abre conexión MySQL y ejecuta:
     SET @id_usuario = 11
     SET @usuario_nombre = 'Admin'
     SET @origen = 'guardar_unidad.php'
            ↓
   UPDATE asignaciones SET ... WHERE id_unidad = X
            ↓
   ── Trigger BEFORE UPDATE se dispara ──
   1. Cuenta cuántos campos auditados cambian (OLD vs NEW)
   2. Si > 0:
      a) Arma un array JSON con un objeto por campo cambiado:
         [{"campo":"cliente","antes":"","despues":"Roque"}, ...]
      b) INSERT 1 fila en auditoria_unidades con ese JSON
   3. Si = 0: no hace nada
   ────────────────────────────────────
            ↓
   El UPDATE se completa, la app sigue normal
            ↓
   Más tarde, otro usuario abre Historial
            ↓
   historial_unidad.php SELECT en auditoria_unidades
            ↓
   PHP json_decode() y renderiza cards
```

### Por qué un trigger en la base y no PHP

Si lo hacíamos solo en PHP, había que parchear ~10 archivos `.php` que escriben a `asignaciones` (`guardar_unidad.php`, `cambio-unidad-guardar.php`, `a_script_levantar.php`, etc.) y rezar que nadie agregara un nuevo `UPDATE` sin llamar al helper de auditoría. Con el trigger en MySQL, **cualquier UPDATE** sobre `asignaciones` queda auditado, no importa qué script lo haga.

### Cómo el trigger sabe qué usuario fue

Los triggers MySQL no tienen acceso directo a `$_SESSION` de PHP. Truco: cuando `conectar()` abre la conexión, setea **variables de sesión MySQL** (`@id_usuario`, `@usuario_nombre`, `@origen`) leyendo de `$_SESSION`. El trigger las lee. Si el script corre por cron sin sesión PHP (ej. `a_script_levantar.php`), las variables quedan en `0` / `'sistema'` y se identifica por `@origen` (el nombre del script).

---

## Instalación en un servidor nuevo

### Pre-requisitos

- MySQL 5.7+ o MariaDB 10.2.7+. Verificá:
  ```sql
  SELECT VERSION();
  ```
- El código del repo ya pulleado (`asignacion/sql/` contiene los SQL).

### Pasos

#### 1. Limpieza previa (solo si ya existió alguna versión vieja)

```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
DROP TABLE IF EXISTS auditoria_unidades;
```

#### 2. Crear la tabla

Ejecutar `asignacion/sql/01_auditoria_unidades_tabla.sql`:

- **CLI**:
  ```bash
  mysql -u <usuario> -p asignacion < /ruta/al/repo/asignacion/sql/01_auditoria_unidades_tabla.sql
  ```
- **phpMyAdmin**: pestaña SQL → pegar el contenido del archivo → Ejecutar.

#### 3. Crear el trigger

Ejecutar `asignacion/sql/02_auditoria_unidades_trigger.sql` (mismo método que el paso 2).

> El archivo usa `DELIMITER $$` porque el trigger contiene varios `;`. Tanto la CLI de MySQL como phpMyAdmin entienden esa sintaxis.

#### 4. Listo

A partir de ahora, **cualquier UPDATE sobre `asignaciones`** queda registrado.

---

## Verificación post-instalación

### Verificar que la tabla y el trigger existen

```sql
-- Estructura de la tabla
SHOW COLUMNS FROM auditoria_unidades;
-- Tiene que tener: id_audit, id_unidad, nro_unidad, fecha, hora,
--   id_usuario, usuario, origen, cant_campos, movimiento (JSON)

-- Trigger instalado
SHOW TRIGGERS FROM asignacion LIKE 'asignaciones';
-- Tiene que aparecer trg_asignaciones_audit_update con event=UPDATE timing=BEFORE
```

### Smoke test

1. Entrar a la app como admin.
2. Abrir una unidad cualquiera.
3. Modificar un campo no crítico (ej: observación) y Guardar.
4. Ejecutar:
   ```sql
   SELECT id_audit, fecha, hora, usuario, cant_campos, movimiento
   FROM auditoria_unidades ORDER BY id_audit DESC LIMIT 1;
   ```
5. Tiene que aparecer la fila con `movimiento` poblado, ej:
   ```json
   [{"campo":"observacion","antes":"texto viejo","despues":"texto nuevo"}]
   ```
6. Volver a la planilla, abrir esa unidad, hacer clic en **Historial**. Tiene que mostrar el card.

---

## Mantenimiento: purga automática

### Política de retención

**6 meses después de `fec_entrega`**. Una unidad sin entregar **nunca se purga** (mantiene todo el historial).

### Script

`asignacion/sql/purgar_auditoria_unidades.php`

Hace:
```sql
DELETE au FROM auditoria_unidades au
JOIN asignaciones a ON a.id_unidad = au.id_unidad
WHERE a.fec_entrega IS NOT NULL
  AND a.fec_entrega <> '0000-00-00'
  AND a.fec_entrega < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
```

Loguea cantidad de filas borradas en `asignacion/api_log.txt`.

### Programar en Linux (VPS)

```bash
crontab -e
```

Agregar (todos los lunes a las 03:00):
```
0 3 * * 1 /usr/bin/php /ruta/al/repo/asignacion/sql/purgar_auditoria_unidades.php
```

### Programar en Windows (Laragon local)

Programador de tareas:
- **Programa**: `C:\laragon\bin\php\<version>\php.exe`
- **Argumentos**: `C:\laragon\www\asignacion\asignacion\sql\purgar_auditoria_unidades.php`
- **Frecuencia**: semanal o diaria.

### Ejecución manual (si querés correrlo a mano)

Por web (requiere estar logueado como perfil 14):
```
https://tu-dominio/asignacion/sql/purgar_auditoria_unidades.php?web=1
```

Por CLI:
```bash
php asignacion/sql/purgar_auditoria_unidades.php
```

---

## Modificar qué campos se auditan

Si mañana necesitás:
- **Dejar de auditar** un campo (ej. `observacion` porque es muy ruidoso)
- **Empezar a auditar** una columna nueva que se agregue a `asignaciones`

Editá **un solo archivo**: `asignacion/sql/02_auditoria_unidades_trigger.sql`.

### Pasos

1. Ubicá el bloque `SET v_count = ...` (~líneas 35-80). Agregá o quitá la línea correspondiente:
   ```sql
   (1 - (OLD.mi_campo <=> NEW.mi_campo)) +
   ```

2. Ubicá los bloques `IF NOT (OLD.x <=> NEW.x) THEN ... END IF;` (~líneas 90 en adelante). Agregá o quitá el bloque del campo:
   ```sql
   IF NOT (OLD.mi_campo <=> NEW.mi_campo) THEN
     SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','mi_campo','antes',OLD.mi_campo,'despues',NEW.mi_campo)); END IF;
   ```

3. Reejecutá el archivo entero contra la base. El `DROP TRIGGER IF EXISTS` del inicio limpia el trigger viejo y crea el nuevo. **Los datos ya guardados en `auditoria_unidades` no se tocan.**

### Importante

Las dos secciones (`v_count` y los `IF`s) tienen que estar **sincronizadas**. Si agregás la línea en `v_count` pero olvidás el `IF`, el evento se crea con `cant_campos` mayor al tamaño real del array JSON (inconsistencia visual; no rompe nada pero queda raro).

### Etiqueta amigable en la UI

Opcionalmente, para que el campo aparezca con nombre legible en lugar del nombre técnico, agregá una entrada al array `$etiquetas` de `asignacion/historial_unidad.php`:
```php
'mi_campo' => 'Mi Campo Bonito',
```

Si no lo hacés, la UI muestra `mi_campo` directamente — funciona igual.

---

## Estructura técnica

### Tabla `auditoria_unidades`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id_audit` | INT AI PK | |
| `id_unidad` | INT | id de la unidad afectada |
| `nro_unidad` | INT | número de unidad (denormalizado, sobrevive si se borra la unidad) |
| `fecha` | DATE | día del cambio |
| `hora` | TIME | hora del cambio |
| `id_usuario` | INT | id del usuario PHP (`$_SESSION['id']`); 0 si fue cron |
| `usuario` | VARCHAR(64) | nombre del usuario; 'sistema' si fue cron |
| `origen` | VARCHAR(96) | nombre del script PHP que originó el UPDATE |
| `cant_campos` | SMALLINT | cantidad de campos modificados (denormalizado para mostrar rápido) |
| `movimiento` | **JSON** | array de objetos `{campo, antes, despues}` — el delta completo |

Índices: `id_unidad`, `fecha`, `id_usuario`.

### Estructura del JSON `movimiento`

```json
[
  {"campo": "cliente",     "antes": "Juan Pérez",  "despues": "Juan A. Pérez"},
  {"campo": "fec_reserva", "antes": null,          "despues": "2026-05-07"},
  {"campo": "estado_tasa", "antes": 0,             "despues": 1}
]
```

- `null` = NULL real en la base.
- `""` = string vacío.
- El orden del array refleja el orden en que el trigger procesa los campos (identificación → modelo → sucursal → estados → fechas → otros).

### Campos auditados (47 columnas)

| Grupo | Columnas |
|-------|----------|
| Identificación | `nro_unidad`, `chasis`, `nro_orden`, `interno`, `patente` |
| Modelo / colores | `id_grupo`, `id_modelo`, `id_color`, `color_uno`, `color_dos`, `color_tres` |
| Sucursal / ubicación | `id_sucursal`, `id_ubicacion`, `id_ubicacion_entrega` |
| Estados | `estado_tasa`, `estado_reserva`, `reservada`, `reserva`, `cancelada`, `entregada`, `pagado`, `no_disponible`, `borrar`, `reventa`, `servicio_conectado`, `con_encuesta` |
| Fechas | `fec_playa`, `fec_despacho`, `fec_arribo`, `fec_reserva`, `fec_limite`, `fec_cancelacion`, `fec_entrega`, `fec_inscripcion`, `fec_pedido` |
| Otros | `costo`, `cliente`, `id_asesor`, `id_negocio`, `id_mes`, `año`, `nro_remito`, `observacion`, `hora`, `id_estado_entrega`, `hora_pedido` |

### Columnas NO auditadas (decisión deliberada)

- `guardado` — se usa internamente para invalidar cachés, ruidoso e inútil.
- `fecha_borrado`, `hora_borrado`, `usuario_borrado` — datos del borrado lógico, ya quedan en otra parte.

### Variables de sesión MySQL

`conectar()` setea estas tres después de abrir la conexión:

| Variable | Origen |
|----------|--------|
| `@id_usuario` | `(int)$_SESSION['id']` o 0 |
| `@usuario_nombre` | `$_SESSION['usuario']` o `'sistema'` |
| `@origen` | `basename($_SERVER['SCRIPT_NAME'])` o `'cli'` |

El trigger las lee con `IFNULL(@id_usuario, 0)`, etc. Las variables de sesión MySQL son **por conexión** — no se mezclan entre usuarios.

### Defensa contra fallos

El trigger tiene un `DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN END;`. Esto significa que **si la auditoría falla por cualquier motivo** (caracteres raros, permisos, etc.), el handler silencia el error y el UPDATE de la unidad se completa igual.

> Filosofía: preferimos perder un registro de auditoría antes que bloquear el guardado del usuario.

Si auditando pasa algo raro y notás que faltan registros, el handler está silenciando algo. Para diagnosticar, comentar temporalmente el handler y reproducir el caso.

---

## Diagnóstico de problemas

### Síntoma: Guardar no guarda, la unidad queda con valores viejos

**Causa probable**: el trigger se está cayendo y no tiene el `EXIT HANDLER` instalado (versión vieja).

**Solución inmediata**:
```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
```
Y volver a correr el archivo `02_auditoria_unidades_trigger.sql` actualizado (que ya tiene el handler).

### Síntoma: Guardar funciona pero no aparece nada en la auditoría

**Diagnóstico**:
```sql
SELECT * FROM auditoria_unidades ORDER BY id_audit DESC LIMIT 5;
```

Si está vacío:

1. **Verificar trigger instalado**:
   ```sql
   SHOW TRIGGERS FROM asignacion LIKE 'asignaciones';
   ```
   Si no aparece, instalalo (paso 3 de la instalación).

2. **Verificar estructura de la tabla** (puede ser una versión vieja):
   ```sql
   SHOW COLUMNS FROM auditoria_unidades;
   ```
   Si NO tiene `cant_campos` y `movimiento`, es la versión vieja (v1). Hay que tirarla y recrearla:
   ```sql
   DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
   DROP TABLE IF EXISTS auditoria_unidades;
   ```
   Y volver a correr los 2 archivos SQL del repo.

3. **Probar JSON manualmente** sobre una unidad:
   ```sql
   SELECT JSON_OBJECT('o',observacion,'c',cliente) FROM asignaciones WHERE id_unidad = <id>;
   ```
   Si tira `Invalid JSON character`, hay UTF-8 inválido en algún campo TEXT (legacy data).

### Síntoma: el botón "Historial" abre la página vacía

**Causa probable**: `historial_unidad.php` está consultando una columna que no existe en la tabla — probablemente la tabla está en versión vieja.

Verificar como en el síntoma anterior. Si la estructura está mal, reinstalar.

### Síntoma: usuario aparece como `sistema` cuando no debería

**Causa**: El módulo desde el cual se editó la unidad no tiene el `func_mysql.php` actualizado para setear `@id_usuario`.

**Verificación**: la columna `origen` te dice qué script lo hizo. Si es algo de `dashboard_recursos/`, `ventas/`, etc., hay que parchear el `func_mysql.php` de ese módulo (copiar el bloque de `SET @id_usuario` desde `asignacion/funciones/func_mysql.php`).

### Logs útiles

- **PHP**: `apache_error.log` o `php_error.log` (en Laragon: `c:\laragon\logs\apache_error.log`).
- **MySQL**: log de errores del servidor (depende del hosting).
- **Auditoría**: `asignacion/api_log.txt` para los logs del script de purga.

---

## Rollback

### Desactivar la auditoría sin perder datos

```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
```

La tabla queda intacta, los datos hasta ahora se conservan, simplemente no se loggean cambios nuevos.

### Desinstalar todo

```sql
DROP TRIGGER IF EXISTS trg_asignaciones_audit_update;
DROP TABLE IF EXISTS auditoria_unidades;
```

Y revertir los `func_mysql.php` (o dejarlos — los `SET @id_usuario = ...` no rompen nada aunque queden, son inocuos sin trigger que los lea).

---

## Preguntas frecuentes

**¿Y si quiero ver qué pasó con TODAS las unidades, no de a una?**
Hoy la UI es solo por unidad (botón "Historial" en `unidad.php`). Si después se necesita un reporte global, se puede agregar a `dashboard_recursos/` con filtros por usuario / sucursal / rango de fechas. El dato está, falta la pantalla.

**¿La auditoría retrasa el guardado?**
Mínimamente. El trigger hace una suma sobre 47 columnas y, si hubo cambios, 1 INSERT. Para una sola fila es prácticamente imperceptible. Para cargas masivas (`guardar_carga_masiva.php`) sí puede acumular: si cargás 1000 unidades nuevas, son 1000 triggers ejecutándose. Aún así, en MySQL con buena configuración eso son < 1 segundo total.

**¿Puedo modificar manualmente la tabla `auditoria_unidades`?**
No conviene. La idea es que sea un registro inmutable de la realidad. Si hay un valor que se loggeó mal, dejá la fila como está y agregá un comentario en `observacion` de la unidad explicando.

**¿Qué pasa si el trigger se rompe en producción?**
Gracias al `EXIT HANDLER`, el peor escenario es que **dejen de generarse registros de auditoría** (silenciosamente). Los guardados siguen funcionando. Si notás que la auditoría dejó de loggear, comentar el handler y revisar el error.

**¿Cuánto crece la tabla?**
Depende del uso. Estimación pesimista: 200 unidades editadas/día × 5 cambios promedio por edición × 365 días = ~370 mil filas/año. Con el JSON pesa unos pocos KB cada una. La purga de 6 meses post-entrega mantiene el tamaño acotado.

**¿Funciona en MariaDB?**
Sí, desde MariaDB 10.2.7 (que tiene el tipo JSON y `JSON_OBJECT` / `JSON_ARRAY_APPEND`). En versiones anteriores hay que portar el trigger a `LONGTEXT` con concat manual.

**¿Qué pasa con los scripts automáticos como `a_script_levantar.php`?**
Quedan registrados con `usuario = 'sistema'`, `id_usuario = 0`, y el `origen` te dice qué script fue (`a_script_levantar.php`, `a_script_asignar.php`, etc.). Útil para distinguir cambios automáticos de cambios manuales.

**¿Tengo que hacer algo cuando agrego una columna nueva a `asignaciones`?**
Solo si querés que esa columna sea auditada. Si la nueva columna te interesa rastrear:
1. Editar `02_auditoria_unidades_trigger.sql` y agregar la columna en los dos lugares (cálculo de `v_count` y bloque `IF`).
2. Reejecutar el archivo en la DB.
3. Opcional: agregar la etiqueta amigable en `historial_unidad.php`.
