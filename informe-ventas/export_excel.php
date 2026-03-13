<?php
/**
 * export_excel.php — Exportación Excel del Informe de Ventas
 * Patrón: HTML table con MIME Excel (igual a excel_planilla_asignacion.php del proyecto)
 */

include("funciones/func_mysql.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    exit();
}

// ── Construir WHERE dinámico (mismo helper que api.php) ────────────────────────
function build_where_clause_ex(array &$params, string &$types): string
{
    $where = "";

    $anio = intval($_REQUEST['anio'] ?? 0);
    if ($anio >= 2015 && $anio <= 2035) {
        $where .= " AND YEAR(r.fecres) = ?"; $params[] = $anio; $types .= 'i';
    }
    $mes = intval($_REQUEST['mes'] ?? 0);
    if ($mes >= 1 && $mes <= 12) {
        $where .= " AND MONTH(r.fecres) = ?"; $params[] = $mes; $types .= 'i';
    }
    $fd = trim($_REQUEST['fecha_desde'] ?? '');
    if ($fd !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fd);
        if ($dt && $dt->format('Y-m-d') === $fd) {
            $where .= " AND r.fecres >= ?"; $params[] = $fd; $types .= 's';
        }
    }
    $fh = trim($_REQUEST['fecha_hasta'] ?? '');
    if ($fh !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $fh);
        if ($dt && $dt->format('Y-m-d') === $fh) {
            $where .= " AND r.fecres <= ?"; $params[] = $fh; $types .= 's';
        }
    }
    $idsuc = intval($_REQUEST['idsucursal'] ?? 0);
    if ($idsuc > 0) { $where .= " AND s.idsucursal = ?"; $params[] = $idsuc; $types .= 'i'; }

    $idusu = intval($_REQUEST['idusuario'] ?? 0);
    if ($idusu > 0) { $where .= " AND u.idusuario = ?"; $params[] = $idusu; $types .= 'i'; }

    $idgrp = intval($_REQUEST['idgrupo'] ?? 0);
    if ($idgrp > 0) { $where .= " AND r.idgrupo = ?"; $params[] = $idgrp; $types .= 'i'; }

    $idmod = intval($_REQUEST['idmodelo'] ?? 0);
    if ($idmod > 0) { $where .= " AND r.idmodelo = ?"; $params[] = $idmod; $types .= 'i'; }

    $marca = trim($_REQUEST['marca'] ?? '');
    if ($marca !== '') { $where .= " AND r.marca LIKE ?"; $params[] = '%'.$marca.'%'; $types .= 's'; }

    $anulada_raw = $_REQUEST['anulada'] ?? '';
    if ($anulada_raw !== '' && $anulada_raw !== '-1') {
        $a = intval($anulada_raw);
        if ($a === 0 || $a === 1) { $where .= " AND r.anulada = ?"; $params[] = $a; $types .= 'i'; }
    }
    $cred_raw = $_REQUEST['credito'] ?? '';
    if ($cred_raw !== '' && $cred_raw !== '-1') {
        $c = intval($cred_raw);
        if ($c === 0 || $c === 1) { $where .= " AND COALESCE(ld.credito,0) = ?"; $params[] = $c; $types .= 'i'; }
    }
    $toma_raw = $_REQUEST['toma_usado'] ?? '';
    if ($toma_raw !== '' && $toma_raw !== '-1') {
        $t = intval($toma_raw);
        if ($t === 0 || $t === 1) { $where .= " AND COALESCE(ld.toma_usado,0) = ?"; $params[] = $t; $types .= 'i'; }
    }
    $compra = strtolower(trim($_REQUEST['compra'] ?? ''));
    if ($compra === 'nuevo' || $compra === 'usado') {
        $where .= " AND LOWER(r.compra) = ?"; $params[] = $compra; $types .= 's';
    }
    return $where;
}

// ── Consulta ────────────────────────────────────────────────────────────────
$params = [];
$types  = '';
$where  = build_where_clause_ex($params, $types);

$sql = "SELECT
    r.idreserva,
    DATE_FORMAT(r.fecres,'%d/%m/%Y') AS fecha,
    YEAR(r.fecres) AS anio,
    MONTH(r.fecres) AS mes,
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
FROM reservas r
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
WHERE r.fecres >= '2020-01-01' AND r.enviada != 0" . $where . "
ORDER BY r.fecres DESC";

global $con;
$stmt = mysqli_prepare($con, $sql);
if ($stmt && !empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// ── Headers Excel ────────────────────────────────────────────────────────────
$filename = 'InformeVentas_' . date('d-m-Y') . '.xls';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
h2 { color: #1e2235; }
table { border-collapse: collapse; width: 100%; }
th {
    background-color: #1e2235;
    color: #ffffff;
    font-weight: bold;
    border: 1px solid #999;
    padding: 6px 10px;
    text-align: center;
}
td {
    border: 1px solid #ccc;
    padding: 4px 8px;
    vertical-align: middle;
}
tr:nth-child(even) td { background-color: #f0f4ff; }
.center { text-align: center; }
.num    { text-align: right; }
</style>
</head>
<body>

<h2>Informe de Ventas — DYV S.A.</h2>
<p>Generado: <?= date('d/m/Y H:i:s') ?> &nbsp;|&nbsp; Usuario: <?= htmlspecialchars($_SESSION['usuario'] ?? '') ?></p>
<br>

<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Fecha</th>
            <th>Año</th>
            <th>Mes</th>
            <th>Sucursal</th>
            <th>Vendedor</th>
            <th>Grupo</th>
            <th>Modelo</th>
            <th>Marca</th>
            <th>Compra</th>
            <th>Anulada</th>
            <th>Crédito</th>
            <th>Toma Usado</th>
            <th>Detalle Usado</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($result)):
            while ($row = mysqli_fetch_assoc($result)):
        ?>
        <tr>
            <td class="center"><?= intval($row['idreserva']) ?></td>
            <td class="center"><?= htmlspecialchars($row['fecha']) ?></td>
            <td class="center"><?= intval($row['anio']) ?></td>
            <td class="center"><?= intval($row['mes']) ?></td>
            <td><?= htmlspecialchars($row['sucursal']) ?></td>
            <td><?= htmlspecialchars($row['vendedor']) ?></td>
            <td><?= htmlspecialchars($row['grupo']) ?></td>
            <td><?= htmlspecialchars($row['modelo']) ?></td>
            <td><?= htmlspecialchars($row['marca']) ?></td>
            <td class="center"><?= htmlspecialchars(ucfirst($row['compra'])) ?></td>
            <td class="center"><?= $row['anulada'] == 1 ? 'Sí' : 'No' ?></td>
            <td class="center"><?= $row['credito'] == 1 ? 'Sí' : 'No' ?></td>
            <td class="center"><?= $row['toma_usado'] == 1 ? 'Sí' : 'No' ?></td>
            <td><?= htmlspecialchars($row['detalleu']) ?></td>
        </tr>
        <?php
            endwhile;
        else:
        ?>
        <tr><td colspan="14" class="center">Sin datos para los filtros seleccionados</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php
if (isset($stmt)) mysqli_stmt_close($stmt);
