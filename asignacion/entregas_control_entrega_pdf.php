<?php

require('fpdf/fpdf.php');

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);


	//cargo en arreglo los colores de la tabla
	$SQL="SELECT * FROM colores ORDER BY color";
	$colores=mysqli_query($con, $SQL);
	$color_a[0]['color']= '-';
	$i=1;
	while ($color=mysqli_fetch_array($colores)) {
		$color_a[$color['idcolor']]['color']= $color['color'];
		$i++;
	}
	//fin de carga de colores

	$SQL="SELECT * FROM grupos WHERE activo = 1";
	$grupos=mysqli_query($con, $SQL);
	$grupo_a[]['grupo']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
		$i++;
	}

	$SQL="SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL);
	$modelos_a[]['modelo']= '-';
	$i=1;
	while ($modelo=mysqli_fetch_array($modelos)) {
		$modelo_a[$modelo['idmodelo']]['modelo']= $modelo['modelo'];
		$i++;
	}

class PDF extends FPDF
{
	// Cabecera de página
	function Header()
	{
		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',15);
			// $this->Cell(0,5,'DERKA Y VARGAS S. A.',0,0,'L');
			$this->Image('imagenes/cabecera_entrega.png' , 20 ,10, 170 , 18);
			$this->Ln();
			// $this->Cell(95,12,'',0,0,'C');
			// $this->Cell(95,12,'CONTROL DE SALIDA',0,0,'C');
			$this->Cell(0,19,'',0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
	}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(true,5);
$pdf->Ln(5);
$pdf->SetFont('Arial','BU',15);
$pdf->Cell(0,5,'CONTROL DE SALIDA',0,0,'C');
$pdf->Ln(10);
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,5,'FECHA:',0,0,'R');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,5,cambiarFormatoFecha(date('Y-m-d')),0,0,'L');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,5,'VEHICULO:',0,0,'R');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,5,utf8_decode(strtoupper($grupo_a[$_GET['grupo']]['grupo'].' '.$modelo_a[$_GET['modelo']]['modelo'])),0,0,'L');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,5,'COLOR:',0,0,'R');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,5,utf8_decode(strtoupper($color_a[$_GET['color']]['color'])),0,0,'L');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,5,'CHASIS:',0,0,'R');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,5,utf8_decode($_GET['chasis']),0,0,'L');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,5,'TITULAR:',0,0,'R');
$pdf->SetFont('Arial','',12);
$pdf->Cell(140,5,utf8_decode(strtoupper($_GET['cliente'])),0,0,'L');
$pdf->Ln(30);

$pdf->Cell(20,0,'',0,0,'R');
$pdf->Cell(60,0,'',1,0,'R');
$pdf->Cell(40,0,'',0,0,'R');
$pdf->Cell(60,0,'',1,0,'R');
$pdf->Ln(5);
$pdf->Cell(20,0,'',0,0,'R');
$pdf->Cell(60,0,'FIRMA Y SELLO GERENTE',0,0,'C');
$pdf->Cell(40,0,'',0,0,'R');
$pdf->Cell(60,0,'FIRMA GUARDIA',0,0,'C');

$pdf->Output('Control_Entregas.pdf','I');
$pdf->close();

?>
