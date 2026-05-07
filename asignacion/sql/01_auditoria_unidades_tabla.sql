-- =====================================================================
-- Auditoría de cambios en `asignaciones` (tabla única)
-- =====================================================================
-- 1 fila = 1 Guardar que modificó al menos un campo.
-- Todo el delta queda en la columna JSON `movimiento` como un array de
-- objetos {campo, antes, despues}.
--
-- Llenada automáticamente por el trigger `trg_asignaciones_audit_update`.
-- Ejecutar UNA SOLA VEZ. Reejecutar NO borra datos (IF NOT EXISTS).
--
-- Requisito: MySQL 5.7+ o MariaDB 10.2.7+ (uso del tipo JSON nativo y
-- de las funciones JSON_OBJECT / JSON_ARRAY_APPEND en el trigger).
-- =====================================================================

CREATE TABLE IF NOT EXISTS `auditoria_unidades` (
  `id_audit`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_unidad`    INT NOT NULL,
  `nro_unidad`   INT NULL,
  `fecha`        DATE NOT NULL,
  `hora`         TIME NOT NULL,
  `id_usuario`   INT NOT NULL DEFAULT 0,
  `usuario`      VARCHAR(64) NOT NULL DEFAULT 'sistema',
  `origen`       VARCHAR(96) NULL,
  `cant_campos`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `movimiento`   JSON NOT NULL,
  PRIMARY KEY (`id_audit`),
  KEY `idx_id_unidad` (`id_unidad`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
