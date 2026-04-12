-- ============================================================
-- Carga inicial: Encuesta de Satisfacción Toyota - Derka y Vargas S.A.
-- ============================================================

SET NAMES utf8;

-- ── 1. Recuperar IDs de áreas (ya creadas por crear_tablas.sql) ──
SELECT id_area INTO @id_general        FROM enc_areas WHERE nombre = 'General'        LIMIT 1;
SELECT id_area INTO @id_concesionario  FROM enc_areas WHERE nombre = 'Concesionario'  LIMIT 1;
SELECT id_area INTO @id_marca          FROM enc_areas WHERE nombre = 'Marca'          LIMIT 1;
SELECT id_area INTO @id_administrativa FROM enc_areas WHERE nombre = 'Administrativa' LIMIT 1;
SELECT id_area INTO @id_entregas       FROM enc_areas WHERE nombre = 'Entregas'       LIMIT 1;

-- ── 2. Encuesta ───────────────────────────────────────────────
INSERT INTO enc_encuestas (nombre, descripcion, mensaje_bienvenida, activa) VALUES (
  'Encuesta de Satisfacción — Entrega 0km',
  'Encuesta de satisfacción post-entrega para clientes que retiraron su unidad 0km.',
  'En Derka y Vargas S.A., concesionario oficial Toyota, nos comprometemos a brindarte la mejor experiencia en cada etapa de tu compra. Nos gustaría conocer tu opinión sobre la atención recibida durante la entrega de tu nuevo vehículo. Tu respuesta es completamente confidencial y nos ayuda a seguir mejorando el servicio que merecés. La encuesta es breve y no te llevará más de 2 minutos. ¡Muchas gracias por confiar en nosotros!',
  1
);

SET @id_enc = LAST_INSERT_ID();

-- ── 3. Preguntas ──────────────────────────────────────────────
-- Concesionario (5 preguntas)
INSERT INTO enc_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, id_area) VALUES
(@id_enc, 1, '¿Cómo calificaría la atención de su asesor comercial?',        1, 1, @id_concesionario),
(@id_enc, 2, 'Rapidez en la atención',                                        1, 1, @id_concesionario),
(@id_enc, 3, 'Conocimiento del vehículo',                                     1, 1, @id_concesionario),
(@id_enc, 4, 'Claridad en las condiciones comerciales',                       1, 1, @id_concesionario),
(@id_enc, 5, 'Cumplimiento de la fecha de entrega prometida',                 1, 1, @id_concesionario);

-- Administrativa (3 preguntas)
INSERT INTO enc_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, id_area) VALUES
(@id_enc, 6, '¿Cómo calificaría la atención del personal administrativo?',   1, 1, @id_administrativa),
(@id_enc, 7, 'La información sobre el trámite fue clara y detallada',         1, 1, @id_administrativa),
(@id_enc, 8, 'Las consultas fueron respondidas a tiempo',                     1, 1, @id_administrativa);

-- Entregas (2 preguntas)
INSERT INTO enc_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, id_area) VALUES
(@id_enc, 9,  '¿Cómo calificaría la atención del responsable de entregas?',  1, 1, @id_entregas),
(@id_enc, 10, '¿Se cumplieron los siguientes puntos en la entrega?',          4, 0, @id_entregas);

-- General y Marca (2 preguntas)
INSERT INTO enc_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, id_area) VALUES
(@id_enc, 11, '¿Qué probabilidad hay de que recomiende nuestra concesionaria a un familiar o amigo?', 1, 1, @id_general),
(@id_enc, 12, '¿Cómo calificaría su satisfacción general con la marca Toyota?',                       1, 1, @id_marca);

-- Observaciones (1 pregunta, sin área)
INSERT INTO enc_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, es_observacion) VALUES
(@id_enc, 13, '¿Tiene algún comentario o sugerencia para ayudarnos a mejorar?', 5, 0, 1);

-- ── 4. Opciones de pregunta 10 (Lista Sí/No) ─────────────────
SELECT id_pregunta INTO @id_p10
FROM enc_preguntas
WHERE id_encuesta = @id_enc AND nro_orden = 10
LIMIT 1;

INSERT INTO enc_opciones (id_pregunta, texto_opcion, nro_orden) VALUES
(@id_p10, 'Se respetó el horario de entrega acordado',               1),
(@id_p10, 'El vehículo fue entregado en perfectas condiciones',      2),
(@id_p10, 'Se explicaron los cuidados y mantenimiento del vehículo', 3),
(@id_p10, 'Se informó sobre los términos de garantía',               4),
(@id_p10, 'Se informó sobre el primer service (1.000 km)',           5),
(@id_p10, 'Se lo invitó a unirse a Toyota Club',                     6),
(@id_p10, 'Se explicó el uso de la aplicación Toyota',               7);

-- ── Verificación ──────────────────────────────────────────────
SELECT 'Áreas cargadas:'    AS resultado, COUNT(*) AS total FROM enc_areas;
SELECT 'Encuestas activas:' AS resultado, COUNT(*) AS total FROM enc_encuestas WHERE activa = 1;
SELECT 'Preguntas:'         AS resultado, COUNT(*) AS total FROM enc_preguntas WHERE baja = 0;
SELECT 'Opciones (p10):'    AS resultado, COUNT(*) AS total FROM enc_opciones WHERE id_pregunta = @id_p10;
