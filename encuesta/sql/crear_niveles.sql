-- Tabla de niveles de satisfacción configurables
CREATE TABLE IF NOT EXISTS enc_niveles (
  id_nivel    INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(80)   NOT NULL,
  valor_desde DECIMAL(4,2)  NOT NULL,
  valor_hasta DECIMAL(4,2)  NOT NULL,
  color       VARCHAR(10)   NOT NULL DEFAULT '#607d8b'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Niveles predeterminados
INSERT IGNORE INTO enc_niveles (nombre, valor_desde, valor_hasta, color) VALUES
  ('Alta satisfacción', 9.0, 10.0, '#1e8449'),
  ('Satisfactorio',     7.0,  8.9, '#1a7abf'),
  ('Regular',           5.0,  6.9, '#d68910'),
  ('A mejorar',         0.0,  4.9, '#c0392b');
