<?php
/*
 * ABM de pagos del Estado de Cuenta (versión moderna de pagos_insertar_filas.php).
 *   mov = 1 insertar | 2 editar | 3 eliminar
 * Deja el resultado en $salida; el endpoint guardar.php lo emite como JSON.
 *
 * Replica la lógica del módulo viejo, con mejoras:
 *   - fecha vacía => NULL ; chequea errores ; responde JSON
 *   - recalcula reservas.cancelada / estadopago a partir de los pagos reales
 *   - cascada de cancelación (tipo 3): marca cancelada + estadopago + notificaciones
 *     internas.  >>> EL ENVÍO DE EMAIL QUEDA DESACTIVADO POR AHORA <<<
 *
 * Requiere: $con, $userId y $puedeEditar (config_app.php).
 */

if (!$puedeEditar) {
    http_response_code(403);
    $salida = ["ok" => false, "error" => "No tenés permiso para registrar pagos"];
    return;
}

function fechaSQL($con, $v) {
    $v = trim((string)$v);
    if ($v === '' || strtolower($v) === 'null' || $v === '0') return "NULL";
    return "'".mysqli_real_escape_string($con, $v)."'";
}
function txt($con, $v) { return mysqli_real_escape_string($con, (string)$v); }

$mov       = (int)($_POST['mov'] ?? 0);
$idreserva = (int)($_POST['idreserva'] ?? 0);
$idpago    = (int)($_POST['nrolin'] ?? 0);
$tipo      = (int)($_POST['tipo_pago'] ?? 0);
$modo      = (int)($_POST['modo_pago'] ?? 0);
$finan     = (int)($_POST['finan'] ?? 0);
$monto     = (float)($_POST['monto_pago'] ?? 0);
$nrorecibo = $_POST['nrorecibo'] ?? '';
$obs       = $_POST['obs'] ?? '';
$fecha     = $_POST['fecha'] ?? '';

if ($idreserva <= 0) { $salida = ["ok"=>false,"error"=>"Reserva inválida"]; return; }

// ─── Validaciones (igual que el módulo viejo) para alta/edición ──────────────
if ($mov === 1 || $mov === 2) {
    if ($fecha === '' || $tipo === 0 || $modo === 0 || $monto == 0) {
        $salida = ["ok"=>false,"error"=>"Ingresá como mínimo fecha, tipo, modo y monto"]; return;
    }
    if (($modo === 3 || $modo === 4) && $finan === 0) {
        $salida = ["ok"=>false,"error"=>"Si es Financiado o Leasing, ingresá la financiera"]; return;
    }
}

// ─── CRUD sobre pagos_lineas ─────────────────────────────────────────────────
if ($mov === 1) {            // insertar
    $sql = "INSERT INTO pagos_lineas (idreserva, fecha, tipo, modo, financiera, nrorecibo, monto, obs, id_usuario)
            VALUES (".$idreserva.", ".fechaSQL($con,$fecha).", ".$tipo.", ".$modo.", ".$finan.",
                    '".txt($con,$nrorecibo)."', ".$monto.", '".txt($con,$obs)."', ".$userId.")";
} elseif ($mov === 2) {      // editar
    if ($idpago <= 0) { $salida = ["ok"=>false,"error"=>"Pago inválido"]; return; }
    $sql = "UPDATE pagos_lineas SET
                fecha = ".fechaSQL($con,$fecha).", tipo = ".$tipo.", modo = ".$modo.",
                financiera = ".$finan.", nrorecibo = '".txt($con,$nrorecibo)."',
                monto = ".$monto.", obs = '".txt($con,$obs)."'
            WHERE idpago = ".$idpago;
} elseif ($mov === 3) {      // eliminar
    if ($idpago <= 0) { $salida = ["ok"=>false,"error"=>"Pago inválido"]; return; }
    $sql = "DELETE FROM pagos_lineas WHERE idpago = ".$idpago;
} else {
    $salida = ["ok"=>false,"error"=>"Operación inválida"]; return;
}
if (!mysqli_query($con, $sql)) {
    http_response_code(500);
    $salida = ["ok"=>false,"error"=>mysqli_error($con)]; return;
}

// ─── Recalcular estado de la reserva a partir de los pagos reales ────────────
$r = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n, COALESCE(MAX(tipo),0) maxtipo, COALESCE(SUM(monto),0) pagado
     FROM pagos_lineas WHERE idreserva = ".$idreserva));
$nPagos    = (int)$r['n'];
$estadopago = $nPagos === 0 ? 0 : (int)$r['maxtipo'];   // 1 seña, 2 a cuenta, 3 cancelación
$cancelada  = ($estadopago === 3) ? 1 : 0;
$pagado     = (float)$r['pagado'];
mysqli_query($con, "UPDATE reservas SET cancelada = ".$cancelada.", estadopago = ".$estadopago." WHERE idreserva = ".$idreserva);

// ─── Cascada de cancelación: notificaciones internas (SIN email por ahora) ───
if (($mov === 1 || $mov === 2) && $tipo === 3) {
    $rv = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM reservas WHERE idreserva = ".$idreserva));
    if ($rv) {
        $g = mysqli_fetch_assoc(mysqli_query($con, "SELECT grupo  FROM grupos  WHERE idgrupo  = ".(int)$rv['idgrupo']));
        $m = mysqli_fetch_assoc(mysqli_query($con, "SELECT modelo FROM modelos WHERE idmodelo = ".(int)$rv['idmodelo']));
        $us = mysqli_fetch_assoc(mysqli_query($con, "SELECT nombre FROM usuarios WHERE idusuario = ".(int)$rv['idusuario']));
        $cl = mysqli_fetch_assoc(mysqli_query($con, "SELECT nombre FROM clientes WHERE idcliente = ".(int)$rv['idcliente']));

        $hoy     = date('Y-m-d');
        $hora    = date('H:i');
        $compra  = txt($con, $rv['compra'] ?? '');
        $interno = txt($con, ($rv['interno'] ?? '').($rv['internou'] ?? ''));
        $modeloT = txt($con, ($g['grupo'] ?? '').' '.($m['modelo'] ?? '').($rv['detalleu'] ?? ''));
        $cliN    = txt($con, $cl['nombre'] ?? '');
        $aseN    = txt($con, $us['nombre'] ?? '');
        $idcli   = (int)$rv['idcliente'];

        $insNot = function($idusuario) use ($con, $hoy, $hora, $compra, $idreserva, $idcli, $interno, $modeloT, $cliN, $aseN) {
            $idusuario = (int)$idusuario;
            mysqli_query($con,
                "INSERT INTO notificaciones (tiponot, fechanot, hora, idusuario, compra, idreserva, idpago, interno, modelo, cliente, asesor, visto, obs)
                 VALUES (6, '$hoy', '$hora', $idusuario, '$compra', $idreserva, $idcli, '$interno', '$modeloT', '$cliN', '$aseN', 0, '-')");
        };

        // destinatarios fijos (notificacionespara tiponot=6) + el asesor de la reserva
        $rp = mysqli_query($con, "SELECT idusuario FROM notificacionespara WHERE tiponot = 6");
        if ($rp) while ($n = mysqli_fetch_assoc($rp)) $insNot($n['idusuario']);
        $insNot($rv['idusuario']);

        // >>> Envío de email DESACTIVADO por ahora (ver módulo viejo pagos_insertar_filas.php).
    }
}

$rOp = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COALESCE(SUM(monto),0) total FROM lineas_detalle WHERE movimiento = 1 AND idreserva = ".$idreserva));
$monto_operacion = (float)$rOp['total'];

$salida = [
    "ok"         => true,
    "pagado"     => $pagado,
    "a_cancelar" => $monto_operacion - $pagado,
    "estadopago" => $estadopago,
    "cancelada"  => $cancelada,
];
