<?php
/*
 * Lógica de datos del Estado de Cuenta (la usan las actions: cuenta_datos,
 * lista, exportar_excel, exportar_pdf).
 *   - ec_datos()    estado de cuenta de un cliente (resumen + pagos)
 *   - ec_lookups()  catálogos para el formulario de pagos (tipos / modos / financieras)
 *   - ec_lista()    listado paginado de clientes activos
 *   - ec_fecha()    YYYY-MM-DD -> d/m/Y
 *
 * Requiere: $con (config/config_app.php).
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
    // Pagado NETO (entra en el saldo: a_cancelar = operación - pagado).
    $pagado = (float)mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COALESCE(SUM(monto),0) total FROM pagos_lineas WHERE idreserva = ".$idreserva))['total'];
    // Desglose para mostrar la composición: pagos reales (+) y devoluciones (-).
    $pagado_bruto = (float)mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COALESCE(SUM(monto),0) total FROM pagos_lineas WHERE monto > 0 AND idreserva = ".$idreserva))['total'];
    $devoluciones = (float)mysqli_fetch_assoc(mysqli_query($con,
        "SELECT COALESCE(-SUM(monto),0) total FROM pagos_lineas WHERE monto < 0 AND idreserva = ".$idreserva))['total'];

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
        'pagado_bruto'    => $pagado_bruto,
        'devoluciones'    => $devoluciones,
        'a_cancelar'      => $monto_operacion - $pagado,
        'pagos'           => $pagos,
    ];
}

// Catálogos para el formulario de pagos (tipos / modos / financieras seleccionables).
function ec_lookups($con) {
    $lookup = function($sql) use ($con) {
        $out = [];
        $r = mysqli_query($con, $sql);
        if ($r) while ($x = mysqli_fetch_assoc($r)) $out[] = $x;
        return $out;
    };
    return [
        'tipos'       => $lookup("SELECT idtipopago AS id, tipopago AS nombre FROM pagos_tipos ORDER BY tipopago"),
        'modos'       => $lookup("SELECT idpagomodo AS id, modo AS nombre FROM pagos_modos ORDER BY modo"),
        'financieras' => $lookup("SELECT idfinanciera AS id, financiera AS nombre FROM financieras WHERE seleccionable = 1 ORDER BY financiera"),
    ];
}

// Listado paginado de clientes activos. Devuelve ['total'=>int, 'rows'=>array].
function ec_lista($con, $suc, $q, $per, $offset) {
    $suc = (int)$suc;
    $qe  = mysqli_real_escape_string($con, $q);

    $W = "r.anulada <> 1 AND r.enviada >= '1'";
    if ($suc > 0) $W .= " AND u.idsucursal = ".$suc;
    if ($q !== '') {
        $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR u.nombre LIKE '%$qe%'".
              " OR r.idreserva LIKE '%$qe%' OR r.nrounidad LIKE '%$qe%' OR r.interno LIKE '%$qe%')";
    }

    $FROM = "FROM reservas r
        INNER JOIN usuarios u ON u.idusuario = r.idusuario
        INNER JOIN clientes c ON c.idcliente = r.idcliente";

    $total = (int)mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) n $FROM WHERE $W"))['n'];

    $sql = "SELECT r.idreserva, r.idcliente, r.idcredito, r.compra, r.detalleu,
                   r.estadopago, r.cancelada,
                   c.nombre AS cliente, u.nombre AS asesor,
                   g.grupo AS grupo, m.modelo AS modelo,
                   cr.estado AS credito_estado
            $FROM
            LEFT JOIN grupos   g  ON g.idgrupo   = r.idgrupo
            LEFT JOIN modelos  m  ON m.idmodelo  = r.idmodelo
            LEFT JOIN creditos cr ON cr.idcredito = r.idcredito
            WHERE $W
            ORDER BY c.nombre ASC, u.nombre ASC
            LIMIT ".(int)$per." OFFSET ".(int)$offset;
    $res = mysqli_query($con, $sql);
    if (!$res) return ['error' => mysqli_error($con)];

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        if ($r['compra'] === 'Nuevo') {
            $g = ($r['grupo']  && $r['grupo']  !== '--') ? $r['grupo']  : '';
            $m = ($r['modelo'] && $r['modelo'] !== '--') ? $r['modelo'] : '';
            $modelo = trim($g.' '.$m);
        } else {
            $modelo = (string)$r['detalleu'];
        }
        $rows[] = [
            'idreserva'      => (int)$r['idreserva'],
            'idcliente'      => (int)$r['idcliente'],
            'idcredito'      => (int)$r['idcredito'],
            'compra'         => $r['compra'],
            'asesor'         => $r['asesor'],
            'cliente'        => $r['cliente'],
            'modelo'         => $modelo,
            'estadopago'     => is_null($r['estadopago']) ? 0 : (int)$r['estadopago'],
            'cancelada'      => (int)$r['cancelada'],
            'credito_estado' => is_null($r['credito_estado']) ? 0 : (int)$r['credito_estado'],
        ];
    }
    return ['total' => $total, 'rows' => $rows];
}

function ec_fecha($d) {
    if (!$d || $d === '0000-00-00') return '';
    $p = explode('-', $d);
    return (count($p) === 3) ? ($p[2].'/'.$p[1].'/'.$p[0]) : $d;
}
