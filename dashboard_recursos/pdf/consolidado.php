<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../funciones/func_mysql.php');
require('../../fpdf/fpdf.php');

conectar();
@session_start();
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
	header('Location: ../../login');
	exit();
}
mysqli_query($con, "SET NAMES 'utf8'");

$sucursalId = isset($_GET['sucursalId']) ? intval($_GET['sucursalId']) : 0;
$whereSucursal = $sucursalId > 0 ? " WHERE idsucursal = " . $sucursalId : '';

function f_ars($n) {
	return '$ ' . number_format((float)$n, 0, ',', '.');
}
function f_txt_cut($s, $max) {
	$s = (string)$s;
	if (strlen($s) <= $max) {
		return $s;
	}
	return substr($s, 0, $max - 2) . '..';
}
function f_date($d) {
	if (!$d || $d === '0000-00-00') {
		return '-';
	}
	$p = explode('-', $d);
	if (count($p) === 3) {
		return $p[2] . '/' . $p[1] . '/' . $p[0];
	}
	return $d;
}

class ConsolidadoPDF extends FPDF {
	public $titleText = 'COSTOS Y RECURSOS - CONSOLIDADO';

	function Header() {
		$this->SetFillColor(30, 41, 59);
		$this->Rect(0, 0, $this->GetPageWidth(), 14, 'F');

		$this->SetTextColor(255, 255, 255);
		$this->SetY(4);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 4, 'DERKA Y VARGAS S.A. - ' . $this->titleText, 0, 1, 'C');

		$this->SetFont('Arial', '', 7);
		$this->Cell(0, 4, 'Emitido: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

		$this->SetTextColor(0, 0, 0);
		$this->SetY(18);
	}

	function Footer() {
		$this->SetY(-10);
		$this->SetTextColor(120, 120, 120);
		$this->SetFont('Arial', 'I', 7);
		$this->Cell(0, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
	}
}

// Summary data by category/sucursal
$rowsBySuc = array();
$totPend = 0;
$totViaje = 0;
$totArribo = 0;

$qPend = 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_no_llegadas';
$qViaje = 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_en_viaje';
$qArribo = 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas';
if ($sucursalId > 0) {
	$qPend .= ' WHERE IdSucursal = ' . $sucursalId;
	$qViaje .= ' WHERE IdSucursal = ' . $sucursalId;
	$qArribo .= ' WHERE IdSucursal = ' . $sucursalId;
}

$mapQueries = array('pendiente' => $qPend, 'viaje' => $qViaje, 'arribo' => $qArribo);
foreach ($mapQueries as $tipo => $sql) {
	$res = mysqli_query($con, $sql);
	if (!$res) {
		continue;
	}

	while ($r = mysqli_fetch_assoc($res)) {
		$id = $r['IdSucursal'];
		if (!isset($rowsBySuc[$id])) {
			$rowsBySuc[$id] = array(
				'Sucursal' => $r['Sucursal'],
				'pendiente' => 0,
				'viaje' => 0,
				'arribo' => 0
			);
		}
		$saldo = (float)$r['Saldo'];
		$rowsBySuc[$id][$tipo] = $saldo;

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
$sqlAsesor =
	"SELECT COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') AS Nombre, SUM(Saldo) AS Saldo, COUNT(*) AS Unidades " .
	"FROM view_asignaciones_saldo_pendiente_corregida" .
	$whereSucursal .
	" GROUP BY COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') " .
	"ORDER BY Saldo DESC LIMIT 8";
$res = mysqli_query($con, $sqlAsesor);
if ($res) {
	while ($r = mysqli_fetch_assoc($res)) {
		$topAsesor[] = $r;
	}
}

$topModelo = array();
$sqlModelo =
	"SELECT COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') AS Nombre, SUM(Saldo) AS Saldo, COUNT(*) AS Unidades " .
	"FROM view_asignaciones_saldo_pendiente_corregida" .
	$whereSucursal .
	" GROUP BY COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') " .
	"ORDER BY Saldo DESC LIMIT 8";
$res = mysqli_query($con, $sqlModelo);
if ($res) {
	while ($r = mysqli_fetch_assoc($res)) {
		$topModelo[] = $r;
	}
}

$detailSql = 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida' . $whereSucursal . ' ORDER BY Sucursal, Reserva';
$detailRes = mysqli_query($con, $detailSql);

$pdf = new ConsolidadoPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 10);

// KPI header
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetFillColor(248, 250, 252);
$pdf->Rect(10, 20, 277, 16, 'DF');
$pdf->SetXY(12, 23);
$pdf->Cell(68, 5, 'Pendiente: ' . f_ars($totPend), 0, 0, 'L');
$pdf->Cell(68, 5, 'En Viaje: ' . f_ars($totViaje), 0, 0, 'L');
$pdf->Cell(68, 5, 'Con Arribo: ' . f_ars($totArribo), 0, 0, 'L');
$pdf->Cell(68, 5, 'Total: ' . f_ars($totGeneral), 0, 1, 'L');

// Summary by branch
$pdf->SetY(40);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(95, 6, 'Sucursal', 0, 0, 'L', true);
$pdf->Cell(45, 6, 'Pendiente', 0, 0, 'R', true);
$pdf->Cell(45, 6, 'En Viaje', 0, 0, 'R', true);
$pdf->Cell(45, 6, 'Con Arribo', 0, 0, 'R', true);
$pdf->Cell(47, 6, 'Total', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 7.5);
$rows = array_values($rowsBySuc);
usort($rows, function($a, $b) {
	return strcmp($a['Sucursal'], $b['Sucursal']);
});

// Mantiene la primera hoja compacta: top 10 sucursales y resto agrupado.
$maxSucRows = 10;
$rowsShown = $rows;
if (count($rows) > $maxSucRows) {
	$rowsShown = array_slice($rows, 0, $maxSucRows);
	$oPend = 0;
	$oViaje = 0;
	$oArribo = 0;
	for ($i = $maxSucRows; $i < count($rows); $i++) {
		$oPend += (float)$rows[$i]['pendiente'];
		$oViaje += (float)$rows[$i]['viaje'];
		$oArribo += (float)$rows[$i]['arribo'];
	}
	$rowsShown[] = array(
		'Sucursal' => 'OTRAS SUCURSALES',
		'pendiente' => $oPend,
		'viaje' => $oViaje,
		'arribo' => $oArribo
	);
}

$fill = false;
foreach ($rowsShown as $r) {
	$fill = !$fill;
	$pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
	$sTotal = (float)$r['pendiente'] + (float)$r['viaje'] + (float)$r['arribo'];
	$pdf->Cell(95, 5.5, utf8_decode(f_txt_cut($r['Sucursal'], 34)), 0, 0, 'L', true);
	$pdf->Cell(45, 5.5, f_ars($r['pendiente']), 0, 0, 'R', true);
	$pdf->Cell(45, 5.5, f_ars($r['viaje']), 0, 0, 'R', true);
	$pdf->Cell(45, 5.5, f_ars($r['arribo']), 0, 0, 'R', true);
	$pdf->Cell(47, 5.5, f_ars($sTotal), 0, 1, 'R', true);
}

$pdf->SetFillColor(226, 232, 240);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(95, 6, 'TOTAL', 0, 0, 'L', true);
$pdf->Cell(45, 6, f_ars($totPend), 0, 0, 'R', true);
$pdf->Cell(45, 6, f_ars($totViaje), 0, 0, 'R', true);
$pdf->Cell(45, 6, f_ars($totArribo), 0, 0, 'R', true);
$pdf->Cell(47, 6, f_ars($totGeneral), 0, 1, 'R', true);

// Advisor and model tables (new requirement)
$pdf->Ln(3);
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(51, 65, 85);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetXY(10, $y);
$pdf->Cell(135, 6, 'Top 10 por Asesor', 0, 0, 'L', true);
$pdf->SetXY(152, $y);
$pdf->Cell(135, 6, 'Top 10 por Modelo', 0, 1, 'L', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', 'B', 7.2);
$pdf->SetXY(10, $y + 6);
$pdf->Cell(78, 5, 'Nombre', 0, 0, 'L');
$pdf->Cell(35, 5, 'Saldo', 0, 0, 'R');
$pdf->Cell(22, 5, 'Unidades', 0, 0, 'R');
$pdf->SetXY(152, $y + 6);
$pdf->Cell(78, 5, 'Nombre', 0, 0, 'L');
$pdf->Cell(35, 5, 'Saldo', 0, 0, 'R');
$pdf->Cell(22, 5, 'Unidades', 0, 1, 'R');

$pdf->SetFont('Arial', '', 7);
$max = max(count($topAsesor), count($topModelo));
if ($max < 4) {
	$max = 4;
}
for ($i = 0; $i < $max; $i++) {
	$lineY = $y + 11 + ($i * 5);
	$pdf->SetFillColor($i % 2 ? 250 : 255, $i % 2 ? 252 : 255, 255);
	$pdf->SetXY(10, $lineY);
	$pdf->Cell(135, 5, '', 0, 0, 'L', true);
	$pdf->SetXY(152, $lineY);
	$pdf->Cell(135, 5, '', 0, 0, 'L', true);

	if (isset($topAsesor[$i])) {
		$r = $topAsesor[$i];
		$pdf->SetXY(10, $lineY);
		$pdf->Cell(78, 5, utf8_decode(f_txt_cut($r['Nombre'], 28)), 0, 0, 'L');
		$pdf->Cell(35, 5, f_ars($r['Saldo']), 0, 0, 'R');
		$pdf->Cell(22, 5, (int)$r['Unidades'], 0, 0, 'R');
	}
	if (isset($topModelo[$i])) {
		$r = $topModelo[$i];
		$pdf->SetXY(152, $lineY);
		$pdf->Cell(78, 5, utf8_decode(f_txt_cut($r['Nombre'], 28)), 0, 0, 'L');
		$pdf->Cell(35, 5, f_ars($r['Saldo']), 0, 0, 'R');
		$pdf->Cell(22, 5, (int)$r['Unidades'], 0, 0, 'R');
	}
}

// Detail page
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(12, 6, 'Mes', 0, 0, 'C', true);
$pdf->Cell(10, 6, 'Anio', 0, 0, 'C', true);
$pdf->Cell(42, 6, 'Modelo/Version', 0, 0, 'L', true);
$pdf->Cell(16, 6, 'Orden', 0, 0, 'C', true);
$pdf->Cell(11, 6, 'Interno', 0, 0, 'C', true);
$pdf->Cell(30, 6, 'Cliente', 0, 0, 'L', true);
$pdf->Cell(24, 6, 'Asesor', 0, 0, 'L', true);
$pdf->Cell(20, 6, 'Sucursal', 0, 0, 'L', true);
$pdf->Cell(16, 6, 'Reserva', 0, 0, 'C', true);
$pdf->Cell(16, 6, 'Arribo', 0, 0, 'C', true);
$pdf->Cell(16, 6, 'Despacho', 0, 0, 'C', true);
$pdf->Cell(21, 6, 'Saldo', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 6.6);

$totalDetalle = 0;
$line = false;
if ($detailRes) {
	while ($u = mysqli_fetch_assoc($detailRes)) {
		if ($pdf->GetY() > 186) {
			$pdf->AddPage();
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetFillColor(30, 41, 59);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(12, 6, 'Mes', 0, 0, 'C', true);
			$pdf->Cell(10, 6, 'Anio', 0, 0, 'C', true);
			$pdf->Cell(42, 6, 'Modelo/Version', 0, 0, 'L', true);
			$pdf->Cell(16, 6, 'Orden', 0, 0, 'C', true);
			$pdf->Cell(11, 6, 'Interno', 0, 0, 'C', true);
			$pdf->Cell(30, 6, 'Cliente', 0, 0, 'L', true);
			$pdf->Cell(24, 6, 'Asesor', 0, 0, 'L', true);
			$pdf->Cell(20, 6, 'Sucursal', 0, 0, 'L', true);
			$pdf->Cell(16, 6, 'Reserva', 0, 0, 'C', true);
			$pdf->Cell(16, 6, 'Arribo', 0, 0, 'C', true);
			$pdf->Cell(16, 6, 'Despacho', 0, 0, 'C', true);
			$pdf->Cell(21, 6, 'Saldo', 0, 1, 'R', true);
			$pdf->SetTextColor(15, 23, 42);
			$pdf->SetFont('Arial', '', 6.6);
		}

		$line = !$line;
		$pdf->SetFillColor($line ? 248 : 255, $line ? 250 : 255, $line ? 252 : 255);

		$modelo = trim($u['Modelo'] . ' ' . $u['Versión']);
		$modelo = f_txt_cut($modelo, 30);
		$cliente = f_txt_cut($u['Cliente'], 22);
		$asesor = f_txt_cut($u['Asesor'], 17);

		$saldo = (float)$u['Saldo'];
		$totalDetalle += $saldo;

		$pdf->Cell(12, 5, $u['Mes'], 0, 0, 'C', true);
		$pdf->Cell(10, 5, $u['Año'], 0, 0, 'C', true);
		$pdf->Cell(42, 5, utf8_decode($modelo), 0, 0, 'L', true);
		$pdf->Cell(16, 5, $u['NroOrden'], 0, 0, 'C', true);
		$pdf->Cell(11, 5, $u['Interno'], 0, 0, 'C', true);
		$pdf->Cell(30, 5, utf8_decode($cliente), 0, 0, 'L', true);
		$pdf->Cell(24, 5, utf8_decode($asesor), 0, 0, 'L', true);
		$pdf->Cell(20, 5, utf8_decode($u['Sucursal']), 0, 0, 'L', true);
		$pdf->Cell(16, 5, f_date($u['Reserva']), 0, 0, 'C', true);
		$pdf->Cell(16, 5, f_date($u['Arribo']), 0, 0, 'C', true);
		$pdf->Cell(16, 5, f_date($u['Despacho']), 0, 0, 'C', true);
		$pdf->Cell(21, 5, f_ars($saldo), 0, 1, 'R', true);
	}
}

$pdf->SetFont('Arial', 'B', 7.2);
$pdf->SetFillColor(226, 232, 240);
$pdf->Cell(213, 6, 'TOTAL DETALLE', 0, 0, 'R', true);
$pdf->Cell(21, 6, f_ars($totalDetalle), 0, 1, 'R', true);

ob_end_clean();
$pdf->Output('I', 'dashboard_recursos_consolidado.pdf');
exit();
?>
