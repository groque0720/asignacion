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
    "ORDER BY Saldo DESC"
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
    "ORDER BY Saldo DESC"
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

// Summary by branch
$pdf->SetY(24);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 7, 'Sucursal', 0, 0, 'L', true);
$pdf->Cell(32, 7, 'Pendiente', 0, 0, 'R', true);
$pdf->Cell(32, 7, 'En Viaje', 0, 0, 'R', true);
$pdf->Cell(32, 7, 'Con Arribo', 0, 0, 'R', true);
$pdf->Cell(34, 7, 'Total', 0, 1, 'R', true);

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

    $pdf->Cell(60, 6, utf8_decode(txt_cut($r['Sucursal'], 28)), 0, 0, 'L', true);
    $pdf->Cell(32, 6, ars($r['pendiente']), 0, 0, 'R', true);
    $pdf->Cell(32, 6, ars($r['viaje']), 0, 0, 'R', true);
    $pdf->Cell(32, 6, ars($r['arribo']), 0, 0, 'R', true);
    $pdf->Cell(34, 6, ars($totalSuc), 0, 1, 'R', true);
}

$pdf->SetFillColor(226, 232, 240);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(60, 7, 'TOTAL', 0, 0, 'L', true);
$pdf->Cell(32, 7, ars($totPend), 0, 0, 'R', true);
$pdf->Cell(32, 7, ars($totViaje), 0, 0, 'R', true);
$pdf->Cell(32, 7, ars($totArribo), 0, 0, 'R', true);
$pdf->Cell(34, 7, ars($totGeneral), 0, 1, 'R', true);

// Seccion completa: Exposicion por Asesor (todos)
$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 7, 'EXPOSICION POR ASESOR (TODOS)', 0, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(226, 232, 240);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(90, 6, 'Asesor', 0, 0, 'L', true);
$pdf->Cell(40, 6, 'Saldo', 0, 0, 'R', true);
$pdf->Cell(30, 6, 'Unidades', 0, 0, 'R', true);
$pdf->Cell(30, 6, '% Total', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 8);
$fill = false;
foreach ($topAsesor as $a) {
    if ($pdf->GetY() > 275) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'EXPOSICION POR ASESOR (TODOS)', 0, 1, 'L', true);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(226, 232, 240);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(90, 6, 'Asesor', 0, 0, 'L', true);
        $pdf->Cell(40, 6, 'Saldo', 0, 0, 'R', true);
        $pdf->Cell(30, 6, 'Unidades', 0, 0, 'R', true);
        $pdf->Cell(30, 6, '% Total', 0, 1, 'R', true);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->SetFont('Arial', '', 8);
    }

    $fill = !$fill;
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
    $saldo = (float)$a['Saldo'];
    $p = $totGeneral > 0 ? round(($saldo / $totGeneral) * 100, 1) : 0;
    $pdf->Cell(90, 6, utf8_decode(txt_cut($a['Nombre'], 42)), 0, 0, 'L', true);
    $pdf->Cell(40, 6, ars($saldo), 0, 0, 'R', true);
    $pdf->Cell(30, 6, (int)$a['Unidades'], 0, 0, 'R', true);
    $pdf->Cell(30, 6, number_format($p, 1, ',', '.') . '%', 0, 1, 'R', true);
}

// Seccion completa: Exposicion por Modelo (todos)
$pdf->Ln(4);
$thisSectionNeedsHeader = false;
if ($pdf->GetY() > 250) {
    $pdf->AddPage();
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 7, 'EXPOSICION POR MODELO (TODOS)', 0, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(226, 232, 240);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(90, 6, 'Modelo', 0, 0, 'L', true);
$pdf->Cell(40, 6, 'Saldo', 0, 0, 'R', true);
$pdf->Cell(30, 6, 'Unidades', 0, 0, 'R', true);
$pdf->Cell(30, 6, '% Total', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 8);
$fill = false;
foreach ($topModelo as $m) {
    if ($pdf->GetY() > 275) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, 'EXPOSICION POR MODELO (TODOS)', 0, 1, 'L', true);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(226, 232, 240);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(90, 6, 'Modelo', 0, 0, 'L', true);
        $pdf->Cell(40, 6, 'Saldo', 0, 0, 'R', true);
        $pdf->Cell(30, 6, 'Unidades', 0, 0, 'R', true);
        $pdf->Cell(30, 6, '% Total', 0, 1, 'R', true);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->SetFont('Arial', '', 8);
    }

    $fill = !$fill;
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
    $saldo = (float)$m['Saldo'];
    $p = $totGeneral > 0 ? round(($saldo / $totGeneral) * 100, 1) : 0;
    $pdf->Cell(90, 6, utf8_decode(txt_cut($m['Nombre'], 42)), 0, 0, 'L', true);
    $pdf->Cell(40, 6, ars($saldo), 0, 0, 'R', true);
    $pdf->Cell(30, 6, (int)$m['Unidades'], 0, 0, 'R', true);
    $pdf->Cell(30, 6, number_format($p, 1, ',', '.') . '%', 0, 1, 'R', true);
}

// Unidades F02: las vistas no las cuentan; se listan aparte, sin sumar a los totales.
$resF02 = mysqli_query($con, 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_f02 ORDER BY Sucursal, NroOrden');
if ($resF02 && mysqli_num_rows($resF02) > 0) {
    $pdf->Ln(4);
    if ($pdf->GetY() > 245) {
        $pdf->AddPage();
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(254, 243, 199);
    $pdf->SetTextColor(146, 64, 14);
    $pdf->Cell(0, 7, 'UNIDADES F02 - NO INCLUIDAS EN LOS TOTALES', 0, 1, 'L', true);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(226, 232, 240);
    $pdf->SetTextColor(30, 41, 59);
    $pdf->Cell(22, 6, 'Orden', 0, 0, 'C', true);
    $pdf->Cell(44, 6, 'Modelo', 0, 0, 'L', true);
    $pdf->Cell(34, 6, 'Cliente', 0, 0, 'L', true);
    $pdf->Cell(22, 6, 'Sucursal', 0, 0, 'L', true);
    $pdf->Cell(18, 6, 'Estado', 0, 0, 'C', true);
    $pdf->Cell(25, 6, 'Costo', 0, 0, 'R', true);
    $pdf->Cell(25, 6, 'Saldo', 0, 1, 'R', true);

    $pdf->SetTextColor(15, 23, 42);
    $pdf->SetFont('Arial', '', 7.5);
    $costoF02 = 0;
    $saldoF02 = 0;
    $fill = false;
    while ($u = mysqli_fetch_assoc($resF02)) {
        if ($pdf->GetY() > 275) {
            $pdf->AddPage();
        }
        if (!$u['pagado_tasa']) {
            $estado = 'Pend. TASA';
        } elseif (empty($u['Arribo'])) {
            $estado = 'En viaje';
        } else {
            $estado = 'Con arribo';
        }

        $fill = !$fill;
        $pdf->SetFillColor(255, $fill ? 251 : 255, $fill ? 235 : 255);
        $pdf->Cell(22, 6, $u['NroOrden'], 0, 0, 'C', true);
        $pdf->Cell(44, 6, utf8_decode(txt_cut(trim($u['Modelo'] . ' ' . $u['Versión']), 28)), 0, 0, 'L', true);
        $pdf->Cell(34, 6, utf8_decode(txt_cut($u['Cliente'], 20)), 0, 0, 'L', true);
        $pdf->Cell(22, 6, utf8_decode(txt_cut($u['Sucursal'], 14)), 0, 0, 'L', true);
        $pdf->Cell(18, 6, $estado, 0, 0, 'C', true);
        $pdf->Cell(25, 6, ars($u['Costo TASA']), 0, 0, 'R', true);
        $pdf->Cell(25, 6, ars($u['Saldo']), 0, 1, 'R', true);

        $costoF02 += (float)$u['Costo TASA'];
        $saldoF02 += (float)$u['Saldo'];
    }

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(254, 243, 199);
    $pdf->Cell(140, 7, 'SUBTOTAL F02 (NO COMPUTA)', 0, 0, 'R', true);
    $pdf->Cell(25, 7, ars($costoF02), 0, 0, 'R', true);
    $pdf->Cell(25, 7, ars($saldoF02), 0, 1, 'R', true);
}

ob_end_clean();
$pdf->Output('I', 'dashboard_recursos_resumen_ejecutivo.pdf');
exit();
