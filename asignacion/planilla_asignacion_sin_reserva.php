﻿
<?php

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
			$this->Cell(150,5,('PLANILLA DE ASIGNACION - SIN RESERVA'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
		$this->SetFont('Arial','I',7);
		$this->SetFont('');
		$this->Cell(0,5,('Pag.').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();
		$this->Cell(9,5,'Nro Un.',0,0,'C');
		$this->Cell(15,5,'Mes',0,0,'C');
		$this->Cell(10,5,('Ano'),0,0,'C');
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

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(true,5);

$SQL="SELECT * FROM grupos WHERE activo = 1 AND cerokilometro = 1 AND posicion>0 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL);

	while ($grupo=mysqli_fetch_array($grupos)) {
		$iteracion = 0;
		$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo=".$grupo['idgrupo']." ORDER BY posicion" ;
		$modelos=mysqli_query($con, $SQL);

		while ($modelo=mysqli_fetch_array($modelos)) {

			$iteracion ++;

			$SQL="SELECT * FROM asignaciones WHERE reserva=0 AND borrar=0 AND entregada = 0 AND id_modelo = ". $modelo['idmodelo'] ." ORDER BY año, id_mes, nro_orden, nro_unidad";
			$unidades = mysqli_query($con, $SQL);

			$cant=mysqli_num_rows($unidades);

			if ($iteracion == 1 and $cant > 0 ) {
				$pdf->Ln(4);
				$pdf->SetFont('Arial','B',10);
				$pdf->SetFont('');
				$pdf->Cell(0,5,$grupo_a[$grupo['idgrupo']]['grupo'],1,1,'C');
				$pdf->Ln(2);
			}

			$pdf->SetFont('Arial','B',8);
			$pdf->SetFont('');

			if ($cant>0) {
				$pdf->Ln(2);
				$pdf->Cell(0,5,$modelo_a[$modelo['idmodelo']]['modelo'],0,1,'C');
				$pdf->Ln(2);
			}
			$pdf->SetFont('Arial','B',6.5);
			$pdf->SetFont('');


			while ($unidad=mysqli_fetch_array($unidades)) {

				if ($unidad['no_disponible'] == 1) {
					$disponible = "// No Disponible //";
				}else{
					$disponible = "";
				}

				if ($unidad['reservada']==1 AND $unidad['estado_reserva']==0 ) {
					$pdf->SetFont('Arial','BI',6.5);
				}else{
					$pdf->SetFont('Arial','B',6.5);
					$pdf->SetFont('');
				}
				$dias = '';


				if ($unidad['fec_arribo']<>'') {
					$dias = ((strtotime($unidad['fec_arribo'])-strtotime(date("Y/m/d"))))/86400;
					$dias = abs($dias);
					$dias = floor($dias);
				}else{
					$dias = '-';
				}


				$pdf->Cell(9,5,($unidad['nro_unidad']),1,0,'C');
				$pdf->Cell(15,5,($mes_a[$unidad['id_mes']]['mes']),1,0,'C');
				$pdf->Cell(10,5,($unidad['año']),1,0,'C');
				$pdf->Cell(18,5,($unidad['nro_orden']),1,0,'C');
				$pdf->Cell(10,5,($unidad['interno']),1,0,'C');
				$pdf->Cell(12,5,cambiarFormatoFecha($unidad['fec_despacho']),1,0,'C');
				$pdf->Cell(12,5,cambiarFormatoFecha($unidad['fec_arribo']),1,0,'C');
				$pdf->Cell(45,5,($grupo_a[$unidad['id_grupo']]['grupo']." ".$modelo_a[$unidad['id_modelo']]['modelo']),1,0,'C');
				$pdf->Cell(13,5,($unidad['chasis']),1,0,'C');

				$pdf->Cell(35,5,($disponible." ".$color_a[$unidad['color_uno']]['color']." - ".$color_a[$unidad['color_dos']]['color']." - ".$color_a[$unidad['color_tres']]['color']),1,0,'C');
				$pdf->Cell(12,5,($color_a[$unidad['id_color']]['color']),1,0,'C');

				if ($unidad['fec_arribo']!='' AND $unidad['fec_arribo']!=null) {
					$pdf->Cell(9,5,($sucursal_a[$unidad['id_ubicacion']]['sucres']),1,0,'C');
				 }else{
					// $pdf->Cell(9,5,($sucursal_a[$unidad['id_sucursal']]['sucres']),1,0,'C');
					$pdf->Cell(9,5,'-',1,0,'C');
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
				$pdf->Cell(9,5,$dias,1,0,'C');
				$pdf->Cell(31,5,($disponible.$unidad['cliente']),1,0,'L');
				$pdf->Cell(17,5,($usuario_a[$unidad['id_asesor']]['nombre']),1,0,'C');
				$pdf->Cell(12,5,cambiarFormatoFecha($unidad['fec_reserva']),1,0,'C');
				$pdf->Ln();
			}

		}
	}

$pdf->Output('PlanilaAsignacion_'.cambiarFormatoFecha(date('Y-m-d')).'Hs'. strftime("%H:%M").'.pdf','I');
$pdf->close();

?>