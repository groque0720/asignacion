<?php
require('../fpdf/fpdf.php');

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

/* ======================================================
   FUNCIONES
====================================================== */

function initTotales() {
    return [
        'llegadas'=>0,'llegadas_sin_cliente'=>0,'llegadas_con_cliente'=>0,'llegadas_sin_cliente_EFV'=>0,
        'no_llegadas'=>0,'no_llegadas_sin_cliente'=>0,'no_llegadas_con_cliente'=>0,'no_llegadas_sin_cliente_EFV'=>0,
        'stock_total'=>0,'libres'=>0,'con_cliente'=>0,'reventas'=>0,'EFV'=>0
    ];
}

function v($value) {
    return ($value == 0 || $value === null) ? '-' : $value;
}

function imprimirSubtotal($pdf, $label, $totales, $dark = false) {

    if ($dark) {
		$pdf->ln(4);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(0);
    } else {
        $pdf->SetFillColor(236,240,241);
        $pdf->SetTextColor(0);
    }

    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(45,5,$label,1,0,'L',true);
	$pdf->Cell(4,5,'',0,0,'C');

	$fila = 1;
    foreach ($totales as $v) {
		$fila++;
        $pdf->Cell(10,5,v($v),1,0,'C',true);
		if ($fila==5 || $fila==9) {
			$pdf->Cell(4,5,'',0,0,'C');
		}

    }
    $pdf->Ln();
    $pdf->SetTextColor(0);
}

/* ======================================================
   CLASE PDF
====================================================== */

class PDF extends FPDF {

	

    function Header() {

		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',8);
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'C');
			$this->Cell(100,5,utf8_decode('PLANILLA DE ESTADO STOCK - CONFIRMADA TASA'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
		$this->SetDrawColor(120, 120, 120);

        // $this->SetFont('Arial','B',10);
        // $this->Cell(0,6,'REPORTE DE STOCK Y ARRIBOS',0,1,'C');
        $this->Ln(2);

        $this->SetFont('Arial','B',7);
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0);

		$this->cell(49,5,'',0,0,'C'); // espacio despues de modelo
		$this->Cell(40,5,'Stock con Arribo',1,0,'C',true);
		$this->Cell(4,5,'',0,0,'C',false); // espacio despues de stock
		$this->Cell(40,5,'Stock sin Arribo',1,0,'C',true);
		$this->Cell(4,5,'',0,0,'C',false); // espacio despues de stock
		$this->Cell(50,5,'Stock Total',1,0,'C',true);
		$this->Ln();

        $this->Cell(45,5,'Modelo',1,0,'C',true);
		$this->Cell(4,5,'',0,0,'C');

        $headers = [
            'Tot','s/cli','c/cli','EFV',
            'Tot','s/cli','c/cli','EFV',
            'Tot','s/cli','c/cli','Rev','EFV',
        ];

	

        foreach ($headers as $h) {
			if ($h=='Rev') {
				$this->SetFillColor(200,200,200);
			} else {
				$this->SetFillColor(255,255,255);
			}

			$this->Cell(10,5,$h,1,0,'C',true);

			if ($h=='EFV') {
				$this->Cell(4,5,'',0,0,'C');
			}


        }

        $this->Ln();
        $this->SetTextColor(0);
    }
}

/* ======================================================
   QUERY
====================================================== */

$SQL = "
SELECT
    g.idgrupo,
    g.grupo,
    m.idmodelo,
    m.modelo,

    COUNT(DISTINCT a.id_unidad) AS stock_total,

    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.estado_reserva = 0 AND a.id_asesor != 2 THEN 1 ELSE 0 END) AS libres,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.estado_reserva = 1 THEN 1 ELSE 0 END) AS con_cliente,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.reventa = 1 THEN 1 ELSE 0 END) AS reventas,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.estado_reserva = 0 AND a.id_asesor = 2 THEN 1 ELSE 0 END) AS EFV,

    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NOT NULL THEN 1 ELSE 0 END) AS llegadas,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NOT NULL AND a.estado_reserva = 0 AND a.id_asesor != 2 THEN 1 ELSE 0 END) AS llegadas_sin_cliente,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NOT NULL AND a.estado_reserva = 1 AND a.id_asesor != 2 THEN 1 ELSE 0 END) AS llegadas_con_cliente,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NOT NULL AND a.estado_reserva = 0 AND a.id_asesor = 2 THEN 1 ELSE 0 END) AS llegadas_sin_cliente_EFV,

    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NULL THEN 1 ELSE 0 END) AS no_llegadas,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NULL AND a.estado_reserva = 0 AND a.id_asesor != 2 THEN 1 ELSE 0 END) AS no_llegadas_sin_cliente,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NULL AND a.estado_reserva = 1 AND a.id_asesor != 2 THEN 1 ELSE 0 END) AS no_llegadas_con_cliente,
    SUM(CASE WHEN a.id_unidad IS NOT NULL AND a.fec_arribo IS NULL AND a.estado_reserva = 0 AND a.id_asesor = 2 THEN 1 ELSE 0 END) AS no_llegadas_sin_cliente_EFV

FROM grupos g
INNER JOIN modelos m ON g.idgrupo = m.idgrupo
LEFT JOIN asignaciones a ON m.idmodelo = a.id_modelo
    AND a.borrar = 0
    AND a.entregada = 0
    AND a.estado_tasa = 1
    AND a.id_negocio = 1
WHERE g.activo = 1
AND m.activo = 1
AND g.idgrupo != 14
GROUP BY g.idgrupo, m.idmodelo
ORDER BY g.posicion, m.posicion
";

$result = mysqli_query($con, $SQL);

/* ======================================================
   GENERACION PDF
====================================================== */

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,10);
$pdf->SetFont('Arial','',7);
$pdf->SetDrawColor(120, 120, 120);

$grupo_actual = null;
$totales_grupo = [];
$totales_generales = [];

while ($row = mysqli_fetch_assoc($result)) {

    if ($grupo_actual !== $row['grupo']) {

        if ($grupo_actual !== null) {
            imprimirSubtotal($pdf, "Subtotal $grupo_actual", $totales_grupo);
        }
		
		$pdf->Ln(2);
        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(223,230,233);
        $pdf->Cell(187,5,$row['grupo'],1,1,'L',true);

        $grupo_actual = $row['grupo'];
        $totales_grupo = initTotales();

    }

    foreach ($totales_grupo as $k => $v) {
        $totales_grupo[$k] += $row[$k];
        $totales_generales[$k] = ($totales_generales[$k] ?? 0) + $row[$k];
    }

    $pdf->SetFont('Arial','',7);
    $pdf->Cell(45,5,$row['modelo'],1);
	$pdf->Cell(4,5,'',0,0,'C');

    foreach ($totales_grupo as $k => $v) {

		if ($k == 'reventas') {
			$pdf->SetTextColor(120,120,120);
		} else {
			$pdf->SetTextColor(0,0,0);
		}

		if ($k=='stock_total' || $k=='llegadas' || $k=='no_llegadas') {
			$pdf->SetFillColor(235,235,235);
		} else {
			$pdf->SetFillColor(255,255,255);
		}

        $pdf->Cell(10,5,v($row[$k]),1,0,'C',true);

		if ($k=='EFV' || $k=='llegadas_sin_cliente_EFV' || $k=='no_llegadas_sin_cliente_EFV') {
			$pdf->Cell(4,5,'',0,0,'C');
		}
	
    }
    $pdf->Ln();
}

imprimirSubtotal($pdf, "Subtotal $grupo_actual", $totales_grupo);
imprimirSubtotal($pdf, "TOTAL GENERAL", $totales_generales, true);

/* ======================================================
   OUTPUT
====================================================== */

$pdf->Output('ReporteStockArribos_'.date('Ymd_His').'.pdf','I');
$pdf->Close();
