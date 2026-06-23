<?php
/*
 * Listado paginado de entregas de USADOS para data.php. Deja el resultado en $salida.
 * Requiere: $con (config_app.php) y helpers eu_* (funciones/consulta.php).
 */

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per  = isset($_GET['per'])  ? (int)$_GET['per']          : 50;
if ($per < 1)   $per = 50;
if ($per > 200) $per = 200;
$offset = ($page - 1) * $per;

$FROM   = eu_from();
$Wbase  = eu_where_base($con);
$Wtabla = eu_where_estado($Wbase);
$order  = eu_order();
$ESTADO = "(CASE WHEN t.id_token IS NULL THEN 0 WHEN COALESCE(t.completada,0)=1 THEN 2 ELSE 1 END)";

// ── Agregados (sobre el filtro base: sucursal + búsqueda, sin estado) ────────
$agg = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT
        COUNT(*) AS total,
        SUM(t.id_token IS NULL) AS sin_generar,
        SUM(t.id_token IS NOT NULL AND COALESCE(t.completada,0)=0) AS pendientes,
        SUM(COALESCE(t.completada,0)=1) AS completadas,
        AVG(r.resultado_promedio) AS prom
     $FROM WHERE $Wbase"));

// ── Total de filas del filtro de tabla (con estado) ─────────────────────────
$totFilas = (int)mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n $FROM WHERE $Wtabla"))['n'];

// ── Página ───────────────────────────────────────────────────────────────
$sql = "SELECT
            v.id_unidad, v.nro_unidad, v.interno,
            v.cliente, v.vehiculo, v.`año` AS anio, v.km, v.dominio,
            v.fec_entrega, v.asesor_venta, v.id_sucursal,
            t.token, COALESCE(t.completada,0) AS completada,
            r.id_respuesta, r.resultado_promedio,
            $ESTADO AS estado
        $FROM
        WHERE $Wtabla
        ORDER BY $order
        LIMIT " . (int)$per . " OFFSET " . (int)$offset;
$res = mysqli_query($con, $sql);
if (!$res) { http_response_code(500); $salida = ["error" => mysqli_error($con)]; return; }

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = [
        'id_unidad'   => (int)$r['id_unidad'],
        'nro_unidad'  => $r['nro_unidad'] !== null ? (int)$r['nro_unidad'] : null,
        'interno'     => $r['interno'] !== null ? (int)$r['interno'] : null,
        'cliente'     => eu_utf8(trim((string)$r['cliente'])),
        'vehiculo'    => eu_utf8(trim((string)$r['vehiculo'])),
        'anio'        => $r['anio'] !== null ? (int)$r['anio'] : null,
        'km'          => $r['km']  !== null ? (int)$r['km']  : null,
        'dominio'     => eu_utf8(trim((string)$r['dominio'])),
        'fec_entrega' => $r['fec_entrega'],
        'asesor'      => eu_utf8(trim((string)$r['asesor_venta'])),
        'id_sucursal' => (int)$r['id_sucursal'],
        'token'       => $r['token'],
        'completada'  => (int)$r['completada'],
        'id_respuesta'=> $r['id_respuesta'] !== null ? (int)$r['id_respuesta'] : null,
        'promedio'    => $r['resultado_promedio'] !== null ? (float)$r['resultado_promedio'] : null,
        'estado'      => (int)$r['estado'],
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
