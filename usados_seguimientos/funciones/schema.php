<?php
/*
 * Crea las tablas del módulo si no existen (idéntico DDL al de usados_docs/_init.php).
 * El módulo nuevo comparte tablas con el viejo; esto sólo garantiza que existan
 * cuando se entra primero por acá. Requiere: $con.
 */

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_items` (
  `id_item`     int(11)      NOT NULL AUTO_INCREMENT,
  `nombre`      varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo`      tinyint(1)   NOT NULL DEFAULT 1,
  `posicion`    int(11)      NOT NULL DEFAULT 0,
  `created_at`  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_seguimiento` (
  `id`          int(11)    NOT NULL AUTO_INCREMENT,
  `id_unidad`   int(11)    NOT NULL,
  `id_item`     int(11)    NOT NULL,
  `estado`      tinyint(1) NOT NULL DEFAULT 0,
  `id_usuario`  int(11)    DEFAULT NULL,
  `observacion` text       DEFAULT NULL,
  `archivo`     varchar(255) DEFAULT NULL,
  `updated_at`  timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_unidad_item` (`id_unidad`, `id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_historial` (
  `id`               int(11)    NOT NULL AUTO_INCREMENT,
  `id_unidad`        int(11)    NOT NULL,
  `id_item`          int(11)    NOT NULL,
  `estado_anterior`  tinyint(1) DEFAULT NULL,
  `estado_nuevo`     tinyint(1) NOT NULL,
  `id_usuario`       int(11)    NOT NULL,
  `fecha`            datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacion`      text       DEFAULT NULL,
  `archivo`          varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unidad_item` (`id_unidad`, `id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_archivos` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `id_unidad`   int(11)      NOT NULL,
  `id_item`     int(11)      NOT NULL,
  `archivo`     varchar(255) NOT NULL,
  `id_usuario`  int(11)      DEFAULT NULL,
  `fecha`       datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_unidad_item` (`id_unidad`, `id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
