<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../funciones/func_mysql.php');
require('../../asignacion/fpdf/fpdf.php');

conectar();
@session_start();
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
    header('Location: ../../login');
    exit();
}
mysqli_query($con, "SET NAMES 'utf8'");

function ars($n) {
    return '$ ' . number_format((float)$n, 0, ',', '.');
}
function txt_cut($s, $max) {
    $s = (string)$s;
    if (strlen($s) <= $max) {
        return $s;
    }
    return substr($s, 0, $max - 2) . '..';
}

class ResumenPDF extends FPDF {
    function Header() {
        $this->SetFillColor(30, 41, 59);
        $this->Rect(0, 0, $this->GetPageWidth(), 16, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 11);
        $this->SetY(4);
        $this->Cell(0, 4, 'DERKA Y VARGAS S.A. - RESUMEN EJECUTIVO RECURSOS', 0, 1, 'C');

        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, 'Emitido: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetY(20);
    }

    function Footer() {
        $this->SetY(-11);
        $this->SetTextColor(120, 120, 120);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$totPend = 0;
$totViaje = 0;
$totArribo = 0;
$matriz = array();

$queries = array(
    'pendiente' => 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_no_llegadas',
    'viaje' => 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_en_viaje',
    'arribo' => 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas'
);

foreach ($queries as $tipo => $sql) {
    $res = mysqli_query($con, $sql);
    if (!$res) {
        continue;
    }

    while ($r = mysqli_fetch_assoc($res)) {
        $id = $r['IdSucursal'];
        if (!isset($matriz[$id])) {
            $matriz[$id] = array(
                'Sucursal' => $r['Sucursal'],
                'pendiente' => 0,
                'viaje' => 0,
                'arribo' => 0
            );
        }

        $saldo = (float)$r['Saldo'];
        $matriz[$id][$tipo] = $saldo;

        if ($tipo === 'pendiente') {
            $totPend += $saldo;
        }
        if ($tipo === 'viaje') {
            $totViaje += $saldo;
        }
        if ($tipo === 'arribo') {
            $totArribo += $saldo;
        }
    }
}

$totGeneral = $totPend + $totViaje + $totArribo;

$topAsesor = array();
$resA = mysqli_query($con,
    "SELECT COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') AS Nombre, SUM(Saldo) AS Saldo, COUNT(*) AS Unidades " .
    "FROM view_asignaciones_saldo_pendiente_corregida " .
    "GROUP BY COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') " .
    "ORDER BY Saldo DESC LIMIT 8"
);
if ($resA) {
    while ($r = mysqli_fetch_assoc($resA)) {
        $topAsesor[] = $r;
    }
}

$topModelo = array();
$resM = mysqli_query($con,
    "SELECT COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') AS Nombre, SUM(Saldo) AS Saldo, COUNT(*) AS Unidades " .
    "FROM view_asignaciones_saldo_pendiente_corregida " .
    "GROUP BY COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') " .
    "ORDER BY Saldo DESC LIMIT 8"
);
if ($resM) {
    while ($r = mysqli_fetch_assoc($resM)) {
        $topModelo[] = $r;
    }
}

$pdf = new ResumenPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 12);

// KPI line
$pdf->SetFillColor(248, 250, 252);
$pdf->SetDrawColor(226, 232, 240);
$pdf->Rect(10, 22, 190, 24, 'DF');

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(51, 65, 85);
$pdf->SetXY(14, 25);
$pdf->Cell(45, 4, 'Pendiente TASA', 0, 0, 'L');
$pdf->Cell(45, 4, 'En Viaje', 0, 0, 'L');
$pdf->Cell(45, 4, 'Con Arribo', 0, 0, 'L');
$pdf->Cell(45, 4, 'Exposicion Total', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->SetX(14);
$pdf->Cell(45, 8, ars($totPend), 0, 0, 'L');
$pdf->Cell(45, 8, ars($totViaje), 0, 0, 'L');
$pdf->Cell(45, 8, ars($totArribo), 0, 0, 'L');
$pdf->Cell(45, 8, ars($totGeneral), 0, 1, 'L');

// Summary by branch
$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(70, 7, 'Sucursal', 0, 0, 'L', true);
$pdf->Cell(30, 7, 'Pendiente', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'En Viaje', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'Con Arribo', 0, 0, 'R', true);
$pdf->Cell(30, 7, 'Total', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 8);

$rows = array_values($matriz);
usort($rows, function($a, $b) {
    return strcmp($a['Sucursal'], $b['Sucursal']);
});

// Evita desborde de la primera hoja: mostrar top 12 sucursales y consolidar resto.
$maxSucRows = 12;
$rowsShown = $rows;
if (count($rows) > $maxSucRows) {
    $rowsShown = array_slice($rows, 0, $maxSucRows);
    $otrosPend = 0;
    $otrosViaje = 0;
    $otrosArribo = 0;
    for ($i = $maxSucRows; $i < count($rows); $i++) {
        $otrosPend += (float)$rows[$i]['pendiente'];
        $otrosViaje += (float)$rows[$i]['viaje'];
        $otrosArribo += (float)$rows[$i]['arribo'];
    }
    $rowsShown[] = array(
        'Sucursal' => 'OTRAS SUCURSALES',
        'pendiente' => $otrosPend,
        'viaje' => $otrosViaje,
        'arribo' => $otrosArribo
    );
}

$fill = false;
foreach ($rowsShown as $r) {
    $fill = !$fill;
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
    $totalSuc = (float)$r['pendiente'] + (float)$r['viaje'] + (float)$r['arribo'];

    $pdf->Cell(70, 6, utf8_decode(txt_cut($r['Sucursal'], 28)), 0, 0, 'L', true);
    $pdf->Cell(30, 6, ars($r['pendiente']), 0, 0, 'R', true);
    $pdf->Cell(30, 6, ars($r['viaje']), 0, 0, 'R', true);
    $pdf->Cell(30, 6, ars($r['arribo']), 0, 0, 'R', true);
    $pdf->Cell(30, 6, ars($totalSuc), 0, 1, 'R', true);
}

$pdf->SetFillColor(226, 232, 240);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(70, 7, 'TOTAL', 0, 0, 'L', true);
$pdf->Cell(30, 7, ars($totPend), 0, 0, 'R', true);
$pdf->Cell(30, 7, ars($totViaje), 0, 0, 'R', true);
$pdf->Cell(30, 7, ars($totArribo), 0, 0, 'R', true);
$pdf->Cell(30, 7, ars($totGeneral), 0, 1, 'R', true);

// Advisor and model sections
$pdf->Ln(4);
$yStart = $pdf->GetY();

$pdf->SetFillColor(51, 65, 85);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(10, $yStart);
$pdf->Cell(92, 6, 'Top 10 por Asesor', 0, 0, 'L', true);
$pdf->SetXY(108, $yStart);
$pdf->Cell(92, 6, 'Top 10 por Modelo', 0, 1, 'L', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', 'B', 7.5);
$pdf->SetXY(10, $yStart + 6);
$pdf->Cell(52, 5, 'Nombre', 0, 0, 'L');
$pdf->Cell(24, 5, 'Saldo', 0, 0, 'R');
$pdf->Cell(16, 5, 'Unid.', 0, 0, 'R');
$pdf->SetXY(108, $yStart + 6);
$pdf->Cell(52, 5, 'Nombre', 0, 0, 'L');
$pdf->Cell(24, 5, 'Saldo', 0, 0, 'R');
$pdf->Cell(16, 5, 'Unid.', 0, 1, 'R');

$pdf->SetFont('Arial', '', 7);
$maxRows = max(count($topAsesor), count($topModelo));
if ($maxRows < 4) {
    $maxRows = 4;
}

for ($i = 0; $i < $maxRows; $i++) {
    $y = $yStart + 11 + ($i * 5);
    $pdf->SetFillColor($i % 2 ? 250 : 255, $i % 2 ? 252 : 255, 255);

    $pdf->SetXY(10, $y);
    $pdf->Cell(92, 5, '', 0, 0, 'L', true);
    $pdf->SetXY(108, $y);
    $pdf->Cell(92, 5, '', 0, 0, 'L', true);

    if (isset($topAsesor[$i])) {
        $a = $topAsesor[$i];
        $nombre = utf8_decode(txt_cut($a['Nombre'], 24));
        $pdf->SetXY(10, $y);
        $pdf->Cell(52, 5, $nombre, 0, 0, 'L');
        $pdf->Cell(24, 5, ars($a['Saldo']), 0, 0, 'R');
        $pdf->Cell(16, 5, (int)$a['Unidades'], 0, 0, 'R');
    }

    if (isset($topModelo[$i])) {
        $m = $topModelo[$i];
        $nombre = utf8_decode(txt_cut($m['Nombre'], 24));
        $pdf->SetXY(108, $y);
        $pdf->Cell(52, 5, $nombre, 0, 0, 'L');
        $pdf->Cell(24, 5, ars($m['Saldo']), 0, 0, 'R');
        $pdf->Cell(16, 5, (int)$m['Unidades'], 0, 0, 'R');
    }
}

ob_end_clean();
$pdf->Output('I', 'dashboard_recursos_resumen_ejecutivo.pdf');
exit();
