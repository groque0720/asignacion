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

$sucursalId = isset($_GET['sucursalId']) ? intval($_GET['sucursalId']) : null;
$sucursal = 'Derka y Vargas S. A.';

$SQL = "SELECT * FROM sucursales WHERE idsucursal = " . intval($sucursalId);
$sucursales = mysqli_query($con, $SQL);

if ($sucursalId !== null) {

    $SQL = "SELECT sucursal FROM sucursales WHERE idsucursal = $sucursalId";
    $resultado = mysqli_query($con, $SQL);

    if (!$resultado) {
        die("Error SQL sucursal: " . mysqli_error($con));
    }

    $fila = mysqli_fetch_assoc($resultado);

    if ($fila) {
        $sucursal = $fila['sucursal'];
    }
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
			// $this->Cell(90,5,('COSTOS Y RECURSOS - '. strtoupper( $this->sucursal )),0,0,'C');
			$titulo = 'CON ARRIBO - ' . strtoupper($this->sucursal);
			$titulo = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $titulo);
			$this->Cell(90,5,$titulo,0,0,'C');
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



$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida WHERE Arribo IS NOT NULL";
if (isset($_GET['sucursalId'])) {
	$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida WHERE idsucursal = $sucursalId AND Arribo IS NOT NULL";
}
$unidades = mysqli_query($con, $SQL);

if (!$unidades) {
    die("Error SQL unidades: " . mysqli_error($con));
}

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

	$pdf->Cell(5,5,$unidad['pagado_tasa']?'Si':'-',1,0,'C');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Costo TASA'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Operacion'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Pagos'], 0, ',','.'),1,0,'R');
	$pdf->Cell(20,5,'$ '.number_format($unidad['Saldo'], 0, ',','.'),1,0,'R');

	$total_costo_tasa += $unidad['Costo TASA'];
	$total_reservas  += $unidad['Operacion'];
	$total_pagos     += $unidad['Pagos'];
	$saldo           += $unidad['Saldo'];


	$pdf->Ln();
}

$pdf->Cell(0,0,'',1,0,'C');
$pdf->Ln(2);

$pdf->Cell(13,5,'',0,0,'C');
$pdf->Cell(10,5,'',0,0,'C');
$pdf->Cell(35,5,'',0,0,'L');
$pdf->Cell(16,5,'',0,0,'C');
$pdf->Cell(10,5,'',0,0,'C');
$pdf->Cell(30,5,'',0,0,'L');
$pdf->Cell(15,5,'',0,0,'L');
$pdf->Cell(18,5,'',0,0,'C');
$pdf->Cell(15,5,'',0,0,'C');
$pdf->Cell(15,5,'',0,0,'C');
$pdf->Cell(15,5,'Total',1,0,'C');
$pdf->Cell(5,5,'',1,0,'C');
$pdf->Cell(20,5,'$ '.number_format($total_costo_tasa, 0, ',','.'),1,0,'R');
$pdf->Cell(20,5,'$ '.number_format($total_reservas, 0, ',','.'),1,0,'R');		
$pdf->Cell(20,5,'$ '.number_format($total_pagos, 0, ',','.'),1,0,'R');
$pdf->Cell(20,5,'$ '.number_format($saldo, 0, ',','.'),1,0,'R');	

$pdf->Ln();

$pdf->Output('Con_Arribo.pdf','I');
$pdf->close();
?>