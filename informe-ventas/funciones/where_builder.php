<?php
/**
 * where_builder.php — WHERE dinámico + FROM base + helpers de prepared statements.
 * Compartido por api.php y los exports del comparativo mes a mes (export_modelo_mes_*.php).
 */

// =============================================================
// FUNCIÓN CENTRAL: Construye la cláusula WHERE dinámica
// Retorna: string con condiciones AND
// Llena: $params (array por referencia) y $types (string por ref)
// SEGURIDAD: todos los valores de usuario van a ? placeholders
// =============================================================
function build_where_clause(array &$params, string &$types, bool $exclude_anio = false, bool $exclude_mes_fechas = false): string
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
    if (!$exclude_mes_fechas) {
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
