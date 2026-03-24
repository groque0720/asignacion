<?php
/**
 * reporte.php — Reporte Ejecutivo para Gerencia
 * Recibe filtros + imágenes base64 de los charts via POST
 */

include("funciones/func_mysql.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}

// ── Build WHERE ────────────────────────────────────────────────────────────────
function build_where_rep(array &$params, string &$types): string
{
    $where = "";
    $anio = intval($_REQUEST['anio'] ?? 0);
    if ($anio >= 2015 && $anio <= 2035) { $where .= " AND YEAR(r.fecres) = ?"; $params[] = $anio; $types .= 'i'; }
    $mes = intval($_REQUEST['mes'] ?? 0);
    if ($mes >= 1 && $mes <= 12) { $where .= " AND MONTH(r.fecres) = ?"; $params[] = $mes; $types .= 'i'; }
    $fd = trim($_REQUEST['fecha_desde'] ?? '');
    if ($fd !== '') { $dt = DateTime::createFromFormat('Y-m-d',$fd); if ($dt && $dt->format('Y-m-d')===$fd) { $where .= " AND r.fecres >= ?"; $params[] = $fd; $types .= 's'; } }
    $fh = trim($_REQUEST['fecha_hasta'] ?? '');
    if ($fh !== '') { $dt = DateTime::createFromFormat('Y-m-d',$fh); if ($dt && $dt->format('Y-m-d')===$fh) { $where .= " AND r.fecres <= ?"; $params[] = $fh; $types .= 's'; } }
    $idsuc = intval($_REQUEST['idsucursal'] ?? 0); if ($idsuc>0) { $where .= " AND s.idsucursal = ?"; $params[] = $idsuc; $types .= 'i'; }
    $idusu = intval($_REQUEST['idusuario']  ?? 0); if ($idusu>0) { $where .= " AND u.idusuario = ?";  $params[] = $idusu; $types .= 'i'; }
    $idgrp = intval($_REQUEST['idgrupo']    ?? 0); if ($idgrp>0) { $where .= " AND r.idgrupo = ?";    $params[] = $idgrp; $types .= 'i'; }
    $idmod = intval($_REQUEST['idmodelo']   ?? 0); if ($idmod>0) { $where .= " AND r.idmodelo = ?";   $params[] = $idmod; $types .= 'i'; }
    $marca = trim($_REQUEST['marca'] ?? '');       if ($marca!=='') { $where .= " AND r.marca LIKE ?"; $params[] = '%'.$marca.'%'; $types .= 's'; }
    $ar = $_REQUEST['anulada'] ?? ''; if ($ar!==''&&$ar!=='-1') { $a=intval($ar); if ($a===0||$a===1) { $where .= " AND r.anulada = ?"; $params[] = $a; $types .= 'i'; } }
    $cr = $_REQUEST['credito'] ?? ''; if ($cr!==''&&$cr!=='-1') { $c=intval($cr); if ($c===0||$c===1) { $where .= " AND COALESCE(ld.credito,0) = ?"; $params[] = $c; $types .= 'i'; } }
    $tr = $_REQUEST['toma_usado'] ?? ''; if ($tr!==''&&$tr!=='-1') { $t=intval($tr); if ($t===0||$t===1) { $where .= " AND COALESCE(ld.toma_usado,0) = ?"; $params[] = $t; $types .= 'i'; } }
    $compra = strtolower(trim($_REQUEST['compra'] ?? '')); if ($compra === 'nuevo' || $compra === 'usado') { $where .= " AND LOWER(r.compra) = ?"; $params[] = $compra; $types .= 's'; }
    return $where;
}

function base_from_rep(): string {
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

function exec_rep(string $sql, array $params, string $types) {
    global $con;
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return null;
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// ── Obtener KPIs ──────────────────────────────────────────────────────────────
$params = []; $types = ''; $where = build_where_rep($params, $types);
$kpi_sql = "SELECT COUNT(*) AS total, SUM(r.anulada) AS anuladas,
    SUM(COALESCE(ld.credito,0)) AS con_credito, SUM(COALESCE(ld.toma_usado,0)) AS con_toma
    " . base_from_rep() . $where;
$kpi_res = exec_rep($kpi_sql, $params, $types);
$kpi_row = $kpi_res ? mysqli_fetch_assoc($kpi_res) : [];
$total_r   = intval($kpi_row['total']       ?? 0);
$anuladas  = intval($kpi_row['anuladas']    ?? 0);
$con_cred  = intval($kpi_row['con_credito'] ?? 0);
$con_toma  = intval($kpi_row['con_toma']    ?? 0);
$pct_anu   = $total_r > 0 ? round($anuladas/$total_r*100,1) : 0;
$pct_cred  = $total_r > 0 ? round($con_cred/$total_r*100,1) : 0;
$pct_toma  = $total_r > 0 ? round($con_toma/$total_r*100,1) : 0;

// ── Top 5 Sucursales ──────────────────────────────────────────────────────────
$params2 = []; $types2 = ''; $where2 = build_where_rep($params2, $types2);
$suc_sql = "SELECT s.sucursal, COUNT(*) AS cnt " . base_from_rep() . $where2 . " GROUP BY s.idsucursal, s.sucursal ORDER BY cnt DESC LIMIT 5";
$suc_res = exec_rep($suc_sql, $params2, $types2);
$sucursales_top = [];
if ($suc_res) while ($r = mysqli_fetch_assoc($suc_res)) $sucursales_top[] = $r;

// ── Top 5 Vendedores ─────────────────────────────────────────────────────────
$params3 = []; $types3 = ''; $where3 = build_where_rep($params3, $types3);
$vend_sql = "SELECT u.nombre AS vendedor, COUNT(*) AS cnt " . base_from_rep() . $where3 . " GROUP BY u.idusuario, u.nombre ORDER BY cnt DESC LIMIT 5";
$vend_res = exec_rep($vend_sql, $params3, $types3);
$vendedores_top = [];
if ($vend_res) while ($r = mysqli_fetch_assoc($vend_res)) $vendedores_top[] = $r;

// ── Resumen mensual ───────────────────────────────────────────────────────────
$params4 = []; $types4 = ''; $where4 = build_where_rep($params4, $types4);
$mes_sql = "SELECT MONTH(r.fecres) AS mes, COUNT(*) AS cnt " . base_from_rep() . $where4 . " GROUP BY MONTH(r.fecres) ORDER BY mes";
$mes_res = exec_rep($mes_sql, $params4, $types4);
$por_mes = array_fill(1, 12, 0);
if ($mes_res) while ($r = mysqli_fetch_assoc($mes_res)) $por_mes[intval($r['mes'])] = intval($r['cnt']);

// ── Comparativo mes a mes por modelo/versión ─────────────────────────────────
$params_mm = []; $types_mm = ''; $where_mm = build_where_rep($params_mm, $types_mm);
$mm_sql = "SELECT
    COALESCE(g.grupo, 'Usados') AS grupo,
    COALESCE(m.modelo, '—')     AS modelo,
    MONTH(r.fecres)             AS mes,
    COUNT(*)                    AS cantidad
" . base_from_rep() . $where_mm . "
GROUP BY r.idgrupo, r.idmodelo, MONTH(r.fecres)
ORDER BY
    (g.posicion IS NULL OR g.posicion = 0) ASC, COALESCE(g.posicion, 9999),
    COALESCE(g.grupo, 'Usados'),
    (m.posicion IS NULL OR m.posicion = 0) ASC, COALESCE(m.posicion, 9999),
    COALESCE(m.modelo, '—'),
    mes";
$mm_res  = exec_rep($mm_sql, $params_mm, $types_mm);
$mm_rows = [];
if ($mm_res) while ($r = mysqli_fetch_assoc($mm_res)) $mm_rows[] = $r;

// Pivot: grupo → modelos → meses
$mm_grupos     = [];
$mm_grupoOrder = [];
foreach ($mm_rows as $r) {
    $gk = $r['grupo'];
    $mk = $gk . '||' . $r['modelo'];
    if (!isset($mm_grupos[$gk])) {
        $mm_grupos[$gk] = ['nombre' => $r['grupo'], 'modelos' => [], 'modeloOrder' => [], 'mesTotals' => [], 'total' => 0];
        $mm_grupoOrder[] = $gk;
    }
    $g = &$mm_grupos[$gk];
    if (!isset($g['modelos'][$mk])) {
        $g['modelos'][$mk] = ['nombre' => $r['modelo'], 'meses' => [], 'total' => 0];
        $g['modeloOrder'][] = $mk;
    }
    $mes = intval($r['mes']); $cant = intval($r['cantidad']);
    $g['modelos'][$mk]['meses'][$mes]  = $cant;
    $g['modelos'][$mk]['total']       += $cant;
    $g['mesTotals'][$mes]              = ($g['mesTotals'][$mes] ?? 0) + $cant;
    $g['total']                       += $cant;
    unset($g);
}
$mm_meses_activos = [];
foreach ($mm_grupoOrder as $gk) {
    foreach (array_keys($mm_grupos[$gk]['mesTotals']) as $m)
        if (!in_array((int)$m, $mm_meses_activos)) $mm_meses_activos[] = (int)$m;
}
sort($mm_meses_activos);

// ── Filtros descripción ───────────────────────────────────────────────────────
$meses_nombre = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$filtros_desc = [];
$anio_f = intval($_REQUEST['anio'] ?? 0); if ($anio_f >= 2015) $filtros_desc[] = 'Año: '.$anio_f;
$mes_f  = intval($_REQUEST['mes']  ?? 0); if ($mes_f >= 1 && $mes_f <= 12) $filtros_desc[] = 'Mes: '.$meses_nombre[$mes_f];
$fd_f   = trim($_REQUEST['fecha_desde'] ?? ''); if ($fd_f) $filtros_desc[] = 'Desde: '.$fd_f;
$fh_f   = trim($_REQUEST['fecha_hasta'] ?? ''); if ($fh_f) $filtros_desc[] = 'Hasta: '.$fh_f;
$filtros_txt = !empty($filtros_desc) ? implode(' | ', $filtros_desc) : 'Sin filtros adicionales (desde 2020)';

// ── Imágenes de charts (base64 via POST) ─────────────────────────────────────
function safe_img(string $key): string {
    $val = $_POST[$key] ?? '';
    // Solo aceptar data URI de imagen
    if (strpos($val, 'data:image/') === 0) {
        return $val;
    }
    return '';
}
$img_mes      = safe_img('chart_img_mes');
$img_anio     = safe_img('chart_img_anio_comp');
$img_sucursal = safe_img('chart_img_sucursal');
$img_vendedor = safe_img('chart_img_vendedor');
$img_anuladas = safe_img('chart_img_anuladas');
$img_credito  = safe_img('chart_img_credito');
$img_toma     = safe_img('chart_img_toma');

$usuario = htmlspecialchars($_SESSION['usuario'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Gerencial — DYV S.A.</title>
    <style>
        /* ── Base ──────────────────────────────────── */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #1a1d2e;
            background: #f0f2f8;
            padding: 20px;
        }

        /* ── Acciones (ocultas al imprimir) ─────────── */
        .print-actions {
            text-align: right;
            margin-bottom: 16px;
        }
        .btn-print {
            background: #4e9af1;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            margin-left: 8px;
        }
        .btn-close {
            background: transparent;
            color: #666;
            border: 1px solid #ccc;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
        }

        /* ── Páginas ────────────────────────────────── */
        .page {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            padding: 30px 36px;
            max-width: 900px;
            margin: 0 auto 24px;
        }

        /* ── Encabezado ─────────────────────────────── */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 3px solid #1e2235;
            margin-bottom: 20px;
        }
        .report-header h1 {
            font-size: 22pt;
            color: #1e2235;
            font-weight: 800;
        }
        .report-header .subtitle {
            font-size: 11pt;
            color: #6b7394;
            margin-top: 4px;
        }
        .report-meta {
            text-align: right;
            font-size: 9pt;
            color: #9fa8c0;
            line-height: 1.8;
        }

        /* ── Sección ────────────────────────────────── */
        .section-title {
            font-size: 10pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7394;
            margin: 20px 0 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eaeaf5;
        }

        /* ── KPI Grid ───────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .kpi-box {
            border: 1px solid #e0e4f0;
            border-radius: 8px;
            padding: 12px 14px;
            border-top: 3px solid #4e9af1;
        }
        .kpi-box:nth-child(2) { border-top-color: #e05c5c; }
        .kpi-box:nth-child(3) { border-top-color: #f1a84e; }
        .kpi-box:nth-child(4) { border-top-color: #63c795; }
        .kpi-box:nth-child(5) { border-top-color: #22d3ee; }
        .kpi-box:nth-child(6) { border-top-color: #a78bfa; }
        .kpi-box:nth-child(7) { border-top-color: #fbbf24; }
        .kpi-box .lbl {
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #9fa8c0;
            letter-spacing: 0.06em;
        }
        .kpi-box .val {
            font-size: 22pt;
            font-weight: 800;
            color: #1e2235;
            line-height: 1.1;
        }

        /* ── Resumen ejecutivo ──────────────────────── */
        .exec-summary {
            background: #f8f9ff;
            border: 1px solid #e0e4f0;
            border-left: 4px solid #4e9af1;
            border-radius: 6px;
            padding: 14px 18px;
            font-size: 10.5pt;
            line-height: 1.7;
            color: #2d3150;
            margin-bottom: 20px;
        }
        .exec-summary strong { color: #1e2235; }

        /* ── Gráficos ───────────────────────────────── */
        .charts-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .charts-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .chart-box {
            border: 1px solid #e0e4f0;
            border-radius: 8px;
            padding: 12px;
        }
        .chart-box .ct { font-size: 8.5pt; font-weight: 700; text-transform: uppercase; color: #9fa8c0; margin-bottom: 6px; }
        .chart-box img { width: 100%; height: auto; max-height: 180px; object-fit: contain; }
        .chart-box .no-img { color: #ccc; font-size: 9pt; text-align: center; padding: 30px 0; }

        /* ── Tablas de ranking ──────────────────────── */
        .rankings {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .rank-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
        .rank-table th {
            background: #1e2235;
            color: #fff;
            padding: 5px 10px;
            font-size: 8.5pt;
            text-align: left;
        }
        .rank-table td { padding: 5px 10px; border-bottom: 1px solid #f0f0f8; }
        .rank-table tr:nth-child(even) td { background: #f8f9ff; }
        .rank-bar-wrap { background: #eaeaf5; border-radius: 4px; height: 8px; overflow: hidden; }
        .rank-bar { background: #4e9af1; height: 8px; border-radius: 4px; }

        /* ── Resumen mensual ────────────────────────── */
        .mes-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; margin-bottom: 20px; }
        .mes-table th { background: #1e2235; color: #fff; padding: 5px 10px; font-size: 8.5pt; text-align: center; }
        .mes-table td { padding: 5px 10px; border: 1px solid #eaeaf5; text-align: center; }
        .mes-table tr:nth-child(even) td { background: #f8f9ff; }

        /* ── Firma ──────────────────────────────────── */
        .footer-report {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #eaeaf5;
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            color: #9fa8c0;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #666;
            padding-top: 4px;
            text-align: center;
            font-size: 8.5pt;
            color: #444;
        }
        .signatures { display: flex; gap: 40px; margin-top: 40px; }

        /* ── Tabla comparativo mes a mes ───────────── */
        .mm-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 6px; }
        .mm-table th {
            background: #1e2235;
            color: #fff;
            padding: 5px 7px;
            font-size: 7.5pt;
            text-align: center;
            white-space: nowrap;
        }
        .mm-table th.col-name { text-align: left; }
        .mm-table td { padding: 4px 7px; border-bottom: 1px solid #eaeaf5; text-align: center; white-space: nowrap; }
        .mm-table td.col-name { text-align: left; }
        .mm-grupo-row td {
            background: #eef0fa;
            font-weight: 700;
            color: #1e2235;
            font-size: 8.5pt;
        }
        .mm-version-row td { color: #3a3f5c; }
        .mm-version-row td.col-name { padding-left: 18px; color: #4a5568; }
        .mm-total-row td {
            background: #1e2235;
            color: #fff;
            font-weight: 800;
            font-size: 8.5pt;
        }
        .pct-pos { color: #1a6b3c; font-weight: 700; }
        .pct-neg { color: #c0392b; font-weight: 700; }

        /* ── Print ──────────────────────────────────── */
        @media print {
            body { background: #fff; padding: 0; font-size: 10pt; }
            .print-actions { display: none !important; }
            .page {
                box-shadow: none;
                border-radius: 0;
                page-break-after: always;
                margin: 0;
                padding: 20px 28px;
            }
            .page:last-child { page-break-after: avoid; }
            .kpi-grid { grid-template-columns: repeat(4,1fr); }
            .charts-grid-2 { grid-template-columns: 1fr 1fr; }
        }

        @page { size: A4; margin: 12mm; }
    </style>
</head>
<body>

<!-- Botones de acción -->
<div class="print-actions">
    <button class="btn-close" onclick="window.close()">
        ✕ Cerrar
    </button>
    <button class="btn-print" onclick="window.print()">
        🖨 Imprimir / Guardar PDF
    </button>
</div>

<!-- ══════════════════════════════════════════════════
     PÁGINA 1 — Resumen Ejecutivo + KPIs
════════════════════════════════════════════════════ -->
<div class="page">

    <!-- Encabezado -->
    <div class="report-header">
        <div>
            <h1>Informe de Ventas</h1>
            <div class="subtitle">DYV S.A. — Reporte Ejecutivo de Reservas Comerciales</div>
        </div>
        <div class="report-meta">
            Generado: <?= date('d/m/Y H:i') ?><br>
            Por: <?= $usuario ?><br>
            Período: <?= htmlspecialchars($filtros_txt) ?>
        </div>
    </div>

    <!-- Resumen ejecutivo -->
    <div class="section-title">Resumen Ejecutivo</div>
    <div class="exec-summary">
        El presente informe analiza <strong><?= number_format($total_r, 0, ',', '.') ?> reservas</strong>
        registradas en el sistema bajo los filtros seleccionados.
        De ese total, <strong><?= $anuladas ?> fueron anuladas</strong> (<?= $pct_anu ?>%),
        <strong><?= number_format($con_cred,0,',','.') ?> se financiaron con crédito</strong> (<?= $pct_cred ?>%)
        y <strong><?= number_format($con_toma,0,',','.') ?> incluyeron toma de vehículo usado</strong> (<?= $pct_toma ?>%).
        <?php if (!empty($sucursales_top)): ?>
        La sucursal con mayor actividad fue <strong><?= htmlspecialchars($sucursales_top[0]['sucursal']) ?></strong>
        con <?= number_format(intval($sucursales_top[0]['cnt']),0,',','.') ?> reservas.
        <?php endif; ?>
        <?php if (!empty($vendedores_top)): ?>
        El vendedor de mejor desempeño fue <strong><?= htmlspecialchars($vendedores_top[0]['vendedor']) ?></strong>
        con <?= number_format(intval($vendedores_top[0]['cnt']),0,',','.') ?> operaciones.
        <?php endif; ?>
    </div>

    <!-- KPIs -->
    <div class="section-title">Indicadores Clave (KPIs)</div>
    <div class="kpi-grid">
        <div class="kpi-box"><div class="lbl">Total Reservas</div><div class="val"><?= number_format($total_r,0,'.','.') ?></div></div>
        <div class="kpi-box"><div class="lbl">Anuladas</div><div class="val"><?= number_format($anuladas,0,'.','.') ?></div></div>
        <div class="kpi-box"><div class="lbl">% Anuladas</div><div class="val"><?= $pct_anu ?>%</div></div>
        <div class="kpi-box"><div class="lbl">Con Crédito</div><div class="val"><?= number_format($con_cred,0,'.','.') ?></div></div>
        <div class="kpi-box"><div class="lbl">% Crédito</div><div class="val"><?= $pct_cred ?>%</div></div>
        <div class="kpi-box"><div class="lbl">Toma Usado</div><div class="val"><?= number_format($con_toma,0,'.','.') ?></div></div>
        <div class="kpi-box"><div class="lbl">% Toma Usado</div><div class="val"><?= $pct_toma ?>%</div></div>
    </div>

    <!-- Resumen mensual -->
    <div class="section-title">Resumen por Mes</div>
    <table class="mes-table">
        <thead>
            <tr>
                <th>Mes</th>
                <?php foreach ($meses_nombre as $m => $n): if ($m === 0) continue; ?>
                <th><?= $n ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Reservas</strong></td>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                <td><?= number_format($por_mes[$m],0,',','.') ?></td>
                <?php endfor; ?>
            </tr>
        </tbody>
    </table>

    <!-- Rankings -->
    <div class="rankings">
        <!-- Top Sucursales -->
        <div>
            <div class="section-title">Top 5 Sucursales</div>
            <?php if (!empty($sucursales_top)): ?>
            <?php $max_suc = intval($sucursales_top[0]['cnt']); ?>
            <table class="rank-table">
                <thead><tr><th>#</th><th>Sucursal</th><th>Reservas</th><th style="width:80px"></th></tr></thead>
                <tbody>
                <?php foreach ($sucursales_top as $i => $s): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($s['sucursal']) ?></td>
                    <td><?= number_format(intval($s['cnt']),0,',','.') ?></td>
                    <td>
                        <div class="rank-bar-wrap">
                            <div class="rank-bar" style="width:<?= $max_suc>0?round(intval($s['cnt'])/$max_suc*100):0 ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?><p style="color:#999;font-size:9pt">Sin datos</p><?php endif; ?>
        </div>

        <!-- Top Vendedores -->
        <div>
            <div class="section-title">Top 5 Vendedores</div>
            <?php if (!empty($vendedores_top)): ?>
            <?php $max_vnd = intval($vendedores_top[0]['cnt']); ?>
            <table class="rank-table">
                <thead><tr><th>#</th><th>Vendedor</th><th>Reservas</th><th style="width:80px"></th></tr></thead>
                <tbody>
                <?php foreach ($vendedores_top as $i => $v): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($v['vendedor']) ?></td>
                    <td><?= number_format(intval($v['cnt']),0,',','.') ?></td>
                    <td>
                        <div class="rank-bar-wrap">
                            <div class="rank-bar" style="background:#a78bfa;width:<?= $max_vnd>0?round(intval($v['cnt'])/$max_vnd*100):0 ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?><p style="color:#999;font-size:9pt">Sin datos</p><?php endif; ?>
        </div>
    </div>

    <!-- Pie de página con firma -->
    <div class="footer-report">
        <div>Documento confidencial. Generado automáticamente por el sistema DYV.</div>
        <div><?= date('d/m/Y') ?></div>
    </div>

</div><!-- /page 1 -->

<!-- ══════════════════════════════════════════════════
     PÁGINA 2 — Gráficos
════════════════════════════════════════════════════ -->
<div class="page">

    <div class="report-header">
        <div>
            <h1>Análisis Gráfico</h1>
            <div class="subtitle">Visualizaciones de reservas — <?= htmlspecialchars($filtros_txt) ?></div>
        </div>
        <div class="report-meta">DYV S.A. — <?= date('d/m/Y') ?></div>
    </div>

    <!-- Fila 1: Reservas por mes + Año actual vs anterior -->
    <div class="charts-grid-2">
        <div class="chart-box">
            <div class="ct">📊 Reservas por Mes</div>
            <?php if ($img_mes): ?>
            <img src="<?= htmlspecialchars($img_mes) ?>" alt="Reservas por Mes">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
        <div class="chart-box">
            <div class="ct">📈 Año Actual vs Año Anterior</div>
            <?php if ($img_anio): ?>
            <img src="<?= htmlspecialchars($img_anio) ?>" alt="Comparación Anual">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
    </div>

    <!-- Fila 2: Sucursal + Vendedores + Anuladas -->
    <div class="charts-grid-3">
        <div class="chart-box">
            <div class="ct">🏢 Por Sucursal</div>
            <?php if ($img_sucursal): ?>
            <img src="<?= htmlspecialchars($img_sucursal) ?>" alt="Por Sucursal">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
        <div class="chart-box">
            <div class="ct">🏆 Top Vendedores</div>
            <?php if ($img_vendedor): ?>
            <img src="<?= htmlspecialchars($img_vendedor) ?>" alt="Top Vendedores">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
        <div class="chart-box">
            <div class="ct">🚫 Anuladas vs Activas</div>
            <?php if ($img_anuladas): ?>
            <img src="<?= htmlspecialchars($img_anuladas) ?>" alt="Anuladas">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
    </div>

    <!-- Fila 3: Crédito + Toma usado -->
    <div class="charts-grid-2">
        <div class="chart-box">
            <div class="ct">💳 Crédito vs Contado</div>
            <?php if ($img_credito): ?>
            <img src="<?= htmlspecialchars($img_credito) ?>" alt="Crédito">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
        <div class="chart-box">
            <div class="ct">🔄 Toma Usado vs Sin Toma</div>
            <?php if ($img_toma): ?>
            <img src="<?= htmlspecialchars($img_toma) ?>" alt="Toma Usado">
            <?php else: ?><div class="no-img">Gráfico no disponible</div><?php endif; ?>
        </div>
    </div>

    <div class="footer-report">
        <div>DYV S.A. — Sistema de Gestión de Reservas</div>
        <div>Página 2 / 3</div>
    </div>

</div><!-- /page 2 -->

<!-- ══════════════════════════════════════════════════
     PÁGINA 3 — Comparativo Mes a Mes por Modelo/Versión
════════════════════════════════════════════════════ -->
<div class="page">

    <div class="report-header">
        <div>
            <h1>Comparativo Mes a Mes</h1>
            <div class="subtitle">Por Modelo / Versión — <?= htmlspecialchars($filtros_txt) ?></div>
        </div>
        <div class="report-meta">DYV S.A. — <?= date('d/m/Y') ?></div>
    </div>

    <?php
    $meses_abrev = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // Helper % variación
    function mm_pct(int $actual, int $anterior): string {
        if ($anterior <= 0) return '';
        $v    = round(($actual - $anterior) / $anterior * 100, 1);
        $sign = $v >= 0 ? '+' : '';
        $cls  = $v >= 0 ? 'pct-pos' : 'pct-neg';
        return '<br><span class="' . $cls . '">' . $sign . $v . '%</span>';
    }

    if (empty($mm_grupoOrder)):
    ?>
    <p style="color:#999;font-size:9pt;text-align:center;padding:30px 0">Sin datos para el período seleccionado.</p>
    <?php else: ?>

    <table class="mm-table">
        <thead>
            <tr>
                <th class="col-name" style="min-width:160px">Grupo / Versión</th>
                <?php foreach ($mm_meses_activos as $m): ?>
                <th><?= $meses_abrev[$m] ?></th>
                <?php endforeach; ?>
                <th style="color:#4e9af1">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $tot_mes    = array_fill_keys($mm_meses_activos, 0);
        $tot_global = 0;

        foreach ($mm_grupoOrder as $gk):
            $g = $mm_grupos[$gk];
        ?>
            <!-- Fila grupo -->
            <tr class="mm-grupo-row">
                <td class="col-name">▸ <?= htmlspecialchars($g['nombre']) ?></td>
                <?php foreach ($mm_meses_activos as $idx => $m):
                    $val  = $g['mesTotals'][$m] ?? 0;
                    $prev = $idx > 0 ? ($g['mesTotals'][$mm_meses_activos[$idx-1]] ?? 0) : null;
                    $tot_mes[$m] += $val; $tot_global += $val;
                ?>
                <td><?= $val > 0 ? $val : '—' ?><?= $prev !== null ? mm_pct($val, $prev) : '' ?></td>
                <?php endforeach; ?>
                <td><?= number_format($g['total'], 0, ',', '.') ?></td>
            </tr>
            <!-- Filas versión -->
            <?php foreach ($g['modeloOrder'] as $mk):
                $mod = $g['modelos'][$mk]; ?>
            <tr class="mm-version-row">
                <td class="col-name"><?= htmlspecialchars($mod['nombre']) ?></td>
                <?php foreach ($mm_meses_activos as $idx => $m):
                    $val  = $mod['meses'][$m] ?? 0;
                    $prev = $idx > 0 ? ($mod['meses'][$mm_meses_activos[$idx-1]] ?? 0) : null;
                ?>
                <td><?= $val > 0 ? $val : '<span style="color:#ccc">—</span>' ?><?= $prev !== null ? mm_pct($val, $prev) : '' ?></td>
                <?php endforeach; ?>
                <td><?= number_format($mod['total'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <!-- Fila total general -->
        <tr class="mm-total-row">
            <td class="col-name">TOTAL GENERAL</td>
            <?php foreach ($mm_meses_activos as $idx => $m):
                $val  = $tot_mes[$m];
                $prev = $idx > 0 ? $tot_mes[$mm_meses_activos[$idx-1]] : null;
            ?>
            <td><?= number_format($val, 0, ',', '.') ?><?= $prev !== null ? mm_pct($val, $prev) : '' ?></td>
            <?php endforeach; ?>
            <td><?= number_format($tot_global, 0, ',', '.') ?></td>
        </tr>
        </tbody>
    </table>

    <?php endif; ?>

    <div class="footer-report">
        <div>DYV S.A. — Sistema de Gestión de Reservas</div>
        <div>Página 3 / 3</div>
    </div>

</div><!-- /page 3 -->

</body>
</html>
