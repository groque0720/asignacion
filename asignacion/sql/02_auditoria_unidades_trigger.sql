-- =====================================================================
-- Trigger BEFORE UPDATE sobre `asignaciones`
-- =====================================================================
-- 1. Cuenta cuántos campos auditados cambian.
-- 2. Si > 0:
--      a) Construye un array JSON `v_json` con un objeto {campo, antes, despues}
--         por cada campo que cambió. Usa JSON_ARRAY_APPEND + JSON_OBJECT.
--      b) INSERT 1 fila en `auditoria_unidades` con ese JSON en `movimiento`.
-- 3. Si = 0: no hace nada (no se crean filas vacías).
--
-- Variables de sesión MySQL leídas (las setea conectar() en func_mysql.php):
--   @id_usuario      (INT)
--   @usuario_nombre  (VARCHAR)
--   @origen          (VARCHAR)
--
-- `<=>` es NULL-safe equal: 1 si iguales (incluso ambos NULL), 0 si distintos.
-- Requisito: MySQL 5.7+ o MariaDB 10.2.7+.
-- =====================================================================

DROP TRIGGER IF EXISTS `trg_asignaciones_audit_update`;

DELIMITER $$

CREATE TRIGGER `trg_asignaciones_audit_update`
BEFORE UPDATE ON `asignaciones`
FOR EACH ROW
BEGIN
  DECLARE v_uid     INT;
  DECLARE v_uname   VARCHAR(64);
  DECLARE v_origen  VARCHAR(96);
  DECLARE v_count   INT;
  DECLARE v_json    JSON;

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
    SET v_json   = JSON_ARRAY();

    -- Identificación
    IF NOT (OLD.nro_unidad <=> NEW.nro_unidad) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','nro_unidad','antes',OLD.nro_unidad,'despues',NEW.nro_unidad)); END IF;
    IF NOT (OLD.chasis <=> NEW.chasis) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','chasis','antes',OLD.chasis,'despues',NEW.chasis)); END IF;
    IF NOT (OLD.nro_orden <=> NEW.nro_orden) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','nro_orden','antes',OLD.nro_orden,'despues',NEW.nro_orden)); END IF;
    IF NOT (OLD.interno <=> NEW.interno) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','interno','antes',OLD.interno,'despues',NEW.interno)); END IF;
    IF NOT (OLD.patente <=> NEW.patente) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','patente','antes',OLD.patente,'despues',NEW.patente)); END IF;

    -- Modelo / colores
    IF NOT (OLD.id_grupo <=> NEW.id_grupo) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_grupo','antes',OLD.id_grupo,'despues',NEW.id_grupo)); END IF;
    IF NOT (OLD.id_modelo <=> NEW.id_modelo) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_modelo','antes',OLD.id_modelo,'despues',NEW.id_modelo)); END IF;
    IF NOT (OLD.id_color <=> NEW.id_color) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_color','antes',OLD.id_color,'despues',NEW.id_color)); END IF;
    IF NOT (OLD.color_uno <=> NEW.color_uno) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','color_uno','antes',OLD.color_uno,'despues',NEW.color_uno)); END IF;
    IF NOT (OLD.color_dos <=> NEW.color_dos) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','color_dos','antes',OLD.color_dos,'despues',NEW.color_dos)); END IF;
    IF NOT (OLD.color_tres <=> NEW.color_tres) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','color_tres','antes',OLD.color_tres,'despues',NEW.color_tres)); END IF;

    -- Sucursal / ubicación
    IF NOT (OLD.id_sucursal <=> NEW.id_sucursal) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_sucursal','antes',OLD.id_sucursal,'despues',NEW.id_sucursal)); END IF;
    IF NOT (OLD.id_ubicacion <=> NEW.id_ubicacion) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_ubicacion','antes',OLD.id_ubicacion,'despues',NEW.id_ubicacion)); END IF;
    IF NOT (OLD.id_ubicacion_entrega <=> NEW.id_ubicacion_entrega) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_ubicacion_entrega','antes',OLD.id_ubicacion_entrega,'despues',NEW.id_ubicacion_entrega)); END IF;

    -- Estados
    IF NOT (OLD.estado_tasa <=> NEW.estado_tasa) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','estado_tasa','antes',OLD.estado_tasa,'despues',NEW.estado_tasa)); END IF;
    IF NOT (OLD.estado_reserva <=> NEW.estado_reserva) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','estado_reserva','antes',OLD.estado_reserva,'despues',NEW.estado_reserva)); END IF;
    IF NOT (OLD.reservada <=> NEW.reservada) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','reservada','antes',OLD.reservada,'despues',NEW.reservada)); END IF;
    IF NOT (OLD.reserva <=> NEW.reserva) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','reserva','antes',OLD.reserva,'despues',NEW.reserva)); END IF;
    IF NOT (OLD.cancelada <=> NEW.cancelada) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','cancelada','antes',OLD.cancelada,'despues',NEW.cancelada)); END IF;
    IF NOT (OLD.entregada <=> NEW.entregada) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','entregada','antes',OLD.entregada,'despues',NEW.entregada)); END IF;
    IF NOT (OLD.pagado <=> NEW.pagado) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','pagado','antes',OLD.pagado,'despues',NEW.pagado)); END IF;
    IF NOT (OLD.no_disponible <=> NEW.no_disponible) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','no_disponible','antes',OLD.no_disponible,'despues',NEW.no_disponible)); END IF;
    IF NOT (OLD.borrar <=> NEW.borrar) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','borrar','antes',OLD.borrar,'despues',NEW.borrar)); END IF;
    IF NOT (OLD.reventa <=> NEW.reventa) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','reventa','antes',OLD.reventa,'despues',NEW.reventa)); END IF;
    IF NOT (OLD.servicio_conectado <=> NEW.servicio_conectado) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','servicio_conectado','antes',OLD.servicio_conectado,'despues',NEW.servicio_conectado)); END IF;

    -- Fechas
    IF NOT (OLD.fec_playa <=> NEW.fec_playa) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_playa','antes',OLD.fec_playa,'despues',NEW.fec_playa)); END IF;
    IF NOT (OLD.fec_despacho <=> NEW.fec_despacho) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_despacho','antes',OLD.fec_despacho,'despues',NEW.fec_despacho)); END IF;
    IF NOT (OLD.fec_arribo <=> NEW.fec_arribo) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_arribo','antes',OLD.fec_arribo,'despues',NEW.fec_arribo)); END IF;
    IF NOT (OLD.fec_reserva <=> NEW.fec_reserva) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_reserva','antes',OLD.fec_reserva,'despues',NEW.fec_reserva)); END IF;
    IF NOT (OLD.fec_limite <=> NEW.fec_limite) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_limite','antes',OLD.fec_limite,'despues',NEW.fec_limite)); END IF;
    IF NOT (OLD.fec_cancelacion <=> NEW.fec_cancelacion) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_cancelacion','antes',OLD.fec_cancelacion,'despues',NEW.fec_cancelacion)); END IF;
    IF NOT (OLD.fec_entrega <=> NEW.fec_entrega) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_entrega','antes',OLD.fec_entrega,'despues',NEW.fec_entrega)); END IF;
    IF NOT (OLD.fec_inscripcion <=> NEW.fec_inscripcion) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_inscripcion','antes',OLD.fec_inscripcion,'despues',NEW.fec_inscripcion)); END IF;
    IF NOT (OLD.fec_pedido <=> NEW.fec_pedido) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','fec_pedido','antes',OLD.fec_pedido,'despues',NEW.fec_pedido)); END IF;

    -- Otros
    IF NOT (OLD.costo <=> NEW.costo) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','costo','antes',OLD.costo,'despues',NEW.costo)); END IF;
    IF NOT (OLD.cliente <=> NEW.cliente) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','cliente','antes',OLD.cliente,'despues',NEW.cliente)); END IF;
    IF NOT (OLD.id_asesor <=> NEW.id_asesor) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_asesor','antes',OLD.id_asesor,'despues',NEW.id_asesor)); END IF;
    IF NOT (OLD.id_negocio <=> NEW.id_negocio) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_negocio','antes',OLD.id_negocio,'despues',NEW.id_negocio)); END IF;
    IF NOT (OLD.id_mes <=> NEW.id_mes) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_mes','antes',OLD.id_mes,'despues',NEW.id_mes)); END IF;
    IF NOT (OLD.`año` <=> NEW.`año`) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','año','antes',OLD.`año`,'despues',NEW.`año`)); END IF;
    IF NOT (OLD.nro_remito <=> NEW.nro_remito) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','nro_remito','antes',OLD.nro_remito,'despues',NEW.nro_remito)); END IF;
    IF NOT (OLD.observacion <=> NEW.observacion) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','observacion','antes',OLD.observacion,'despues',NEW.observacion)); END IF;
    IF NOT (OLD.hora <=> NEW.hora) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','hora','antes',OLD.hora,'despues',NEW.hora)); END IF;
    IF NOT (OLD.id_estado_entrega <=> NEW.id_estado_entrega) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','id_estado_entrega','antes',OLD.id_estado_entrega,'despues',NEW.id_estado_entrega)); END IF;
    IF NOT (OLD.hora_pedido <=> NEW.hora_pedido) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','hora_pedido','antes',OLD.hora_pedido,'despues',NEW.hora_pedido)); END IF;
    IF NOT (OLD.con_encuesta <=> NEW.con_encuesta) THEN
      SET v_json = JSON_ARRAY_APPEND(v_json, '$', JSON_OBJECT('campo','con_encuesta','antes',OLD.con_encuesta,'despues',NEW.con_encuesta)); END IF;

    INSERT INTO auditoria_unidades
      (id_unidad, nro_unidad, fecha, hora, id_usuario, usuario, origen, cant_campos, movimiento)
    VALUES
      (NEW.id_unidad, NEW.nro_unidad, CURDATE(), CURTIME(), v_uid, v_uname, v_origen, v_count, v_json);
  END IF;
END$$

DELIMITER ;
