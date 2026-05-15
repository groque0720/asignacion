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
-- Requisito: MariaDB 10.0+ o MySQL 5.5+. No usa el tipo JSON nativo ni
-- funciones JSON (`JSON_OBJECT`, etc.) para soportar el VPS de producción
-- que corre MariaDB 10.1. El trigger arma el JSON con CONCAT + REPLACE.
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
  `movimiento`   LONGTEXT NOT NULL,
  PRIMARY KEY (`id_audit`),
  KEY `idx_id_unidad` (`id_unidad`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
