-- =====================================================================
-- Fase 1 — PASO 0: backup de las claves ANTES de desplegar
-- =====================================================================
-- CORRER ESTO PRIMERO, ANTES QUE NADA. Es la única forma de volver atrás.
--
-- Por qué: desde que se despliega la Fase 1, cada usuario que entra con su
-- clave en texto plano queda re-hasheado en la base. Si después se vuelve al
-- código viejo (que compara `WHERE clave = '<texto plano>'`), ese código no
-- puede matchear un hash bcrypt y esos usuarios quedan afuera PARA SIEMPRE:
-- el texto plano ya no existe en ningún lado.
--
-- Con esta tabla, el rollback es restaurar y listo (ver fase1_99_rollback.sql).

CREATE TABLE `usuarios_backup_prefase1` AS
  SELECT `idusuario`, `clave` FROM `usuarios`;

-- Verificación: las dos cuentas tienen que dar el mismo número, y
-- `hasheadas` debería ser 0 si todavía no se desplegó nada.
SELECT
  (SELECT COUNT(*) FROM `usuarios`)                    AS usuarios,
  (SELECT COUNT(*) FROM `usuarios_backup_prefase1`)    AS respaldados,
  (SELECT SUM(`clave` LIKE '$2y$%') FROM `usuarios`)   AS hasheadas;

-- ---------------------------------------------------------------------
-- CHEQUEO OBLIGATORIO antes de seguir: el ancho de la columna `clave`.
-- Un hash bcrypt mide 60 caracteres. Si en producción la columna fuera más
-- angosta, MySQL lo truncaría EN SILENCIO (producción no usa sql_mode
-- estricto) y el usuario quedaría con un hash mutilado: entra una vez y no
-- puede entrar nunca más.
-- Tiene que decir varchar(255), o al menos algo >= 60.
-- ---------------------------------------------------------------------
SELECT `COLUMN_TYPE`
FROM `information_schema`.`COLUMNS`
WHERE `TABLE_SCHEMA` = DATABASE()
  AND `TABLE_NAME`   = 'usuarios'
  AND `COLUMN_NAME`  = 'clave';
