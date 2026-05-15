-- =====================================================================
-- Trigger BEFORE UPDATE sobre `asignaciones`
-- =====================================================================
-- 1. Cuenta cuántos campos auditados cambian.
-- 2. Si > 0:
--      a) Construye un array JSON `v_json` con un objeto {campo, antes, despues}
--         por cada campo que cambió. Usa CONCAT + REPLACE (no funciones JSON
--         nativas, para soportar MariaDB 10.0+).
--      b) INSERT 1 fila en `auditoria_unidades` con ese JSON en `movimiento`.
-- 3. Si = 0: no hace nada (no se crean filas vacías).
--
-- Variables de sesión MySQL leídas (las setea conectar() en func_mysql.php):
--   @id_usuario      (INT)
--   @usuario_nombre  (VARCHAR)
--   @origen          (VARCHAR)
--
-- `<=>` es NULL-safe equal: 1 si iguales (incluso ambos NULL), 0 si distintos.
-- Requisito: MariaDB 10.0+ o MySQL 5.5+. El JSON se construye con CONCAT
-- + REPLACE para escapar comillas / backslash / saltos de línea, así NO
-- depende de JSON_OBJECT / JSON_ARRAY_APPEND (que sólo existen en
-- MariaDB 10.2.3+ / MySQL 5.7+).
-- =====================================================================

DROP TRIGGER  IF EXISTS `trg_asignaciones_audit_update`;
DROP FUNCTION IF EXISTS `fn_aud_json_obj`;

DELIMITER $$

-- ---------------------------------------------------------------------
-- Función helper: construye un objeto JSON {"campo":"X","antes":A,"despues":B}
-- - Si el valor es NULL → emite `null` literal (sin comillas).
-- - Si el valor no es NULL → lo trata como string y lo escapa (comilla,
--   backslash, CR, LF, TAB). Números y fechas se serializan como strings
--   entre comillas — PHP json_decode los devuelve como strings, idéntico
--   a como los manejaba la versión con JSON_OBJECT.
-- ---------------------------------------------------------------------
CREATE FUNCTION `fn_aud_json_obj`(
  p_campo   VARCHAR(64),
  p_antes   TEXT,
  p_despues TEXT
) RETURNS TEXT
  DETERMINISTIC
  CONTAINS SQL
BEGIN
  DECLARE v_antes_json   TEXT;
  DECLARE v_despues_json TEXT;

  IF p_antes IS NULL THEN
    SET v_antes_json = 'null';
  ELSE
    SET v_antes_json = CONCAT('"',
      REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(p_antes,
        '\\', '\\\\'),
        '"',  '\\"'),
        CHAR(13), '\\r'),
        CHAR(10), '\\n'),
        CHAR(9),  '\\t'),
      '"');
  END IF;

  IF p_despues IS NULL THEN
    SET v_despues_json = 'null';
  ELSE
    SET v_despues_json = CONCAT('"',
      REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(p_despues,
        '\\', '\\\\'),
        '"',  '\\"'),
        CHAR(13), '\\r'),
        CHAR(10), '\\n'),
        CHAR(9),  '\\t'),
      '"');
  END IF;

  RETURN CONCAT('{"campo":"', p_campo, '","antes":', v_antes_json, ',"despues":', v_despues_json, '}');
END$$

CREATE TRIGGER `trg_asignaciones_audit_update`
BEFORE UPDATE ON `asignaciones`
FOR EACH ROW
BEGIN
  DECLARE v_uid     INT;
  DECLARE v_uname   VARCHAR(64);
  DECLARE v_origen  VARCHAR(96);
  DECLARE v_count   INT;
  DECLARE v_json    LONGTEXT;
  DECLARE v_sep     VARCHAR(1);

  -- IMPORTANTE: si la auditoría falla por cualquier motivo (UTF-8 inválido en
  -- algún TEXT, version rara de MySQL, etc.) el handler silencia el error y
  -- el UPDATE de la unidad se completa igual. Preferimos perder un registro
  -- de auditoría antes que bloquear el guardado del usuario.
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN END;

  -- Cuenta campos cambiados
  SET v_count =
    (1 - (OLD.nro_unidad           <=> NEW.nro_unidad)) +
    (1 - (OLD.chasis               <=> NEW.chasis)) +
    (1 - (OLD.nro_orden            <=> NEW.nro_orden)) +
    (1 - (OLD.interno              <=> NEW.interno)) +
    (1 - (OLD.patente              <=> NEW.patente)) +
    (1 - (OLD.id_grupo             <=> NEW.id_grupo)) +
    (1 - (OLD.id_modelo            <=> NEW.id_modelo)) +
    (1 - (OLD.id_color             <=> NEW.id_color)) +
    (1 - (OLD.color_uno            <=> NEW.color_uno)) +
    (1 - (OLD.color_dos            <=> NEW.color_dos)) +
    (1 - (OLD.color_tres           <=> NEW.color_tres)) +
    (1 - (OLD.id_sucursal          <=> NEW.id_sucursal)) +
    (1 - (OLD.id_ubicacion         <=> NEW.id_ubicacion)) +
    (1 - (OLD.id_ubicacion_entrega <=> NEW.id_ubicacion_entrega)) +
    (1 - (OLD.estado_tasa          <=> NEW.estado_tasa)) +
    (1 - (OLD.estado_reserva       <=> NEW.estado_reserva)) +
    (1 - (OLD.reservada            <=> NEW.reservada)) +
    (1 - (OLD.reserva              <=> NEW.reserva)) +
    (1 - (OLD.cancelada            <=> NEW.cancelada)) +
    (1 - (OLD.entregada            <=> NEW.entregada)) +
    (1 - (OLD.pagado               <=> NEW.pagado)) +
    (1 - (OLD.no_disponible        <=> NEW.no_disponible)) +
    (1 - (OLD.borrar               <=> NEW.borrar)) +
    (1 - (OLD.reventa              <=> NEW.reventa)) +
    (1 - (OLD.servicio_conectado   <=> NEW.servicio_conectado)) +
    (1 - (OLD.fec_playa            <=> NEW.fec_playa)) +
    (1 - (OLD.fec_despacho         <=> NEW.fec_despacho)) +
    (1 - (OLD.fec_arribo           <=> NEW.fec_arribo)) +
    (1 - (OLD.fec_reserva          <=> NEW.fec_reserva)) +
    (1 - (OLD.fec_limite           <=> NEW.fec_limite)) +
    (1 - (OLD.fec_cancelacion      <=> NEW.fec_cancelacion)) +
    (1 - (OLD.fec_entrega          <=> NEW.fec_entrega)) +
    (1 - (OLD.fec_inscripcion      <=> NEW.fec_inscripcion)) +
    (1 - (OLD.fec_pedido           <=> NEW.fec_pedido)) +
    (1 - (OLD.costo                <=> NEW.costo)) +
    (1 - (OLD.cliente              <=> NEW.cliente)) +
    (1 - (OLD.id_asesor            <=> NEW.id_asesor)) +
    (1 - (OLD.id_negocio           <=> NEW.id_negocio)) +
    (1 - (OLD.id_mes               <=> NEW.id_mes)) +
    (1 - (OLD.`año`                <=> NEW.`año`)) +
    (1 - (OLD.nro_remito           <=> NEW.nro_remito)) +
    (1 - (OLD.observacion          <=> NEW.observacion)) +
    (1 - (OLD.hora                 <=> NEW.hora)) +
    (1 - (OLD.id_estado_entrega    <=> NEW.id_estado_entrega)) +
    (1 - (OLD.hora_pedido          <=> NEW.hora_pedido)) +
    (1 - (OLD.con_encuesta         <=> NEW.con_encuesta));

  IF v_count > 0 THEN
    SET v_uid    = IFNULL(@id_usuario, 0);
    SET v_uname  = IFNULL(@usuario_nombre, 'sistema');
    SET v_origen = IFNULL(@origen, '');
    SET v_json   = '';
    SET v_sep    = '';

    -- Identificación
    IF NOT (OLD.nro_unidad <=> NEW.nro_unidad) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('nro_unidad', OLD.nro_unidad, NEW.nro_unidad)); SET v_sep = ','; END IF;
    IF NOT (OLD.chasis <=> NEW.chasis) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('chasis', OLD.chasis, NEW.chasis)); SET v_sep = ','; END IF;
    IF NOT (OLD.nro_orden <=> NEW.nro_orden) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('nro_orden', OLD.nro_orden, NEW.nro_orden)); SET v_sep = ','; END IF;
    IF NOT (OLD.interno <=> NEW.interno) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('interno', OLD.interno, NEW.interno)); SET v_sep = ','; END IF;
    IF NOT (OLD.patente <=> NEW.patente) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('patente', OLD.patente, NEW.patente)); SET v_sep = ','; END IF;

    -- Modelo / colores
    IF NOT (OLD.id_grupo <=> NEW.id_grupo) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_grupo', OLD.id_grupo, NEW.id_grupo)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_modelo <=> NEW.id_modelo) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_modelo', OLD.id_modelo, NEW.id_modelo)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_color <=> NEW.id_color) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_color', OLD.id_color, NEW.id_color)); SET v_sep = ','; END IF;
    IF NOT (OLD.color_uno <=> NEW.color_uno) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('color_uno', OLD.color_uno, NEW.color_uno)); SET v_sep = ','; END IF;
    IF NOT (OLD.color_dos <=> NEW.color_dos) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('color_dos', OLD.color_dos, NEW.color_dos)); SET v_sep = ','; END IF;
    IF NOT (OLD.color_tres <=> NEW.color_tres) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('color_tres', OLD.color_tres, NEW.color_tres)); SET v_sep = ','; END IF;

    -- Sucursal / ubicación
    IF NOT (OLD.id_sucursal <=> NEW.id_sucursal) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_sucursal', OLD.id_sucursal, NEW.id_sucursal)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_ubicacion <=> NEW.id_ubicacion) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_ubicacion', OLD.id_ubicacion, NEW.id_ubicacion)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_ubicacion_entrega <=> NEW.id_ubicacion_entrega) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_ubicacion_entrega', OLD.id_ubicacion_entrega, NEW.id_ubicacion_entrega)); SET v_sep = ','; END IF;

    -- Estados
    IF NOT (OLD.estado_tasa <=> NEW.estado_tasa) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('estado_tasa', OLD.estado_tasa, NEW.estado_tasa)); SET v_sep = ','; END IF;
    IF NOT (OLD.estado_reserva <=> NEW.estado_reserva) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('estado_reserva', OLD.estado_reserva, NEW.estado_reserva)); SET v_sep = ','; END IF;
    IF NOT (OLD.reservada <=> NEW.reservada) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('reservada', OLD.reservada, NEW.reservada)); SET v_sep = ','; END IF;
    IF NOT (OLD.reserva <=> NEW.reserva) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('reserva', OLD.reserva, NEW.reserva)); SET v_sep = ','; END IF;
    IF NOT (OLD.cancelada <=> NEW.cancelada) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('cancelada', OLD.cancelada, NEW.cancelada)); SET v_sep = ','; END IF;
    IF NOT (OLD.entregada <=> NEW.entregada) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('entregada', OLD.entregada, NEW.entregada)); SET v_sep = ','; END IF;
    IF NOT (OLD.pagado <=> NEW.pagado) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('pagado', OLD.pagado, NEW.pagado)); SET v_sep = ','; END IF;
    IF NOT (OLD.no_disponible <=> NEW.no_disponible) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('no_disponible', OLD.no_disponible, NEW.no_disponible)); SET v_sep = ','; END IF;
    IF NOT (OLD.borrar <=> NEW.borrar) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('borrar', OLD.borrar, NEW.borrar)); SET v_sep = ','; END IF;
    IF NOT (OLD.reventa <=> NEW.reventa) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('reventa', OLD.reventa, NEW.reventa)); SET v_sep = ','; END IF;
    IF NOT (OLD.servicio_conectado <=> NEW.servicio_conectado) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('servicio_conectado', OLD.servicio_conectado, NEW.servicio_conectado)); SET v_sep = ','; END IF;

    -- Fechas
    IF NOT (OLD.fec_playa <=> NEW.fec_playa) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_playa', OLD.fec_playa, NEW.fec_playa)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_despacho <=> NEW.fec_despacho) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_despacho', OLD.fec_despacho, NEW.fec_despacho)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_arribo <=> NEW.fec_arribo) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_arribo', OLD.fec_arribo, NEW.fec_arribo)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_reserva <=> NEW.fec_reserva) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_reserva', OLD.fec_reserva, NEW.fec_reserva)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_limite <=> NEW.fec_limite) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_limite', OLD.fec_limite, NEW.fec_limite)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_cancelacion <=> NEW.fec_cancelacion) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_cancelacion', OLD.fec_cancelacion, NEW.fec_cancelacion)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_entrega <=> NEW.fec_entrega) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_entrega', OLD.fec_entrega, NEW.fec_entrega)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_inscripcion <=> NEW.fec_inscripcion) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_inscripcion', OLD.fec_inscripcion, NEW.fec_inscripcion)); SET v_sep = ','; END IF;
    IF NOT (OLD.fec_pedido <=> NEW.fec_pedido) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('fec_pedido', OLD.fec_pedido, NEW.fec_pedido)); SET v_sep = ','; END IF;

    -- Otros
    IF NOT (OLD.costo <=> NEW.costo) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('costo', OLD.costo, NEW.costo)); SET v_sep = ','; END IF;
    IF NOT (OLD.cliente <=> NEW.cliente) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('cliente', OLD.cliente, NEW.cliente)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_asesor <=> NEW.id_asesor) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_asesor', OLD.id_asesor, NEW.id_asesor)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_negocio <=> NEW.id_negocio) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_negocio', OLD.id_negocio, NEW.id_negocio)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_mes <=> NEW.id_mes) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_mes', OLD.id_mes, NEW.id_mes)); SET v_sep = ','; END IF;
    IF NOT (OLD.`año` <=> NEW.`año`) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('año', OLD.`año`, NEW.`año`)); SET v_sep = ','; END IF;
    IF NOT (OLD.nro_remito <=> NEW.nro_remito) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('nro_remito', OLD.nro_remito, NEW.nro_remito)); SET v_sep = ','; END IF;
    IF NOT (OLD.observacion <=> NEW.observacion) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('observacion', OLD.observacion, NEW.observacion)); SET v_sep = ','; END IF;
    IF NOT (OLD.hora <=> NEW.hora) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('hora', OLD.hora, NEW.hora)); SET v_sep = ','; END IF;
    IF NOT (OLD.id_estado_entrega <=> NEW.id_estado_entrega) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('id_estado_entrega', OLD.id_estado_entrega, NEW.id_estado_entrega)); SET v_sep = ','; END IF;
    IF NOT (OLD.hora_pedido <=> NEW.hora_pedido) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('hora_pedido', OLD.hora_pedido, NEW.hora_pedido)); SET v_sep = ','; END IF;
    IF NOT (OLD.con_encuesta <=> NEW.con_encuesta) THEN
      SET v_json = CONCAT(v_json, v_sep, fn_aud_json_obj('con_encuesta', OLD.con_encuesta, NEW.con_encuesta)); SET v_sep = ','; END IF;

    INSERT INTO auditoria_unidades
      (id_unidad, nro_unidad, fecha, hora, id_usuario, usuario, origen, cant_campos, movimiento)
    VALUES
      (NEW.id_unidad, NEW.nro_unidad, CURDATE(), CURTIME(), v_uid, v_uname, v_origen, v_count, CONCAT('[', v_json, ']'));
  END IF;
END$$

DELIMITER ;
