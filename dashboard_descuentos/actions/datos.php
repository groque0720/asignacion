<?php
/*
 * Datos del dashboard para data.php. Deja el resultado en $salida.
 * Requiere: $con (config_app.php) y dd_* (funciones/consulta.php).
 *
 * Trae las filas (una por unidad 0km entregada) y las agrega en PHP a:
 * KPIs, ranking por sucursal / modelo / vendedor, tendencia mensual y tabla detalle.
 */

$f = [
    'anio'       => isset($_GET['anio'])       ? (int)$_GET['anio']       : (int)date('Y'),
    'desde'      => isset($_GET['desde'])      ? trim($_GET['desde'])     : '',
    'hasta'      => isset($_GET['hasta'])      ? trim($_GET['hasta'])     : '',
    'idsucursal' => isset($_GET['idsucursal']) ? (int)$_GET['idsucursal'] : 0,
    'idgrupo'    => isset($_GET['idgrupo'])    ? (int)$_GET['idgrupo']    : 0,
    'idvendedor' => isset($_GET['idvendedor']) ? (int)$_GET['idvendedor'] : 0,
];

$r = dd_filas($con, $f);
if (isset($r['error'])) { http_response_code(500); $salida = ['error' => $r['error']]; return; }
$rows = $r['rows'];

// ── KPIs globales ───────────────────────────────────────────────────────────
$entregadas = count($rows);
$conDesc = 0; $montoDesc = 0.0; $operNeta = 0.0; $bruto = 0.0;
foreach ($rows as $x) {
    $conDesc   += $x['con_desc'];
    $montoDesc += $x['descuento'];
    $operNeta  += $x['operacion'];
    $bruto     += $x['bruto'];
}
$kpis = [
    'entregadas'    => $entregadas,
    'conDescuento'  => $conDesc,
    'penetracion'   => $entregadas ? round(100 * $conDesc / $entregadas, 1) : 0,
    'montoDescuento'=> round($montoDesc),
    'operacionNeta' => round($operNeta),
    'descPromedio'  => $conDesc ? round($montoDesc / $conDesc) : 0,        // por unidad con descuento
    'descPctGlobal' => $bruto > 0 ? round(100 * $montoDesc / $bruto, 1) : 0, // descuento / precio bruto
];

$porSucursal = dd_agrupar($rows, function ($x) { return $x['sucursal']; });
$porModelo   = dd_agrupar($rows, function ($x) { return $x['modelo']; });
$porVendedor = dd_agrupar($rows,
    function ($x) { return $x['vendedor']; },
    function ($x) { return ['sucursal' => $x['sucursal']]; });

// ── Tendencia mensual (por mes de entrega) ──────────────────────────────────
$tend = [];
foreach ($rows as $x) {
    $p = substr($x['fecha'], 0, 7); // YYYY-MM
    if (!$p) continue;
    if (!isset($tend[$p])) $tend[$p] = ['periodo'=>$p, 'entregadas'=>0, 'conDesc'=>0, 'monto'=>0.0];
    $tend[$p]['entregadas']++;
    $tend[$p]['conDesc'] += $x['con_desc'];
    $tend[$p]['monto']   += $x['descuento'];
}
ksort($tend);
$tendencia = array_values($tend);
foreach ($tendencia as &$t) {
    $t['monto']       = round($t['monto']);
    $t['penetracion'] = $t['entregadas'] ? round(100 * $t['conDesc'] / $t['entregadas'], 1) : 0;
    $mm = (int)substr($t['periodo'], 5, 2);
    $t['etiqueta'] = dd_mes_nombre($mm) . ' ' . substr($t['periodo'], 0, 4);
}
unset($t);

$salida = [
    'ok'          => true,
    'filtros'     => $f,
    'kpis'        => $kpis,
    'porSucursal' => $porSucursal,
    'porModelo'   => $porModelo,
    'porVendedor' => $porVendedor,
    'tendencia'   => $tendencia,
    'tabla'       => $rows,
    'opciones'    => dd_opciones($con, $f),
];
