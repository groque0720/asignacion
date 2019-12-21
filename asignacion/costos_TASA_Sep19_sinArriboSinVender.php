
<?php
set_time_limit(300);
require('fpdf/fpdf.php');

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
		$this->Cell(150,5,utf8_decode('COSTOS UNIDADES TASA SEPTIEMBRE 2019 - SIN ARRIBO SIN VENDER'),0,0,'C');
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
$this->Cell(19,5,'Nro Orden',0,0,'C');
$this->Cell(15,5,'Fec. Playa',0,0,'C');
$this->Cell(15,5,'Despacho',0,0,'C');
$this->Cell(47,5,'Modelo',0,0,'C');
$this->Cell(27,5,'Costo',0,0,'C');
$this->Cell(27,5,'Recurso',0,0,'C');
$this->Cell(15,5,'Asignado',0,0,'C');
$this->Cell(9,5,'D./Ub.',0,0,'C');
$this->Cell(9,5,'C./Pat.',0,0,'C');
$this->Cell(36,5,'Cliente',0,0,'C');
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

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(auto,5);
			

	$SQL="SELECT * FROM view_asignaciones_costos_pagadas_sep_nolleg_sin_vender";
	$unidades = mysqli_query($con, $SQL);

	$pdf->SetFont('Arial','B',8);
	$pdf->SetFont('');

	if ($cant>0) {
		$pdf->Cell(0,5,$modelo_a[$modelo['idmodelo']]['modelo'],0,1,'C');
		$pdf->Ln(2);
	}
	$pdf->SetFont('Arial','B',6.5);
	$pdf->SetFont('');


	$cant_unidades=0;
	$fec_anterior='';
	$acum=0;
	$parcial=0;
	$acum_r=0;
	$parcial_r=0;
	while ($unidad=mysqli_fetch_array($unidades)) { 

		$cant_unidades++;

		$pdf->SetFont('Arial','B',8);
		$pdf->SetFont('');

		if ($fec_anterior!=$unidad['fec_playa']) {

			if ($cant_unidades!=1) {
				$acum=$acum+$parcial;
				$pdf->Cell(130,5,'Total Parcial',0,0,'R');
				$pdf->Cell(27,5,'$ '.number_format($parcial, 2, ',','.'),1,0,'R');
				$pdf->Cell(27,5,'$ '.number_format($parcial_r, 2, ',','.'),1,0,'R');
				$pdf->Ln();
				$pdf->Cell(130,5,'Acumulado ',0,0,'R');
				$pdf->Cell(27,5,'$ '.number_format($acum, 2, ',','.'),1,0,'R');
				$pdf->Cell(27,5,'$ '.number_format($acum_r, 2, ',','.'),1,0,'R');
				$pdf->Ln();
				$parcial=0;
				$parcial_r=0;
			}

			$pdf->Cell(0,5,cambiarFormatoFecha($unidad['fec_playa']),0,1,'L');
			$fec_anterior=$unidad['fec_playa'];
		}

		$pdf->SetFont('Arial','B',6.5);
		$pdf->SetFont('');


		if ($unidad['reservada']==1 AND $unidad['estado_reserva']==0 ) {
			$pdf->SetFont('Arial','BI',6.5);
		}else{
			$pdf->SetFont('Arial','B',6.5);
			$pdf->SetFont('');
		}

		$pdf->Cell(9,5,utf8_decode($unidad['nro_unidad']),1,0,'C');
		$pdf->Cell(15,5,utf8_decode($mes_a[$unidad['id_mes']]['mes']),1,0,'C');
		$pdf->Cell(10,5,utf8_decode($unidad['año']),1,0,'C');
		$pdf->Cell(19,5,utf8_decode($unidad['nro_orden']),1,0,'C');
		$pdf->Cell(15,5,cambiarFormatoFecha($unidad['fec_playa']),1,0,'C');
		$pdf->Cell(15,5,cambiarFormatoFecha($unidad['fec_despacho']),1,0,'C');
		$pdf->Cell(47,5,utf8_decode($grupo_a[$unidad['id_grupo']]['grupo']." ".$modelo_a[$unidad['id_modelo']]['modelo']),1,0,'C');
		$pdf->Cell(27,5,'$ '.number_format($unidad['costo'], 2, ',','.'),1,0,'R');

		$SQL="SELECT * FROM reservas_suma_montos WHERE nrounidad = ".$unidad['nro_unidad'];
		$montos=mysqli_query($con, $SQL);
		
		$cant_monto=mysqli_num_rows($montos)+1;

		if ($cant_monto>0) {
			$monto=mysqli_fetch_array($montos);
			$monto_res=(int)$monto['monto'];
		}else{
			$monto_res=0;
		}

		$SQL="SELECT * FROM reservas_suma_pagos WHERE nrounidad = ".$unidad['nro_unidad'];
		$montos=mysqli_query($con, $SQL);
		
		$cant_monto=mysqli_num_rows($montos);

		if ($cant_monto>0) {
			$monto=mysqli_fetch_array($montos);
			$monto_pago=(int)$monto['pagos'];
		}else{
			$monto_pago=0;
		}		

		$parcial_r=$parcial_r+($monto_res-$monto_pago);
		$acum_r=$acum_r+($monto_res-$monto_pago);

		//recurso
		$pdf->Cell(27,5,'$ '.number_format(($monto_res-$monto_pago), 2, ',','.'),1,0,'R');
		$pdf->Cell(15,5,utf8_decode($color_a[$unidad['id_color']]['color']),1,0,'C');

		if ($unidad['fec_arribo']!='' AND $unidad['fec_arribo']!=null) {
			$pdf->Cell(9,5,utf8_decode($sucursal_a[$unidad['id_ubicacion']]['sucres']),1,0,'C');
		 }else{
			$pdf->Cell(9,5,utf8_decode($sucursal_a[$unidad['id_sucursal']]['sucres']),1,0,'C');
		}
		//resalto la fuente de cancelación - Pedido Don Vargas
		$pdf->SetFont('Arial','B',7.5);
		
		if ($unidad['cancelada']==1) { $can= 'Si';}else{$can= '';}
		if ($unidad['patentada']==1) { $pat= '/Si';}else{$pat= '';}

		$pdf->Cell(9,5,$can."".$pat,1,0,'C');

		//retorno a la fuente original de la primera fila
		if ($unidad['reservada']==1 AND $unidad['estado_reserva']==0 ) {
			$pdf->SetFont('Arial','BI',6.5);
		}else{
			$pdf->SetFont('Arial','B',6.5);
			$pdf->SetFont('');
		}
		$pdf->Cell(36,5,utf8_decode($unidad['cliente']),1,0,'L');
		$pdf->Cell(17,5,utf8_decode($usuario_a[$unidad['id_asesor']]['nombre']),1,0,'C');
		$pdf->Cell(12,5,cambiarFormatoFecha($unidad['fec_reserva']),1,0,'C');
		$pdf->Ln();

		$parcial=$parcial+$unidad['costo'];
	}

	$acum=$acum+$parcial;

	$pdf->SetFont('Arial','B',8);
	$pdf->SetFont('');

	$pdf->Cell(130,5,'Total Parcial',0,0,'R');
	$pdf->Cell(27,5,number_format($parcial, 2, ',','.'),1,0,'R');
	$pdf->Cell(27,5,number_format($parcial_r, 2, ',','.'),1,0,'R');

	$pdf->Ln();
	$pdf->Cell(130,5,'Acumulado ',0,0,'R');
	$pdf->Cell(27,5,number_format($acum, 2, ',','.'),1,0,'R');
	$pdf->Cell(27,5,number_format($acum_r, 2, ',','.'),1,0,'R');
	
	$pdf->Ln(2);

$pdf->Output('Costos_TASA_'.cambiarFormatoFecha(date('Y-m-d')).'Hs'. strftime("%H:%M").'.pdf','I');
$pdf->close();

?>