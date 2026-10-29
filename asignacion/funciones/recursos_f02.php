<?php
// Unidades con Nro Orden F02...: las vistas de recursos no las cuentan (igual que TPA).
// Se listan al final de los PDF costos_recursos_*.php, aparte y SIN sumar a los totales.
// $where: el mismo filtro de la consulta principal del reporte (sin la palabra WHERE).

function pdf_unidades_f02($pdf, $con, $where) {
	$SQL = "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_f02";
	if ($where != '') {
		$SQL .= " WHERE $where";
	}
	$unidades = mysqli_query($con, $SQL);
	if (!$unidades || mysqli_num_rows($unidades) == 0) {
		return;
	}

	$pdf->Ln(4);
	if ($pdf->GetY() > 180) {
		$pdf->AddPage('L','A4');
	}
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(0,5,'UNIDADES F02 - NO INCLUIDAS EN LOS TOTALES',0,1,'L');
	$pdf->SetFont('Arial','',6.5);

	$total_costo_tasa = 0;
	$total_reservas   = 0;
	$total_pagos      = 0;
	$saldo            = 0;

	while ($unidad = mysqli_fetch_array($unidades)) {
		$modelo_version = $unidad['Modelo'].' '.$unidad['Versión'];
		if (strlen($modelo_version) > 25) {
			$modelo_version = substr($modelo_version, 0, 25).'..';
		}
		$cliente = $unidad['Cliente'];
		if (strlen($cliente) > 20) {
			$cliente = substr($cliente, 0, 20).'..';
		}
		$asesor = $unidad['Asesor'];
		if (strlen($asesor) > 10) {
			$asesor = substr($asesor, 0, 10).'..';
		}

		$pdf->Cell(13,5,$unidad['Mes'],1,0,'C');
		$pdf->Cell(10,5,$unidad['Año'],1,0,'C');
		$pdf->Cell(35,5,$modelo_version,1,0,'L');
		$pdf->Cell(16,5,$unidad['NroOrden'],1,0,'C');
		$pdf->Cell(10,5,$unidad['Interno'],1,0,'C');
		$pdf->Cell(30,5,$cliente,1,0,'L');
		$pdf->Cell(15,5,$asesor,1,0,'L');
		$pdf->Cell(18,5,utf8_decode($unidad['Sucursal']),1,0,'C');
		$pdf->Cell(15,5,cambiarFormatoFecha($unidad['Reserva']),1,0,'C');
		$pdf->Cell(15,5,cambiarFormatoFecha($unidad['Arribo']),1,0,'C');
		$pdf->Cell(15,5,cambiarFormatoFecha($unidad['Despacho']),1,0,'C');
		$pdf->Cell(5,5,$unidad['pagado_tasa']?'Si':'-',1,0,'C');
		$pdf->Cell(20,5,'$ '.number_format($unidad['Costo TASA'], 0, ',','.'),1,0,'R');
		$pdf->Cell(20,5,'$ '.number_format($unidad['Operacion'], 0, ',','.'),1,0,'R');
		$pdf->Cell(20,5,'$ '.number_format($unidad['Pagos'], 0, ',','.'),1,0,'R');
		$pdf->Cell(20,5,'$ '.number_format($unidad['Saldo'], 0, ',','.'),1,0,'R');
		$pdf->Ln();

		$total_costo_tasa += $unidad['Costo TASA'];
		$total_reservas   += $unidad['Operacion'];
		$total_pagos      += $unidad['Pagos'];
		$saldo            += $unidad['Saldo'];
	}

	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(197,5,'SUBTOTAL F02 (NO COMPUTA)',1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($total_costo_tasa, 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($total_reservas, 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($total_pagos, 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($saldo, 0, ',','.'),1,0,'R');
	$pdf->Ln();
}
