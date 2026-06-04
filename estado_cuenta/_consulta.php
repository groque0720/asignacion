<?php
/*
 * Lógica compartida del Estado de Cuenta (la usan data.php, pdf.php, excel.php).
 */

// Devuelve el estado de cuenta de un cliente, o null si no tiene reserva.
function ec_datos($con, $idcliente) {
    $idcliente = (int)$idcliente;

    $res = mysqli_query($con,
        "SELECT r.idreserva, r.idcredito, r.idcliente, c.nombre AS cliente, u.nombre AS asesor
         FROM reservas r
         INNER JOIN usuarios u ON u.idusuario = r.idusuario
         INNER JOIN clientes c ON c.idcliente = r.idcliente
         WHERE r.idcliente = ".$idcliente." LIMIT 1");
    if (!$res || mysqli_num_rows($res) === 0) return null;
    $cab = mysqli_fetch_assoc($res);
    $idreserva = (int)$cab['idreserva'];

    $rCred = mysqli_query($con,
        "SELECT tc.tipocredito AS credito, fi.financiera AS financiera, ld.monto AS monto
         FROM reservas r
         INNER JOIN lineas_detalle ld ON ld.idreserva = r.idreserva
         INNER JOIN codigos co        ON co.idcodigo = ld.idcodigo
         INNER JOIN tipos_creditos tc ON tc.idtipocredito = co.tipocredito
         INNER JOIN financieras fi    ON fi.idfinanciera = co.financiera
         WHERE co.credito = '1' AND r.cancelada = '0' AND r.idcliente = ".$idcliente." LIMIT 1");
    $cred = ($rCred && mysqli_num_rows($rCred)) ? mysqli_fetch_assoc($rCred) : null;

    $monto_operacion = (float)mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COALESCE(SUM(monto),0) total FROM lineas_detalle WHERE movimiento = 1 AND idreserva = ".$idreserva))['total'];
    $pagado = (float)mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COALESCE(SUM(monto),0) total FROM pagos_lineas WHERE idreserva = ".$idreserva))['total'];

    $rPagos = mysqli_query($con,
        "SELECT pl.idpago, pl.fecha, pl.nrorecibo, pl.monto, pl.obs,
                pl.tipo AS tipo_id,      pt.tipopago AS tipo,
                pl.modo AS modo_id,      pm.modo AS modo,
                pl.financiera AS fin_id, fi.financiera AS financiera
         FROM pagos_lineas pl
         LEFT JOIN pagos_tipos pt ON pt.idtipopago = pl.tipo
         LEFT JOIN pagos_modos pm ON pm.idpagomodo = pl.modo
         LEFT JOIN financieras fi ON fi.idfinanciera = pl.financiera
         WHERE pl.idreserva = ".$idreserva."
         ORDER BY pl.fecha ASC, pl.idpago ASC");
    $pagos = [];
    if ($rPagos) {
        while ($p = mysqli_fetch_assoc($rPagos)) {
            $pagos[] = [
                'idpago'     => (int)$p['idpago'],
                'fecha'      => ($p['fecha'] && $p['fecha'] !== '0000-00-00') ? $p['fecha'] : null,
                'tipo_id'    => (int)$p['tipo_id'], 'tipo' => $p['tipo'],
                'modo_id'    => (int)$p['modo_id'], 'modo' => $p['modo'],
                'fin_id'     => (int)$p['fin_id'],  'financiera' => $p['financiera'],
                'nrorecibo'  => $p['nrorecibo'],
                'monto'      => (float)$p['monto'],
                'obs'        => $p['obs'],
            ];
        }
    }

    return [
        'idcliente'       => $idcliente,
        'idreserva'       => $idreserva,
        'idcredito'       => (int)$cab['idcredito'],
        'cliente'         => $cab['cliente'],
        'asesor'          => $cab['asesor'],
        'credito'         => $cred ? $cred['credito'] : '',
        'financiera_cred' => $cred ? $cred['financiera'] : '',
        'monto_cred'      => $cred ? (float)$cred['monto'] : 0,
        'monto_operacion' => $monto_operacion,
        'pagado'          => $pagado,
        'a_cancelar'      => $monto_operacion - $pagado,
        'pagos'           => $pagos,
    ];
}

function ec_fecha($d) {
    if (!$d || $d === '0000-00-00') return '';
    $p = explode('-', $d);
    return (count($p) === 3) ? ($p[2].'/'.$p[1].'/'.$p[0]) : $d;
}
