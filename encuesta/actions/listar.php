<?php
/*
 * Listado paginado de entregas de 0km para data.php. Deja el resultado en $salida.
 * Requiere: $con (config_app.php) y helpers enc_* (funciones/consulta.php).
 *
 * El estado se toma de asignaciones.con_encuesta (0/1/2), no se deriva.
 */

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per  = isset($_GET['per'])  ? (int)$_GET['per']          : 50;
if ($per < 1)   $per = 50;
if ($per > 200) $per = 200;
$offset = ($page - 1) * $per;

$FROM   = enc_from();
$Wbase  = enc_where_base($con);
$Wtabla = enc_where_estado($Wbase);
$order  = enc_order();

// ── Agregados (sobre el filtro base: sucursal + búsqueda, sin estado) ────────
$agg = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT
        COUNT(*) AS total,
        SUM(a.con_encuesta = 0) AS sin_generar,
        SUM(a.con_encuesta = 1) AS pendientes,
        SUM(a.con_encuesta = 2) AS completadas,
        AVG(er.resultado_promedio) AS prom
     $FROM WHERE $Wbase"));

// ── Total de filas del filtro de tabla (con estado) ─────────────────────────
$totFilas = (int)mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n $FROM WHERE $Wtabla"))['n'];

// ── Página ───────────────────────────────────────────────────────────────
$sql = "SELECT
            a.id_unidad, a.fec_entrega, a.cliente, a.chasis, a.nro_orden,
            a.con_encuesta AS estado, a.id_sucursal,
            g.grupo, m.modelo, u.nombre AS asesor, s.sucursal,
            t.token,
            er.id_respuesta, er.resultado_promedio
        $FROM
        WHERE $Wtabla
        ORDER BY $order
        LIMIT " . (int)$per . " OFFSET " . (int)$offset;
$res = mysqli_query($con, $sql);
if (!$res) { http_response_code(500); $salida = ["error" => mysqli_error($con)]; return; }

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = [
        'id_unidad'    => (int)$r['id_unidad'],
        'fec_entrega'  => $r['fec_entrega'],
        'cliente'      => enc_utf8(trim((string)$r['cliente'])),
        'chasis'       => enc_utf8(trim((string)$r['chasis'])),
        'nro_orden'    => enc_utf8(trim((string)$r['nro_orden'])),
        'grupo'        => enc_utf8(trim((string)$r['grupo'])),
        'modelo'       => enc_utf8(trim((string)$r['modelo'])),
        'asesor'       => enc_utf8(trim((string)$r['asesor'])),
        'id_sucursal'  => (int)$r['id_sucursal'],
        'sucursal'     => enc_utf8(trim((string)$r['sucursal'])),
        'token'        => $r['token'],
        'estado'       => (int)$r['estado'],
        'id_respuesta' => $r['id_respuesta'] !== null ? (int)$r['id_respuesta'] : null,
        'promedio'     => $r['resultado_promedio'] !== null ? (float)$r['resultado_promedio'] : null,
    ];
}

$salida = [
    'ok'    => true,
    'page'  => $page,
    'per'   => $per,
    'total' => $totFilas,
    'pages' => $per > 0 ? (int)ceil($totFilas / $per) : 1,
    'kpis'  => [
        'total'       => (int)$agg['total'],
        'sin_generar' => (int)$agg['sin_generar'],
        'pendientes'  => (int)$agg['pendientes'],
        'completadas' => (int)$agg['completadas'],
        'prom'        => $agg['prom'] !== null ? round((float)$agg['prom'], 2) : null,
    ],
    'rows'  => $rows,
];
