<?php
/**
 * export_pdf.php — Exportación PDF del Informe de Ventas
 * Usa FPDF (existente en /asignacion/fpdf/fpdf.php)
 */

require('../asignacion/fpdf/fpdf.php');
include("funciones/func_mysql.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    exit();
}

// ── KPIs ──────────────────────────────────────────────────────────────────────
function get_kpis_pdf(): array
{
    global $con;
    $params = [];
    $types  = '';
    $where  = build_where_pdf($params, $types);

    $sql = "SELECT COUNT(*) AS total,
        SUM(r.anulada) AS anuladas,
        SUM(COALESCE(ld.credito,0)) AS con_credito,
        SUM(COALESCE(ld.toma_usado,0)) AS con_toma
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
    WHERE r.fecres >= '2020-01-01' AND r.enviada != 0" . $where;

    $stmt = mysqli_prepare($con, $sql);
    if ($stmt && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (!$stmt) return [];
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    $total = intval($row['total'] ?? 0);
    $anu   = intval($row['anuladas'] ?? 0);
    $cred  = intval($row['con_credito'] ?? 0);
    $toma  = intval($row['con_toma'] ?? 0);
    return [
        'total'        => $total,
        'anuladas'     => $anu,
        'pct_anuladas' => $total > 0 ? round($anu/$total*100,1) : 0,
        'con_credito'  => $cred,
        'pct_credito'  => $total > 0 ? round($cred/$total*100,1) : 0,
        'con_toma'     => $toma,
        'pct_toma'     => $total > 0 ? round($toma/$total*100,1) : 0,
    ];
}

// ── Build WHERE ────────────────────────────────────────────────────────────────
function build_where_pdf(array &$params, string &$types): string
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
    $idusu = intval($_REQUEST['idusuario'] ?? 0);  if ($idusu>0) { $where .= " AND u.idusuario = ?";  $params[] = $idusu; $types .= 'i'; }
    $idgrp = intval($_REQUEST['idgrupo'] ?? 0);    if ($idgrp>0) { $where .= " AND r.idgrupo = ?";    $params[] = $idgrp; $types .= 'i'; }
    $idmod = intval($_REQUEST['idmodelo'] ?? 0);   if ($idmod>0) { $where .= " AND r.idmodelo = ?";   $params[] = $idmod; $types .= 'i'; }
    $marca = trim($_REQUEST['marca'] ?? '');       if ($marca!=='') { $where .= " AND r.marca LIKE ?"; $params[] = '%'.$marca.'%'; $types .= 's'; }
    $ar = $_REQUEST['anulada'] ?? ''; if ($ar!=='' && $ar!=='-1') { $a=intval($ar); if ($a===0||$a===1) { $where .= " AND r.anulada = ?"; $params[] = $a; $types .= 'i'; } }
    $cr = $_REQUEST['credito'] ?? ''; if ($cr!=='' && $cr!=='-1') { $c=intval($cr); if ($c===0||$c===1) { $where .= " AND COALESCE(ld.credito,0) = ?"; $params[] = $c; $types .= 'i'; } }
    $tr = $_REQUEST['toma_usado'] ?? ''; if ($tr!=='' && $tr!=='-1') { $t=intval($tr); if ($t===0||$t===1) { $where .= " AND COALESCE(ld.toma_usado,0) = ?"; $params[] = $t; $types .= 'i'; } }
    $compra = strtolower(trim($_REQUEST['compra'] ?? '')); if ($compra === 'nuevo' || $compra === 'usado') { $where .= " AND LOWER(r.compra) = ?"; $params[] = $compra; $types .= 's'; }
    return $where;
}

// ── Datos tabla ────────────────────────────────────────────────────────────────
function get_data_pdf()
{
    global $con;
    $params = [];
    $types  = '';
    $where  = build_where_pdf($params, $types);

    $sql = "SELECT r.idreserva,
        DATE_FORMAT(r.fecres,'%d/%m/%Y') AS fecha,
        s.sucursal, u.nombre AS vendedor,
        COALESCE(g.grupo,'—') AS grupo, COALESCE(m.modelo,'—') AS modelo,
        COALESCE(r.marca,'') AS marca, COALESCE(r.compra,'') AS compra, r.anulada,
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

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return null;
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// ── Clase PDF personalizada ────────────────────────────────────────────────────
class InformePDF extends FPDF
{
    public $kpis = [];
    public $filtros_str = '';

    function Header()
    {
        // Fondo cabecera
        $this->SetFillColor(30, 34, 53);
        $this->Rect(0, 0, $this->GetPageWidth(), 22, 'F');

        $this->SetY(4);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 8, 'DYV S.A. - Informe de Ventas', 0, 1, 'C');

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(160, 168, 192);
        $this->Cell(0, 5, 'Generado: ' . date('d/m/Y H:i'), 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetY(25);

        // KPIs en cabecera (solo pag 1)
        if ($this->PageNo() == 1 && !empty($this->kpis)) {
            $k = $this->kpis;
            $this->SetFillColor(240, 244, 255);
            $this->SetFont('Arial', 'B', 8);
            $w = $this->GetPageWidth() / 7;

            $items = [
                ['Total',        number_format($k['total'],0,'.','.'),       [30,34,53]],
                ['Anuladas',     number_format($k['anuladas'],0,'.','.'),     [224,92,92]],
                ['% Anuladas',   $k['pct_anuladas'].'%',                     [241,168,78]],
                ['Con Crédito',  number_format($k['con_credito'],0,'.','.'), [99,199,149]],
                ['% Crédito',    $k['pct_credito'].'%',                      [34,211,238]],
                ['Toma Usado',   number_format($k['con_toma'],0,'.','.'),    [167,139,250]],
                ['% Toma Usado', $k['pct_toma'].'%',                         [251,191,36]],
            ];

            foreach ($items as $item) {
                $this->SetFillColor($item[2][0], $item[2][1], $item[2][2]);
                $this->SetTextColor(255, 255, 255);
                $this->Cell($w, 5, $item[0], 0, 0, 'C', true);
            }
            $this->Ln();
            $this->SetFont('Arial', 'B', 11);
            foreach ($items as $item) {
                $this->SetFillColor($item[2][0], $item[2][1], $item[2][2]);
                $this->SetTextColor(255, 255, 255);
                $this->Cell($w, 7, $item[1], 0, 0, 'C', true);
            }
            $this->Ln();

            if ($this->filtros_str !== '') {
                $this->SetFont('Arial', 'I', 7);
                $this->SetTextColor(100, 100, 100);
                $this->Cell(0, 5, 'Filtros: ' . $this->filtros_str, 0, 1, 'L');
            }
            $this->Ln(2);
        } elseif ($this->PageNo() > 1) {
            $this->Ln(3);
        }

        // Cabecera de columnas
        $this->SetFillColor(30, 34, 53);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 7);
        $cols = [11, 17, 30, 36, 16, 24, 14, 12, 10, 10, 10, 47];
        $hdrs = ['ID','Fecha','Sucursal','Vendedor','Grupo','Modelo','Marca','Compra','Anul.','Cred.','Toma','Detalle Usado'];
        foreach ($cols as $i => $w) {
            $this->Cell($w, 6, $hdrs[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
    }

    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(130, 130, 130);
        $this->Cell(0, 5, 'Informe de Ventas DYV S.A.    Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// ── Construir descripción de filtros ───────────────────────────────────────────
$filtros_parts = [];
$anio_f = intval($_REQUEST['anio'] ?? 0);
if ($anio_f >= 2015) $filtros_parts[] = 'Año: ' . $anio_f;
$mes_f  = intval($_REQUEST['mes'] ?? 0);
if ($mes_f >= 1 && $mes_f <= 12) {
    $meses = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $filtros_parts[] = 'Mes: ' . $meses[$mes_f];
}
$fd = trim($_REQUEST['fecha_desde'] ?? ''); if ($fd) $filtros_parts[] = 'Desde: '.$fd;
$fh = trim($_REQUEST['fecha_hasta'] ?? ''); if ($fh) $filtros_parts[] = 'Hasta: '.$fh;
$filtros_str = implode(' | ', $filtros_parts);

// ── Generar PDF ────────────────────────────────────────────────────────────────
$kpis   = get_kpis_pdf();
$result = get_data_pdf();

$pdf = new InformePDF('L', 'mm', 'A4');
$pdf->kpis        = $kpis;
$pdf->filtros_str = $filtros_str;
$pdf->AliasNbPages();
$pdf->SetMargins(5, 52, 5);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 12);
$pdf->SetFont('Arial', '', 7);

$cols = [11, 17, 30, 36, 16, 24, 14, 12, 10, 10, 10, 47];
$fill = false;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($pdf->GetY() > 185) {
            $pdf->AddPage();
        }

        $fill_color = $fill ? [248, 250, 255] : [255, 255, 255];
        $pdf->SetFillColor($fill_color[0], $fill_color[1], $fill_color[2]);

        $pdf->Cell($cols[0],  5, $row['idreserva'],    'LRB', 0, 'C', true);
        $pdf->Cell($cols[1],  5, $row['fecha'],        'LRB', 0, 'C', true);
        $pdf->Cell($cols[2],  5, mb_strimwidth($row['sucursal'],0,18,'..'), 'LRB', 0, 'L', true);
        $pdf->Cell($cols[3],  5, mb_strimwidth($row['vendedor'],0,24,'..'), 'LRB', 0, 'L', true);
        $pdf->Cell($cols[4],  5, mb_strimwidth($row['grupo'],0,12,'..'),    'LRB', 0, 'L', true);
        $pdf->Cell($cols[5],  5, mb_strimwidth($row['modelo'],0,16,'..'),   'LRB', 0, 'L', true);
        $pdf->Cell($cols[6],  5, mb_strimwidth($row['marca'],0,10,'..'),       'LRB', 0, 'C', true);
        $pdf->Cell($cols[7],  5, ucfirst($row['compra']),                     'LRB', 0, 'C', true);
        $pdf->Cell($cols[8],  5, $row['anulada']   ==1 ? 'Si' : 'No',        'LRB', 0, 'C', true);
        $pdf->Cell($cols[9],  5, $row['credito']   ==1 ? 'Si' : 'No',        'LRB', 0, 'C', true);
        $pdf->Cell($cols[10], 5, $row['toma_usado']==1 ? 'Si' : 'No',        'LRB', 0, 'C', true);
        $pdf->Cell($cols[11], 5, mb_strimwidth($row['detalleu'],0,32,'..'),   'LRB', 1, 'L', true);

        $fill = !$fill;
    }
}

$pdf->Output('I', 'InformeVentas_' . date('d-m-Y') . '.pdf');
