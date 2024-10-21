<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
require('fpdf/fpdf.php');
@session_start();
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../login");
	//ademas salgo de este script
	exit();
}

$sucursalId = isset($_GET['sucursalId']) ? $_GET['sucursalId'] : null;
$sucursal = 'Derka y Vargas S. A.';

if ($sucursalId) {
	$SQL="SELECT * FROM sucursales WHERE idsucursal = $sucursalId";
	$sucursales = mysqli_query($con, $SQL);
	$sucursal = mysqli_fetch_array($sucursales)['sucursal'];
} 

// $p=$_SESSION["idperfil"];

class PDF extends FPDF
{
	private $sucursal;

    // Constructor para recibir el valor de la sucursal
    function __construct($sucursal) {
        parent::__construct();
        $this->sucursal = $sucursal; // Asignar el valor de la sucursal a una propiedad de la clase
    }
	// Cabecera de página
	function Header()
	{
		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',10);
			$this->Cell(100,5,'DERKA Y VARGAS S. A.',0,0,'L');
			$this->Cell(90,5,('COSTOS Y RECURSOS - '. strtoupper( $this->sucursal )),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
	$this->SetFont('Arial','I',7);
	$this->SetFont('');
	$this->Cell(0,5,('Pag.').$this->PageNo().'/{nb}',0,0,'R');
	$this->Ln();

	$this->Cell(13,5,'Mes.',0,0,'C');
	$this->Cell(10,5,utf8_decode('Año'),0,0,'C');
	$this->Cell(35,5,('Modelo - Version'),0,0,'C');
	$this->Cell(16,5,'Nro Orden',0,0,'C');
	$this->Cell(10,5,'Interno',0,0,'C');
	// $this->Cell(16,5,('Chasis'),0,0,'C');
	// $this->Cell(15,5,('Tomo'),0,0,'C');
	$this->Cell(30,5,('Cliente'),0,0,'C');
	$this->Cell(15,5,('Asesor'),0,0,'C');
	$this->Cell(18,5,'Sucursal',0,0,'C');
	$this->Cell(15,5,'Reserva',0,0,'C');
	$this->Cell(15,5,'Arribo',0,0,'C');
	$this->Cell(15,5,'Despacho.',0,0,'C');
	// $this->Cell(15,5,'Canc.',0,0,'C');
	$this->Cell(5,5,'$T',0,0,'C');
	$this->Cell(20,5,'Costo TASA',0,0,'C');
	$this->Cell(20,5,'Total Reserva',0,0,'C');
	$this->Cell(20,5,'Total Pagos',0,0,'C');
	$this->Cell(20,5,'Saldo',0,0,'C');

	$this->Ln();
	$this->Cell(0,0,'',1,0,'C');
	$this->Ln(3);
	}
}

$pdf = new PDF($sucursal);
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(1,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');



$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida";
if (isset($_GET['sucursalId'])) {
	$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida WHERE idsucursal = $sucursalId";
}
$unidades = mysqli_query($con, $SQL);

$nro = 0;

$total_costo_tasa = 0;
$total_reservas=0;
$total_pagos=0;
$saldo=0;

while ($unidad=mysqli_fetch_array($unidades)) {

	$pdf->Cell(13,5,($unidad['Mes']),1,0,'C');
	$pdf->Cell(10,5,($unidad['Año']),1,0,'C');

	// --- Model Version
	$largo=strlen($unidad['Modelo'].' '.$unidad['Versión']);
	$modelo_version=$unidad['Modelo'].' '.$unidad['Versión'];
	if ($largo>25) {
		$cortar=$largo-25;
		$modelo_version=substr($unidad['Modelo'].' '.$unidad['Versión'], 0, -$cortar).'..';
	}
	$pdf->Cell(35,5,($modelo_version),1,0,'L');
	$pdf->Cell(16,5,($unidad['NroOrden']),1,0,'C');
	$pdf->Cell(10,5,($unidad['Interno']),1,0,'C');
	// $pdf->Cell(16,5,($unidad['Chasis']),1,0,'C');

	// --- Cliente
	$largo=strlen($unidad['Cliente']);
	$cliente=$unidad['Cliente'];
	if ($largo>20) {
		$cortar=$largo-20;
		$cliente=substr($unidad['Cliente'], 0, -$cortar).'..';
	}
	$pdf->Cell(30,5,($cliente),1,0,'L');

	// --- Asesor
	$largo=strlen($unidad['Asesor']);
	$asesor=$unidad['Asesor'];
	if ($largo>10) {
		$cortar=$largo-10;
		$asesor=substr($unidad['Asesor'], 0, -$cortar).'..';
	}
	$pdf->Cell(15,5,($asesor),1,0,'L');
	$pdf->Cell(18,5,(utf8_decode($unidad['Sucursal'])),1,0,'C');
	$pdf->Cell(15,5,(cambiarFormatoFecha($unidad['Reserva'])),1,0,'C');
	$pdf->Cell(15,5,(cambiarFormatoFecha($unidad['Arribo'])),1,0,'C');
	$pdf->Cell(15,5,(cambiarFormatoFecha($unidad['Despacho'])),1,0,'C');

	$pdf->Cell(5,5,$unidad['Pagado TASA']?'Si':'-',1,0,'C');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Costo TASA'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Operacion'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Pagos'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Saldo'], 0, ',','.'),1,0,'R');




	$pdf->Ln();
}
// $pdf->Ln();
// $pdf->SetFont('Arial','BI',8);
// $pdf->Cell(177,5,'Total General   ',0,0,'R');
// $pdf->SetFont('Arial','B',6.5);
// if ($es_gerente==1) {
// 	$pdf->Cell(16,5,'$ '.number_format($total_gral_toma, 0, ',','.'),1,0,'R');
// 	$pdf->Cell(16,5,'$ '.number_format($total_gral_costo, 0, ',','.'),1,0,'R');
// 	$pdf->Cell(16,5,'$ '.number_format($total_gral_costo_rep, 0, ',','.'),1,0,'R');
// }else{
// 	$pdf->Cell(16,5,'$ -',1,0,'R');
// 	$pdf->Cell(16,5,'$ -',1,0,'R');
// 	$pdf->Cell(16,5,'$ -',1,0,'R');
// }
// $pdf->Cell(16,5,'$ '.number_format($total_gral_transferencia, 0, ',','.'),1,0,'R');
// $pdf->Cell(16,5,'$ '.number_format($total_gral_p_venta, 0, ',','.'),1,0,'R');
// $pdf->Cell(16,5,'$ '.number_format($total_gral_p_info, 0, ',','.'),1,0,'R');

$pdf->Ln();

$pdf->Output('Stock.pdf','I');
$pdf->close();
?>