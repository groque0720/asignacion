<?php
/*
 * Endpoint JSON del dashboard de resultados (0km). Agregados + tabla + opciones de filtros.
 * Filtros GET: suc, desde, hasta, asesor (texto), anio, mes, grupo (id), modelo (id).
 * Fuente: enc_respuestas + enc_tokens + asignaciones (JOIN grupos/modelos/usuarios).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';
require __DIR__ . '/funciones/consulta.php';     // enc_utf8
header('Content-Type: application/json; charset=utf-8');

$suc    = isset($_GET['suc'])    ? (int)$_GET['suc']      : 0;
$asesor = isset($_GET['asesor']) ? trim($_GET['asesor'])  : '';
$desde  = isset($_GET['desde'])  ? trim($_GET['desde'])   : '';
$hasta  = isset($_GET['hasta'])  ? trim($_GET['hasta'])   : '';
$anio   = isset($_GET['anio'])   ? (int)$_GET['anio']     : 0;
$mes    = isset($_GET['mes'])    ? (int)$_GET['mes']      : 0;
$grupo  = isset($_GET['grupo'])  ? (int)$_GET['grupo']    : 0;
$modelo = isset($_GET['modelo']) ? (int)$_GET['modelo']   : 0;
$area   = isset($_GET['area'])   ? (int)$_GET['area']     : 0;

$FROM = "FROM enc_respuestas r
         JOIN enc_tokens t ON t.id_token = r.id_token
         JOIN asignaciones a ON a.id_unidad = r.id_asignacion
         LEFT JOIN usuarios u ON u.idusuario = a.id_asesor
         LEFT JOIN grupos g ON g.idgrupo = a.id_grupo
         LEFT JOIN modelos m ON m.idmodelo = a.id_modelo";

$W = "1=1";
if ($suc > 0)       $W .= " AND a.id_sucursal = $suc";
if ($asesor !== '') { $ae = mysqli_real_escape_string($con, $asesor); $W .= " AND u.nombre LIKE '%$ae%'"; }
if ($desde !== '')  { $de = mysqli_real_escape_string($con, $desde);  $W .= " AND DATE(t.fecha_respuesta) >= '$de'"; }
if ($hasta !== '')  { $he = mysqli_real_escape_string($con, $hasta);  $W .= " AND DATE(t.fecha_respuesta) <= '$he'"; }
if ($anio > 0)      $W .= " AND YEAR(t.fecha_respuesta) = $anio";
if ($mes > 0)       $W .= " AND MONTH(t.fecha_respuesta) = $mes";
if ($grupo > 0)     $W .= " AND a.id_grupo = $grupo";
if ($modelo > 0)    $W .= " AND a.id_modelo = $modelo";

// Filtro por ÁREA: si se elige un área, el "promedio" de todas las métricas pasa a ser
// el promedio de las preguntas ponderadas/mostradas de esa área por respuesta (score_area).
// Sólo entran las respuestas que tienen datos de esa área.
$score = 'r.resultado_promedio';
if ($area > 0) {
    $FROM .= " LEFT JOIN (
        SELECT rd.id_respuesta, ROUND(AVG(rd.respuesta_valor),2) AS score_area
        FROM enc_respuestas_detalle rd
        JOIN enc_preguntas p2 ON p2.id_pregunta = rd.id_pregunta
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

// Generadas (para tasa de respuesta): tokens con mismos filtros NO-temporales (suc/asesor/grupo/modelo)
$Wt = "1=1";
if ($suc > 0)       $Wt .= " AND a.id_sucursal = $suc";
if ($asesor !== '') { $ae = mysqli_real_escape_string($con, $asesor); $Wt .= " AND u.nombre LIKE '%$ae%'"; }
if ($grupo > 0)     $Wt .= " AND a.id_grupo = $grupo";
if ($modelo > 0)    $Wt .= " AND a.id_modelo = $modelo";
$generadas = (int)mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n FROM enc_tokens t
            JOIN asignaciones a ON a.id_unidad = t.id_asignacion
            LEFT JOIN usuarios u ON u.idusuario = a.id_asesor
     WHERE $Wt"))['n'];
$tasa = $generadas > 0 ? round($completadas / $generadas * 100, 1) : null;

// Tendencia mensual
$por_mes = [];
$qm = mysqli_query($con,
    "SELECT DATE_FORMAT(t.fecha_respuesta,'%Y-%m') mes, COUNT(*) n, AVG($score) prom
     $FROM WHERE $W AND t.fecha_respuesta IS NOT NULL GROUP BY mes ORDER BY mes ASC");
while ($m = mysqli_fetch_assoc($qm)) $por_mes[] = ['mes' => $m['mes'], 'n' => (int)$m['n'], 'prom' => round((float)$m['prom'], 2)];

// Distribución por nivel (bucket en PHP)
$niveles = [];
$qn = mysqli_query($con, "SELECT nombre, valor_desde, valor_hasta, color FROM enc_niveles ORDER BY valor_desde DESC");
while ($n = mysqli_fetch_assoc($qn)) $niveles[] = ['nombre' => enc_utf8($n['nombre']), 'desde' => (float)$n['valor_desde'], 'hasta' => (float)$n['valor_hasta'], 'color' => $n['color'], 'n' => 0];
$qv = mysqli_query($con, "SELECT $score p $FROM WHERE $W AND $score IS NOT NULL");
while ($row = mysqli_fetch_assoc($qv)) {
    $v = (float)$row['p'];
    foreach ($niveles as &$nv) { if ($v >= $nv['desde'] && $v <= $nv['hasta']) { $nv['n']++; break; } }
    unset($nv);
}

// Promedio por área
$por_area = [];
$qa = mysqli_query($con,
    "SELECT a2.nombre, a2.color, AVG(d.respuesta_valor) prom, COUNT(*) n
     $FROM
     JOIN enc_respuestas_detalle d ON d.id_respuesta = r.id_respuesta
     JOIN enc_preguntas p ON p.id_pregunta = d.id_pregunta
     JOIN enc_areas a2 ON a2.id_area = p.id_area
     WHERE $W AND d.mostrada = 1 AND p.pondera = 1 AND d.respuesta_valor IS NOT NULL
     GROUP BY a2.id_area ORDER BY prom DESC");
while ($a = mysqli_fetch_assoc($qa)) $por_area[] = ['nombre' => enc_utf8($a['nombre']), 'color' => $a['color'], 'prom' => round((float)$a['prom'], 2)];

// Promedio por sucursal
$por_suc = [];
$qs = mysqli_query($con,
    "SELECT a.id_sucursal, s.sucursal, AVG($score) prom, COUNT(*) n
     $FROM LEFT JOIN sucursales s ON s.idsucursal = a.id_sucursal
     WHERE $W GROUP BY a.id_sucursal ORDER BY a.id_sucursal ASC");
while ($s = mysqli_fetch_assoc($qs)) $por_suc[] = ['id_sucursal' => (int)$s['id_sucursal'], 'sucursal' => enc_utf8($s['sucursal']), 'prom' => round((float)$s['prom'], 2), 'n' => (int)$s['n']];

// Top asesores
$top = [];
$qt = mysqli_query($con,
    "SELECT u.nombre asesor, AVG($score) prom, COUNT(*) n
     $FROM WHERE $W AND u.nombre IS NOT NULL GROUP BY u.idusuario HAVING n > 0 ORDER BY prom DESC, n DESC LIMIT 12");
while ($a = mysqli_fetch_assoc($qt)) $top[] = ['asesor' => enc_utf8($a['asesor']), 'prom' => round((float)$a['prom'], 2), 'n' => (int)$a['n']];

// Tabla de respuestas
$tabla = [];
$qf = mysqli_query($con,
    "SELECT r.id_respuesta, t.fecha_respuesta, $score AS resultado_promedio,
            a.cliente, a.chasis, a.nro_orden, a.id_sucursal,
            g.grupo, m.modelo, u.nombre AS asesor
     $FROM WHERE $W ORDER BY t.fecha_respuesta DESC LIMIT 300");
while ($f = mysqli_fetch_assoc($qf)) {
    $veh = trim(trim((string)$f['grupo']) . ' ' . trim((string)$f['modelo']));
    $tabla[] = [
        'id_respuesta' => (int)$f['id_respuesta'],
        'fecha'        => $f['fecha_respuesta'],
        'cliente'      => enc_utf8(trim((string)$f['cliente'])),
        'vehiculo'     => enc_utf8($veh),
        'chasis'       => enc_utf8(trim((string)$f['chasis'])),
        'nro_orden'    => enc_utf8(trim((string)$f['nro_orden'])),
        'asesor'       => enc_utf8(trim((string)$f['asesor'])),
        'id_sucursal'  => (int)$f['id_sucursal'],
        'promedio'     => $f['resultado_promedio'] !== null ? (float)$f['resultado_promedio'] : null,
    ];
}

// ── Opciones de filtros (propias del 0km): años, grupos, modelos con respuestas ──
$anios = [];
$qy = mysqli_query($con, "SELECT DISTINCT YEAR(t.fecha_respuesta) y
                          FROM enc_tokens t JOIN enc_respuestas r ON r.id_token = t.id_token
                          WHERE t.fecha_respuesta IS NOT NULL ORDER BY y DESC");
while ($y = mysqli_fetch_assoc($qy)) if ((int)$y['y'] > 0) $anios[] = (int)$y['y'];

$grupos = [];
$qg = mysqli_query($con, "SELECT DISTINCT g.idgrupo, g.grupo
                          FROM grupos g JOIN asignaciones a ON a.id_grupo = g.idgrupo
                          JOIN enc_respuestas r ON r.id_asignacion = a.id_unidad
                          WHERE g.grupo IS NOT NULL ORDER BY g.grupo ASC");
while ($g = mysqli_fetch_assoc($qg)) $grupos[] = ['id' => (int)$g['idgrupo'], 'nombre' => enc_utf8($g['grupo'])];

$modelos = [];
$qmd = mysqli_query($con, "SELECT DISTINCT m.idmodelo, m.modelo
                           FROM modelos m JOIN asignaciones a ON a.id_modelo = m.idmodelo
                           JOIN enc_respuestas r ON r.id_asignacion = a.id_unidad
                           WHERE m.modelo IS NOT NULL ORDER BY m.modelo ASC");
while ($md = mysqli_fetch_assoc($qmd)) $modelos[] = ['id' => (int)$md['idmodelo'], 'nombre' => enc_utf8($md['modelo'])];

$areas = [];
$qar = mysqli_query($con, "SELECT id_area, nombre FROM enc_areas ORDER BY nro_orden, nombre");
while ($ar = mysqli_fetch_assoc($qar)) $areas[] = ['id' => (int)$ar['id_area'], 'nombre' => enc_utf8($ar['nombre'])];

echo json_encode([
    'ok' => true,
    'kpis' => ['completadas' => $completadas, 'generadas' => $generadas, 'prom' => $prom, 'tasa' => $tasa],
    'por_mes' => $por_mes, 'por_nivel' => $niveles, 'por_area' => $por_area,
    'por_sucursal' => $por_suc, 'top_asesores' => $top, 'tabla' => $tabla,
    'opciones' => ['anios' => $anios, 'grupos' => $grupos, 'modelos' => $modelos, 'areas' => $areas],
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
