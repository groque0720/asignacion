<?php
/*
 * Endpoint JSON del dashboard de resultados (usados). Agregados + tabla + opciones.
 * Filtros GET: suc, desde, hasta, asesor (texto), anio, mes, area (id).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';
require __DIR__ . '/funciones/consulta.php';     // eu_utf8
header('Content-Type: application/json; charset=utf-8');

$suc    = isset($_GET['suc'])    ? (int)$_GET['suc']      : 0;
$asesor = isset($_GET['asesor']) ? trim($_GET['asesor'])  : '';
$desde  = isset($_GET['desde'])  ? trim($_GET['desde'])   : '';
$hasta  = isset($_GET['hasta'])  ? trim($_GET['hasta'])   : '';
$anio   = isset($_GET['anio'])   ? (int)$_GET['anio']     : 0;
$mes    = isset($_GET['mes'])    ? (int)$_GET['mes']      : 0;
$area   = isset($_GET['area'])   ? (int)$_GET['area']     : 0;

$FROM = "FROM encu_respuestas r
         JOIN encu_tokens t ON t.id_token = r.id_token
         JOIN view_asignaciones_usados_entregadas v ON v.id_unidad = r.id_asignacion";
$W = "1=1";
if ($suc > 0)      $W .= " AND v.id_sucursal = $suc";
if ($asesor !== '') { $ae = mysqli_real_escape_string($con, $asesor); $W .= " AND v.asesor_venta LIKE '%$ae%'"; }
if ($desde !== '')  { $de = mysqli_real_escape_string($con, $desde);  $W .= " AND DATE(t.fecha_respuesta) >= '$de'"; }
if ($hasta !== '')  { $he = mysqli_real_escape_string($con, $hasta);  $W .= " AND DATE(t.fecha_respuesta) <= '$he'"; }
if ($anio > 0)      $W .= " AND YEAR(t.fecha_respuesta) = $anio";
if ($mes > 0)       $W .= " AND MONTH(t.fecha_respuesta) = $mes";

// Filtro por ÁREA: el "promedio" pasa a ser el de las preguntas ponderadas/mostradas
// de esa área por respuesta (score_area). Sólo entran respuestas con datos de esa área.
$score = 'r.resultado_promedio';
if ($area > 0) {
    $FROM .= " LEFT JOIN (
        SELECT rd.id_respuesta, ROUND(AVG(rd.respuesta_valor),2) AS score_area
        FROM encu_respuestas_detalle rd
        JOIN encu_preguntas p2 ON p2.id_pregunta = rd.id_pregunta
        WHERE p2.id_area = $area AND p2.pondera = 1 AND rd.mostrada = 1
        GROUP BY rd.id_respuesta
    ) area_sc ON area_sc.id_respuesta = r.id_respuesta";
    $score = 'area_sc.score_area';
    $W .= " AND area_sc.score_area IS NOT NULL";
}

// KPIs
$k = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) completadas, AVG($score) prom $FROM WHERE $W"));
$completadas = (int)$k['completadas'];
$prom = $k['prom'] !== null ? round((float)$k['prom'], 2) : null;

// Generadas (tasa): tokens con mismos filtros suc/asesor
$Wt = "1=1";
if ($suc > 0)       $Wt .= " AND v.id_sucursal = $suc";
if ($asesor !== '') { $ae = mysqli_real_escape_string($con, $asesor); $Wt .= " AND v.asesor_venta LIKE '%$ae%'"; }
$generadas = (int)mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n FROM encu_tokens t JOIN view_asignaciones_usados_entregadas v ON v.id_unidad = t.id_asignacion WHERE $Wt"))['n'];
$tasa = $generadas > 0 ? round($completadas / $generadas * 100, 1) : null;

// Tendencia mensual
$por_mes = [];
$qm = mysqli_query($con,
    "SELECT DATE_FORMAT(t.fecha_respuesta,'%Y-%m') mes, COUNT(*) n, AVG($score) prom
     $FROM WHERE $W AND t.fecha_respuesta IS NOT NULL GROUP BY mes ORDER BY mes ASC");
while ($m = mysqli_fetch_assoc($qm)) $por_mes[] = ['mes' => $m['mes'], 'n' => (int)$m['n'], 'prom' => round((float)$m['prom'], 2)];

// Distribución por nivel
$niveles = [];
$qn = mysqli_query($con, "SELECT nombre, valor_desde, valor_hasta, color FROM encu_niveles ORDER BY valor_desde DESC");
while ($n = mysqli_fetch_assoc($qn)) $niveles[] = ['nombre' => eu_utf8($n['nombre']), 'desde' => (float)$n['valor_desde'], 'hasta' => (float)$n['valor_hasta'], 'color' => $n['color'], 'n' => 0];
$qv = mysqli_query($con, "SELECT $score p $FROM WHERE $W AND $score IS NOT NULL");
while ($row = mysqli_fetch_assoc($qv)) {
    $v = (float)$row['p'];
    foreach ($niveles as &$nv) { if ($v >= $nv['desde'] && $v <= $nv['hasta']) { $nv['n']++; break; } }
    unset($nv);
}

// Promedio por área (todas las áreas, independiente del filtro)
$por_area = [];
$qa = mysqli_query($con,
    "SELECT a2.nombre, a2.color, AVG(d.respuesta_valor) prom, COUNT(*) n
     $FROM
     JOIN encu_respuestas_detalle d ON d.id_respuesta = r.id_respuesta
     JOIN encu_preguntas p ON p.id_pregunta = d.id_pregunta
     JOIN encu_areas a2 ON a2.id_area = p.id_area
     WHERE $W AND d.mostrada = 1 AND p.pondera = 1 AND d.respuesta_valor IS NOT NULL
     GROUP BY a2.id_area ORDER BY prom DESC");
while ($a = mysqli_fetch_assoc($qa)) $por_area[] = ['nombre' => eu_utf8($a['nombre']), 'color' => $a['color'], 'prom' => round((float)$a['prom'], 2)];

// Promedio por sucursal
$por_suc = [];
$qs = mysqli_query($con,
    "SELECT v.id_sucursal, s.sucursal, AVG($score) prom, COUNT(*) n
     $FROM LEFT JOIN sucursales s ON s.idsucursal = v.id_sucursal
     WHERE $W GROUP BY v.id_sucursal ORDER BY v.id_sucursal ASC");
while ($s = mysqli_fetch_assoc($qs)) $por_suc[] = ['id_sucursal' => (int)$s['id_sucursal'], 'sucursal' => eu_utf8($s['sucursal']), 'prom' => round((float)$s['prom'], 2), 'n' => (int)$s['n']];

// Top asesores
$top = [];
$qt = mysqli_query($con,
    "SELECT v.asesor_venta asesor, AVG($score) prom, COUNT(*) n
     $FROM WHERE $W GROUP BY v.asesor_venta HAVING n > 0 ORDER BY prom DESC, n DESC LIMIT 12");
while ($a = mysqli_fetch_assoc($qt)) $top[] = ['asesor' => eu_utf8($a['asesor']), 'prom' => round((float)$a['prom'], 2), 'n' => (int)$a['n']];

// Tabla de respuestas
$tabla = [];
$qf = mysqli_query($con,
    "SELECT r.id_respuesta, t.fecha_respuesta, $score AS resultado_promedio,
            v.cliente, v.vehiculo, v.asesor_venta, v.id_sucursal
     $FROM WHERE $W ORDER BY t.fecha_respuesta DESC LIMIT 300");
while ($f = mysqli_fetch_assoc($qf)) {
    $tabla[] = [
        'id_respuesta' => (int)$f['id_respuesta'],
        'fecha'        => $f['fecha_respuesta'],
        'cliente'      => eu_utf8(trim((string)$f['cliente'])),
        'vehiculo'     => eu_utf8(trim((string)$f['vehiculo'])),
        'asesor'       => eu_utf8(trim((string)$f['asesor_venta'])),
        'id_sucursal'  => (int)$f['id_sucursal'],
        'promedio'     => $f['resultado_promedio'] !== null ? (float)$f['resultado_promedio'] : null,
    ];
}

// ── Opciones de filtros: años con respuestas + áreas ──
$anios = [];
$qy = mysqli_query($con, "SELECT DISTINCT YEAR(t.fecha_respuesta) y
                          FROM encu_tokens t JOIN encu_respuestas r ON r.id_token = t.id_token
                          WHERE t.fecha_respuesta IS NOT NULL ORDER BY y DESC");
while ($y = mysqli_fetch_assoc($qy)) if ((int)$y['y'] > 0) $anios[] = (int)$y['y'];

$areas = [];
$qar = mysqli_query($con, "SELECT id_area, nombre FROM encu_areas ORDER BY nro_orden, nombre");
while ($ar = mysqli_fetch_assoc($qar)) $areas[] = ['id' => (int)$ar['id_area'], 'nombre' => eu_utf8($ar['nombre'])];

echo json_encode([
    'ok' => true,
    'kpis' => ['completadas' => $completadas, 'generadas' => $generadas, 'prom' => $prom, 'tasa' => $tasa],
    'por_mes' => $por_mes, 'por_nivel' => $niveles, 'por_area' => $por_area,
    'por_sucursal' => $por_suc, 'top_asesores' => $top, 'tabla' => $tabla,
    'opciones' => ['anios' => $anios, 'areas' => $areas],
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
