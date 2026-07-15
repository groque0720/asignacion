-- =====================================================================
-- Fase 1b de la actualización de seguridad — Forzar cambio de clave
-- =====================================================================
-- Agrega una marca por usuario. La próxima vez que un usuario marcado
-- inicie sesión, el login lo manda a elegir una clave nueva que cumpla la
-- política (mayúscula + minúscula + símbolo + mínimo 8) antes de dejarlo
-- entrar. Ver login/cambiar_clave.php y login/validarusuario.php.
--
-- Default 0 = NO fuerza a nadie (seguro de aplicar en producción sin
-- efecto inmediato). El rollout se activa con el UPDATE de abajo cuando
-- se decida empezar.

ALTER TABLE `usuarios`
  ADD COLUMN `debe_cambiar_clave` TINYINT(1) NOT NULL DEFAULT 0;

-- ---------------------------------------------------------------------
-- ACTIVACIÓN DEL ROLLOUT (correr cuando se decida arrancar el proceso):
-- fuerza el cambio a todos los usuarios activos en su próximo login.
-- Descomentar y ejecutar:
--
--   UPDATE `usuarios` SET `debe_cambiar_clave` = 1 WHERE `activo` = 1;
--
-- Para forzar sólo a un subconjunto (ej. claves débiles conocidas),
-- filtrar por idusuario en lugar de activo = 1.
-- ---------------------------------------------------------------------
