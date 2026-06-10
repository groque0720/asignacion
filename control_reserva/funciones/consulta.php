<?php
/*
 * Lógica de datos del Control de Reservas (la usan las actions).
 * Requiere: $con (config_app.php).
 *
 * Replica las queries de ventas/web/control_reservas.php + _cuerpo + _filtro + _paginas,
 * pero resolviendo grupo/modelo/factura con LEFT JOIN (1 query) en vez de N por fila.
 */

// Listado paginado de reservas (enviada >= 1). Devuelve ['total'=>int, 'rows'=>array].
function cr_lista($con, $q, $per, $offset) {
    $qe = mysqli_real_escape_string($con, $q);

    $W = "r.enviada >= 1";
    if ($q !== '') $W .= " AND (r.idreserva LIKE '%$qe%' OR c.nombre LIKE '%$qe%')";

    $FROM = "FROM reservas r
        INNER JOIN clientes c ON c.idcliente = r.idcliente
        INNER JOIN usuarios u ON u.idusuario = r.idusuario
        LEFT JOIN grupos   g ON g.idgrupo   = r.idgrupo
        LEFT JOIN modelos  m ON m.idmodelo  = r.idmodelo
        LEFT JOIN facturas f ON f.idfactura = r.idfactura";

    $total = (int)mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) n $FROM WHERE $W"))['n'];

    // Orden idéntico al original: enviada ASC, fecres DESC.
    $sql = "SELECT r.idreserva, r.compra, r.detalleu, r.fecres, r.enviada, r.estadopago,
                   r.idcliente, r.anulada, r.idfactura,
                   c.nombre AS cliente, u.nombre AS asesor,
                   g.grupo AS grupo, m.modelo AS modelo,
                   f.estado AS factura_estado
            $FROM
            WHERE $W
            ORDER BY r.enviada ASC, r.fecres DESC
            LIMIT ".(int)$per." OFFSET ".(int)$offset;
    $res = mysqli_query($con, $sql);
    if (!$res) return ['error' => mysqli_error($con)];

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        // Modelo: si la compra es "Nuevo" → grupo + modelo; si es usado → detalleu.
        if ($r['compra'] === 'Nuevo') {
            $g = ($r['grupo']  && $r['grupo']  !== '--') ? $r['grupo']  : '';
            $m = ($r['modelo'] && $r['modelo'] !== '--') ? $r['modelo'] : '';
            $modelo = trim($g.' '.$m);
        } else {
            $modelo = (string)$r['detalleu'];
        }
        $rows[] = [
            'idreserva'      => (int)$r['idreserva'],
            'compra'         => $r['compra'],
            'asesor'         => $r['asesor'],
            'cliente'        => $r['cliente'],
            'fecres'         => ($r['fecres'] && $r['fecres'] !== '0000-00-00') ? $r['fecres'] : null,
            'modelo'         => $modelo,
            'enviada'        => (int)$r['enviada'],
            'estadopago'     => is_null($r['estadopago']) ? 0 : (int)$r['estadopago'],
            'idcliente'      => (int)$r['idcliente'],
            'anulada'        => (int)$r['anulada'],
            'idfactura'      => (int)$r['idfactura'],
            'factura_estado' => is_null($r['factura_estado']) ? 0 : (int)$r['factura_estado'],
        ];
    }
    return ['total' => $total, 'rows' => $rows];
}

// Notificaciones sin ver del usuario (igual que control_res_act_noti.php).
function cr_noti_count($con, $idusuario) {
    $idusuario = (int)$idusuario;
    $r = mysqli_query($con,
        "SELECT COUNT(*) n FROM notificaciones WHERE idusuario = ".$idusuario." AND visto = 0 AND borrar = 0");
    return $r ? (int)mysqli_fetch_assoc($r)['n'] : 0;
}
