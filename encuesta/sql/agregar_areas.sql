-- ============================================================
-- Migración: Áreas responsables para preguntas de encuesta
-- Ejecutar UNA SOLA VEZ en instalaciones existentes del módulo
-- ============================================================

CREATE TABLE IF NOT EXISTS enc_areas (
  id_area   INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(80) NOT NULL,
  color     VARCHAR(10) NOT NULL DEFAULT '#607d8b',
  nro_orden TINYINT NOT NULL DEFAULT 99
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO enc_areas (nombre, color, nro_orden) VALUES
  ('General',        '#607d8b', 1),
  ('Concesionario',  '#1a5276', 2),
  ('Marca',          '#7b1fa2', 3),
  ('Administrativa', '#2e7d32', 4),
  ('Entregas',       '#e65100', 5),
  ('Créditos',       '#4e342e', 6),
  ('Ventas',         '#0277bd', 7);

ALTER TABLE enc_preguntas
  ADD COLUMN id_area INT NULL DEFAULT NULL AFTER pondera,
  ADD CONSTRAINT fk_preg_area FOREIGN KEY (id_area) REFERENCES enc_areas(id_area);
