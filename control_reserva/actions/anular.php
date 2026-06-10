<?php
/*
 * Anula una reserva. Deja el resultado en $salida.
 * Réplica fiel de ventas/web/reserva_anular.php:
 *   - marca anulada=1, resetea nrounidad/interno/nroorden, graba obsanulada con la causa
 *   - si la reserva estaba enviada (>0), genera notificaciones internas (tiponot=3)
 *     a los destinatarios de notificacionespara, excluyendo la lista fija y al asesor
 *     de la reserva.  >>> El envío de email queda DESACTIVADO, igual que en el original. <<<
 *
 * Requiere: $con y $puedeControlar (config_app.php).
 */

if (!$puedeControlar) {
    http_response_code(403);
    $salida = ["ok" => false, "error" => "No tenés permiso para anular reservas"];
    return;
}

$idres = (int)($_POST['idres'] ?? 0);
$obser = trim((string)($_POST['obs'] ?? ''));
if ($idres <= 0) { $salida = ["ok" => false, "error" => "Reserva inválida"]; return; }
if ($obser === '') { $salida = ["ok" => false, "error" => "Ingresá el motivo de la anulación"]; return; }

$obserSQL = mysqli_real_escape_string($con, $obser);
$fechaDMY = date('d-m-Y');

$sql = "UPDATE reservas SET
            nrounidad = null,
            interno = '',
            nroorden = '',
            anulada = 1,
            obsanulada = ' ".$fechaDMY." RESERVA ANULADA - CAUSA:".$obserSQL."'
        WHERE idreserva = ".$idres;
if (!mysqli_query($con, $sql)) {
    http_response_code(500);
    $salida = ["ok" => false, "error" => mysqli_error($con)];
    return;
}

// Datos de la reserva para armar las notificaciones.
$res = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT r.idusuario, u.nombre AS usuario, g.grupo AS grupo, c.nombre AS cliente,
            r.idreserva, m.modelo AS modelo, r.detalleu, r.compra, r.interno, r.internou, r.enviada
     FROM reservas r
     INNER JOIN clientes c ON c.idcliente = r.idcliente
     INNER JOIN usuarios u ON u.idusuario = r.idusuario
     LEFT JOIN grupos    g ON g.idgrupo   = r.idgrupo
     LEFT JOIN modelos   m ON m.idmodelo  = r.idmodelo
     WHERE r.idreserva = ".$idres));

if ($res && (int)$res['enviada'] > 0) {
    $hora = date('H:i');

    // Lista fija de usuarios a excluir + el asesor de la reserva (idéntico al original).
    $usuarios_not = [5, 7, 10, 9, 8, 63, 88, 155, 160, 6];
    $id_usuario_reserva = (int)$res['idusuario'];
    if (($key = array_search($id_usuario_reserva, $usuarios_not)) !== false) unset($usuarios_not[$key]);
    $usuarios_not[] = $id_usuario_reserva;
    $lista_usuarios = implode(',', array_map('intval', $usuarios_not));

    $rp = mysqli_query($con,
        "SELECT idusuario FROM notificacionespara WHERE tiponot = 3 AND idusuario NOT IN ($lista_usuarios)");

    $fecha   = date('Y-m-d');
    $compra  = mysqli_real_escape_string($con, (string)$res['compra']);
    $modeloT = mysqli_real_escape_string($con, ($res['grupo'] ?? '').' '.($res['modelo'] ?? '').($res['detalleu'] ?? ''));
    $cliN    = mysqli_real_escape_string($con, (string)$res['cliente']);
    $aseN    = mysqli_real_escape_string($con, (string)$res['usuario']);
    $internoTrim = trim(($res['interno'] ?? '').($res['internou'] ?? ''));
    $internoSQL  = ($internoTrim === '') ? 'NULL' : "'".mysqli_real_escape_string($con, $internoTrim)."'";

    if ($rp) {
        while ($not = mysqli_fetch_assoc($rp)) {
            $idDest = (int)$not['idusuario'];
            mysqli_query($con,
                "INSERT INTO notificaciones (tiponot, fechanot, hora, idusuario, compra, idreserva, interno, modelo, cliente, asesor, visto, obs)
                 VALUES (3, '$fecha', '$hora', $idDest, '$compra', $idres, $internoSQL, '$modeloT', '$cliN', '$aseN', 0, '$obserSQL')");
        }
    }
}

$salida = ["ok" => true];
