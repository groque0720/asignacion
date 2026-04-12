---
name: Módulo Encuesta de Satisfacción 0km
description: Nuevo módulo en carpeta /encuesta — sistema de encuesta post-entrega para clientes 0km
type: project
---

Sistema de encuesta de satisfacción para clientes que retiraron su unidad 0km.

**Why:** El usuario quiere medir la satisfacción del cliente post-entrega mediante un link/QR único público, con encuestas configurables (preguntas ponderadas, condicionales, múltiple opción).

**How to apply:** Al desarrollar este módulo, seguir el CLAUDE.md en `/encuesta/CLAUDE.md`. Siempre consultar ese archivo antes de escribir código del módulo.

Puntos clave:
- Carpeta: `/encuesta/`
- Tablas nuevas con prefijo `enc_` (NO tocar tablas existentes sin confirmación)
- `asignaciones.con_encuesta` — uso pendiente de confirmar con usuario
- Solo 1 encuesta activa a la vez
- Encuesta pública (sin login) via token único en URL
- Stack: PHP procedural + MySQLi + jQuery 2.1.3 + CSS roquesystem (sin Bootstrap)
