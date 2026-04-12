-- ============================================================
-- Módulo: Encuesta de Satisfacción 0km
-- Tablas con prefijo enc_
-- Ejecutar una sola vez en la base de datos 'asignacion'
-- ============================================================

CREATE TABLE IF NOT EXISTS enc_encuestas (
  id_encuesta        INT AUTO_INCREMENT PRIMARY KEY,
  nombre             VARCHAR(200) NOT NULL,
  descripcion        TEXT,
  mensaje_bienvenida TEXT,
  activa             TINYINT(1) NOT NULL DEFAULT 0,
  fecha_creacion     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  baja               TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_areas (
  id_area   INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(80) NOT NULL,
  color     VARCHAR(10) NOT NULL DEFAULT '#607d8b',
  nro_orden TINYINT NOT NULL DEFAULT 99,
  UNIQUE KEY uk_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO enc_areas (nombre, color, nro_orden) VALUES
  ('General',        '#607d8b', 1),
  ('Concesionario',  '#1a5276', 2),
  ('Marca',          '#7b1fa2', 3),
  ('Administrativa', '#2e7d32', 4),
  ('Entregas',       '#e65100', 5),
  ('Créditos',       '#4e342e', 6),
  ('Ventas',         '#0277bd', 7);

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_preguntas (
  id_pregunta      INT AUTO_INCREMENT PRIMARY KEY,
  id_encuesta      INT NOT NULL,
  nro_orden        INT NOT NULL DEFAULT 0,
  texto_pregunta   TEXT NOT NULL,
  tipo_pregunta    TINYINT(1) NOT NULL DEFAULT 1,
  -- 1 = escala 1 a 10
  -- 2 = si / no simple
  -- 3 = selección múltiple (checkboxes)
  -- 4 = lista si/no (sub-ítems)
  -- 5 = texto libre / observaciones
  pondera          TINYINT(1) NOT NULL DEFAULT 1,
  -- tipos 4 y 5: siempre 0. tipos 1,2,3: configurable
  id_area          INT NULL DEFAULT NULL,         -- área responsable (enc_areas)
  es_observacion   TINYINT(1) NOT NULL DEFAULT 0,
  -- lógica condicional (en la pregunta DESTINO):
  -- "Mostrar si respuesta a [cond_id_preg_ref] [cond_operador] [cond_valor]"
  cond_id_preg_ref INT DEFAULT NULL,
  cond_operador    VARCHAR(5) DEFAULT NULL,  -- '<','<=','=','>=','>','!='
  cond_valor       VARCHAR(50) DEFAULT NULL,
  baja             TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_encuesta) REFERENCES enc_encuestas(id_encuesta),
  FOREIGN KEY (id_area)     REFERENCES enc_areas(id_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_opciones (
  id_opcion    INT AUTO_INCREMENT PRIMARY KEY,
  id_pregunta  INT NOT NULL,
  texto_opcion VARCHAR(300) NOT NULL,
  nro_orden    INT NOT NULL DEFAULT 0,
  baja         TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_pregunta) REFERENCES enc_preguntas(id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_tokens (
  id_token       INT AUTO_INCREMENT PRIMARY KEY,
  token          VARCHAR(64) NOT NULL,
  id_asignacion  INT NOT NULL,
  id_encuesta    INT NOT NULL,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta DATETIME DEFAULT NULL,
  completada     TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uk_token       (token),
  UNIQUE KEY uk_asignacion  (id_asignacion),
  FOREIGN KEY (id_encuesta) REFERENCES enc_encuestas(id_encuesta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_respuestas (
  id_respuesta       INT AUTO_INCREMENT PRIMARY KEY,
  id_token           INT NOT NULL,
  id_asignacion      INT NOT NULL,
  id_encuesta        INT NOT NULL,
  fecha_completada   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resultado_promedio DECIMAL(4,2) DEFAULT NULL,
  UNIQUE KEY uk_token (id_token),
  FOREIGN KEY (id_token) REFERENCES enc_tokens(id_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_respuestas_detalle (
  id_detalle      INT AUTO_INCREMENT PRIMARY KEY,
  id_respuesta    INT NOT NULL,
  id_pregunta     INT NOT NULL,
  respuesta_valor DECIMAL(4,2) DEFAULT NULL,
  respuesta_texto TEXT DEFAULT NULL,
  mostrada        TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (id_respuesta) REFERENCES enc_respuestas(id_respuesta),
  FOREIGN KEY (id_pregunta)  REFERENCES enc_preguntas(id_pregunta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS enc_respuestas_opciones (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  id_detalle    INT NOT NULL,
  id_opcion     INT NOT NULL,
  valor_elegido TINYINT(1) NOT NULL DEFAULT 1,
  -- tipo 3 selección múltiple: 1 = seleccionada
  -- tipo 4 lista si/no: 1 = sí, 0 = no
  FOREIGN KEY (id_detalle) REFERENCES enc_respuestas_detalle(id_detalle),
  FOREIGN KEY (id_opcion)  REFERENCES enc_opciones(id_opcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
