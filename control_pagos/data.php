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
include("_consulta.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

// ─── Parámetros de paginación ───────────────────────────────────────────────
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per   = isset($_GET['per'])  ? (int)$_GET['per']           : 50;
if ($per < 1)   $per = 50;
if ($per > 500) $per = 500;
$offset = ($page - 1) * $per;

// ─── Filtro (compartido con las exportaciones) ──────────────────────────────
list($W, $orderBy) = cp_where($con);

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
        r.cancelada, r.anulada, r.enviada, r.estadopago, r.idfactura, r.idcredito, r.idcliente,
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
$out = [];
foreach ($rows as $id => $r) {
    $c = cp_calc($agg[$id] ?? []);

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
        'modelo'         => cp_modelo_texto($r),
        'tipo_venta'     => $r['tipo_venta'],
        'usado'          => $c['usado'],
        'usado_p'        => $c['usado_p'],
        'efectivo'       => $c['efectivo'],
        'credito'        => $c['credito'],
        'leasing'        => $c['leasing'],
        'saldo'          => $c['saldo'],
        'fecres'         => $r['fecres'],
        'llego'          => $llego_ok ? $r['llego'] : null,
        'fechacanc'      => ($r['fechacanc'] && $r['fechacanc'] !== '0000-00-00') ? $r['fechacanc'] : null,
        'fechaentrega'   => ($r['fechaentrega'] && $r['fechaentrega'] !== '0000-00-00') ? $r['fechaentrega'] : null,
        'obs'            => $r['obs'],
        'cancelada'      => (int)$r['cancelada'],
        'anulada'        => (int)$r['anulada'],
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
