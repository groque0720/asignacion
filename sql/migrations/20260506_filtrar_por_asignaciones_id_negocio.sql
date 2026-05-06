-- ============================================================================
-- Migración: cambiar filtro de negocio en las 4 vistas del dashboard
-- De:  usuarios.id_negocio = 1   (negocio del asesor)
-- A:   asignaciones.id_negocio = 1   (negocio de la asignación)
--
-- Motivo: 107 unidades tienen su asesor con usuarios.id_negocio = NULL
--         pero asignaciones.id_negocio = 1. Quedaban excluidas del dashboard
--         pese a pertenecer al negocio. Filtrando por la asignación se incluyen.
--
-- Backup previo: _backup_views_YYYYMMDD_HHMMSS.sql en este mismo directorio.
-- Para revertir: ejecutar el archivo de backup correspondiente.
-- ============================================================================

-- 1) MAESTRA --------------------------------------------------------------
ALTER VIEW view_asignaciones_saldo_pendiente_corregida AS
SELECT
    `asignaciones`.`nro_unidad`     AS `NroUn.`,
    `meses`.`mes`                   AS `Mes`,
    `asignaciones`.`año`            AS `Año`,
    `grupos`.`grupo`                AS `Modelo`,
    `modelos`.`modelo`              AS `Versión`,
    `asignaciones`.`nro_orden`      AS `NroOrden`,
    `asignaciones`.`interno`        AS `Interno`,
    `asignaciones`.`chasis`         AS `Chasis`,
    `asignaciones`.`cliente`        AS `Cliente`,
    `usuarios`.`nombre`             AS `Asesor`,
    `sucursales`.`sucursal`         AS `Sucursal`,
    `asignaciones`.`fec_reserva`    AS `Reserva`,
    `asignaciones`.`fec_arribo`     AS `Arribo`,
    `asignaciones`.`fec_despacho`   AS `Despacho`,
    `asignaciones`.`estado_tasa`    AS `Confirmada TASA`,
    `asignaciones`.`cancelada`      AS `Cancelada`,
    `asignaciones`.`pagado`         AS `pagado_tasa`,
    `modelos`.`costo`               AS `Costo TASA`,
    `reservas_suma_montos`.`monto`  AS `Operacion`,
    `reservas_suma_pagos`.`pagos`   AS `Pagos`,
    (COALESCE(`reservas_suma_montos`.`monto`,0) - COALESCE(`reservas_suma_pagos`.`pagos`,0)) AS `Saldo`,
    `sucursales`.`idsucursal`       AS `idsucursal`,
    `asignaciones`.`estado_reserva` AS `EstadoReserva`
FROM ((((((( `asignaciones`
    JOIN `grupos`   ON `asignaciones`.`id_grupo`  = `grupos`.`idgrupo`)
    JOIN `modelos`  ON `asignaciones`.`id_modelo` = `modelos`.`idmodelo`)
    JOIN `usuarios` ON `asignaciones`.`id_asesor` = `usuarios`.`idusuario`)
    JOIN `meses`    ON `asignaciones`.`id_mes`    = `meses`.`idmes`)
    LEFT JOIN `reservas_suma_montos` ON `asignaciones`.`nro_unidad` = `reservas_suma_montos`.`nrounidad`)
    LEFT JOIN `reservas_suma_pagos`  ON `asignaciones`.`nro_unidad` = `reservas_suma_pagos`.`nrounidad`)
    JOIN `sucursales` ON `usuarios`.`idsucursal` = `sucursales`.`idsucursal`)
WHERE `asignaciones`.`guardado` = 1
  AND `asignaciones`.`borrar`    = 0
  AND `asignaciones`.`entregada` = 0
  AND `asignaciones`.`id_negocio` = 1   -- ← cambio: antes usuarios.id_negocio
  AND `asignaciones`.`estado_tasa` = 1
  AND `asignaciones`.`nro_orden` NOT LIKE 'TPA%'
  AND ((`asignaciones`.`año` < YEAR(CURDATE()))
       OR (`asignaciones`.`año` = YEAR(CURDATE()) AND `asignaciones`.`id_mes` <= MONTH(CURDATE())))
ORDER BY `asignaciones`.`año`, `asignaciones`.`id_mes`;


-- 2) NO LLEGADAS (Pendiente Pago TASA) -----------------------------------
ALTER VIEW view_asignaciones_saldo_pendiente_corregida_no_llegadas AS
SELECT
    `sucursales`.`idsucursal` AS `IdSucursal`,
    `sucursales`.`sucursal`   AS `Sucursal`,
    SUM(COALESCE(`reservas_suma_montos`.`monto`,0) - COALESCE(`reservas_suma_pagos`.`pagos`,0)) AS `Saldo`
FROM ((((`asignaciones`
    JOIN `usuarios`   ON `asignaciones`.`id_asesor` = `usuarios`.`idusuario`)
    JOIN `sucursales` ON `usuarios`.`idsucursal`    = `sucursales`.`idsucursal`)
    LEFT JOIN `reservas_suma_montos` ON `asignaciones`.`nro_unidad` = `reservas_suma_montos`.`nrounidad`)
    LEFT JOIN `reservas_suma_pagos`  ON `asignaciones`.`nro_unidad` = `reservas_suma_pagos`.`nrounidad`)
WHERE `asignaciones`.`guardado` = 1
  AND `asignaciones`.`borrar`    = 0
  AND `asignaciones`.`entregada` = 0
  AND `asignaciones`.`id_negocio` = 1   -- ← cambio
  AND `asignaciones`.`estado_tasa` = 1
  AND `asignaciones`.`pagado` = 0
  AND `asignaciones`.`nro_orden` NOT LIKE 'TPA%'
  AND ((`asignaciones`.`año` < YEAR(CURDATE()))
       OR (`asignaciones`.`año` = YEAR(CURDATE()) AND `asignaciones`.`id_mes` <= MONTH(CURDATE())))
GROUP BY `sucursales`.`idsucursal`, `sucursales`.`sucursal`
ORDER BY `sucursales`.`sucursal`;


-- 3) EN VIAJE -------------------------------------------------------------
-- Nota: esta vista parte de sucursales con LEFT JOIN para devolver fila por
-- sucursal aunque no tenga unidades. El filtro de negocio estaba en la
-- condición de JOIN (no en WHERE), lo movemos al JOIN de asignaciones.
ALTER VIEW view_asignaciones_saldo_pendiente_corregida_en_viaje AS
SELECT
    `s`.`idsucursal` AS `IdSucursal`,
    `s`.`sucursal`   AS `Sucursal`,
    COALESCE(SUM(COALESCE(`rm`.`monto`,0) - COALESCE(`rp`.`pagos`,0)), 0) AS `Saldo`
FROM ((((`sucursales` `s`
    LEFT JOIN `usuarios` `u`
           ON  `u`.`idsucursal` = `s`.`idsucursal`)
    LEFT JOIN `asignaciones` `a`
           ON  `a`.`id_asesor`     = `u`.`idusuario`
          AND `a`.`guardado`      = 1
          AND `a`.`fec_arribo`    IS NULL
          AND `a`.`borrar`        = 0
          AND `a`.`entregada`     = 0
          AND `a`.`estado_tasa`   = 1
          AND `a`.`pagado`        = 1
          AND `a`.`id_negocio`    = 1   -- ← cambio (antes u.id_negocio en este JOIN)
          AND `a`.`chasis`        IS NOT NULL
          AND `a`.`chasis`        <> ''
          AND `a`.`nro_orden`     NOT LIKE 'TPA%'
          AND ((`a`.`año` < YEAR(CURDATE()))
               OR (`a`.`año` = YEAR(CURDATE()) AND `a`.`id_mes` <= MONTH(CURDATE()))))
    LEFT JOIN `reservas_suma_montos` `rm` ON `a`.`nro_unidad` = `rm`.`nrounidad`)
    LEFT JOIN `reservas_suma_pagos`  `rp` ON `a`.`nro_unidad` = `rp`.`nrounidad`)
GROUP BY `s`.`idsucursal`, `s`.`sucursal`
ORDER BY `s`.`sucursal`;


-- 4) LLEGADAS (Con Arribo) -- ya fue tocada antes para agregar Costo y reservas
ALTER VIEW view_asignaciones_saldo_pendiente_corregida_llegadas AS
SELECT
    `sucursales`.`idsucursal` AS `IdSucursal`,
    `sucursales`.`sucursal`   AS `Sucursal`,
    SUM(COALESCE(`reservas_suma_montos`.`monto`,0) - COALESCE(`reservas_suma_pagos`.`pagos`,0)) AS `Saldo`,
    SUM(COALESCE(`modelos`.`costo`,0)) AS `Costo`,
    SUM(CASE WHEN `asignaciones`.`estado_reserva` = 1 THEN COALESCE(`modelos`.`costo`,0) ELSE 0 END) AS `CostoConReserva`,
    SUM(CASE WHEN `asignaciones`.`estado_reserva` = 1 THEN 0 ELSE COALESCE(`modelos`.`costo`,0) END) AS `CostoSinReserva`,
    COUNT(*) AS `Unidades`,
    SUM(CASE WHEN `asignaciones`.`estado_reserva` = 1 THEN 1 ELSE 0 END) AS `UnidadesConReserva`
FROM (((((`asignaciones`
    JOIN `usuarios`   ON `asignaciones`.`id_asesor` = `usuarios`.`idusuario`)
    JOIN `sucursales` ON `usuarios`.`idsucursal`    = `sucursales`.`idsucursal`)
    JOIN `modelos`    ON `asignaciones`.`id_modelo` = `modelos`.`idmodelo`)
    LEFT JOIN `reservas_suma_montos` ON `asignaciones`.`nro_unidad` = `reservas_suma_montos`.`nrounidad`)
    LEFT JOIN `reservas_suma_pagos`  ON `asignaciones`.`nro_unidad` = `reservas_suma_pagos`.`nrounidad`)
WHERE `asignaciones`.`guardado` = 1
  AND `asignaciones`.`borrar`   = 0
  AND `asignaciones`.`entregada`= 0
  AND `asignaciones`.`id_negocio` = 1   -- ← cambio
  AND `asignaciones`.`estado_tasa` = 1
  AND `asignaciones`.`pagado`   = 1
  AND `asignaciones`.`fec_arribo` IS NOT NULL
  AND `asignaciones`.`fec_arribo` <> ''
  AND `asignaciones`.`nro_orden` NOT LIKE 'TPA%'
  AND ((`asignaciones`.`año` < YEAR(CURDATE()))
       OR (`asignaciones`.`año` = YEAR(CURDATE()) AND `asignaciones`.`id_mes` <= MONTH(CURDATE())))
GROUP BY `sucursales`.`idsucursal`, `sucursales`.`sucursal`
ORDER BY `sucursales`.`sucursal`;
