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

$p=$_SESSION["idperfil"];

//cargo en arreglo los colores de la tabla
	$SQL="SELECT * FROM asignaciones_usados_colores ORDER BY color";
	$colores=mysqli_query($con, $SQL);
	$color_a[0]['color']= '-';
	$i=1;
	while ($color=mysqli_fetch_array($colores)) {
		$color_a[$color['id_color']]['color']= $color['color'];
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
	$por_a[]['grupo_res']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$por_a[$grupo['idgrupo']]['grupo_res']= $grupo['grupo_res'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_marcas";
	$usados_marcas=mysqli_query($con, $SQL);
	$marca_a[]['grupo']= '-';
	$i=1;
	while ($marca=mysqli_fetch_array($usados_marcas)) {
		$marca_a[$marca['id_marca']]['marca']= $marca['marca'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_modelos";
	$usados_modelos=mysqli_query($con, $SQL);
	$modelo_a[]['modelo']= '-';
	$i=1;
	while ($modelo=mysqli_fetch_array($usados_modelos)) {
		$modelo_a[$modelo['id_modelo']]['modelo']= $modelo['modelo'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_versiones";
	$usados_versiones=mysqli_query($con, $SQL);
	$version_a[]['grupo']= '-';
	$i=1;
	while ($version=mysqli_fetch_array($usados_versiones)) {
		$version_a[$version['id_version']]['version']= $version['version'];
		$i++;
	}


class PDF extends FPDF
{
	// Cabecera de página
	function Header()
	{
		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',10);
			$this->Cell(100,5,'DERKA Y VARGAS S. A.',0,0,'L');
			$this->Cell(100,5,utf8_decode('PLANILLA DE UNIDADES USADAS PENDIENTE DE CANCELACION'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
	$this->SetFont('Arial','I',7);
	$this->SetFont('');
	$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
	$this->Ln();


	$this->Cell(10,5,'`Nro. Rva.',0,0,'C');
	$this->Cell(10,5,'Interno',0,0,'C');
	$this->Cell(52,5,utf8_decode('Marca - Modelo - Versión'),0,0,'C');
	$this->Cell(10,5,utf8_decode('Año'),0,0,'C');
	$this->Cell(20,5,'Dominio',0,0,'C');
	$this->Cell(25,5,'Color',0,0,'C');
	$this->Cell(49,5,'Cliente',0,0,'C');
	$this->Cell(25,5,'Asesor',0,0,'C');	
	$this->Cell(15,5,utf8_decode('Fec. Rva.'),0,0,'C');	
	$this->Cell(20,5,utf8_decode('Monto Operación'),0,0,'C');
	$this->Cell(20,5,'Pagado',0,0,'C');
	$this->Cell(20,5,'Saldo',0,0,'C');


	$this->Ln();
	$this->Cell(0,0,'',1,0,'C');
	$this->Ln(3);
	}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(1,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');

$total_gral_toma=0;
$total_gral_costo=0;
$total_gral_p_venta=0;
$total_gral_p_info=0;

$SQL="SELECT * FROM usados_sin_cancelar";
$usados = mysqli_query($con, $SQL);


while ($usado=mysqli_fetch_array($usados)) {

	// if ($usado['reservada']==1) {
	// 	$pdf->SetFont('Arial','BI',6.5);
	// }else{
	// 	$pdf->SetFont('Arial','B',6.5);
	// 	$pdf->SetFont(''); 
	// }

	$pdf->Cell(10,5,$usado['id_reserva'],1,0,'C');
	$pdf->Cell(10,5,$usado['interno_usado'],1,0,'C');
	$pdf->Cell(52,5,strtoupper($usado['detalleu']),1,0,'L');
	$pdf->Cell(10,5,$usado['anou'],1,0,'C');
	$pdf->Cell(20,5,strtoupper($usado['dominiou']),1,0,'C');
	$pdf->Cell(25,5,strtoupper($usado['coloru']),1,0,'C');
	$pdf->Cell(49,5,strtoupper(utf8_decode($usado['cliente'])),1,0,'L');
	$pdf->Cell(25,5,strtoupper(utf8_decode($usado['asesor'])),1,0,'L');
	$pdf->Cell(15,5,cambiarFormatoFecha($usado['fecha']),1,0,'C');

	$SQL="SELECT SUM(monto) AS monto FROM lineas_detalle WHERE idreserva = ".$usado['id_reserva'];
	$montos=mysqli_query($con, $SQL);
	$monto = mysqli_fetch_array($montos);

	$pdf->Cell(20,5,'$ '.number_format($monto['monto'], 2, ',','.'),1,0,'R');

	$SQL="SELECT SUM(monto) AS monto FROM pagos_lineas WHERE idreserva = ".$usado['id_reserva'];
	$pagos=mysqli_query($con, $SQL);
	$pago = mysqli_fetch_array($pagos);

	$pdf->Cell(20,5,'$ '.number_format($pago['monto'], 2, ',','.'),1,0,'R');

	$pdf->Cell(20,5,'$ '.number_format($monto['monto'] - $pago['monto'], 2, ',','.'),1,0,'R');

	$pdf->Ln();

}


$pdf->Ln();

$pdf->Output('usados_sin_cancelar.pdf','I');
$pdf->close();

?>