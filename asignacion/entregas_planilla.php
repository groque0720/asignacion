
<?php

require('fpdf/fpdf.php');

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

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

	$SQL="SELECT * FROM entregas_ubicaciones WHERE activo = 1";
	$ubicaciones=mysqli_query($con, $SQL);
	$ubicaciones_a[0]['ubicacion_entrega']= '-';
	$i=1;
	while ($ubicacion=mysqli_fetch_array($ubicaciones)) {
		$ubicaciones_a[$ubicacion['id_ubicacion_entrega']]['ubicacion_entrega']= $ubicacion['ubicacion_entrega'];
		$i++;
	}

	$SQL="SELECT * FROM entregas_estados_unidad WHERE activo = 1";
	$estados=mysqli_query($con, $SQL);
	$estados_a[0]['estado_unidad']= '-';
	$i=1;
	while ($estado=mysqli_fetch_array($estados)) {
		$estados_a[$estado['id_estado_entrega']]['estado_unidad']= $estado['estado_unidad'];
		$estados_a[$estado['id_estado_entrega']]['color']= $estado['color'];
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
		$this->Cell(150,5,utf8_decode('UNIDADES - SECTOR ENTREGAS'),0,0,'C');
		$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
		$this->Ln();
		$this->Cell(0,0,'',1,0,'C');
		$this->Ln();
	}
$this->SetFont('Arial','I',7);
$this->SetFont('');
$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
$this->Ln();
$this->Cell(8,5,'Nro.',0,0,'C');
$this->Cell(10,5,'Interno',0,0,'C');
$this->Cell(19,5,'Nro Orden',0,0,'C');
$this->Cell(15,5,'Chasis',0,0,'C');
$this->Cell(47,5,'Modelo',0,0,'C');
$this->Cell(13,5,'Llego',0,0,'C');
$this->Cell(15,5,'Color',0,0,'C');
$this->Cell(14,5,'Ubic.',0,0,'C');
$this->Cell(7,5,'Canc.',0,0,'C');
$this->Cell(30,5,'Cliente',0,0,'C');
$this->Cell(17,5,'Asesor',0,0,'C');
$this->Cell(13,5,'Pedido',0,0,'C');
$this->Cell(9,5,'Hora',0,0,'C');
$this->Cell(20,5,'Estado',0,0,'C');

$this->Cell(40,5,utf8_decode('Observación'),0,0,'C');
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

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(auto,5);
$unidades = mysqli_query($con, $_POST['sql']);

$fila=0;
while ( $unidad=mysqli_fetch_array($unidades)) { $fila++; $libre = '';

$pdf->Cell(8,5,$fila,1,0,'C');
$pdf->Cell(10,5,$unidad['interno'],1,0,'C');
$pdf->Cell(19,5,trim($unidad['nro_orden']),1,0,'C');
$pdf->Cell(15,5,$unidad['chasis'],1,0,'C');
$pdf->Cell(47,5,$grupo_a[$unidad['id_grupo']]['grupo']." ".$modelo_a[$unidad['id_modelo']]['modelo'],1,0,'L');
$pdf->Cell(13,5,cambiarFormatoFecha($unidad['fec_arribo']),1,0,'C');
$pdf->Cell(15,5,utf8_decode($color_a[$unidad['id_color']]['color']),1,0,'C');
$pdf->Cell(14,5,utf8_decode($ubicaciones_a[$unidad['id_ubicacion_entrega']]['ubicacion_entrega']),1,0,'C');

	if ($unidad['cancelada']==1) { $resp='Si';}else{$resp= '-';}

$pdf->Cell(7,5,$resp,1,0,'C');

	$largo_cliente=strlen ($unidad['cliente']);
	if ($largo_cliente>=22) {
		$cliente = substr($unidad['cliente'], 0, 22)."-*-";
	}else{
		$cliente =$unidad['cliente'];
	}

$pdf->Cell(30,5,utf8_decode($cliente),1,0,'L');
$pdf->Cell(17,5,utf8_decode($usuario_a[$unidad['id_asesor']]['nombre']),1,0,'C');
$pdf->Cell(13,5,cambiarFormatoFecha($unidad['fec_pedido']),1,0,'C');
$pdf->Cell(9,5,cambiarFormatohora($unidad['hora_pedido']),1,0,'C');

	$largo_estado=strlen ($estados_a[$unidad['id_estado_entrega']]['estado_unidad']);
	if ($largo_estado>=15) {
		$estado = substr($estados_a[$unidad['id_estado_entrega']]['estado_unidad'], 0, 10)." -*-";
	}else{
		$estado =$estados_a[$unidad['id_estado_entrega']]['estado_unidad'];
	}

$pdf->Cell(20,5,utf8_decode($estado),1,0,'C');

	$largo_obs=strlen ($unidad['observacion']);
	if ($largo_obs>=27) {
		$observacion = substr($unidad['observacion'], 0, 24)." -*-";
	}else{
		$observacion =$unidad['observacion'];
	}


$pdf->Cell(40,5,utf8_decode($observacion),1,0,'L');
$pdf->Ln();
}

$pdf->Output('Entregas_unidades'.cambiarFormatoFecha(date('Y-m-d')).'Hs'. strftime("%H:%M").'.pdf','I');
$pdf->close();

?>