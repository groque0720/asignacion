<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
require('fpdf/fpdf.php');
extract($_GET);

list($anio,$mes_filtro, $dia)=explode("-",$fecha);

	$SQL="SELECT * FROM sucursales";
	$sucursales=mysqli_query($con, $SQL);
	$sucursal_a[0]['sucres']= '-';
	$i=1;
	while ($sucursal=mysqli_fetch_array($sucursales)) {
		$sucursal_a[$i]['sucursal']= $sucursal['sucursal'];
		$sucursal_a[$i]['id']= $sucursal['idsucursal'];
		$i++;
	}

	$SQL="SELECT * FROM meses";
		$meses=mysqli_query($con, $SQL);
		$i=1;
		while ($mes=mysqli_fetch_array($meses)) {
			$mes_a[$i]['mes']= $mes['mes'];
			$i++;
		}

	$SQL="SELECT * FROM grupos WHERE activo = 1";
	$grupos=mysqli_query($con, $SQL);
	$grupo_a[]['grupo']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
		$i++;
	}


class PDF extends FPDF
{
	// Cabecera de página
	function Header()
	{
		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',8);
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'C');
			$this->Cell(100,5,utf8_decode('REPORTE RECEPCION - NO COMPRA PRECIO'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}

		$this->Ln();
		$this->Ln();
		$this->Ln(3);
	}
}

// $total_modo = [];

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
// $pdf->SetAutoPageBreak(true,6);
$pdf->SetFont('Arial','BI',8);




$pdf->Cell(0,5,'ACUMULADO DIARIO',0,1,'C');
$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln();
$pdf->Cell(20,5,'FECHA:',0,0,'L');
$pdf->Cell(50,5,cambiarFormatoFecha($fecha),0,1,'L');
$pdf->Cell(20,5,'SUCURSAL:',0,0,'L');
$pdf->Cell(50,5,strtoupper(utf8_decode($sucursal_a[$id]['sucursal'])),0,1,'L');
$pdf->Ln(1);

$SQL="SELECT * FROM recepcion_modo_acercamiento";
$modos = mysqli_query($con, $SQL);
$cantidad_de_modos = $modos->num_rows;
$ancho_modos = 25;


$pdf->Cell(40,5,'Modelos / Acercamiento',1,0,'C');
while ($modo = mysqli_fetch_array($modos)) {
	$pdf->Cell($ancho_modos,5,utf8_decode($modo['modo_acercamiento']),1,0,'C');
	$total_modo[$modo['id_modo_acercamiento']] = 0;
}
$pdf->Cell($ancho_modos,5,'Total',1,0,'C');
$pdf->Ln();
$pdf->SetFont('');



$SQL="SELECT * FROM grupos WHERE activo = 1 AND cerokilometro = 1 AND posicion>0 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL);

	while ($grupo=mysqli_fetch_array($grupos)) {
		// $pdf->SetFont('Arial','B',10);
		// $pdf->SetFont('');
		$cant_modelo = 0;
		$pdf->Cell(40,5,'   '.$grupo_a[$grupo['idgrupo']]['grupo'],1,0,'L');
			$SQL="SELECT * FROM recepcion_modo_acercamiento";
			$modos = mysqli_query($con, $SQL);

			while ($modo = mysqli_fetch_array($modos)) {
				$SQL = "SELECT Count(recepcion.id_grupo) AS cantidad FROM recepcion
						 WHERE recepcion.id_sucursal =".$id." AND recepcion.motivo_no_compra = 1 AND recepcion.fecha = '".$fecha."' AND recepcion.id_grupo = ".$grupo['idgrupo']." AND recepcion.id_acercamiento = ".$modo['id_modo_acercamiento'];
				$registros = mysqli_query($con, $SQL);
				$registro = mysqli_fetch_array($registros);
				if ($registro['cantidad']>0) {
					$pdf->Cell($ancho_modos,5,$registro['cantidad'],1,0,'C');
				}else{
					$pdf->Cell($ancho_modos,5,'-',1,0,'C');
				}
				$cant_modelo = $cant_modelo + $registro['cantidad'];
				$total_modo[$modo['id_modo_acercamiento']] = $total_modo[$modo['id_modo_acercamiento']] + $registro['cantidad'];
			}
		$pdf->Cell($ancho_modos,5,$cant_modelo,1,0,'C');
		$pdf->Ln();

	}
$pdf->SetFont('Arial','BI',8);
$pdf->Cell(40,5,'Total',1,0,'C');
$total_gral = 0;
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	while ($modo = mysqli_fetch_array($modos)) {
		$pdf->Cell($ancho_modos,5,$total_modo[$modo['id_modo_acercamiento']],1,0,'C');
		$total_gral = $total_gral + $total_modo[$modo['id_modo_acercamiento']];
	}

$pdf->Cell($ancho_modos,5,$total_gral,1,0,'C');
$pdf->Ln();







$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','BI',8);
$pdf->Cell(0,5,'ACUMULADO MES',0,1,'C');
$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln();
$pdf->Ln();



$pdf->Cell(20,5,'MES:',0,0,'L');
$pdf->Cell(50,5,$mes_a[$mes_filtro+0]['mes'].' '.$anio,0,1,'L');
$pdf->Cell(20,5,'SUCURSAL:',0,0,'L');
$pdf->Cell(50,5,strtoupper(utf8_decode($sucursal_a[$id]['sucursal'])),0,1,'L');
$pdf->Ln(1);


$SQL="SELECT * FROM recepcion_modo_acercamiento";
$modos = mysqli_query($con, $SQL);
$cantidad_de_modos = $modos->num_rows;


$pdf->Cell(40,5,'Modelos / Acercamiento',1,0,'C');
while ($modo = mysqli_fetch_array($modos)) {
	$pdf->Cell($ancho_modos,5,utf8_decode($modo['modo_acercamiento']),1,0,'C');
	$total_modo[$modo['id_modo_acercamiento']] = 0;
}
$pdf->Cell($ancho_modos,5,'Total',1,0,'C');
$pdf->Ln();
$pdf->SetFont('');

$SQL="SELECT * FROM grupos WHERE activo = 1 AND cerokilometro = 1 AND posicion>0 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL);

	while ($grupo=mysqli_fetch_array($grupos)) {
		// $pdf->SetFont('Arial','B',10);
		// $pdf->SetFont('');
		$cant_modelo = 0;
		$pdf->Cell(40,5,'   '.$grupo_a[$grupo['idgrupo']]['grupo'],1,0,'L');
			$SQL="SELECT * FROM recepcion_modo_acercamiento";
			$modos = mysqli_query($con, $SQL);

			while ($modo = mysqli_fetch_array($modos)) {
				$SQL = "SELECT Count(recepcion.id_grupo) AS cantidad FROM recepcion
						 WHERE recepcion.id_sucursal =".$id." AND recepcion.motivo_no_compra = 1 AND MONTH(recepcion.fecha) = '".$mes_filtro."' AND recepcion.fecha <= '".$fecha."' AND YEAR(recepcion.fecha) = '".$anio."' AND recepcion.id_grupo = ".$grupo['idgrupo']." AND recepcion.id_acercamiento = ".$modo['id_modo_acercamiento'];
				$registros = mysqli_query($con, $SQL);
				$registro = mysqli_fetch_array($registros);
				if ($registro['cantidad']>0) {
					$pdf->Cell($ancho_modos,5,$registro['cantidad'],1,0,'C');
				}else{
					$pdf->Cell($ancho_modos,5,'-',1,0,'C');
				}
				$cant_modelo = $cant_modelo + $registro['cantidad'];
				$total_modo[$modo['id_modo_acercamiento']] = $total_modo[$modo['id_modo_acercamiento']] + $registro['cantidad'];
			}
		$pdf->Cell($ancho_modos,5,$cant_modelo,1,0,'C');
		$pdf->Ln();

	}

$pdf->SetFont('Arial','BI',8);
$pdf->Cell(40,5,'Total',1,0,'C');
$total_gral = 0;
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	while ($modo = mysqli_fetch_array($modos)) {
		$pdf->Cell($ancho_modos,5,$total_modo[$modo['id_modo_acercamiento']],1,0,'C');
		$total_gral = $total_gral + $total_modo[$modo['id_modo_acercamiento']];
	}

$pdf->Cell($ancho_modos,5,$total_gral,1,0,'C');

// $pdf->Output('Stock.pdf','I');
// $pdf->close();
$pdf->AddPage('P','A4');



// ----------------------------------------- ACUMULADO DYV - DIARIO

$pdf->SetFont('Arial','BI',8);
$pdf->Cell(0,5,'ACUMULADO DIARIO - DERKA Y VARGAS',0,1,'C');
$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(20,5,'FECHA:',0,0,'L');
$pdf->Cell(50,5,cambiarFormatoFecha($fecha),0,1,'L');
$pdf->Ln(1);

$SQL="SELECT * FROM recepcion_modo_acercamiento";
$modos = mysqli_query($con, $SQL);
$cantidad_de_modos = $modos->num_rows;


$pdf->Cell(40,5,'Sucursales / Acercamiento',1,0,'C');
while ($modo = mysqli_fetch_array($modos)) {
	$pdf->Cell($ancho_modos,5,utf8_decode($modo['modo_acercamiento']),1,0,'C');
	$total_modo[$modo['id_modo_acercamiento']] = 0;
}
$pdf->Cell($ancho_modos,5,'Total',1,0,'C');
$pdf->Ln();
$pdf->SetFont('');

$SQL="SELECT * FROM sucursales";
$sucursales=mysqli_query($con, $SQL);

while ($sucursal = mysqli_fetch_array($sucursales)) {
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	$pdf->Cell(40,5,'   '.utf8_decode($sucursal['sucursal']),1,0,'L');
	$cant_sucursal = 0;
	while ($modo = mysqli_fetch_array($modos)) {
		$SQL="SELECT Count(recepcion.id_sucursal) AS cantidad FROM recepcion INNER JOIN grupos ON recepcion.id_grupo = grupos.idgrupo
		WHERE grupos.grupo <> '' AND grupos.activo = 1  AND recepcion.motivo_no_compra = 1 AND  recepcion.fecha = '".$fecha."' AND recepcion.id_acercamiento = ".$modo['id_modo_acercamiento']." AND
		recepcion.id_sucursal=".$sucursal['idsucursal'];
		$registros = mysqli_query($con, $SQL);
		$registro = mysqli_fetch_array($registros);
		$pdf->Cell($ancho_modos,5,$registro['cantidad'],1,0,'C');
		$cant_sucursal = $cant_sucursal + $registro['cantidad'];
		$total_modo[$modo['id_modo_acercamiento']] = $total_modo[$modo['id_modo_acercamiento']] + $registro['cantidad'];
	}
	$pdf->Cell($ancho_modos,5,$cant_sucursal,1,0,'C');
	$pdf->Ln();
}


$pdf->SetFont('Arial','BI',8);
$pdf->Cell(40,5,'Total',1,0,'C');
$total_gral = 0;
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	while ($modo = mysqli_fetch_array($modos)) {
		$pdf->Cell($ancho_modos,5,$total_modo[$modo['id_modo_acercamiento']],1,0,'C');
		$total_gral = $total_gral + $total_modo[$modo['id_modo_acercamiento']];
	}

$pdf->Cell($ancho_modos,5,$total_gral,1,0,'C');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();





// ----------------------------------------- ACUMULADO DYV - MENSUAL

$pdf->SetFont('Arial','BI',8);
$pdf->Cell(0,5,'ACUMULADO MENSUAL - DERKA Y VARGAS',0,1,'C');
$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(20,5,'MES:',0,0,'L');
$pdf->Cell(50,5,$mes_a[$mes_filtro+0]['mes'].' '.$anio,0,1,'L');
$pdf->Cell(20,5,'Hasta Fecha:',0,0,'L');
$pdf->Cell(50,5,cambiarFormatoFecha($fecha),0,1,'L');
$pdf->Ln(1);

$SQL="SELECT * FROM recepcion_modo_acercamiento";
$modos = mysqli_query($con, $SQL);
$cantidad_de_modos = $modos->num_rows;


$pdf->Cell(40,5,'Sucursales / Acercamiento',1,0,'C');
while ($modo = mysqli_fetch_array($modos)) {
	$pdf->Cell($ancho_modos,5,utf8_decode($modo['modo_acercamiento']),1,0,'C');
	$total_modo[$modo['id_modo_acercamiento']] = 0;
}
$pdf->Cell($ancho_modos,5,'Total',1,0,'C');
$pdf->Ln();
$pdf->SetFont('');

$SQL="SELECT * FROM sucursales";
$sucursales=mysqli_query($con, $SQL);

while ($sucursal = mysqli_fetch_array($sucursales)) {
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	$pdf->Cell(40,5,'   '.utf8_decode($sucursal['sucursal']),1,0,'L');
	$cant_sucursal = 0;
	while ($modo = mysqli_fetch_array($modos)) {
		$SQL="SELECT Count(recepcion.id_sucursal) AS cantidad FROM recepcion INNER JOIN grupos ON recepcion.id_grupo = grupos.idgrupo
		WHERE grupos.grupo <> '' AND grupos.activo = 1  AND recepcion.motivo_no_compra = 1 AND MONTH(recepcion.fecha) = '".$mes_filtro."' AND recepcion.fecha <= '".$fecha."' AND YEAR(recepcion.fecha) = '".$anio."' AND recepcion.id_acercamiento = ".$modo['id_modo_acercamiento']." AND
		recepcion.id_sucursal=".$sucursal['idsucursal'];
		$registros = mysqli_query($con, $SQL);
		$registro = mysqli_fetch_array($registros);
		$pdf->Cell($ancho_modos,5,$registro['cantidad'],1,0,'C');
		$cant_sucursal = $cant_sucursal + $registro['cantidad'];
		$total_modo[$modo['id_modo_acercamiento']] = $total_modo[$modo['id_modo_acercamiento']] + $registro['cantidad'];
	}
	$pdf->Cell($ancho_modos,5,$cant_sucursal,1,0,'C');
	$pdf->Ln();
}


$pdf->SetFont('Arial','BI',8);
$pdf->Cell(40,5,'Total',1,0,'C');
$total_gral = 0;
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	while ($modo = mysqli_fetch_array($modos)) {
		$pdf->Cell($ancho_modos,5,$total_modo[$modo['id_modo_acercamiento']],1,0,'C');
		$total_gral = $total_gral + $total_modo[$modo['id_modo_acercamiento']];
	}

$pdf->Cell($ancho_modos,5,$total_gral,1,0,'C');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

// ------------------------------- ACUMULADO ANUAL DERKA Y VARGAS


$pdf->SetFont('Arial','BI',8);
$pdf->Cell(0,5,'ACUMULADO ANUAL - DERKA Y VARGAS',0,1,'C');
$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(20,5,utf8_decode('AÑO:'),0,0,'L');
$pdf->Cell(50,5,' '.$anio,0,1,'L');
$pdf->Cell(20,5,utf8_decode('Hasta Fecha:'),0,0,'L');
$pdf->Cell(50,5,cambiarFormatoFecha($fecha),0,1,'L');
$pdf->Ln(1);

$SQL="SELECT * FROM recepcion_modo_acercamiento";
$modos = mysqli_query($con, $SQL);
$cantidad_de_modos = $modos->num_rows;


$pdf->Cell(40,5,'Sucursales / Acercamiento',1,0,'C');
while ($modo = mysqli_fetch_array($modos)) {
	$pdf->Cell($ancho_modos,5,utf8_decode($modo['modo_acercamiento']),1,0,'C');
	$total_modo[$modo['id_modo_acercamiento']] = 0;
}
$pdf->Cell($ancho_modos,5,'Total',1,0,'C');
$pdf->Ln();
$pdf->SetFont('');

$SQL="SELECT * FROM sucursales";
$sucursales=mysqli_query($con, $SQL);

while ($sucursal = mysqli_fetch_array($sucursales)) {
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	$pdf->Cell(40,5,'   '.utf8_decode($sucursal['sucursal']),1,0,'L');
	$cant_sucursal = 0;
	while ($modo = mysqli_fetch_array($modos)) {
		$SQL="SELECT Count(recepcion.id_sucursal) AS cantidad FROM recepcion INNER JOIN grupos ON recepcion.id_grupo = grupos.idgrupo
		WHERE grupos.grupo <> '' AND recepcion.motivo_no_compra = 1 AND grupos.activo = 1  AND recepcion.fecha <= '".$fecha."' AND YEAR(recepcion.fecha) = '".$anio."' AND recepcion.id_acercamiento = ".$modo['id_modo_acercamiento']." AND
		recepcion.id_sucursal=".$sucursal['idsucursal'];
		$registros = mysqli_query($con, $SQL);
		$registro = mysqli_fetch_array($registros);
		$pdf->Cell($ancho_modos,5,$registro['cantidad'],1,0,'C');
		$cant_sucursal = $cant_sucursal + $registro['cantidad'];
		$total_modo[$modo['id_modo_acercamiento']] = $total_modo[$modo['id_modo_acercamiento']] + $registro['cantidad'];
	}
	$pdf->Cell($ancho_modos,5,$cant_sucursal,1,0,'C');
	$pdf->Ln();
}


$pdf->SetFont('Arial','BI',8);
$pdf->Cell(40,5,'Total',1,0,'C');
$total_gral = 0;
	$SQL="SELECT * FROM recepcion_modo_acercamiento";
	$modos = mysqli_query($con, $SQL);
	while ($modo = mysqli_fetch_array($modos)) {
		$pdf->Cell($ancho_modos,5,$total_modo[$modo['id_modo_acercamiento']],1,0,'C');
		$total_gral = $total_gral + $total_modo[$modo['id_modo_acercamiento']];
	}

$pdf->Cell($ancho_modos,5,$total_gral,1,0,'C');
$pdf->Ln();




$pdf->Output('Reporte_Recepción_No_Compra','I');
$pdf->close();




 ?>