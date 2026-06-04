<?php
/*
 * Lógica compartida del módulo Control de Pagos.
 * La usan data.php (JSON paginado) y las exportaciones excel.php / pdf.php,
 * para que el FILTRO y el CÁLCULO financiero estén definidos en un solo lugar.
 */

// Construye [$W, $orderBy] a partir de $_GET (mismos filtros que el módulo viejo).
function cp_where($con) {
    $suc   = isset($_GET['suc'])   ? (int)$_GET['suc']      : 0;
    $est   = isset($_GET['est'])   ? (string)$_GET['est']   : '11';
    $q     = isset($_GET['q'])     ? trim($_GET['q'])       : '';
    $campo = isset($_GET['campo']) ? (string)$_GET['campo'] : 'todo';
    $venta = isset($_GET['venta']) ? trim($_GET['venta'])   : '';

    $sortMap = [
        'idreserva' => 'r.idreserva',
        'nrounidad' => 'r.nrounidad', 'interno' => 'r.interno', 'nroorden' => 'r.nroorden',
        'asesor'    => 'u.nombre',    'cliente' => 'c.nombre',  'fecres'   => 'r.fecres',
        'llego'     => 'r.llego',     'fechacanc' => 'r.fechacanc',
    ];
    $sort    = (isset($_GET['sort']) && isset($sortMap[$_GET['sort']])) ? $sortMap[$_GET['sort']] : null;
    $dir     = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'desc') ? 'DESC' : 'ASC';
    $orderBy = $sort ? ($sort.' '.$dir) : 'u.nombre ASC, c.nombre ASC';

    $qe = mysqli_real_escape_string($con, $q);
    $ve = mysqli_real_escape_string($con, $venta);

    // Nota: r.entregada está siempre en 0 (campo muerto); la entrega real se marca con fechaentrega.
    $W = "r.enviada >= '1'";

    // Con búsqueda se ignoran sucursal/estado/venta y se busca en TODO.
    // Las ANULADAS sí aparecen al buscar (para poder ubicarlas), salvo en la búsqueda por asesor.
    if ($q !== '') {
        switch ($campo) {
            case 'nr':       $W .= " AND r.idreserva = '".$qe."'"; break;
            case 'nu':       $W .= " AND r.nrounidad = '".$qe."'"; break;
            case 'orden':    $W .= " AND r.nroorden LIKE '%$qe%'"; break;
            case 'interno':  $W .= " AND r.interno LIKE '%$qe%'"; break;
            case 'asesor':   // por asesor: solo NO entregadas (fechaentrega vacía) y NO anuladas
                $W .= " AND u.nombre LIKE '%$qe%' AND r.anulada <> 1".
                      " AND (r.fechaentrega IS NULL OR r.fechaentrega = '' OR r.fechaentrega = '0000-00-00')";
                break;
            case 'cliente':  $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR c.tfijo LIKE '%$qe%' OR c.tcelu LIKE '%$qe%')"; break;
            default:         $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR c.tfijo LIKE '%$qe%' OR c.tcelu LIKE '%$qe%'".
                                   " OR r.idreserva LIKE '%$qe%' OR r.nroorden LIKE '%$qe%' OR r.nrounidad LIKE '%$qe%'".
                                   " OR r.interno LIKE '%$qe%' OR f.nombre LIKE '%$qe%')";
        }
    } else {
        $W .= " AND r.anulada <> 1";   // vistas por estado: nunca anuladas
        if ($suc > 0) $W .= " AND u.idsucursal = ".$suc;
        switch ($est) {
            case '1':  $W .= " AND r.llego IS NOT NULL AND r.llego <> 0"; break;
            case '11': $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0"; break;
            case '12': $W .= " AND r.cancelada = 1 AND r.llego IS NOT NULL AND r.llego <> 0"; break;
            case '2':  $W .= " AND (r.llego IS NULL OR r.llego = '')"; break;
            case '21': $W .= " AND r.cancelada = 1 AND (r.llego IS NULL OR r.llego = '')"; break;
            case '3':  $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0 AND datediff(curdate(), r.llego) > 10"; break;
            case '4':  $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0 AND (datediff(curdate(), r.fechacanc) > 0 OR r.fechacanc = 0 OR r.fechacanc = '')"; break;
        }
        if ($venta !== '') $W .= " AND r.venta = '".$ve."'";
    }

    return [$W, $orderBy];
}

// Texto descriptivo del estado seleccionado (para títulos de exportación).
function cp_estado_nombre() {
    $est = isset($_GET['est']) ? (string)$_GET['est'] : '11';
    $m = [
        '1'=>'Llegadas Todas', '11'=>'Llegadas No Canceladas', '12'=>'Llegadas Canceladas',
        '2'=>'No Llegadas', '21'=>'No Llegadas Canceladas', '3'=>'Llegadas +10 días',
        '4'=>'Cancelación Vencida',
    ];
    return $m[$est] ?? '';
}
function cp_sucursal_nombre() {
    $suc = isset($_GET['suc']) ? (int)$_GET['suc'] : 0;
    $m = [0=>'Todas', 1=>'Resistencia', 2=>'Sáenz Peña', 3=>'Villa Ángela', 4=>'Charata'];
    return $m[$suc] ?? 'Todas';
}

// Cálculo financiero (idéntico al módulo viejo) a partir de los 8 agregados.
function cp_calc($a) {
    $pl_usado    = (float)($a['pl_usado']    ?? 0);
    $pl_efectivo = (float)($a['pl_efectivo'] ?? 0);
    $pl_credito  = (float)($a['pl_credito']  ?? 0);
    $pl_leasing  = (float)($a['pl_leasing']  ?? 0);
    $ld_usado    = (float)($a['ld_usado']    ?? 0);
    $ld_mov1     = (float)($a['ld_mov1']     ?? 0);
    $ld_credito  = (float)($a['ld_credito']  ?? 0);
    $ld_leasing  = (float)($a['ld_leasing']  ?? 0);

    $total_usado = $ld_usado - $pl_usado;
    $efectivo    = $ld_mov1 - $ld_leasing - $ld_credito - $pl_usado - $pl_efectivo - $total_usado;
    $credito     = $ld_credito - $pl_credito;
    $leasing     = $ld_leasing - $pl_leasing;
    $saldo       = $efectivo + $credito + $leasing + $total_usado;

    return [
        'usado'    => $total_usado,
        'usado_p'  => ($pl_usado > 0),
        'efectivo' => $efectivo,
        'credito'  => $credito,
        'leasing'  => $leasing,
        'saldo'    => $saldo,
    ];
}

function cp_modelo_texto($r) {
    if (($r['compra'] ?? '') === 'Nuevo') {
        $g = (isset($r['grupo'])  && $r['grupo']  !== null && $r['grupo']  !== '--') ? $r['grupo']  : '';
        $m = (isset($r['modelo']) && $r['modelo'] !== null && $r['modelo'] !== '--') ? $r['modelo'] : '';
        return trim($g.' '.$m);
    }
    return (string)($r['detalleu'] ?? '');
}

function cp_fecha($d) {
    if (!$d || $d === '0000-00-00') return '';
    $p = explode('-', $d);
    return (count($p) === 3) ? ($p[2].'/'.$p[1].'/'.$p[0]) : $d;
}

// Trae TODAS las filas del filtro (sin paginar) con las columnas ya calculadas.
// Usa derived-table JOINs (una pasada agregada), así sirve para cualquier volumen.
function cp_fetch_todo($con, $W, $orderBy) {
    $sql = "SELECT
            r.idreserva, r.nrounidad, r.interno, r.nroorden, r.fecres, r.llego, r.fechacanc,
            r.fechaentrega, r.venta AS tipo_venta, r.compra, r.detalleu, r.obscanc AS obs,
            c.nombre AS cliente, u.nombre AS asesor,
            g.grupo AS grupo, m.modelo AS modelo,
            COALESCE(pl.pl_usado,0)   AS pl_usado,
            COALESCE(pl.pl_efectivo,0) AS pl_efectivo,
            COALESCE(pl.pl_credito,0)  AS pl_credito,
            COALESCE(pl.pl_leasing,0)  AS pl_leasing,
            COALESCE(ld.ld_usado,0)    AS ld_usado,
            COALESCE(ld.ld_mov1,0)     AS ld_mov1,
            COALESCE(ld.ld_credito,0)  AS ld_credito,
            COALESCE(ld.ld_leasing,0)  AS ld_leasing
        FROM reservas r
        INNER JOIN clientes c ON c.idcliente = r.idcliente
        INNER JOIN usuarios u ON u.idusuario = r.idusuario
        LEFT  JOIN facturas f ON f.idfactura = r.idfactura
        LEFT  JOIN grupos   g ON g.idgrupo   = r.idgrupo
        LEFT  JOIN modelos  m ON m.idmodelo  = r.idmodelo
        LEFT JOIN (SELECT idreserva,
                SUM(CASE WHEN modo=6 THEN monto ELSE 0 END)                AS pl_usado,
                SUM(CASE WHEN modo IN (1,2,5,7,8,9) THEN monto ELSE 0 END) AS pl_efectivo,
                SUM(CASE WHEN modo=3 THEN monto ELSE 0 END)                AS pl_credito,
                SUM(CASE WHEN modo=4 THEN monto ELSE 0 END)                AS pl_leasing
            FROM pagos_lineas GROUP BY idreserva) pl ON pl.idreserva = r.idreserva
        LEFT JOIN (SELECT ld.idreserva,
                SUM(CASE WHEN ld.idcodigo=51 THEN ld.monto ELSE 0 END)     AS ld_usado,
                SUM(CASE WHEN ld.movimiento=1 THEN ld.monto ELSE 0 END)    AS ld_mov1,
                SUM(CASE WHEN co.tipocredito='1' THEN ld.monto ELSE 0 END) AS ld_credito,
                SUM(CASE WHEN co.tipocredito='3' THEN ld.monto ELSE 0 END) AS ld_leasing
            FROM lineas_detalle ld LEFT JOIN codigos co ON co.idcodigo = ld.idcodigo
            GROUP BY ld.idreserva) ld ON ld.idreserva = r.idreserva
        WHERE $W
        ORDER BY $orderBy";

    $res = mysqli_query($con, $sql);
    if (!$res) return [[], mysqli_error($con)];

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = array_merge($r, cp_calc($r), ['modelo_txt' => cp_modelo_texto($r)]);
    }
    return [$rows, ''];
}
