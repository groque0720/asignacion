<?php
require('fpdf/fpdf.php');
// require('fpdf.php');

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	//cargo en un arreglo todos los meses que ocuparia en la tabla.
		$SQL="SELECT * FROM meses";
		$meses=mysqli_query($con, $SQL);
		$i=1;
		while ($mes=mysqli_fetch_array($meses)) {
			$mes_a[$i]['mes']= $mes['mes'];
			$i++;
		}
	//fin de carga de meses.
	//
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
	//
	//	cargo los destinos de unidad
	$SQL="SELECT * FROM sucursales";
	$sucursales=mysqli_query($con, $SQL);
	$sucursal_a[0]['sucres']= '-';
	$i=1;
	while ($sucursal=mysqli_fetch_array($sucursales)) {
		$sucursal_a[$i]['sucres']= $sucursal['sucres'];
		$i++;
	}
	//fin de carga de sucursales
	//
	//
	$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
	$usuarios=mysqli_query($con, $SQL);
	$usuario_a[1]['nombre']= '-';
	$i=1;
	while ($usuario=mysqli_fetch_array($usuarios)) {
		$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
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
		$this->SetFont('Arial','B',10);
		$this->Cell(60,5,'DERKA Y VARGAS S. A.',0,0,'L');
		$this->Cell(150,5,utf8_decode('PLANILLA DE ASIGNACIÓN'),0,0,'C');
		$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
		$this->Ln();
		$this->Cell(0,0,'',1,0,'C');
		$this->Ln();
	}
$this->SetFont('Arial','I',7);
$this->SetFont('');
$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
$this->Ln();
$this->Cell(9,5,'Nro Un.',0,0,'C');
$this->Cell(15,5,'Mes',0,0,'C');
$this->Cell(10,5,utf8_decode('Año'),0,0,'C');
$this->Cell(18,5,'Nro Orden',0,0,'C');
$this->Cell(10,5,'Interno',0,0,'C');
$this->Cell(12,5,'Despacho',0,0,'C');
$this->Cell(12,5,'Arribo',0,0,'C');
$this->Cell(45,5,'Modelo',0,0,'C');
$this->Cell(13,5,'Chasis',0,0,'C');
$this->Cell(35,5,'Colores Pedidos',0,0,'C');
$this->Cell(12,5,'Asignado',0,0,'C');
$this->Cell(9,5,'D./Ub.',0,0,'C');
$this->Cell(9,5,'Canc.',0,0,'C');
$this->Cell(9,5,'Ant.',0,0,'C');
$this->Cell(31,5,'Cliente',0,0,'C');
$this->Cell(17,5,'Asesor',0,0,'C');
$this->Cell(12,5,'Reserva',0,0,'C');
$this->Ln();
$this->Cell(0,0,'',1,0,'C');
$this->Ln(3);
}

// Pie de página
// function Footer()
// {
//     // Posición: a 1,5 cm del final
//     $this->SetY(-15);
//     // Arial italic 8
//     $this->SetFont('Arial','I',8);
//     // Número de página
//     $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
// }
}


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'¡Hola, Mundo!');
$pdf->Output();
?>