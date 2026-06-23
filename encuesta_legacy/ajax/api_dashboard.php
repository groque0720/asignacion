<?php
/**
 * api_dashboard.php — API JSON del Dashboard de Encuestas
 * Todos los datos pasan por aquí. 100% dinámico: áreas y preguntas se leen de DB.
 */
// Endpoint JSON: nunca mostrar notices/warnings en la respuesta
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

include("../funciones/func_mysql.php");
conectar();
@session_start();

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

header('Content-Type: application/json; charset=UTF-8');

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

switch ($action) {
    case 'filters':         echo json_encode(get_filters());         break;
    case 'kpis':            echo json_encode(get_kpis());            break;
    case 'chart_tendencia': echo json_encode(get_chart_tendencia()); break;
    case 'chart_sucursal':  echo json_encode(get_chart_sucursal());  break;
    case 'chart_asesor':    echo json_encode(get_chart_asesor());    break;
    case 'chart_areas':     echo json_encode(get_chart_areas());     break;
    case 'chart_dist':      echo json_encode(get_chart_dist());      break;
    case 'table':           echo json_encode(get_table());           break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no reconocida: $action"]);
}

// =============================================================
// HELPER: niveles de resultado desde enc_niveles
// =============================================================
function get_niveles(): array
{
    global $con;
    $res = mysqli_query($con, "SELECT id_nivel, nombre, valor_desde, valor_hasta, color FROM enc_niveles ORDER BY valor_desde DESC");
    $niveles = [];
    while ($n = mysqli_fetch_assoc($res)) $niveles[] = $n;
    return $niveles;
}

// =============================================================
// BASE FROM: JOINs compartidos por todas las queries
// =============================================================
function base_from(): string
{
    return "FROM enc_respuestas r
    LEFT JOIN enc_tokens t  ON r.id_token      = t.id_token
    LEFT JOIN asignaciones a ON r.id_asignacion = a.id_unidad
    LEFT JOIN usuarios u    ON a.id_asesor      = u.idusuario
    LEFT JOIN grupos g      ON a.id_grupo       = g.idgrupo
    LEFT JOIN modelos m     ON a.id_modelo      = m.idmodelo
    LEFT JOIN sucursales s  ON a.id_sucursal    = s.idsucursal
    WHERE 1=1";
}

// JOIN opcional para filtro por área (agrega columna score_area)
function area_join(): string
{
    $id_area = intval($_REQUEST['id_area'] ?? 0);
    if ($id_area > 0) {
        return " LEFT JOIN (
            SELECT rd.id_respuesta, ROUND(AVG(rd.respuesta_valor), 2) AS score_area
            FROM enc_respuestas_detalle rd
            JOIN enc_preguntas p ON rd.id_pregunta = p.id_pregunta
            WHERE p.id_area = $id_area AND p.pondera = 1 AND rd.mostrada = 1
            GROUP BY rd.id_respuesta
        ) area_sc ON area_sc.id_respuesta = r.id_respuesta";
    }
    return "";
}

// Expresión SQL del score según área seleccionada
function score_expr(): string
{
    $id_area = intval($_REQUEST['id_area'] ?? 0);
    if ($id_area > 0) return "COALESCE(area_sc.score_area, 0)";
    return "COALESCE(r.resultado_promedio, 0)";
}

// =============================================================
// WHERE DINÁMICO — todos los filtros
// =============================================================
function build_where(array &$params, string &$types): string
{
    $w = '';

    // AÑO
    $anio = intval($_REQUEST['anio'] ?? 0);
    if ($anio >= 2015 && $anio <= 2099) {
        $w .= " AND YEAR(r.fecha_completada) = ?";
        $params[] = $anio; $types .= 'i';
    }

    // MES
    $mes = intval($_REQUEST['mes'] ?? 0);
    if ($mes >= 1 && $mes <= 12) {
        $w .= " AND MONTH(r.fecha_completada) = ?";
        $params[] = $mes; $types .= 'i';
    }

    // FECHA DESDE
    $fd = trim($_REQUEST['fecha_desde'] ?? '');
    if ($fd !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fd);
        if ($dt && $dt->format('Y-m-d') === $fd) {
            $w .= " AND DATE(r.fecha_completada) >= ?";
            $params[] = $fd; $types .= 's';
        }
    }

    // FECHA HASTA
    $fh = trim($_REQUEST['fecha_hasta'] ?? '');
    if ($fh !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fh);
        if ($dt && $dt->format('Y-m-d') === $fh) {
            $w .= " AND DATE(r.fecha_completada) <= ?";
            $params[] = $fh; $types .= 's';
        }
    }

    // SUCURSAL
    $suc = intval($_REQUEST['idsucursal'] ?? 0);
    if ($suc > 0) { $w .= " AND a.id_sucursal = ?"; $params[] = $suc; $types .= 'i'; }

    // ASESOR
    $asesor = intval($_REQUEST['id_asesor'] ?? 0);
    if ($asesor > 0) { $w .= " AND a.id_asesor = ?"; $params[] = $asesor; $types .= 'i'; }

    // GRUPO
    $grupo = intval($_REQUEST['idgrupo'] ?? 0);
    if ($grupo > 0) { $w .= " AND a.id_grupo = ?"; $params[] = $grupo; $types .= 'i'; }

    // MODELO
    $modelo = intval($_REQUEST['idmodelo'] ?? 0);
    if ($modelo > 0) { $w .= " AND a.id_modelo = ?"; $params[] = $modelo; $types .= 'i'; }

    return $w;
}

// =============================================================
// HELPERS DE EJECUCIÓN
// =============================================================
function exec_q(string $sql, array $params, string $types): array
{
    global $con;
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return [];
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) { mysqli_stmt_close($stmt); return []; }
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

function exec_one(string $sql, array $params, string $types): array
{
    $rows = exec_q($sql, $params, $types);
    return $rows[0] ?? [];
}

// =============================================================
// ACTION: filters — opciones para los selects
// =============================================================
function get_filters(): array
{
    global $con;

    // Sucursales que tienen encuestas
    $r = mysqli_query($con, "SELECT DISTINCT s.idsucursal, s.sucursal
        FROM sucursales s
        JOIN asignaciones a ON a.id_sucursal = s.idsucursal
        JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad
        ORDER BY s.sucursal");
    $sucursales = [];
    while ($row = mysqli_fetch_assoc($r)) $sucursales[] = ["id" => $row['idsucursal'], "label" => $row['sucursal']];

    // Asesores que tienen encuestas
    $r = mysqli_query($con, "SELECT DISTINCT u.idusuario, u.nombre
        FROM usuarios u
        JOIN asignaciones a ON a.id_asesor = u.idusuario
        JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad
        ORDER BY u.nombre");
    $asesores = [];
    while ($row = mysqli_fetch_assoc($r)) $asesores[] = ["id" => $row['idusuario'], "label" => $row['nombre']];

    // Grupos que tienen encuestas
    $r = mysqli_query($con, "SELECT DISTINCT g.idgrupo, g.grupo
        FROM grupos g
        JOIN asignaciones a ON a.id_grupo = g.idgrupo
        JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad
        ORDER BY g.grupo");
    $grupos = [];
    while ($row = mysqli_fetch_assoc($r)) $grupos[] = ["id" => $row['idgrupo'], "label" => $row['grupo']];

    // Modelos que tienen encuestas
    $r = mysqli_query($con, "SELECT DISTINCT m.idmodelo, m.modelo
        FROM modelos m
        JOIN asignaciones a ON a.id_modelo = m.idmodelo
        JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad
        ORDER BY m.modelo");
    $modelos = [];
    while ($row = mysqli_fetch_assoc($r)) $modelos[] = ["id" => $row['idmodelo'], "label" => $row['modelo']];

    // Áreas (dinámico — se agregan solas cuando se crean en config)
    $r = mysqli_query($con, "SELECT id_area, nombre, color FROM enc_areas ORDER BY nro_orden, nombre");
    $areas = [];
    while ($row = mysqli_fetch_assoc($r)) $areas[] = ["id" => $row['id_area'], "label" => $row['nombre'], "color" => $row['color']];

    // Años disponibles en enc_respuestas
    $r = mysqli_query($con, "SELECT DISTINCT YEAR(fecha_completada) AS anio FROM enc_respuestas ORDER BY anio DESC");
    $anios = [];
    while ($row = mysqli_fetch_assoc($r)) $anios[] = intval($row['anio']);

    // Niveles de resultado (para colorear scores dinámicamente en el cliente)
    $niveles = [];
    foreach (get_niveles() as $n) {
        $niveles[] = [
            'nombre' => $n['nombre'],
            'color'  => $n['color'],
            'desde'  => (float)$n['valor_desde'],
            'hasta'  => (float)$n['valor_hasta'],
        ];
    }

    return compact('sucursales', 'asesores', 'grupos', 'modelos', 'areas', 'anios', 'niveles');
}

// =============================================================
// ACTION: kpis
// =============================================================
function get_kpis(): array
{
    global $con;
    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    // Construir SELECT dinámico: COUNT + AVG + un SUM por cada nivel
    $niveles_def = get_niveles();
    $nivel_selects = [];
    foreach ($niveles_def as $i => $n) {
        $desde = number_format((float)$n['valor_desde'], 4, '.', '');
        $hasta = number_format((float)$n['valor_hasta'], 4, '.', '');
        $nivel_selects[] = "SUM(CASE WHEN $sc >= $desde AND $sc <= $hasta THEN 1 ELSE 0 END) AS niv$i";
    }
    $sel_extra = empty($nivel_selects) ? '' : ', ' . implode(', ', $nivel_selects);

    $sql = "SELECT COUNT(*) AS total, ROUND(AVG($sc), 2) AS promedio$sel_extra "
         . base_from() . $aj . $where;

    $row = exec_one($sql, $params, $types);

    $total    = intval($row['total'] ?? 0);
    $promedio = isset($row['promedio']) && $row['promedio'] !== null ? round((float)$row['promedio'], 1) : null;

    // Armar array de niveles con cantidad y porcentaje
    $niveles_kpi = [];
    foreach ($niveles_def as $i => $n) {
        $cnt = intval($row["niv$i"] ?? 0);
        $niveles_kpi[] = [
            'nombre'   => $n['nombre'],
            'color'    => $n['color'],
            'desde'    => (float)$n['valor_desde'],
            'hasta'    => (float)$n['valor_hasta'],
            'cantidad' => $cnt,
            'pct'      => $total > 0 ? round($cnt / $total * 100, 1) : 0,
        ];
    }

    // Áreas dinámicas: una tarjeta por área
    $params2 = []; $types2 = '';
    $where2  = build_where($params2, $types2);
    $sql_areas = "SELECT ar.id_area, ar.nombre, ar.color,
                         ROUND(AVG(rd.respuesta_valor), 1) AS promedio,
                         COUNT(DISTINCT r.id_respuesta) AS total_resp
                  FROM enc_areas ar
                  JOIN enc_preguntas p  ON p.id_area = ar.id_area AND p.baja = 0 AND p.pondera = 1
                  JOIN enc_respuestas_detalle rd ON rd.id_pregunta = p.id_pregunta AND rd.mostrada = 1
                  JOIN enc_respuestas r ON rd.id_respuesta = r.id_respuesta
                  JOIN asignaciones a ON r.id_asignacion = a.id_unidad
                  LEFT JOIN sucursales s ON a.id_sucursal = s.idsucursal
                  WHERE 1=1" . $where2 . "
                  GROUP BY ar.id_area, ar.nombre, ar.color
                  ORDER BY ar.nro_orden";
    $areas_rows = exec_q($sql_areas, $params2, $types2);
    $areas = [];
    foreach ($areas_rows as $ar) {
        $areas[] = [
            "nombre"  => $ar['nombre'],
            "color"   => $ar['color'],
            "promedio" => $ar['promedio'] !== null ? (float)$ar['promedio'] : null,
            "total"   => intval($ar['total_resp'])
        ];
    }

    return [
        "total"    => $total,
        "promedio" => $promedio,
        "niveles"  => $niveles_kpi,
        "areas"    => $areas,
    ];
}

// =============================================================
// ACTION: chart_tendencia — promedio mensual últimos meses
// =============================================================
function get_chart_tendencia(): array
{
    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    $sql = "SELECT
        DATE_FORMAT(r.fecha_completada, '%Y-%m') AS periodo,
        ROUND(AVG($sc), 2) AS promedio,
        COUNT(*) AS cantidad
    " . base_from() . $aj . $where . "
    GROUP BY DATE_FORMAT(r.fecha_completada, '%Y-%m')
    ORDER BY periodo ASC
    LIMIT 24";

    $rows = exec_q($sql, $params, $types);

    $meses_es = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun',
                 '07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];

    $labels = []; $promedios = []; $cantidades = [];
    foreach ($rows as $r) {
        $parts = explode('-', $r['periodo']);
        $labels[]     = ($meses_es[$parts[1]] ?? $parts[1]) . ' ' . substr($parts[0], 2);
        $promedios[]  = $r['promedio'] !== null ? round((float)$r['promedio'], 1) : null;
        $cantidades[] = intval($r['cantidad']);
    }

    return compact('labels', 'promedios', 'cantidades');
}

// =============================================================
// ACTION: chart_sucursal — promedio por sucursal
// =============================================================
function get_chart_sucursal(): array
{
    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    $sql = "SELECT
        COALESCE(s.sucursal, 'Sin sucursal') AS sucursal,
        ROUND(AVG($sc), 1) AS promedio,
        COUNT(*) AS cantidad
    " . base_from() . $aj . $where . "
    GROUP BY a.id_sucursal, s.sucursal
    ORDER BY promedio DESC";

    $rows = exec_q($sql, $params, $types);

    $labels = []; $promedios = []; $cantidades = [];
    foreach ($rows as $r) {
        $labels[]     = $r['sucursal'];
        $promedios[]  = $r['promedio'] !== null ? (float)$r['promedio'] : 0;
        $cantidades[] = intval($r['cantidad']);
    }

    return compact('labels', 'promedios', 'cantidades');
}

// =============================================================
// ACTION: chart_asesor — top 15 asesores por promedio (mín. 2)
// =============================================================
function get_chart_asesor(): array
{
    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    $sql = "SELECT
        u.nombre AS asesor,
        ROUND(AVG($sc), 1) AS promedio,
        COUNT(*) AS cantidad
    " . base_from() . $aj . $where . "
    GROUP BY a.id_asesor, u.nombre
    HAVING COUNT(*) >= 1
    ORDER BY promedio DESC
    LIMIT 15";

    $rows = exec_q($sql, $params, $types);

    $labels = []; $promedios = []; $cantidades = [];
    foreach ($rows as $r) {
        $labels[]     = $r['asesor'];
        $promedios[]  = $r['promedio'] !== null ? (float)$r['promedio'] : 0;
        $cantidades[] = intval($r['cantidad']);
    }

    return compact('labels', 'promedios', 'cantidades');
}

// =============================================================
// ACTION: chart_areas — promedio por área (100% dinámico)
// =============================================================
function get_chart_areas(): array
{
    $params = []; $types = '';
    $where  = build_where($params, $types);

    // Para este chart siempre mostramos TODAS las áreas, sin importar el filtro id_area
    $sql = "SELECT ar.nombre, ar.color,
                   ROUND(AVG(rd.respuesta_valor), 1) AS promedio,
                   COUNT(DISTINCT r.id_respuesta) AS cantidad
            FROM enc_areas ar
            JOIN enc_preguntas p  ON p.id_area = ar.id_area AND p.baja = 0 AND p.pondera = 1
            JOIN enc_respuestas_detalle rd ON rd.id_pregunta = p.id_pregunta AND rd.mostrada = 1
            JOIN enc_respuestas r  ON rd.id_respuesta = r.id_respuesta
            JOIN asignaciones a   ON r.id_asignacion  = a.id_unidad
            LEFT JOIN sucursales s ON a.id_sucursal   = s.idsucursal
            WHERE 1=1" . $where . "
            GROUP BY ar.id_area, ar.nombre, ar.color
            ORDER BY ar.nro_orden";

    $rows = exec_q($sql, $params, $types);

    $labels = []; $promedios = []; $colores = []; $cantidades = [];
    foreach ($rows as $r) {
        $labels[]     = $r['nombre'];
        $promedios[]  = $r['promedio'] !== null ? (float)$r['promedio'] : 0;
        $colores[]    = $r['color'] ?: '#4e9af1';
        $cantidades[] = intval($r['cantidad']);
    }

    return compact('labels', 'promedios', 'colores', 'cantidades');
}

// =============================================================
// ACTION: chart_dist — distribución de puntajes usando enc_niveles
// =============================================================
function get_chart_dist(): array
{
    $niveles_def = get_niveles();
    if (empty($niveles_def)) {
        return ['labels' => [], 'cantidades' => [], 'colores' => []];
    }

    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    // Un SUM por cada nivel definido
    $select_parts = [];
    foreach ($niveles_def as $i => $n) {
        $desde = number_format((float)$n['valor_desde'], 4, '.', '');
        $hasta = number_format((float)$n['valor_hasta'], 4, '.', '');
        $select_parts[] = "SUM(CASE WHEN $sc >= $desde AND $sc <= $hasta THEN 1 ELSE 0 END) AS niv$i";
    }
    $sql = "SELECT " . implode(', ', $select_parts) . " " . base_from() . $aj . $where;

    $row = exec_one($sql, $params, $types);

    $labels = []; $cantidades = []; $colores = [];
    foreach ($niveles_def as $i => $n) {
        $labels[]     = $n['nombre'];
        $cantidades[] = intval($row["niv$i"] ?? 0);
        $colores[]    = $n['color'] ?: '#4e9af1';
    }

    return compact('labels', 'cantidades', 'colores');
}

// =============================================================
// ACTION: table — listado de respuestas para DataTables
// =============================================================
function get_table(): array
{
    $params = []; $types = '';
    $where  = build_where($params, $types);
    $sc     = score_expr();
    $aj     = area_join();

    $sql = "SELECT
        r.id_respuesta,
        DATE_FORMAT(r.fecha_completada, '%d/%m/%Y') AS fecha,
        a.cliente,
        COALESCE(g.grupo, '-')    AS grupo,
        COALESCE(m.modelo, '-')   AS modelo,
        u.nombre                  AS asesor,
        COALESCE(s.sucursal, '-') AS sucursal,
        ROUND($sc, 1)             AS score
    " . base_from() . $aj . $where . "
    ORDER BY r.fecha_completada DESC
    LIMIT 500";

    $rows = exec_q($sql, $params, $types);

    $data = [];
    foreach ($rows as $r) {
        $score = $r['score'] !== null ? (float)$r['score'] : null;
        if ($score !== null) {
            if ($score >= 8)     $color = '#63c795';
            elseif ($score >= 6) $color = '#f1a84e';
            else                 $color = '#e05c5c';
            $score_html = "<span style='font-weight:700;color:{$color};'>" . number_format($score, 1) . "</span>";
        } else {
            $score_html = "<span style='color:#6b7394;'>-</span>";
        }
        $acc_html = "<a href='../resultados/detalle.php?id={$r['id_respuesta']}' class='btn-tbl btn-tbl-eye' title='Ver detalle'><i class='fa fa-eye'></i></a>"
                  . "<a href='../resultados/pdf.php?id={$r['id_respuesta']}' class='btn-tbl btn-tbl-pdf' title='Ver PDF' target='_blank'><i class='fa fa-file-pdf'></i></a>";
        $data[] = [
            $r['fecha'],
            htmlspecialchars($r['cliente']),
            htmlspecialchars($r['grupo']),
            htmlspecialchars($r['modelo']),
            htmlspecialchars($r['asesor']),
            htmlspecialchars($r['sucursal']),
            $score_html,
            $acc_html,
        ];
    }

    return ["data" => $data, "total" => count($data)];
}
