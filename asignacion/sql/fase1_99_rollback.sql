-- =====================================================================
-- Fase 1 — ROLLBACK (sólo si algo sale mal)
-- =====================================================================
-- Requiere haber corrido fase1_00_backup_claves.sql ANTES del despliegue.
-- Sin esa tabla, las claves re-hasheadas NO se pueden recuperar.
--
-- ORDEN: primero volver el CÓDIGO (git), después correr esto.
-- Al revés queda una ventana en la que el código nuevo re-hashea de nuevo
-- lo que este script acaba de restaurar.
--
--   git revert -m 1 <sha-del-merge>     # y desplegar el revert
--   ...y recién entonces, este script.

-- 1) Restaura las claves como estaban antes de la Fase 1.
UPDATE `usuarios` u
  JOIN `usuarios_backup_prefase1` b ON b.`idusuario` = u.`idusuario`
  SET u.`clave` = b.`clave`;

-- 2) Verificación exacta: compara contra el backup fila por fila.
--    `sin_restaurar` TIENE que dar 0. Si da cualquier otra cosa, NO seguir.
SELECT COUNT(*) AS sin_restaurar
FROM `usuarios` u
  JOIN `usuarios_backup_prefase1` b ON b.`idusuario` = u.`idusuario`
WHERE u.`clave` <> b.`clave`;

-- 2b) Usuarios creados DESPUÉS del backup (altas hechas durante la ventana):
--     no están respaldados, así que quedan con su clave hasheada y el código
--     viejo no los va a poder validar. Si aparece alguno, hay que resetearle
--     la clave a mano desde el admin.
SELECT u.`idusuario`, u.`usuario`
FROM `usuarios` u
  LEFT JOIN `usuarios_backup_prefase1` b ON b.`idusuario` = u.`idusuario`
WHERE b.`idusuario` IS NULL;

-- 3) La columna `debe_cambiar_clave` se puede DEJAR: el código viejo la
--    ignora y no molesta. Sólo si se quiere dejar la tabla igual que antes:
--
--   ALTER TABLE `usuarios` DROP COLUMN `debe_cambiar_clave`;

-- 4) Recién cuando la Fase 1 esté confirmada y estable, borrar el backup.
--    OJO: son las claves de todos en texto plano. Mientras exista, es tan
--    sensible como la tabla original.
--
--   DROP TABLE `usuarios_backup_prefase1`;

-- ---------------------------------------------------------------------
-- EFECTO SECUNDARIO a tener en cuenta: si alguien cambió su clave por
-- login/cambiar_clave.php durante la ventana, el rollback lo devuelve a su
-- clave ANTERIOR. Va a entrar igual, pero con la vieja, no con la que eligió.
-- ---------------------------------------------------------------------
