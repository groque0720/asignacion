<?php
/**
 * api.php — API JSON para el Dashboard de Ventas
 * Todos los datos del dashboard pasan por este archivo.
 */

include("funciones/func_mysql.php");
conectar();
@session_start();

// Verificar autenticación
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

header('Content-Type: application/json; charset=UTF-8');

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

switch ($action) {
    case 'filters':        echo json_encode(get_filters());          break;
    case 'kpis':           echo json_encode(get_kpis());             break;
    case 'chart_mes':      echo json_encode(get_chart_mes());        break;
    case 'chart_anio_comp':echo json_encode(get_chart_anio_comp());  break;
    case 'chart_sucursal': echo json_encode(get_chart_sucursal());   break;
    case 'chart_vendedor': echo json_encode(get_chart_vendedor());   break;
    case 'chart_credito':  echo json_encode(get_chart_credito());    break;
    case 'chart_toma':     echo json_encode(get_chart_toma());       break;
    case 'chart_anuladas':    echo json_encode(get_chart_anuladas());      break;
    case 'chart_compra':      echo json_encode(get_chart_compra());        break;
    case 'comp_grupo':        echo json_encode(get_comp_grupo());         break;
    case 'modelos_by_grupo':  echo json_encode(get_modelos_by_grupo());    break;
    case 'table':             echo json_encode(get_table());               break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Acción no reconocida"]);
}

// =============================================================
// FUNCIÓN CENTRAL: Construye la cláusula WHERE dinámica
// Retorna: string con condiciones AND
// Llena: $params (array por referencia) y $types (string por ref)
// SEGURIDAD: todos los valores de usuario van a ? placeholders
// =============================================================
function build_where_clause(array &$params, string &$types, bool $exclude_anio = false): string
{
    $where = "";

    // --- AÑO ---
    if (!$exclude_anio) {
        $anio = intval($_REQUEST['anio'] ?? 0);
        if ($anio >= 2015 && $anio <= 2035) {
            $where  .= " AND YEAR(r.fecres) = ?";
            $params[] = $anio;
            $types   .= 'i';
        }
    }

    // --- MES ---
    $mes = intval($_REQUEST['mes'] ?? 0);
    if ($mes >= 1 && $mes <= 12) {
        $where  .= " AND MONTH(r.fecres) = ?";
        $params[] = $mes;
        $types   .= 'i';
    }

    // --- FECHA DESDE ---
    $fecha_desde = trim($_REQUEST['fecha_desde'] ?? '');
    if ($fecha_desde !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fecha_desde);
        if ($dt && $dt->format('Y-m-d') === $fecha_desde) {
            $where  .= " AND r.fecres >= ?";
            $params[] = $fecha_desde;
            $types   .= 's';
        }
    }

    // --- FECHA HASTA ---
    $fecha_hasta = trim($_REQUEST['fecha_hasta'] ?? '');
    if ($fecha_hasta !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fecha_hasta);
        if ($dt && $dt->format('Y-m-d') === $fecha_hasta) {
            $where  .= " AND r.fecres <= ?";
            $params[] = $fecha_hasta;
            $types   .= 's';
        }
    }

    // --- SUCURSAL ---
    $idsucursal = intval($_REQUEST['idsucursal'] ?? 0);
    if ($idsucursal > 0) {
        $where  .= " AND s.idsucursal = ?";
        $params[] = $idsucursal;
        $types   .= 'i';
    }

    // --- VENDEDOR ---
    $idusuario = intval($_REQUEST['idusuario'] ?? 0);
    if ($idusuario > 0) {
        $where  .= " AND u.idusuario = ?";
        $params[] = $idusuario;
        $types   .= 'i';
    }

    // --- GRUPO ---
    $idgrupo = intval($_REQUEST['idgrupo'] ?? 0);
    if ($idgrupo > 0) {
        $where  .= " AND r.idgrupo = ?";
        $params[] = $idgrupo;
        $types   .= 'i';
    }

    // --- MODELO ---
    $idmodelo = intval($_REQUEST['idmodelo'] ?? 0);
    if ($idmodelo > 0) {
        $where  .= " AND r.idmodelo = ?";
        $params[] = $idmodelo;
        $types   .= 'i';
    }

    // --- MARCA ---
    $marca = trim($_REQUEST['marca'] ?? '');
    if ($marca !== '') {
        $marca_like = '%' . $marca . '%';
        $where  .= " AND r.marca LIKE ?";
        $params[] = $marca_like;
        $types   .= 's';
    }

    // --- ANULADA (solo 0 o 1) ---
    $anulada_raw = $_REQUEST['anulada'] ?? '';
    if ($anulada_raw !== '' && $anulada_raw !== '-1') {
        $anulada = intval($anulada_raw);
        if ($anulada === 0 || $anulada === 1) {
            $where  .= " AND r.anulada = ?";
            $params[] = $anulada;
            $types   .= 'i';
        }
    }

    // --- CRÉDITO (solo 0 o 1) ---
    $credito_raw = $_REQUEST['credito'] ?? '';
    if ($credito_raw !== '' && $credito_raw !== '-1') {
        $credito = intval($credito_raw);
        if ($credito === 0 || $credito === 1) {
            $where  .= " AND COALESCE(ld.credito,0) = ?";
            $params[] = $credito;
            $types   .= 'i';
        }
    }

    // --- TOMA USADO (solo 0 o 1) ---
    $toma_raw = $_REQUEST['toma_usado'] ?? '';
    if ($toma_raw !== '' && $toma_raw !== '-1') {
        $toma = intval($toma_raw);
        if ($toma === 0 || $toma === 1) {
            $where  .= " AND COALESCE(ld.toma_usado,0) = ?";
            $params[] = $toma;
            $types   .= 'i';
        }
    }

    // --- COMPRA (nuevo / usado) ---
    $compra = strtolower(trim($_REQUEST['compra'] ?? ''));
    if ($compra === 'nuevo' || $compra === 'usado') {
        $where  .= " AND LOWER(r.compra) = ?";
        $params[] = $compra;
        $types   .= 's';
    }

    return $where;
}

// =============================================================
// BASE SQL: FROM + JOINs compartido por todas las funciones
// =============================================================
function base_from_sql(): string
{
    return "FROM reservas r
    LEFT JOIN grupos g ON r.idgrupo = g.idgrupo
    LEFT JOIN modelos m ON r.idmodelo = m.idmodelo
    INNER JOIN usuarios u ON r.idusuario = u.idusuario
    INNER JOIN sucursales s ON u.idsucursal = s.idsucursal
    LEFT JOIN (
        SELECT ld.idreserva,
            MAX(CASE WHEN ld.idcodigo = 51 THEN 1 ELSE 0 END) AS toma_usado,
            MAX(CASE WHEN c.credito = 1 THEN 1 ELSE 0 END) AS credito
        FROM lineas_detalle ld
        INNER JOIN codigos c ON ld.idcodigo = c.idcodigo
        GROUP BY ld.idreserva
    ) ld ON r.idreserva = ld.idreserva
    WHERE r.fecres >= '2020-01-01' AND r.enviada != 0";
}

// Helper: ejecutar query con prepared statements y devolver array de rows
function exec_prepared(string $sql, array $params, string $types): ?array
{
    global $con;
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) { return null; }
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        mysqli_stmt_close($stmt);
        return null;
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

// Helper: ejecutar query y devolver primera fila
function exec_prepared_one(string $sql, array $params, string $types): ?array
{
    $rows = exec_prepared($sql, $params, $types);
    return (!empty($rows)) ? $rows[0] : null;
}

// =============================================================
// ACTION: filters — opciones para los selects de filtros
// =============================================================
function get_filters(): array
{
    global $con;

    $result_suc = mysqli_query($con, "SELECT idsucursal, sucursal FROM sucursales ORDER BY posicion, sucursal");
    $sucursales = [];
    while ($r = mysqli_fetch_assoc($result_suc)) {
        $sucursales[] = ["id" => $r['idsucursal'], "label" => $r['sucursal']];
    }

    $result_vend = mysqli_query($con, "SELECT u.idusuario, u.nombre FROM usuarios u
        INNER JOIN sucursales s ON u.idsucursal = s.idsucursal
        WHERE u.activo = 1 ORDER BY u.nombre");
    $vendedores = [];
    while ($r = mysqli_fetch_assoc($result_vend)) {
        $vendedores[] = ["id" => $r['idusuario'], "label" => $r['nombre']];
    }

    $result_grp = mysqli_query($con, "SELECT idgrupo, grupo FROM grupos ORDER BY posicion, grupo");
    $grupos = [];
    while ($r = mysqli_fetch_assoc($result_grp)) {
        $grupos[] = ["id" => $r['idgrupo'], "label" => $r['grupo']];
    }

    $result_mod = mysqli_query($con, "SELECT idmodelo, modelo FROM modelos ORDER BY modelo");
    $modelos = [];
    while ($r = mysqli_fetch_assoc($result_mod)) {
        $modelos[] = ["id" => $r['idmodelo'], "label" => $r['modelo']];
    }

    $result_marc = mysqli_query($con, "SELECT DISTINCT marca FROM reservas WHERE marca IS NOT NULL AND marca != '' ORDER BY marca");
    $marcas = [];
    while ($r = mysqli_fetch_assoc($result_marc)) {
        $marcas[] = $r['marca'];
    }

    // Años disponibles
    $result_anios = mysqli_query($con, "SELECT DISTINCT YEAR(fecres) AS anio FROM reservas WHERE fecres >= '2020-01-01' ORDER BY anio DESC");
    $anios = [];
    while ($r = mysqli_fetch_assoc($result_anios)) {
        $anios[] = intval($r['anio']);
    }

    return compact('sucursales', 'vendedores', 'grupos', 'modelos', 'marcas', 'anios');
}

// =============================================================
// ACTION: kpis — 7 métricas principales
// =============================================================
function get_kpis(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT
        COUNT(*) AS total,
        SUM(r.anulada) AS anuladas,
        SUM(COALESCE(ld.credito,0)) AS con_credito,
        SUM(COALESCE(ld.toma_usado,0)) AS con_toma,
        SUM(CASE WHEN LOWER(r.compra)='nuevo' THEN 1 ELSE 0 END) AS nuevas,
        SUM(CASE WHEN LOWER(r.compra)='usado' THEN 1 ELSE 0 END) AS usadas
    " . base_from_sql() . $where;

    $row = exec_prepared_one($sql, $params, $types);

    if (!$row) {
        return ["total" => 0, "anuladas" => 0, "pct_anuladas" => 0,
                "con_credito" => 0, "pct_credito" => 0, "con_toma" => 0, "pct_toma" => 0];
    }

    $total       = intval($row['total']);
    $anuladas    = intval($row['anuladas']);
    $con_credito = intval($row['con_credito']);
    $con_toma    = intval($row['con_toma']);
    $nuevas      = intval($row['nuevas']);
    $usadas      = intval($row['usadas']);

    return [
        "total"        => $total,
        "anuladas"     => $anuladas,
        "pct_anuladas" => $total > 0 ? round(($anuladas / $total) * 100, 1) : 0,
        "con_credito"  => $con_credito,
        "pct_credito"  => $total > 0 ? round(($con_credito / $total) * 100, 1) : 0,
        "con_toma"     => $con_toma,
        "pct_toma"     => $total > 0 ? round(($con_toma / $total) * 100, 1) : 0,
        "nuevas"       => $nuevas,
        "usadas"       => $usadas,
    ];
}

// =============================================================
// ACTION: chart_mes — reservas por mes (año seleccionado)
// =============================================================
function get_chart_mes(): array
{
    $params = [];
    $types  = '';
    // Si no hay filtro de año, usar el año actual
    $anio_req = intval($_REQUEST['anio'] ?? 0);
    if ($anio_req < 2015 || $anio_req > 2035) {
        $anio_req = intval(date('Y'));
        $params[] = $anio_req;
        $types   .= 'i';
        $where    = " AND YEAR(r.fecres) = ?";
        // Aplicar otros filtros sin el año
        $params2 = [];
        $types2  = '';
        $where2  = build_where_clause($params2, $types2, true); // exclude_anio=true
        $params  = array_merge($params, $params2);
        $types  .= $types2;
        $where  .= $where2;
    } else {
        $where = build_where_clause($params, $types);
    }

    $sql = "SELECT MONTH(r.fecres) AS mes, COUNT(*) AS cantidad
    " . base_from_sql() . $where . "
    GROUP BY MONTH(r.fecres) ORDER BY mes";

    $rows = exec_prepared($sql, $params, $types);

    $data = array_fill(0, 12, 0);
    foreach ((array)$rows as $r) {
        $data[intval($r['mes']) - 1] = intval($r['cantidad']);
    }

    return [
        "labels" => ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "data"   => $data,
        "anio"   => $anio_req
    ];
}

// =============================================================
// ACTION: chart_anio_comp — año actual vs año anterior
// =============================================================
function get_chart_anio_comp(): array
{
    $anio_actual = intval($_REQUEST['anio'] ?? date('Y'));
    if ($anio_actual < 2015 || $anio_actual > 2035) $anio_actual = intval(date('Y'));
    $anio_ant1   = $anio_actual - 1;
    $anio_ant2   = $anio_actual - 2;

    // Filtros sin año para aplicar los demás
    $params_base = [];
    $types_base  = '';
    $where_base  = build_where_clause($params_base, $types_base, true);

    $sql_tpl = "SELECT MONTH(r.fecres) AS mes, COUNT(*) AS cantidad
    " . base_from_sql() . " AND YEAR(r.fecres) = ?" . $where_base . "
    GROUP BY MONTH(r.fecres) ORDER BY mes";

    $run = function(int $anio) use ($sql_tpl, $params_base, $types_base) {
        $p = array_merge([$anio], $params_base);
        $t = 'i' . $types_base;
        $rows = exec_prepared($sql_tpl, $p, $t);
        $data = array_fill(0, 12, 0);
        foreach ((array)$rows as $r) { $data[intval($r['mes']) - 1] = intval($r['cantidad']); }
        return $data;
    };

    $data_act  = $run($anio_actual);
    $data_ant1 = $run($anio_ant1);
    $data_ant2 = $run($anio_ant2);

    return [
        "labels"        => ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        "anio_actual"   => ["label" => (string)$anio_actual, "data" => $data_act,  "total" => array_sum($data_act)],
        "anio_anterior" => ["label" => (string)$anio_ant1,   "data" => $data_ant1, "total" => array_sum($data_ant1)],
        "anio_anterior2"=> ["label" => (string)$anio_ant2,   "data" => $data_ant2, "total" => array_sum($data_ant2)],
    ];
}

// =============================================================
// ACTION: chart_sucursal
// =============================================================
function get_chart_sucursal(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT s.sucursal, COUNT(*) AS cantidad
    " . base_from_sql() . $where . "
    GROUP BY s.idsucursal, s.sucursal ORDER BY cantidad DESC";

    $rows = exec_prepared($sql, $params, $types);

    $labels = [];
    $data   = [];
    foreach ((array)$rows as $r) {
        $labels[] = $r['sucursal'];
        $data[]   = intval($r['cantidad']);
    }
    return ["labels" => $labels, "data" => $data];
}

// =============================================================
// ACTION: chart_vendedor — Top 10
// =============================================================
function get_chart_vendedor(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT u.nombre AS vendedor, COUNT(*) AS cantidad
    " . base_from_sql() . $where . "
    GROUP BY u.idusuario, u.nombre ORDER BY cantidad DESC LIMIT 10";

    $rows = exec_prepared($sql, $params, $types);

    $labels = [];
    $data   = [];
    foreach ((array)$rows as $r) {
        $labels[] = $r['vendedor'];
        $data[]   = intval($r['cantidad']);
    }
    return ["labels" => $labels, "data" => $data];
}

// =============================================================
// ACTION: chart_credito
// =============================================================
function get_chart_credito(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT
        SUM(CASE WHEN COALESCE(ld.credito,0)=1 THEN 1 ELSE 0 END) AS con_credito,
        SUM(CASE WHEN COALESCE(ld.credito,0)=0 THEN 1 ELSE 0 END) AS sin_credito
    " . base_from_sql() . $where;

    $row = exec_prepared_one($sql, $params, $types);
    return [
        "labels" => ["Con Crédito", "Contado"],
        "data"   => [intval($row['con_credito'] ?? 0), intval($row['sin_credito'] ?? 0)]
    ];
}

// =============================================================
// ACTION: chart_toma
// =============================================================
function get_chart_toma(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT
        SUM(CASE WHEN COALESCE(ld.toma_usado,0)=1 THEN 1 ELSE 0 END) AS con_toma,
        SUM(CASE WHEN COALESCE(ld.toma_usado,0)=0 THEN 1 ELSE 0 END) AS sin_toma
    " . base_from_sql() . $where;

    $row = exec_prepared_one($sql, $params, $types);
    return [
        "labels" => ["Con Toma Usado", "Sin Toma"],
        "data"   => [intval($row['con_toma'] ?? 0), intval($row['sin_toma'] ?? 0)]
    ];
}

// =============================================================
// ACTION: chart_anuladas
// =============================================================
function get_chart_anuladas(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT
        SUM(CASE WHEN r.anulada=1 THEN 1 ELSE 0 END) AS anuladas,
        SUM(CASE WHEN r.anulada=0 THEN 1 ELSE 0 END) AS activas
    " . base_from_sql() . $where;

    $row = exec_prepared_one($sql, $params, $types);
    return [
        "labels" => ["Activas", "Anuladas"],
        "data"   => [intval($row['activas'] ?? 0), intval($row['anuladas'] ?? 0)]
    ];
}

// =============================================================
// ACTION: chart_compra — Nuevo vs Usado
// =============================================================
function get_chart_compra(): array
{
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    $sql = "SELECT
        SUM(CASE WHEN LOWER(r.compra)='nuevo' THEN 1 ELSE 0 END)  AS nuevo,
        SUM(CASE WHEN LOWER(r.compra)='usado' THEN 1 ELSE 0 END)  AS usado,
        SUM(CASE WHEN LOWER(r.compra) NOT IN ('nuevo','usado') OR r.compra IS NULL THEN 1 ELSE 0 END) AS otro
    " . base_from_sql() . $where;

    $row = exec_prepared_one($sql, $params, $types);
    $nuevo = intval($row['nuevo'] ?? 0);
    $usado = intval($row['usado'] ?? 0);
    $otro  = intval($row['otro']  ?? 0);

    $labels = ["Nuevo", "Usado"];
    $data   = [$nuevo, $usado];
    if ($otro > 0) { $labels[] = "Otro/Sin dato"; $data[] = $otro; }

    return ["labels" => $labels, "data" => $data];
}

// =============================================================
// ACTION: comp_grupo — Comparación por Grupo × 3 Años
// =============================================================
function get_comp_grupo(): array
{
    $anio_actual = intval($_REQUEST['anio'] ?? date('Y'));
    if ($anio_actual < 2015 || $anio_actual > 2035) $anio_actual = intval(date('Y'));
    $anio_ant1 = $anio_actual - 1;
    $anio_ant2 = $anio_actual - 2;

    // Filtros sin año ni mes (para comparar los 3 años con el resto de filtros)
    $params_base = [];
    $types_base  = '';
    $where_base  = build_where_clause($params_base, $types_base, true);

    $sql = "SELECT
        COALESCE(g.grupo, 'Usados') AS grupo,
        SUM(CASE WHEN YEAR(r.fecres) = ? THEN 1 ELSE 0 END) AS a0,
        SUM(CASE WHEN YEAR(r.fecres) = ? THEN 1 ELSE 0 END) AS a1,
        SUM(CASE WHEN YEAR(r.fecres) = ? THEN 1 ELSE 0 END) AS a2
    " . base_from_sql() . " AND YEAR(r.fecres) IN (?,?,?)" . $where_base . "
    GROUP BY r.idgrupo, g.grupo
    ORDER BY (g.grupo IS NULL) ASC, a0 DESC, a1 DESC";

    $p = array_merge([$anio_actual, $anio_ant1, $anio_ant2, $anio_actual, $anio_ant1, $anio_ant2], $params_base);
    $t = 'iii' . 'iii' . $types_base;

    $rows = exec_prepared($sql, $p, $t);

    return [
        'anios' => [(string)$anio_actual, (string)$anio_ant1, (string)$anio_ant2],
        'rows'  => $rows ?? []
    ];
}

// =============================================================
// ACTION: modelos_by_grupo — Versiones dependientes del grupo
// =============================================================
function get_modelos_by_grupo(): array
{
    global $con;
    $idgrupo = intval($_REQUEST['idgrupo'] ?? 0);
    if ($idgrupo <= 0) {
        // Sin grupo seleccionado: devolver todos los modelos
        $res = mysqli_query($con, "SELECT DISTINCT m.idmodelo, m.modelo
            FROM modelos m
            INNER JOIN reservas r ON r.idmodelo = m.idmodelo
            WHERE r.fecres >= '2020-01-01'
            ORDER BY m.modelo");
    } else {
        $stmt = mysqli_prepare($con,
            "SELECT DISTINCT m.idmodelo, m.modelo
            FROM modelos m
            INNER JOIN reservas r ON r.idmodelo = m.idmodelo
            WHERE r.idgrupo = ? AND r.fecres >= '2020-01-01'
            ORDER BY m.modelo");
        mysqli_stmt_bind_param($stmt, 'i', $idgrupo);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
    }
    $modelos = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $modelos[] = ["id" => intval($r['idmodelo']), "label" => $r['modelo']];
    }
    return $modelos;
}

// =============================================================
// ACTION: table — DataTables server-side
// =============================================================
function get_table(): array
{
    global $con;

    // Parámetros DataTables
    $draw    = intval($_REQUEST['draw']   ?? 1);
    $start   = intval($_REQUEST['start']  ?? 0);
    $length  = intval($_REQUEST['length'] ?? 25);
    $search  = trim($_REQUEST['search']['value'] ?? '');

    // Validar length (evitar abusos)
    if (!in_array($length, [10, 25, 50, 100, 200])) $length = 25;

    // Whitelist de columnas para ORDER BY
    // Col 4 = Modelo-Versión (fusionada grupo+modelo/detalleu) → ordena por grupo
    $col_map = [
        0 => 'r.idreserva',
        1 => 'r.fecres',
        2 => 's.sucursal',
        3 => 'u.nombre',
        4 => 'g.grupo',
        5 => 'r.compra',
        6 => 'r.anulada',
        7 => 'ld.credito',
        8 => 'ld.toma_usado',
    ];

    $order_col_idx = intval($_REQUEST['order'][0]['column'] ?? 1);
    $order_dir_raw = strtolower(trim($_REQUEST['order'][0]['dir'] ?? 'desc'));
    $order_dir     = ($order_dir_raw === 'asc') ? 'ASC' : 'DESC';
    $order_col     = $col_map[$order_col_idx] ?? 'r.fecres';

    // -- Contar total sin filtros (solo base) --
    $total_sql = "SELECT COUNT(*) AS cnt FROM reservas r WHERE r.fecres >= '2020-01-01' AND r.enviada != 0";
    $total_res = mysqli_query($con, $total_sql);
    $total_row = mysqli_fetch_assoc($total_res);
    $records_total = intval($total_row['cnt'] ?? 0);

    // -- Construir WHERE dinámico --
    $params = [];
    $types  = '';
    $where  = build_where_clause($params, $types);

    // -- Búsqueda global --
    $search_where = '';
    if ($search !== '') {
        $search_like = '%' . $search . '%';
        $search_where = " AND (s.sucursal LIKE ? OR u.nombre LIKE ? OR g.grupo LIKE ? OR m.modelo LIKE ? OR r.marca LIKE ? OR ld.detalleu LIKE ?)";
        $params[] = $search_like;
        $params[] = $search_like;
        $params[] = $search_like;
        $params[] = $search_like;
        $params[] = $search_like;
        $params[] = $search_like;
        $types   .= 'ssssss';
    }

    $full_where = $where . $search_where;

    // -- Count filtrado --
    $count_sql = "SELECT COUNT(*) AS cnt " . base_from_sql() . $full_where;
    $count_row = exec_prepared_one($count_sql, $params, $types);
    $records_filtered = intval($count_row['cnt'] ?? 0);

    // -- Data con paginación --
    $data_sql = "SELECT
        r.idreserva,
        DATE_FORMAT(r.fecres, '%d-%m-%Y') AS fecres,
        s.sucursal,
        u.nombre AS vendedor,
        COALESCE(g.grupo,'—') AS grupo,
        COALESCE(m.modelo,'—') AS modelo,
        COALESCE(r.marca,'') AS marca,
        COALESCE(r.compra,'') AS compra,
        r.anulada,
        COALESCE(ld.credito,0) AS credito,
        COALESCE(ld.toma_usado,0) AS toma_usado,
        COALESCE(r.detalleu,'') AS detalleu
    " . base_from_sql() . $full_where . "
    ORDER BY {$order_col} {$order_dir}
    LIMIT " . $length . " OFFSET " . $start;

    $rows = exec_prepared($data_sql, $params, $types);

    return [
        "draw"            => $draw,
        "recordsTotal"    => $records_total,
        "recordsFiltered" => $records_filtered,
        "data"            => $rows ?? []
    ];
}
