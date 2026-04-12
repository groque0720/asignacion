# Módulo: Encuesta de Satisfacción 0km

## Propósito
Sistema de encuesta de satisfacción post-entrega para clientes que retiraron su unidad 0km.
Permite generar un link/QR único por entrega, que el cliente completa desde su celular o PC de forma pública (sin login). Una vez completada, el link queda obsoleto.

---

## Stack Técnico (consistente con el resto del proyecto)
- **Backend**: PHP procedural + MySQLi (sin ORM, sin frameworks)
- **Frontend**: jQuery 2.1.3 (copia local), jQuery UI cuando se necesite
- **CSS**: Sistema custom `roquesystem.css` + `iconos.css` (sin Bootstrap)
- **Alertas**: SweetAlert (copia local en `alertas_query/`)
- **Base de datos**: MySQL (conexión via `../config/config_mysql.php`)
- **Auth**: Sesión PHP (`$_SESSION["autentificado"] == "SI"`)
- **QR**: Google Charts API (online, requiere internet — servidor lo tiene)

---

## Fuente de Datos Principal

Unidades entregadas desde `asignaciones` JOIN `usuarios`:
```sql
SELECT asignaciones.*, usuarios.nombre AS asesor
FROM asignaciones
JOIN usuarios ON asignaciones.id_asesor = usuarios.idusuario
WHERE asignaciones.entregada = 1
  AND asignaciones.borrar = 0
  AND asignaciones.guardado = 1
  AND asignaciones.fec_entrega >= '2023/01/01'
ORDER BY asignaciones.fec_entrega DESC
```

**Columna `con_encuesta`** en `asignaciones` — estados:
- `0` = sin encuesta generada
- `1` = token generado, pendiente de respuesta
- `2` = encuesta completada por el cliente

Se actualiza a `1` automáticamente al abrir `encuesta_puente.php`.
Se actualiza a `2` al guardar las respuestas del cliente.

---

## Tablas Nuevas a Crear (prefijo `enc_`)

### `enc_encuestas` — Definición de encuesta
```sql
CREATE TABLE enc_encuestas (
  id_encuesta     INT AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(200) NOT NULL,
  descripcion     TEXT,
  mensaje_bienvenida TEXT,                        -- intro antes de la primera pregunta
  activa          TINYINT(1) DEFAULT 0,           -- solo 1 puede estar activa
  fecha_creacion  DATETIME DEFAULT CURRENT_TIMESTAMP,
  baja            TINYINT(1) DEFAULT 0
);
```

### `enc_preguntas` — Preguntas de la encuesta
```sql
CREATE TABLE enc_preguntas (
  id_pregunta       INT AUTO_INCREMENT PRIMARY KEY,
  id_encuesta       INT NOT NULL,
  nro_orden         INT NOT NULL,
  texto_pregunta    TEXT NOT NULL,
  tipo_pregunta     TINYINT(1) NOT NULL,
    -- 1 = escala 1 a 10
    -- 2 = si/no simple  (si=10, no=0 para ponderación)
    -- 3 = selección múltiple (opciones en enc_opciones, pondera por % seleccionadas)
    -- 4 = lista si/no (sub-items en enc_opciones, NO pondera)
    -- 5 = texto libre / observaciones (NO pondera)
  pondera           TINYINT(1) DEFAULT 1,
    -- tipo 4 y tipo 5: siempre 0
    -- tipo 3: pondera como % de opciones seleccionadas → escala 1-10
    -- tipo 1 y 2: pondera si = 1
  es_observacion    TINYINT(1) DEFAULT 0,         -- marca la pregunta de comentarios
  -- Lógica condicional (en la pregunta DESTINO):
  -- "Mostrar esta pregunta SI [preg_ref] [operador] [valor]"
  cond_id_preg_ref  INT DEFAULT NULL,             -- id_pregunta de referencia
  cond_operador     VARCHAR(5) DEFAULT NULL,       -- '<', '<=', '=', '>=', '>', '!='
  cond_valor        VARCHAR(50) DEFAULT NULL,      -- valor a comparar (ej: '7')
  -- NULL en cond_id_preg_ref = sin condición, siempre mostrar
  baja              TINYINT(1) DEFAULT 0,
  FOREIGN KEY (id_encuesta) REFERENCES enc_encuestas(id_encuesta)
);
```

### `enc_opciones` — Opciones para tipo 3 y tipo 4
```sql
CREATE TABLE enc_opciones (
  id_opcion       INT AUTO_INCREMENT PRIMARY KEY,
  id_pregunta     INT NOT NULL,
  texto_opcion    VARCHAR(300) NOT NULL,
  nro_orden       INT DEFAULT 0,
  baja            TINYINT(1) DEFAULT 0,
  FOREIGN KEY (id_pregunta) REFERENCES enc_preguntas(id_pregunta)
);
```

### `enc_tokens` — Token único por entrega
```sql
CREATE TABLE enc_tokens (
  id_token        INT AUTO_INCREMENT PRIMARY KEY,
  token           VARCHAR(64) NOT NULL UNIQUE,    -- SHA256 único
  id_asignacion   INT NOT NULL,                   -- asignaciones.id_unidad
  id_encuesta     INT NOT NULL,
  fecha_creacion  DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta DATETIME DEFAULT NULL,
  completada      TINYINT(1) DEFAULT 0,
  UNIQUE KEY uk_asignacion (id_asignacion),       -- 1 token por entrega
  FOREIGN KEY (id_encuesta) REFERENCES enc_encuestas(id_encuesta)
);
```

### `enc_respuestas` — Cabecera de respuesta
```sql
CREATE TABLE enc_respuestas (
  id_respuesta       INT AUTO_INCREMENT PRIMARY KEY,
  id_token           INT NOT NULL UNIQUE,
  id_asignacion      INT NOT NULL,
  id_encuesta        INT NOT NULL,
  fecha_completada   DATETIME DEFAULT CURRENT_TIMESTAMP,
  resultado_promedio DECIMAL(4,2) DEFAULT NULL,
  FOREIGN KEY (id_token) REFERENCES enc_tokens(id_token)
);
```

### `enc_respuestas_detalle` — Respuesta por pregunta
```sql
CREATE TABLE enc_respuestas_detalle (
  id_detalle      INT AUTO_INCREMENT PRIMARY KEY,
  id_respuesta    INT NOT NULL,
  id_pregunta     INT NOT NULL,
  respuesta_valor DECIMAL(4,2) DEFAULT NULL,      -- escala 1-10, si/no, % múltiple
  respuesta_texto TEXT DEFAULT NULL,               -- texto libre
  mostrada        TINYINT(1) DEFAULT 1,            -- 0=omitida por condición
  FOREIGN KEY (id_respuesta) REFERENCES enc_respuestas(id_respuesta),
  FOREIGN KEY (id_pregunta) REFERENCES enc_preguntas(id_pregunta)
);
```

### `enc_respuestas_opciones` — Opciones marcadas (tipo 3 y 4)
```sql
CREATE TABLE enc_respuestas_opciones (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  id_detalle      INT NOT NULL,
  id_opcion       INT NOT NULL,
  valor_elegido   TINYINT(1) DEFAULT 1,           -- tipo 3: 1=seleccionada; tipo 4: 1=si, 0=no
  FOREIGN KEY (id_detalle) REFERENCES enc_respuestas_detalle(id_detalle),
  FOREIGN KEY (id_opcion) REFERENCES enc_opciones(id_opcion)
);
```

---

## Reglas de Ponderación

| Tipo | Pondera | Valor al promedio |
|------|---------|-------------------|
| Escala 1-10 | Sí (si `pondera=1`) | El valor directo (1-10) |
| Si/No simple | Sí (si `pondera=1`) | Sí=10, No=0 |
| Selección múltiple | Sí (si `pondera=1`) | (seleccionadas / total_opciones) × 10 |
| Lista Si/No | NO (siempre `pondera=0`) | No entra al promedio |
| Texto libre | NO (siempre `pondera=0`) | No entra al promedio |
| Omitida por condición | NO | No entra al promedio aunque `pondera=1` |

**Promedio final** = suma(valores ponderados de preguntas mostradas con pondera=1) / count(preguntas mostradas con pondera=1)

---

## Lógica Condicional

La condición se almacena en la **pregunta destino** (la que puede omitirse):
- `cond_id_preg_ref`: qué pregunta ya respondida se evalúa
- `cond_operador` + `cond_valor`: la comparación

Ejemplo: Pregunta 2 tiene `cond_id_preg_ref=1`, `cond_operador='<'`, `cond_valor='7'`
→ Se muestra solo si la respuesta a la pregunta 1 fue < 7

**Evaluación en JS** (cliente, en tiempo real):
- Al responder cada pregunta y avanzar, JS recalcula qué preguntas del resto aplican mostrar
- Las preguntas omitidas se marcan `mostrada=0` en el detalle al guardar
- El orden de evaluación sigue el `nro_orden`

---

## Diseño de la Encuesta Pública (Mobile-First)

- **Una pregunta por pantalla** — ocupa todo el viewport
- **Indicador de avance** — barra de progreso + "Pregunta X de Y" (Y = total visibles estimado)
- **Sin scroll vertical** — todo en una pantalla
- **Botón "Siguiente"** — avanza; en la última pregunta dice "Finalizar"
- **Botón "Anterior"** — permite retroceder y cambiar respuesta
- **Diseño limpio y grande** — tipografía generosa, botones táctiles cómodos
- **Pantalla de bienvenida** — si hay `mensaje_bienvenida`, se muestra antes de la pregunta 1
- **Pantalla de agradecimiento** — al finalizar, mensaje de cierre

---

## Perfiles con Acceso al Panel Admin

Por ahora: `idperfil IN (1, 5)`
- Perfil 1: admin general
- Perfil 5: operador asignación

Futuro (no implementar todavía):
- Asesores: ver sus propias encuestas
- Gerentes de sucursal: ver encuestas de su sucursal

---

## Estructura de Archivos del Módulo

```
encuesta/
├── CLAUDE.md
├── index.php                       # Panel principal con nav (requiere auth)
├── funciones/
│   └── func_mysql.php
├── css/
│   ├── encuesta_admin.css          # Estilos panel interno
│   └── encuesta_publica.css        # Estilos encuesta mobile-first
├── js/
│   └── jquery-2.1.3.min.js
├── alertas_query/                  # SweetAlert (copiar)
├── en_proceso/                     # Loading (copiar)
│
├── -- CONFIG URL --
├── config.php                      # Constante BASE_URL para links de encuesta
│
├── -- LISTA DE ENTREGAS --
├── entregas.php                    # Lista de unidades entregadas + estado encuesta
├── entregas_contenido.php          # Tabla (include)
│
├── -- PÁGINA PUENTE --
├── puente.php                      # Info cliente + link + QR (requiere auth)
├── puente_generar_token.php        # AJAX: genera token al cargar puente
│
├── -- CONFIGURACIÓN DE ENCUESTA --
├── config_encuestas.php            # Lista encuestas
├── config_encuesta_form.php        # Crear/editar encuesta
├── config_encuesta_guardar.php     # AJAX handler
├── config_encuesta_activar.php     # AJAX: activar/desactivar encuesta
├── config_preguntas.php            # Lista preguntas de una encuesta
├── config_pregunta_form.php        # Crear/editar pregunta + opciones + condición
├── config_pregunta_guardar.php     # AJAX handler
├── config_pregunta_orden.php       # AJAX: reordenar preguntas (drag or arrows)
├── config_pregunta_baja.php        # AJAX: dar de baja pregunta
├── config_opcion_guardar.php       # AJAX: agregar/editar opción
├── config_opcion_baja.php          # AJAX: dar de baja opción
│
├── -- ENCUESTA PÚBLICA (sin auth) --
├── responder.php                   # Encuesta pública por token (?t=TOKEN)
├── responder_guardar.php           # POST: guarda respuestas + calcula promedio
├── gracias.php                     # Pantalla final post-encuesta
├── expirada.php                    # Token inválido o ya completado
│
└── -- RESULTADOS --
    ├── resultados.php              # Lista de encuestas completadas + promedio
    ├── resultado_detalle.php       # Detalle de respuestas de una entrega
    └── resultado_pdf.php           # Genera PDF del reporte
```

---

## Flujo Completo

### Flujo Admin

1. Admin entra a `entregas.php` → ve lista de entregas con estado (sin generar / pendiente / completada)
2. Hace clic en una entrega → va a `puente.php?id=X`
3. `puente.php` llama via AJAX a `puente_generar_token.php` → genera token si no existe → actualiza `asignaciones.con_encuesta = 1`
4. Puente muestra: datos del cliente + vehículo, link copiable, botón abrir link, imagen QR
5. Admin entrega la tablet al cliente O copia el link para enviárselo después

### Flujo Cliente

1. Cliente abre link `responder.php?t=TOKEN`
2. PHP valida token: si inválido o completado → redirige a `expirada.php`
3. Muestra pantalla de bienvenida (si tiene mensaje)
4. Una pregunta por pantalla con botones Anterior/Siguiente
5. JS evalúa condiciones al avanzar
6. Al finalizar → POST a `responder_guardar.php`
7. PHP guarda respuestas, calcula promedio, marca token como completado, actualiza `asignaciones.con_encuesta = 2`
8. Redirige a `gracias.php`

---

## URL de Encuesta

Configurada en `config.php`:
```php
define('BASE_URL_ENCUESTA', 'http://localhost/encuesta/responder.php');
// Producción: define('BASE_URL_ENCUESTA', 'https://dominio.com/encuesta/responder.php');
```

Link final: `BASE_URL_ENCUESTA . '?t=' . $token`

---

## QR Code

Usar **Google Charts API**:
```
https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=[URL_ENCODEADA]
```
Simple, sin dependencias, siempre disponible con internet.

---

## Reporte PDF

- Librería: FPDF (ya existe en otros módulos, copiar)
- Contenido del reporte: datos del cliente/vehículo, fecha, resultado promedio, detalle por pregunta
- Acceso desde `resultados.php` y desde `resultado_detalle.php`

---

## Áreas Responsables

Cada pregunta puede tener asignada un **área responsable** (`enc_areas`):
- `enc_areas`: `id_area`, `nombre`, `color` (hex), `nro_orden`
- `enc_preguntas.id_area`: FK nullable — NULL = sin área asignada
- Gestión desde `config_areas.php` (acceso desde el botón "Áreas" en la pestaña Configurar)
- Áreas predefinidas: General, Concesionario, Marca, Administrativa, Entregas, Créditos

**Impacto en resultados:**
- `resultado_detalle.php`: badge de área por pregunta + tarjetas de promedio por área
- `_tab_resultados.php`: tarjetas de promedio global por área (todas las respuestas)
- `resultado_pdf.php`: área mostrada como etiqueta junto al tipo de pregunta

**Migración en instalaciones existentes:** ejecutar `sql/agregar_areas.sql`

---

## Pendientes / Decisiones Tomadas

| Punto | Decisión |
|-------|----------|
| `con_encuesta` | 0=sin generar, 1=pendiente, 2=completada |
| Generación de token | Automática al abrir `puente.php` |
| Perfiles de acceso | `idperfil IN (1, 5, 14)` |
| Lógica condicional | Condición en pregunta destino, evaluada en JS |
| Selección múltiple ponderación | (seleccionadas/total) × 10 |
| Lista si/no ponderación | NO pondera |
| QR | Google Charts API |
| Resultados | Lista + detalle (con resumen por área) + PDF |
| URL | Constante `BASE_URL_ENCUESTA` en `config.php` |
| Diseño encuesta pública | Mobile-first, 1 pregunta por pantalla, progress bar, botones Anterior/Siguiente |
| Áreas responsables | Tabla `enc_areas`, FK en `enc_preguntas.id_area` (nullable) |

---

## Estado del Desarrollo

- [x] Análisis y diseño
- [x] CLAUDE.md creado
- [x] `sql/crear_tablas.sql` (incluye enc_areas + id_area en enc_preguntas)
- [x] `sql/agregar_areas.sql` — migración para instancias existentes
- [x] `funciones/func_mysql.php`
- [x] `config.php` (BASE_URL_ENCUESTA, perfiles, fecha_desde)
- [x] `index.php` — shell con cabecera + nav (Entregas / Configurar / Resultados)
- [x] `_tab_entregas.php` — tabla de entregas con estado encuesta
- [x] `_tab_config.php` — lista + form inline de encuestas + botón Áreas
- [x] `_tab_resultados.php` — tabla de resultados + promedio global + promedios por área
- [x] `puente.php` — página puente (datos cliente + link + QR)
- [x] `puente_generar_token.php` — AJAX: genera token, actualiza con_encuesta=1
- [x] `config_encuesta_guardar.php` / `config_encuesta_activar.php` / `config_encuesta_baja.php`
- [x] `config_areas.php` — gestión de áreas (CRUD)
- [x] `config_area_guardar.php` / `config_area_baja.php`
- [x] `config_preguntas.php` — lista de preguntas con badge de área
- [x] `config_pregunta_form.php` — crear/editar pregunta (tipo, área, pondera, opciones, condición)
- [x] `config_pregunta_guardar.php` / `config_pregunta_baja.php` / `config_pregunta_orden.php`
- [x] `config_opcion_guardar.php` / `config_opcion_baja.php`
- [x] `responder.php` — encuesta pública (slides, lógica condicional JS, progreso)
- [x] `responder_guardar.php` — guarda respuestas, calcula promedio, actualiza con_encuesta=2
- [x] `gracias.php` / `expirada.php`
- [x] `resultado_detalle.php` — detalle de respuestas + badge área + resumen por área
- [x] `resultado_pdf.php` — genera PDF con FPDF (incluye área en cada pregunta)

## Próximos pasos sugeridos
- [ ] Ejecutar `sql/crear_tablas.sql` (nueva instalación) O `sql/agregar_areas.sql` (instancia existente)
- [ ] Ajustar `BASE_URL_ENCUESTA` en `config.php` según el entorno
- [ ] Prueba completa: crear encuesta → configurar áreas → agregar preguntas → generar link → responder → ver resultado
- [ ] Dashboard de resultados avanzado (gráficos, filtros por sucursal/asesor/fecha)
- [ ] Acceso de asesores a sus propias encuestas (idperfil adicionales)
