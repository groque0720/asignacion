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
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'C');
			$this->Cell(100,5,utf8_decode('PLANILLA DE STOCK - EFV (No Confirmadas)'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}


		$this->SetFont('Arial','I',6.5);
		$this->SetFont('');
		$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();


		$this->Cell(37,5,'Meses',1,0,'C');

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

		for ($i=1; $i < 10 ; $i++) {

			if ($m_a>12) {
				$m_a=$m_a-12;
				$a_a++;
			}
			$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes
			$this->Cell(15,5,$mes_a[$m_a].' '.$a_a,1,0,'C');
			$m_a++;

		}

		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes
		$this->Cell(15,5,'Total',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');//linea divisoria por mes

		$this->Ln();
		$this->Cell(37,5,'Modelos',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Cell(7.5,5,'stock',1,0,'C');
		$this->Cell(7.5,5,'Asig.',1,0,'C');
		$this->Cell(0.3,5,'',1,0,'C');
		$this->Ln();
		$this->Ln(5);
	}
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(true,6);
$pdf->SetFont('Arial','B',6.5);
$pdf->SetFont('');

$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND posicion > 0 AND activo = 1 ORDER BY posicion";
$grupos = mysqli_query($con, $SQL);
$cant_grupo=0;

while ($grupo=mysqli_fetch_array($grupos)) {

	if ($grupo['agrupar']==1) {
		$cant_grupo++;
	}
	$pdf->SetFont('Arial','BI',7);
	$pdf->Cell(0,5,$grupo['grupo'],0,0);
	$pdf->SetFont('Arial','B',6.5);
	$pdf->SetFont('');
	$pdf->Ln();

	$SQL="SELECT * FROM modelos WHERE posicion > 0 AND activo = 1 AND idgrupo = ".$grupo['idgrupo']." ORDER BY posicion";
	$modelos=mysqli_query($con, $SQL);

	while ($modelo=mysqli_fetch_array($modelos)) {

		$m_a = (int)date("n");
		$a_a = (int)date("Y");
		//reinicio totales
		$stock_a[9]['cant']=0;
		$reserva_a[9]['cant']=0;
		$stock_ant[0]['cant']=0;
		$acum=0;
		$s_p=0;
		for ($i=0; $i < 9; $i++) {

			if ($m_a>12) {
				$m_a=$m_a-12;
				$a_a++;
			}
			//Cantidad de Stock por parte de TASA
			$SQL="SELECT * FROM view_stock_tasa_gral WHERE  id_modelo =".$modelo['idmodelo']." AND id_mes = ".$m_a." AND año = ".$a_a;
			$stocks=mysqli_query($con, $SQL);
			$cant_stock= !empty($stocks) ? mysqli_num_rows($stocks) : 0;
			if ($cant_stock>0) {
				$stock=mysqli_fetch_array($stocks);
				$stock_a[$i]['cant']=$stock['cantidad'];
				$stock_a[9]['cant']=(int)$stock_a[9]['cant']+(int)$stock['cantidad'];
			}else{
				$stock_a[$i]['cant']=0;
			}

			//Cantidad de Stock Libre Anteriores
			if ($i==0) {
				$SQL="SELECT sum(cantidad) AS cantidad FROM view_stock_libre_anteriores_efv WHERE id_modelo =".$modelo['idmodelo'];
				$stocks_anterior=mysqli_query($con, $SQL);
				$cant_stock_anterior= !empty($stocks_anterior) ? mysqli_num_rows($stocks_anterior) : 0;
				if ($cant_stock_anterior>0) {
					$stock=mysqli_fetch_array($stocks_anterior);
					$stock_ant[$i]['cant']=$stock['cantidad'];
					$stock_ant_g[$i]['cant']=$stock_ant_g[$i]['cant']+$stock_ant[$i]['cant'];
					$reserva_a[9]['cant']=(int)$stock['cantidad'];
				}else{
					$stock_ant[$i]['cant']=0;
				}
			}

			//Cantidad de Reservas realizadas actuales
			$SQL="SELECT * FROM view_stock_libre_actuales_efv WHERE id_modelo =".$modelo['idmodelo']." AND id_mes = ".$m_a." AND año = ".$a_a;
			$reservas=mysqli_query($con, $SQL);
			$cant_stock= !empty($reservas) ? mysqli_num_rows($reservas) : 0;
			if ($cant_stock>0) {
				$reserva=mysqli_fetch_array($reservas);
				$reserva_a[$i]['cant']=$reserva['cantidad'];
				$reserva_a[9]['cant']=(int)$reserva_a[9]['cant']+(int)$reserva['cantidad'];
			}else{
				$reserva_a[$i]['cant']=0;
			}

			$m_a++;
		}
		//cantidad por parte de
		$largo=strlen($modelo['modelo']);
		$nombre_modelo = $modelo['modelo'];
		if ($largo > 20) {
			$cortar = $largo - 20;
			$nombre_modelo = substr($modelo['modelo'], 0, -$cortar).'..';
		}
		$pdf->Cell(37,5,' '.$nombre_modelo ,1,0);
		$acum=0;
		if ((int)$stock_ant[0]['cant']+(int)$reserva_a[0]['cant']!=0){
			$s_p=(int)$stock_ant[0]['cant']+(int)$reserva_a[0]['cant'];
			$acum=(int)$stock_ant[0]['cant']+(int)$reserva_a[0]['cant'];
		}else{
			$s_p='-';
		}

		if ($stock_a[0]['cant']!=0) {
			$s_a=$stock_a[0]['cant'];
		}else{
			$s_a='-';
		}

		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);

		$pdf->Cell(7.5,5,$s_p,1,0,'C');
		$pdf->Cell(7.5,5,$s_a,1,0,'C');
		//Relleno Linea Con datos traidos por modelos.-

		for ($i=1; $i <= 9; $i++) {

			if ((int)$reserva_a[$i]['cant']!=0 AND $i!=9){
				$s_p=(int)$reserva_a[$i]['cant'];
				$acum=$acum+$s_p;
			}else{
				$s_p='-';
			}

			if ($i==9) {
				if ($acum!=0) {
					$s_p=$acum;
				}else{
					$s_p='-';
				}
			}

			if ($stock_a[$i]['cant']!=0) {
				$s_a=$stock_a[$i]['cant'];
			}else{
				$s_a='-';
			}

			$pdf->SetDrawColor(60, 60, 60);
			$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
			$pdf->SetDrawColor(184, 184, 184);

			$pdf->Cell(7.5,5,$s_p,1,0,'C');
			$pdf->Cell(7.5,5,$s_a,1,0,'C');
		}

		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);

		$pdf->Ln();

		//Acumulo Totales TASA Por MODELO y GENERAL
		for ($i=0; $i < 10; $i++) {
				$total_m[$i]['cant']=(int) (!empty($total_m[$i]['cant']) ? $total_m[$i]['cant'] : 0 )+(int)$stock_a[$i]['cant'];
				$total_g[$i]['cant']=(int) (!empty($total_g[$i]['cant']) ? $total_g[$i]['cant'] : 0 )+(int)$stock_a[$i]['cant'];
		}
		$total_r_m[9]['cant']=0;
		//Acumulo Totales RESERVAS Por MODELO y GENERAL
		for ($i=0; $i < 9; $i++) {
			if ($i==0) {
				$total_r_m[$i]['cant']=(int)$total_r_m[$i]['cant']+(int)$stock_ant[0]['cant']+(int)$reserva_a[0]['cant'];
				$total_r_g[$i]['cant']=(int)$total_r_g[$i]['cant']+(int)$stock_ant[0]['cant']+(int)$reserva_a[0]['cant'];
				$total_r_m[9]['cant']=(int)$total_r_m[9]['cant'] + (int)$total_r_m[$i]['cant'];

			}else{
				$total_r_m[$i]['cant']=(int)$total_r_m[$i]['cant'] + (int)$reserva_a[$i]['cant'];
				$total_r_g[$i]['cant']=(int)$total_r_g[$i]['cant'] + (int)$reserva_a[$i]['cant'];
				$total_r_m[9]['cant']=(int)$total_r_m[9]['cant'] + (int)$total_r_m[$i]['cant'];
			}
		}

	}

	$total_r_g[9]['cant']=$total_r_g[9]['cant']+$total_r_m[9]['cant'];


		$pdf->SetFont('Arial','BI',6.5);
		$pdf->Cell(37,5,'TOTAL '.$grupo['grupo'],1,0);
		//Relleno Linea Total de Modelo
		for ($i=0; $i < 10; $i++) {
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,$total_r_m[$i]['cant'],1,0,'C');
		$pdf->Cell(7.5,5,$total_m[$i]['cant'],1,0,'C');
		}
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Ln();
		$pdf->Ln(2);


		for ($i=0; $i < 10; $i++) {
			$total_m[$i]['cant']=0;
		}
		for ($i=0; $i < 10; $i++) {
			$total_r_m[$i]['cant']=0;
		}

		$stock_ant_m[0]['cant']=0;


	if ($cant_grupo==2) {
		$cant_grupo++;
		$pdf->Cell(37,5,'TOTAL HILUX',1,0);
		//Relleno Linea Total de MOdelo o Total Gral
		for ($i=0; $i < 10; $i++) {
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,$total_r_g[$i]['cant'],1,0,'C');
		$pdf->Cell(7.5,5,$total_g[$i]['cant'],1,0,'C');
		}
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Ln();
		$pdf->Ln(3);
	}
	$pdf->SetFont('');
}
		$pdf->SetFont('Arial','BI',6.5);
		$pdf->Cell(37,5,'TOTAL GRAL',1,0);
		for ($i=0; $i < 10; $i++) {
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Cell(7.5,5,$total_r_g[$i]['cant'],1,0,'C');
		$pdf->Cell(7.5,5,$total_g[$i]['cant'],1,0,'C');
		}
		$pdf->SetDrawColor(60, 60, 60);
		$pdf->Cell(0.3,5,'',1,0,'C');//linea separador de meses
		$pdf->SetDrawColor(184, 184, 184);
		$pdf->Ln();
		$pdf->Ln(3);

$pdf->Output('Stock.pdf','I');
$pdf->close();

 ?>

