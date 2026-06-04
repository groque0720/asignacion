<?php
/*
 * Motor de datos del módulo Control de Pagos (versión moderna).
 * Devuelve JSON paginado. Reemplaza las ~12 consultas POR FILA del módulo viejo
 * (ventas/web/control_pagos_cliente_cuerpo.php) por:
 *   - 1 query de totales (count + saldo total del filtro completo)
 *   - 1 query de la página (filas + dimensiones)
 *   - 2 queries de agregados SOLO para los idreserva de la página (pagos / líneas)
 *
 * Fórmula financiera (idéntica al módulo viejo, verificada):
 *   total_usado  = ld_usado - pl_usado
 *   efectivo     = ld_mov1 - ld_leasing - ld_credito - pl_usado - pl_efectivo - total_usado
 *   credito      = ld_credito - pl_credito
 *   leasing      = ld_leasing - pl_leasing
 *   saldo        = efectivo + credito + leasing + total_usado   (== ld_mov1 - Σpagos)
 */

header('Content-Type: application/json; charset=utf-8');
@session_start();
include("funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

// ─── Parámetros ─────────────────────────────────────────────────────────────
$suc   = isset($_GET['suc'])   ? (int)$_GET['suc']            : 0;
$est   = isset($_GET['est'])   ? (string)$_GET['est']         : '11';
$q     = isset($_GET['q'])     ? trim($_GET['q'])             : '';
$campo = isset($_GET['campo']) ? (string)$_GET['campo']       : 'todo';
$venta = isset($_GET['venta']) ? trim($_GET['venta'])         : '';
$page  = isset($_GET['page'])  ? max(1, (int)$_GET['page'])   : 1;
$per   = isset($_GET['per'])   ? (int)$_GET['per']            : 50;
if ($per < 1)   $per = 50;
if ($per > 500) $per = 500;
$offset = ($page - 1) * $per;

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

// ─── WHERE (mismos filtros que el módulo viejo) ─────────────────────────────
$W = "r.anulada <> 1 AND r.entregada < 3 AND r.enviada >= '1'";
// Cuando hay búsqueda, se ignoran los filtros de sucursal/estado/venta y se busca
// en TODO (igual que el módulo viejo). Así se puede encontrar una reserva aunque
// no haya llegado, esté en otra sucursal, etc.
if ($q !== '') {
    switch ($campo) {
        case 'nr':                                                  // Nro Reserva: exacto
            $W .= " AND r.idreserva = '".$qe."'"; break;
        case 'nu':                                                  // Nro Unidad: exacto
            $W .= " AND r.nrounidad = '".$qe."'"; break;
        case 'orden':                                               // Nro Orden: parcial
            $W .= " AND r.nroorden LIKE '%$qe%'"; break;
        case 'interno':                                             // Interno: parcial
            $W .= " AND r.interno LIKE '%$qe%'"; break;
        case 'cliente':                                             // Cliente: nombre o documento
            $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR c.tfijo LIKE '%$qe%' OR c.tcelu LIKE '%$qe%')"; break;
        default:                                                    // Todo
            $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR c.tfijo LIKE '%$qe%' OR c.tcelu LIKE '%$qe%'".
                  " OR r.idreserva LIKE '%$qe%' OR r.nroorden LIKE '%$qe%' OR r.nrounidad LIKE '%$qe%'".
                  " OR r.interno LIKE '%$qe%' OR f.nombre LIKE '%$qe%')";
    }
} else {
    if ($suc > 0) $W .= " AND u.idsucursal = ".$suc;

    switch ($est) {
        case '1':  $W .= " AND r.llego IS NOT NULL AND r.llego <> 0"; break;                                   // llegadas todas
        case '11': $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0"; break;               // llegadas no canceladas
        case '12': $W .= " AND r.cancelada = 1 AND r.llego IS NOT NULL AND r.llego <> 0"; break;               // llegadas canceladas
        case '2':  $W .= " AND (r.llego IS NULL OR r.llego = '')"; break;                                      // no llegadas
        case '21': $W .= " AND r.cancelada = 1 AND (r.llego IS NULL OR r.llego = '')"; break;                  // no llegadas canceladas
        case '3':  $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0 AND datediff(curdate(), r.llego) > 10"; break; // +10 días
        case '4':  $W .= " AND r.cancelada = 0 AND r.llego IS NOT NULL AND r.llego <> 0 AND (datediff(curdate(), r.fechacanc) > 0 OR r.fechacanc = 0 OR r.fechacanc = '')"; break; // cancelación vencida
    }
    if ($venta !== '') $W .= " AND r.venta = '".$ve."'";
}

$BASE_JOINS = "FROM reservas r
    INNER JOIN clientes c ON c.idcliente = r.idcliente
    INNER JOIN usuarios u ON u.idusuario = r.idusuario
    LEFT  JOIN facturas f ON f.idfactura = r.idfactura";

// ─── Totales del filtro completo (1 query) ──────────────────────────────────
$sqlTot = "SELECT COUNT(*) AS filas,
    COALESCE(SUM(COALESCE(ldx.mov1,0) - COALESCE(plx.tot,0)),0) AS saldo_total
    $BASE_JOINS
    LEFT JOIN (SELECT idreserva, SUM(CASE WHEN movimiento=1 THEN monto ELSE 0 END) AS mov1
               FROM lineas_detalle GROUP BY idreserva) ldx ON ldx.idreserva = r.idreserva
    LEFT JOIN (SELECT idreserva, SUM(monto) AS tot
               FROM pagos_lineas WHERE modo IN (1,2,3,4,5,6,7,8,9) GROUP BY idreserva) plx ON plx.idreserva = r.idreserva
    WHERE $W";
$rTot = mysqli_query($con, $sqlTot);
if (!$rTot) { http_response_code(500); echo json_encode(["error"=>mysqli_error($con), "sql"=>$sqlTot]); exit; }
$tot = mysqli_fetch_assoc($rTot);
$filas = (int)$tot['filas'];
$saldo_total = (float)$tot['saldo_total'];

// ─── Página (1 query) ───────────────────────────────────────────────────────
$sqlPage = "SELECT
        r.idreserva, r.nrounidad, r.interno, r.nroorden, r.fecres, r.llego, r.fechacanc,
        r.cancelada, r.enviada, r.estadopago, r.idfactura, r.idcredito, r.idcliente,
        r.venta AS tipo_venta, r.compra, r.detalleu, r.obscanc AS obs, r.fechaentrega,
        c.nombre AS cliente, u.nombre AS asesor, u.idsucursal,
        g.grupo AS grupo, m.modelo AS modelo,
        f.estado AS factura_estado, cr.estado AS credito_estado
    $BASE_JOINS
    LEFT JOIN grupos   g  ON g.idgrupo   = r.idgrupo
    LEFT JOIN modelos  m  ON m.idmodelo  = r.idmodelo
    LEFT JOIN creditos cr ON cr.idcredito = r.idcredito
    WHERE $W
    ORDER BY $orderBy
    LIMIT $per OFFSET $offset";
$rPage = mysqli_query($con, $sqlPage);
if (!$rPage) { http_response_code(500); echo json_encode(["error"=>mysqli_error($con), "sql"=>$sqlPage]); exit; }

$rows = [];
$ids  = [];
while ($r = mysqli_fetch_assoc($rPage)) {
    $rows[$r['idreserva']] = $r;
    $ids[] = (int)$r['idreserva'];
}

// ─── Agregados SOLO de los idreserva de la página (2 queries) ───────────────
$agg = [];
if (!empty($ids)) {
    $in = implode(',', $ids);

    $rPl = mysqli_query($con,
        "SELECT idreserva,
            SUM(CASE WHEN modo=6 THEN monto ELSE 0 END)                  AS pl_usado,
            SUM(CASE WHEN modo IN (1,2,5,7,8,9) THEN monto ELSE 0 END)   AS pl_efectivo,
            SUM(CASE WHEN modo=3 THEN monto ELSE 0 END)                  AS pl_credito,
            SUM(CASE WHEN modo=4 THEN monto ELSE 0 END)                  AS pl_leasing
         FROM pagos_lineas WHERE idreserva IN ($in) GROUP BY idreserva");
    while ($a = mysqli_fetch_assoc($rPl)) {
        $agg[$a['idreserva']] = array_merge($agg[$a['idreserva']] ?? [], $a);
    }

    $rLd = mysqli_query($con,
        "SELECT ld.idreserva,
            SUM(CASE WHEN ld.idcodigo=51 THEN ld.monto ELSE 0 END)        AS ld_usado,
            SUM(CASE WHEN ld.movimiento=1 THEN ld.monto ELSE 0 END)       AS ld_mov1,
            SUM(CASE WHEN co.tipocredito='1' THEN ld.monto ELSE 0 END)    AS ld_credito,
            SUM(CASE WHEN co.tipocredito='3' THEN ld.monto ELSE 0 END)    AS ld_leasing
         FROM lineas_detalle ld
         LEFT JOIN codigos co ON co.idcodigo = ld.idcodigo
         WHERE ld.idreserva IN ($in) GROUP BY ld.idreserva");
    while ($a = mysqli_fetch_assoc($rLd)) {
        $agg[$a['idreserva']] = array_merge($agg[$a['idreserva']] ?? [], $a);
    }
}

// ─── Armar salida ───────────────────────────────────────────────────────────
function modeloTexto($r) {
    if ($r['compra'] === 'Nuevo') {
        $g = ($r['grupo']  !== null && $r['grupo']  !== '--') ? $r['grupo']  : '';
        $m = ($r['modelo'] !== null && $r['modelo'] !== '--') ? $r['modelo'] : '';
        return trim($g.' '.$m);
    }
    return (string)$r['detalleu'];
}

$out = [];
foreach ($rows as $id => $r) {
    $a = $agg[$id] ?? [];
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

    $llego_ok = ($r['llego'] !== null && $r['llego'] !== '' && $r['llego'] !== '0000-00-00');

    $out[] = [
        'idreserva'      => (int)$r['idreserva'],
        'idcliente'      => (int)$r['idcliente'],
        'idcredito'      => (int)$r['idcredito'],
        'idfactura'      => (int)$r['idfactura'],
        'nrounidad'      => $r['nrounidad'],
        'interno'        => $r['interno'],
        'nroorden'       => $r['nroorden'],
        'asesor'         => $r['asesor'],
        'cliente'        => $r['cliente'],
        'modelo'         => modeloTexto($r),
        'tipo_venta'     => $r['tipo_venta'],
        'usado'          => $total_usado,
        'usado_p'        => ($pl_usado > 0),
        'efectivo'       => $efectivo,
        'credito'        => $credito,
        'leasing'        => $leasing,
        'saldo'          => $saldo,
        'fecres'         => $r['fecres'],
        'llego'          => $llego_ok ? $r['llego'] : null,
        'fechacanc'      => ($r['fechacanc'] && $r['fechacanc'] !== '0000-00-00') ? $r['fechacanc'] : null,
        'fechaentrega'   => ($r['fechaentrega'] && $r['fechaentrega'] !== '0000-00-00') ? $r['fechaentrega'] : null,
        'obs'            => $r['obs'],
        'cancelada'      => (int)$r['cancelada'],
        'enviada'        => (int)$r['enviada'],
        'estadopago'     => is_null($r['estadopago']) ? 0 : (int)$r['estadopago'],
        'factura_estado' => is_null($r['factura_estado']) ? 0 : (int)$r['factura_estado'],
        'credito_estado' => is_null($r['credito_estado']) ? 0 : (int)$r['credito_estado'],
        'tiene_arribo'   => $llego_ok,
    ];
}

echo json_encode([
    'ok'          => true,
    'page'        => $page,
    'per'         => $per,
    'total'       => $filas,
    'pages'       => $per > 0 ? (int)ceil($filas / $per) : 1,
    'saldo_total' => $saldo_total,
    'rows'        => $out,
], JSON_UNESCAPED_UNICODE);

mysqli_close($con);
