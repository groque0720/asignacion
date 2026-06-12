<?php
/*
 * Reserva un plan libre / edita la reserva propia (datos del cliente).
 * Deja el resultado en $salida; el endpoint reservar.php lo emite como JSON.
 *
 * Reglas (idénticas al módulo viejo reservar_plan_avanzado.php):
 *   - No se puede reservar un plan ya reservado por OTRO asesor.
 *   - estado_id pasa a 2 (Reservado) y usuario_venta_id al usuario actual.
 *   - fechas vacías => NULL (sql_mode estricto en dev).
 *
 * Requiere: $con, $userId (config_app.php) y tpa_* (funciones/consulta.php).
 */

$uuid = isset($_POST['planUuId']) ? trim((string)$_POST['planUuId']) : '';
if ($uuid === '') { $salida = ["ok" => false, "error" => "Plan inválido"]; return; }

$uuidEsc = mysqli_real_escape_string($con, $uuid);

// ¿Está reservado por otro? (estado <> 1 y vendido por otro usuario)
$chk = mysqli_query($con,
    "SELECT estado_id, usuario_venta_id FROM tpa_planes_avanzados
     WHERE uuid = '$uuidEsc' AND estado_id <> 1");
$cur = $chk ? mysqli_fetch_assoc($chk) : null;
if ($cur && (int)$cur['usuario_venta_id'] !== (int)$userId) {
    $salida = ["ok" => false, "error" => "Este plan ya fue reservado por otro asesor."];
    return;
}

$monto_reserva = tpa_num($_POST['monto_reserva'] ?? 0);

$SQL = "UPDATE tpa_planes_avanzados SET "
     . "estado_id = 2, "
     . "usuario_venta_id = " . (int)$userId . ", "
     . "monto_reserva = " . $monto_reserva . ", "
     . "fecha_reserva = "  . tpa_fechaSQL($con, $_POST['fecha_reserva'] ?? '') . ", "
     . "hora_reserva = '"  . tpa_txt($con, $_POST['hora_reserva'] ?? '') . "', "
     . "modelo_version_retirar = '" . tpa_txt($con, $_POST['modelo_version_retirar'] ?? '') . "', "
     . "cliente = '"    . tpa_txt($con, $_POST['cliente'] ?? '') . "', "
     . "sexo = '"       . tpa_txt($con, $_POST['sexo'] ?? '') . "', "
     . "fecha_nacimiento = " . tpa_fechaSQL($con, $_POST['fecha_nacimiento'] ?? '') . ", "
     . "edad = '"       . tpa_txt($con, $_POST['edad'] ?? '') . "', "
     . "dni = '"        . tpa_txt($con, $_POST['dni'] ?? '') . "', "
     . "cuil = '"       . tpa_txt($con, $_POST['cuil'] ?? '') . "', "
     . "direccion = '"  . tpa_txt($con, $_POST['direccion'] ?? '') . "', "
     . "localidad = '"  . tpa_txt($con, $_POST['localidad'] ?? '') . "', "
     . "provincia = '"  . tpa_txt($con, $_POST['provincia'] ?? '') . "', "
     . "email = '"      . tpa_txt($con, $_POST['email'] ?? '') . "', "
     . "celular = '"    . tpa_txt($con, $_POST['celular'] ?? '') . "' "
     . "WHERE uuid = '$uuidEsc'";

if (!mysqli_query($con, $SQL)) {
    http_response_code(500);
    $salida = ["ok" => false, "error" => mysqli_error($con)];
    return;
}

$salida = ["ok" => true];
