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
$es_gerente=$_SESSION["es_gerente"];
$id_usuario = $_SESSION["id"];

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
			$this->Cell(150,5,('PLANILLA DE UNIDADES USADAS'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}
	$this->SetFont('Arial','I',7);
	$this->SetFont('');
	$this->Cell(0,5,('Pag.').$this->PageNo().'/{nb}',0,0,'R');
	$this->Ln();
	$this->Cell(4,5,'Nro.',0,0,'C');
	$this->Cell(7,5,'Un.',0,0,'C');
	$this->Cell(7,5,'Interno',0,0,'C');
	$this->Cell(48,5,('Marca - Modelo - Version'),0,0,'C');
	$this->Cell(5,5,'Por',0,0,'C');
	$this->Cell(7,5,('Ano'),0,0,'C');
	$this->Cell(10,5,'Km',0,0,'C');
	$this->Cell(13,5,'Dominio',0,0,'C');
	$this->Cell(13,5,'Color',0,0,'C');
	$this->Cell(29,5,('Ult. Dueno'),0,0,'C');
	// $this->Cell(15,5,('Tomo'),0,0,'C');
	$this->Cell(13,5,('Fec. Rec.'),0,0,'C');
	$this->Cell(6,5,('Ant.'),0,0,'C');
	$this->Cell(17,5,'Toma+Imp.',0,0,'C');
	$this->Cell(15,5,'Costo Cont.',0,0,'C');
	$this->Cell(15,5,'Costo Rep.',0,0,'C');
	$this->Cell(17,5,'$ Transf.',0,0,'C');
	$this->Cell(15,5,'$ Venta',0,0,'C');
	$this->Cell(15,5,'$ Contado',0,0,'C');
	$this->Cell(15,5,'$ Info',0,0,'C');
	$this->Cell(9,5,'Ubic.',0,0,'C');
	$this->Cell(6,5,'Canc.',0,0,'C');
	$this->Cell(20,5,'Cliente',0,0,'C');
	$this->Cell(17,5,'Asesor',0,0,'C');
	$this->Cell(14,5,('Fec. Rva.'),0,0,'C');
	$this->Ln();
	$this->Cell(0,0,'',1,0,'C');
	$this->Ln(3);
	}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','LEGAL');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(1,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');

$total_gral_toma=0;
$total_gral_costo=0;
$total_gral_p_venta=0;
$total_gral_p_info=0;
$total_gral_transferencia=0;

$SQL="SELECT * FROM asignaciones_usados_estados ORDER BY posicion";
$estado_usado = mysqli_query($con, $SQL);

$nro = 0;

$total_costo_rep = 0;
$total_gral_costo_rep=0;

while ($estado=mysqli_fetch_array($estado_usado)) {

   // usuarios permitidos a ver otros estados de los usados
  $user_permitidos = [1, 2, 11, 16, 27, 36, 41, 45, 46, 47, 49, 56, 72, 89, 94, 103, 106, 124];
   // condicional para mostrar otros estados segun usuarios permitidos.
  if ( $estado['id_estado_usado'] == 1 or in_array($id_usuario,$user_permitidos) ) {

	$total_toma=0;
	$total_costo=0;
	$total_p_venta=0;
	$total_p_info=0;
	$total_transferencia=0;
	$nro = 0;

	$SQL="SELECT *, DATEDIFF(DATE(NOW()),fec_recepcion)as ant FROM asignaciones_usados WHERE entregado = 0 AND id_estado =".$estado['id_estado_usado']." ORDER BY vehiculo";
	$usados=mysqli_query($con, $SQL);
	$cant=mysqli_num_rows($usados);

	if ($cant>0) {

		$pdf->SetFont('Arial','BI',8);

		$pdf->Cell(0,5,($estado['estado_usado']),0,0,'L');
		$pdf->Ln();



		while ($usado=mysqli_fetch_array($usados)) {
			$nro++;

			if ($usado['reservada']==1 AND $usado['estado_reserva']==0) {
				$pdf->SetFont('Arial','BI',6.5);
			}else{
				$pdf->SetFont('Arial','B',6.5);
				$pdf->SetFont('');
			}
			$pdf->Cell(4,5,$nro ,1,0,'C');
			$pdf->Cell(7,5,$usado['nro_unidad'],1,0,'C');
			$pdf->Cell(7,5,$usado['interno'],1,0,'C');
			$largo=strlen($usado['vehiculo']);
			$vehiculo=$usado['vehiculo'];
			if ($largo>39) {
				$cortar=$largo-39;
				$vehiculo=substr($usado['vehiculo'], 0, -$cortar).'[..]';
			}
			$pdf->Cell(49,5,($vehiculo),1,0,'L');
			$pdf->Cell(3,5,$por_a[$usado['por']]['grupo_res'],1,0,'C');
			$pdf->Cell(8,5,$usado['año'],1,0,'C');
			$pdf->Cell(10,5,number_format($usado['km'], 0, ',','.'),1,0,'R');
			$pdf->Cell(13,5,$usado['dominio'],1,0,'C');
			$pdf->Cell(13,5,$color_a[$usado['color']]['color'],1,0,'C');



			$largo=strlen($usado['ultimo_dueño']);
			$ultimo_dueño=$usado['ultimo_dueño'];
			if ($largo>19) {
				$cortar=$largo-19;
				$ultimo_dueño=substr($usado['ultimo_dueño'], 0, -$cortar).'..';
			}
			$pdf->Cell(30,5,($ultimo_dueño),1,0,'L');

			// $asesor_toma=$usuario_a[$usado['asesortoma']]['nombre'];
			// $largo=strlen($usuario_a[$usado['asesortoma']]['nombre']);
			// if ($largo>10) {
			// 	$cortar=$largo-10;
			// 	$asesor_toma=substr($usuario_a[$usado['asesortoma']]['nombre'], 0, -$cortar).'..';
			// }

			// $pdf->Cell(15,5,($asesor_toma),1,0,'L');
			$pdf->Cell(12,5,cambiarFormatoFecha($usado['fec_recepcion']),1,0,'C');

			if ($usado['ant']/30>=1) {
				$antiguedad = number_format(((int)$usado['ant']/30), 0, ',','.');
			}else{
				$antiguedad = '-';
			}

			$pdf->Cell(6,5,$antiguedad,1,0,'C');

			if ($es_gerente==1) {
				$pdf->Cell(16,5,'$ '.number_format($usado['toma_mas_impuesto'], 0, ',','.'),1,0,'R');
				$pdf->Cell(16,5,'$ '.number_format($usado['costo_contable'], 0, ',','.'),1,0,'R');
				$pdf->Cell(16,5,'$ '.number_format($usado['costo_reparacion'], 0, ',','.'),1,0,'R');
				// $pdf->Cell(16,5,'$ '.number_format($usado['precio_venta'], 0, ',','.'),1,0,'R');
			}else{
				$pdf->Cell(16,5,'$ -',1,0,'R');
				$pdf->Cell(16,5,'$ -',1,0,'R');
				$pdf->Cell(16,5,'$ -',1,0,'R');
				// $pdf->Cell(16,5,'$ -',1,0,'R');
			}
			$pdf->Cell(16,5,'$ '.number_format($usado['transferencia'], 0, ',','.'),1,0,'R');
			$pdf->Cell(16,5,'$ '.number_format($usado['precio_venta'], 0, ',','.'),1,0,'R');
			$pdf->Cell(16,5,'$ '.number_format($usado['precio_contado'], 0, ',','.'),1,0,'R');
			$pdf->Cell(16,5,'$ '.number_format($usado['precio_info'], 0, ',','.'),1,0,'R');
			$pdf->Cell(8,5,$sucursal_a[$usado['id_sucursal']]['sucres'],1,0,'C');

			if ($usado['reservada']==1) {
				if ($usado['fecha_cancelacion']==null) { $canc = 'No';}else{ $canc = 'Si';}
			}else{
				$canc = '-';
			}
			$pdf->Cell(6,5,$canc,1,0,'C');

			$largo=strlen($usado['cliente']);
			$cliente=$usado['cliente'];
			if ($largo>15) {
				$cortar=$largo-15;
				$cliente=substr($usado['cliente'], 0, -$cortar).'..';
			}

			$pdf->Cell(23,5,($cliente),1,0,'L');

			$asesor_venta=$usuario_a[$usado['id_asesor']]['nombre'];
			$largo=strlen($usuario_a[$usado['id_asesor']]['nombre']);
			if ($largo>10) {
				$cortar=$largo-10;
				$asesor_venta=substr($usuario_a[$usado['id_asesor']]['nombre'], 0, -$cortar).'..';
			}

			$pdf->Cell(15,5,($asesor_venta),1,0,'L');


			$pdf->Cell(11,5,cambiarFormatoFecha($usado['fec_reserva']),1,0,'C');
			$pdf->Ln();

			$total_toma=$total_toma + $usado['toma_mas_impuesto'];
			$total_costo=$total_costo + $usado['costo_contable'];
			$total_p_venta= $total_p_venta + $usado['precio_venta'];
			$total_p_info = $total_p_info + $usado['precio_info'];
			$total_costo_rep = $total_costo_rep + $usado['costo_reparacion'];
			$total_transferencia = $total_transferencia + $usado['transferencia'];

			$total_gral_toma = $total_gral_toma + $usado['toma_mas_impuesto'];
			$total_gral_costo = $total_gral_costo + $usado['costo_contable'];
			$total_gral_p_venta = $total_gral_p_venta + $usado['precio_venta'];
			$total_gral_p_info = $total_gral_p_info + $usado['precio_info'];
			$total_gral_costo_rep = $total_gral_costo_rep + $usado['costo_reparacion'];
			$total_gral_transferencia = $total_gral_transferencia + $usado['transferencia'];

		}

		$pdf->SetFont('Arial','BI',8);
		$pdf->Cell(177,5,'Total '.($estado['estado_usado']).'   ',0,0,'R');
		$pdf->SetFont('Arial','B',6.5);

		if ($es_gerente==1) {
			$pdf->Cell(16,5,'$ '.number_format($total_toma, 0, ',','.'),1,0,'R');
			$pdf->Cell(16,5,'$ '.number_format($total_costo, 0, ',','.'),1,0,'R');
			$pdf->Cell(16,5,'$ '.number_format($total_costo_rep, 0, ',','.'),1,0,'R');
			// $pdf->Cell(16,5,'$ '.number_format($total_p_venta, 0, ',','.'),1,0,'R');
		}else{
			$pdf->Cell(16,5,'$ -',1,0,'R');
			$pdf->Cell(16,5,'$ -',1,0,'R');
			$pdf->Cell(16,5,'$ -',1,0,'R');
			// $pdf->Cell(16,5,'$ -',1,0,'R');
		}
		$pdf->Cell(16,5,'$ '.number_format($total_transferencia, 0, ',','.'),1,0,'R');
		$pdf->Cell(16,5,'$ '.number_format($total_p_venta, 0, ',','.'),1,0,'R');
		$pdf->Cell(16,5,'$ '.number_format($total_p_info, 0, ',','.'),1,0,'R');

		$pdf->Ln();
	}

  } // cierre de condicional de otras vistas
}
$pdf->Ln();
$pdf->SetFont('Arial','BI',8);
$pdf->Cell(177,5,'Total General   ',0,0,'R');
$pdf->SetFont('Arial','B',6.5);
if ($es_gerente==1) {
	$pdf->Cell(16,5,'$ '.number_format($total_gral_toma, 0, ',','.'),1,0,'R');
	$pdf->Cell(16,5,'$ '.number_format($total_gral_costo, 0, ',','.'),1,0,'R');
	$pdf->Cell(16,5,'$ '.number_format($total_gral_costo_rep, 0, ',','.'),1,0,'R');
}else{
	$pdf->Cell(16,5,'$ -',1,0,'R');
	$pdf->Cell(16,5,'$ -',1,0,'R');
	$pdf->Cell(16,5,'$ -',1,0,'R');
}
$pdf->Cell(16,5,'$ '.number_format($total_gral_transferencia, 0, ',','.'),1,0,'R');
$pdf->Cell(16,5,'$ '.number_format($total_gral_p_venta, 0, ',','.'),1,0,'R');
$pdf->Cell(16,5,'$ '.number_format($total_gral_p_info, 0, ',','.'),1,0,'R');

$pdf->Ln();

$pdf->Output('Stock.pdf','I');
$pdf->close();

?>