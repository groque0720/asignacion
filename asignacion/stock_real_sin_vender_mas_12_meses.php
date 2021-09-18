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
		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',8);
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'L');
			$this->Cell(170,5,('PLANILLA DE STOCK'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'R');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}


		$this->SetFont('Arial','I',6.5);
		$this->SetFont('');
		$this->Cell(0,5,('Pag.').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();


		// linea ---------
		$this->Cell(45,5,'Meses',1,0,'C');

		$m_a = (int)date("n");
		$a_a=(int)date("y");
		$mes_a[1]='Ene';
		$mes_a[2]='Feb';
		$mes_a[3]='Mar';
		$mes_a[4]='Abr';
		$mes_a[5]='May';
		$mes_a[6]='Jun';
		$mes_a[7]='Jul';
		$mes_a[8]='Ago';
		$mes_a[9]='Sep';
		$mes_a[10]='Oct';
		$mes_a[11]='Nov';
		$mes_a[12]='Dic';

		for ($i=1; $i < 14 ; $i++) {

			if ($m_a>12) {
				$m_a=$m_a-12;
				$a_a++;
			}
			$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes
			$this->Cell(15,5,$mes_a[$m_a].' '.$a_a,1,0,'C');
			$m_a++;
		}

		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes
		$this->Cell(15,5,"(+)". $mes_a[$m_a].' '.$a_a,1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes

		$this->Cell(15,5,'Total',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes

		// Linea ------------

		$this->Ln();
		$this->Cell(36,5,'Modelos',1,0,'C');
		$this->Cell(0.8,5,'',0,0,'C');

		$this->Cell(7.4,5,'S/C',1,0,'C');
		$this->Cell(0.8,5,'',0,0,'C');

		for ($i=1; $i < 15 ; $i++) {

		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');

		}
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes

		$this->Ln();
		$this->Ln(5);
	}
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(true,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');

	for ($i=0; $i < 13; $i++) {
		$stock_ant_g[$i]['cant'] = 0;
		$total_m[$i]['cant'] = 0;
		$total_g[$i]['cant'] = 0;
		$total_r_m[$i]['cant'] = 0;
		$total_r_g[$i]['cant'] = 0;
	}

$SQL="SELECT * FROM grupos WHERE idgrupo<>1 AND cerokilometro = 1 AND posicion > 0 AND activo = 1 ORDER BY posicion";
$grupos = mysqli_query($con, $SQL);

$cant_grupo=0;

$acumulado_gral = [];

while ($grupo=mysqli_fetch_array($grupos)) {

	if ($grupo['agrupar']==1) {
		$cant_grupo++;
	}

	$pdf->SetFont('Arial','BI',8);
	$pdf->Cell(0,5,$grupo['grupo'],0,0);
	$pdf->SetFont('Arial','B',6.5);
	$pdf->SetFont('');
	$pdf->Ln();

	$SQL="SELECT * FROM modelos WHERE posicion > 0 AND activo = 1 AND idgrupo = ".$grupo['idgrupo']." ORDER BY posicion";
	$modelos=mysqli_query($con, $SQL);

	$acum_cant_pisadas = 0;
	$acum_modelo_mes = [];

	while ($modelo=mysqli_fetch_array($modelos)) {

		$m_a = (int)date("n");
		$a_a = (int)date("Y");

		$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND reservada = 1 AND estado_reserva = 0 AND entregada = 0 AND borrar = 0 AND id_modelo = ". $modelo['idmodelo'];
		$pisadas=mysqli_query($con, $SQL);
		$cant_pisada=mysqli_fetch_array($pisadas);
		// $pisadas_modelos[$modelo['idmodelo']]['cantidad'] = $cant_pisada['cantidad'];
		$acum_modelo_mes['pisadas'] = isset($acum_modelo_mes['pisadas']) ? ($acum_modelo_mes['pisadas']+$cant_pisada['cantidad']) : $cant_pisada['cantidad'];
		$acumulado_gral['pisadas'] = isset($acumulado_gral['pisadas']) ? ($acumulado_gral['pisadas']+$cant_pisada['cantidad']) : $cant_pisada['cantidad'];

		$largo=strlen($modelo['modelo']);
		$nombre_modelo = $modelo['modelo'];
		if ($largo > 25) {
			$cortar = $largo - 25;
			$nombre_modelo = substr($modelo['modelo'], 0, -$cortar).'..';
		}

		$pdf->Cell(36,5,' '.$nombre_modelo,1,0);
		$pdf->Cell(0.8,5,'',0,0,'C');
		$pdf->Cell(7.4,5,$cant_pisada['cantidad'],1,0,'C');
		$pdf->Cell(0.8,5,'',0,0,'C');
		// $pdf->Ln();

		$acum_cant_pisadas = $acum_cant_pisadas + $cant_pisada['cantidad'];

		// cantidad total de asignadas TASA ------------
		$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND estado_tasa = 1 AND borrar = 0 AND id_modelo = ". $modelo['idmodelo']." AND ((id_mes >= $m_a AND año = $a_a) OR año > $a_a)";
		$result_asig_tasa=mysqli_query($con, $SQL);
		$asig_tasa=mysqli_fetch_array($result_asig_tasa);
		$tot_asig_tasa = $asig_tasa['cantidad'];
		$tot_asig_tasa_meses = $asig_tasa['cantidad'];
		$acum_modelo_mes['asig'] = isset($acum_modelo_mes['asig']) ? ($acum_modelo_mes['asig']+$asig_tasa['cantidad']) : $asig_tasa['cantidad'];
		$acumulado_gral['asig'] = isset($acumulado_gral['asig']) ? ($acumulado_gral['asig']+$asig_tasa['cantidad']) : $asig_tasa['cantidad'];

		//cantidad total de reservadas DYV ------------------


		$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND estado_tasa = 1 AND estado_reserva = 0 AND entregada = 0 AND  borrar = 0 AND id_modelo = ". $modelo['idmodelo']." AND ((id_mes < $m_a AND año = $a_a) OR año < $a_a)";
		$result_stock_ant=mysqli_query($con, $SQL);
		$stock_ant=mysqli_fetch_array($result_stock_ant);


		$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND reservada = 1 AND estado_reserva = 1 AND borrar = 0 AND id_modelo = ". $modelo['idmodelo']." AND ((id_mes >= $m_a AND año = $a_a) OR año > $a_a)";
		$result_reserva_dyv=mysqli_query($con, $SQL);
		$reserva_dyv=mysqli_fetch_array($result_reserva_dyv);
		$tot_reserva_dyv = $reserva_dyv['cantidad']-$stock_ant['cantidad'];
		$tot_reserva_dyv_meses = $reserva_dyv['cantidad'];

		$acum_modelo_mes['reservas'] = isset($acum_modelo_mes['reservas']) ? ($acum_modelo_mes['reservas']+$reserva_dyv['cantidad']+$stock_ant['cantidad']-$stock_ant['cantidad']) : $reserva_dyv['cantidad']-$stock_ant['cantidad'];
		$acumulado_gral['reservas'] = isset($acumulado_gral['reservas']) ? ($acumulado_gral['reservas']+$reserva_dyv['cantidad']-$stock_ant['cantidad']) : $reserva_dyv['cantidad']-$stock_ant['cantidad'];


		for ($i=1; $i < 14 ; $i++) {

			if ($m_a>12) {
				$m_a=$m_a-12;
				$a_a++;
			}

			$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND estado_tasa = 1 AND borrar = 0 AND id_modelo = ". $modelo['idmodelo']." AND id_mes =".$m_a." AND año = ".$a_a;
			$result_asig_tasa=mysqli_query($con, $SQL);
			$asig_tasa=mysqli_fetch_array($result_asig_tasa);
			$tot_asig_tasa_meses = $tot_asig_tasa_meses - $asig_tasa['cantidad'];
			$acum_modelo_mes[$i]['asignadas'] = isset($acum_modelo_mes[$i]['asignadas']) ? $acum_modelo_mes[$i]['asignadas'] + $asig_tasa['cantidad'] : $asig_tasa['cantidad'] ;

			if ($i==1) {
				$stock_ant = $stock_ant['cantidad'];
			}else{
				$stock_ant = 0;
			}


			$SQL = "SELECT count(id_unidad) as cantidad FROM asignaciones WHERE id_negocio = 1 AND reservada = 1 AND estado_reserva = 1 AND  borrar = 0 AND id_modelo = ". $modelo['idmodelo']." AND id_mes =".$m_a." AND año = ".$a_a;
			$result_result_dyv=mysqli_query($con, $SQL);
			$reservas_dyv=mysqli_fetch_array($result_result_dyv);
			$tot_reserva_dyv_meses = $tot_reserva_dyv_meses - $reservas_dyv['cantidad'];

			$acum_modelo_mes[$i]['reservas'] = isset($acum_modelo_mes[$i]['reservas']) ? $acum_modelo_mes[$i]['reservas'] + $reservas_dyv['cantidad'] - $stock_ant : $reservas_dyv['cantidad']-$stock_ant;

			$acumulado_gral[$i]['stock'] = isset($acumulado_gral[$i]['stock']) ? ($acumulado_gral[$i]['stock'] + $asig_tasa['cantidad'] - $reservas_dyv['cantidad']  + $stock_ant) : ($asig_tasa['cantidad'] - $reservas_dyv['cantidad'] + $stock_ant);

			$acumulado_gral[$i]['asig'] = isset($acumulado_gral[$i]['asig']) ? ($acumulado_gral[$i]['asig'] + $asig_tasa['cantidad']) : ($asig_tasa['cantidad']);

			if (($asig_tasa['cantidad'] - $reservas_dyv['cantidad'] + $stock_ant)==0) {
				$stock = '-';
			}else{
				$stock = $asig_tasa['cantidad'] - $reservas_dyv['cantidad'] + $stock_ant;
			}

			if ($asig_tasa['cantidad'] == 0) {
				$asignadas = '-';
			}else{
				$asignadas = $asig_tasa['cantidad'];
			}

			$pdf->Cell(0.3,5,'',1,0,'C');
			$pdf->Cell(7.5,5,$stock,1,0,'C');
			$pdf->Cell(7.5,5,$asignadas,1,0,'C');

			$m_a++;
		}

		$acum_modelo_mes['stock_meses_sig'] = isset($acum_modelo_mes['stock_meses_sig']) ? ($acum_modelo_mes['stock_meses_sig'] +$tot_asig_tasa_meses - $tot_reserva_dyv_meses) : ($tot_asig_tasa_meses - $tot_reserva_dyv_meses);

		$acumulado_gral['stock_meses_sig'] = isset($acumulado_gral['stock_meses_sig']) ? ($acumulado_gral['stock_meses_sig'] + $tot_asig_tasa_meses - $tot_reserva_dyv_meses) : ($tot_asig_tasa_meses - $tot_reserva_dyv_meses);

		if (($tot_asig_tasa_meses - $tot_reserva_dyv_meses) == 0) {
			$stock_meses_sig = '-';
		}else{
			$stock_meses_sig = $tot_asig_tasa_meses - $tot_reserva_dyv_meses;
		}

		$pdf->Cell(0.3,5,'',1,0,'C');
		$pdf->Cell(7.5,5,$stock_meses_sig,1,0,'C');
		$pdf->Cell(7.5,5,$tot_asig_tasa_meses !=0? $tot_asig_tasa_meses : '-',1,0,'C');

		$acum_modelo_mes['asig_meses_sig'] = isset($acum_modelo_mes['asig_meses_sig']) ? $acum_modelo_mes['asig_meses_sig'] + $tot_asig_tasa_meses : $tot_asig_tasa_meses;
		$acumulado_gral['asig_meses_sig'] = isset($acumulado_gral['asig_meses_sig']) ? ($acumulado_gral['asig_meses_sig']+ $tot_asig_tasa_meses) : $tot_asig_tasa_meses;


		$pdf->Cell(0.3,5,'',1,0,'C');
		$pdf->Cell(7.5,5,$tot_asig_tasa-$tot_reserva_dyv,1,0,'C');
		$pdf->Cell(7.5,5,$tot_asig_tasa,1,0,'C');

		$pdf->Ln();
	}

	// totales por modelos
		$pdf->SetFont('Arial','BI',6.5);
		$pdf->Cell(36,5,'TOTAL '.$grupo['grupo'],1,0);
		$pdf->Cell(0.8,5,'',0,0,'C');
		$pdf->Cell(7.4,5,$acum_cant_pisadas,1,0,'C');
		$pdf->Cell(0.8,5,'',0,0,'C');

		for ($i=1; $i < 14 ; $i++) {
			$pdf->Cell(0.3,5,'',1,0,'C');

			if ($acum_modelo_mes[$i]['asignadas'] - $acum_modelo_mes[$i]['reservas'] == 0) {
				$acum_modelo_mes_stock = '-';
			}else{
				$acum_modelo_mes_stock = $acum_modelo_mes[$i]['asignadas'] - $acum_modelo_mes[$i]['reservas'];
			}
			$pdf->Cell(7.5,5,$acum_modelo_mes_stock,1,0,'C');
			$pdf->Cell(7.5,5,$acum_modelo_mes[$i]['asignadas'],1,0,'C');
		}

		$pdf->Cell(0.3,5,'',1,0,'C');
		$pdf->Cell(7.5,5,$acum_modelo_mes['stock_meses_sig'],1,0,'C');
		$pdf->Cell(7.5,5,$acum_modelo_mes['asig_meses_sig'],1,0,'C');

		$pdf->Cell(0.3,5,'',1,0,'C');
		$pdf->Cell(7.5,5,$acum_modelo_mes['asig'] - $acum_modelo_mes['reservas'],1,0,'C');
		$pdf->Cell(7.5,5,$acum_modelo_mes['asig'],1,0,'C');
		$pdf->Ln();

	if ($cant_grupo==2) {
		$cant_grupo++;
		$pdf->Ln();
		$pdf->Cell(36,5,'TOTAL HILUX',1,0);
		$pdf->Cell(0.8,5,'',0,0,'C');
		$pdf->Cell(7.4,5,$acumulado_gral['pisadas'],1,0,'C');
		$pdf->Cell(0.8,5,'',0,0,'C');

		//Relleno Linea Total de MOdelo o Total Gral
		for ($i=1; $i < 14; $i++) {
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,$acumulado_gral[$i]['stock'],1,0,'C');
		$pdf->Cell(7.5,5,$acumulado_gral[$i]['asig'],1,0,'C');
		}

		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,$acumulado_gral['stock_meses_sig'],1,0,'C');
		$pdf->Cell(7.5,5,$acumulado_gral['asig_meses_sig'],1,0,'C');
		$pdf->SetDrawColor(184, 184, 184);

		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,($acumulado_gral['asig']-$acumulado_gral['reservas']),1,0,'C');
		$pdf->Cell(7.5,5,$acumulado_gral['asig'],1,0,'C');
		$pdf->SetDrawColor(184, 184, 184);

		$pdf->Ln();
		$pdf->Ln(2);
	}
	$pdf->Ln(3);
}

	$pdf->SetDrawColor(60, 60, 60);
	$pdf->Cell(36,5,'TOTAL GRAL',1,0);
	$pdf->Cell(0.8,5,'',0,0,'C');
	$pdf->Cell(7.4,5,$acumulado_gral['pisadas'],1,0,'C');
	$pdf->Cell(0.8,5,'',0,0,'C');

	//Relleno Linea Total de MOdelo o Total Gral
	for ($i=1; $i < 14; $i++) {
	// $pdf->SetDrawColor(60, 60, 60);
	$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
	// $pdf->SetDrawColor(184, 184, 184);
	$pdf->Cell(7.5,5,$acumulado_gral[$i]['stock'],1,0,'C');
	$pdf->Cell(7.5,5,$acumulado_gral[$i]['asig'],1,0,'C');
	}

	// $pdf->SetDrawColor(60, 60, 60);
	$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
	// $pdf->SetDrawColor(184, 184, 184);
	$pdf->Cell(7.5,5,$acumulado_gral['stock_meses_sig'],1,0,'C');
	$pdf->Cell(7.5,5,$acumulado_gral['asig_meses_sig'],1,0,'C');


	// $pdf->SetDrawColor(60, 60, 60);
	$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
	// $pdf->SetDrawColor(184, 184, 184);
	$pdf->Cell(7.5,5,($acumulado_gral['asig']-$acumulado_gral['reservas']),1,0,'C');
	$pdf->Cell(7.5,5,$acumulado_gral['asig'],1,0,'C');
	$pdf->Cell(0.3,5,'',1,0,'C');


$pdf->Output('Stock(+12Meses).pdf','I');
$pdf->close();

 ?>

