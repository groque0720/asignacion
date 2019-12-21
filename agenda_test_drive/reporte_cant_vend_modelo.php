<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
require('fpdf/fpdf.php');

class PDF extends FPDF
{
	// Cabecera de página
	function Header()
	{
		global $modelos;
		global $ancho;

		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',8);
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'C');
			$this->Cell(100,5,utf8_decode('REPORTE DE TEST DRIVE)'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}

		$this->SetFont('Arial','I',6.5);
		$this->SetFont('');
		$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();

		$this->Cell(37,5,'Sucursal / Vendedor',1,0,'C');



		while ($modelo=mysqli_fetch_array($modelos)) {
			$this->Cell($ancho,5,utf8_decode($modelo['modelo']),1,0,'C');
		}


		//$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes
		


		$this->Ln();

		$this->Ln(3);
	}
}

$SQL="SELECT * FROM modelos_test_drive";
$modelos = mysqli_query($con, $SQL);
$cant_modelos = mysqli_num_rows($modelos);
$ancho = 153 / $cant_modelos;


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
// $pdf->SetAutoPageBreak(auto,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');

$pdf->Output('Stock.pdf','I',1);
$pdf->close();

 ?>

