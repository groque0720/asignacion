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
		global $grupos;

			if ($_GET['suc']==0) {
				$sucursal = 'Todas';
			}else{
				switch ($_GET['suc']) {
					case 1:
						$sucursal='Resistencia';
						break;
					case 2:
						$sucursal='Saenz Peña';
						break;
					case 3:
						$sucursal='Charata';
						break;
					case 4:
						$sucursal='Villa Angela';
						break;
				}
			}

			if ($_GET['insc']==1) {
				$patente = 'Si';
			}else{
				$patente = 'No';
			}

		if ($this->PageNo()==1) {
			$this->SetFont('Arial','B',8);
			$this->Cell(45,5,'DERKA Y VARGAS S. A.',0,0,'C');
			$this->Cell(100,5,('REPORTE DE GESTORIA RESUMEN'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
			// $this->Cell(0,5,('Sucursal :'.$sucursal.' Período :'.cambiarFormatoFecha($_GET['desde']).' al '.cambiarFormatoFecha($_GET['hasta']).' Inscriptas :'.$patente) ,0,0,'C');
			$this->Cell(45,5,('Sucursal: '.$sucursal),0,0,'C');
			$this->Cell(100,5,('Período: '.cambiarFormatoFecha($_GET['desde']).' al '.cambiarFormatoFecha($_GET['hasta'])),0,0,'C');
			$this->Cell(0,5,('Inscriptas: '.$patente),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}


		$this->SetFont('Arial','I',5);
		$this->SetFont('');
		$this->Cell(0,5,('Página').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();

		$this->Cell(28,5,'Sucursal / Registro',1,0,'C');
		$columna=1;
		while ($grupo=mysqli_fetch_array($grupos)) { $columna++;

			$this->Cell(11,5,$grupo['grupo'],1,0,'C');
			if ($columna==3) {
				$this->SetFont('Arial','BI',5);
				$this->Cell(11,5,"TOT HILUX",1,0,'C');
				$this->SetFont('');
			}
		}
		$this->Cell(11,5,'TOTAL',1,0,'C');
		$this->Ln();
	}
}


$desde=$_GET['desde'];
$hasta = $_GET['hasta'];
$sucursal=$_GET['suc'];
$inscripto = $_GET['insc'];

if ($inscripto==0) {
	$cadena=" AND fec_ins IS NULL AND fec_rec_gestoria >= '".$desde."' AND fec_rec_gestoria <= '".$hasta."'";
}else{
	$cadena=" AND fec_ins >= '".$desde."' AND fec_ins <= '".$hasta."'";
}

if ($sucursal!=0) {
	$cadena .= " AND id_sucursal = ".$sucursal;
}

$SQL="SELECT * FROM grupos WHERE idgrupo <> 11 AND idgrupo <> 10 AND activo = 1 AND posicion > 0 AND cerokilometro = 1 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL);
$grupos_dos=mysqli_query($con, $SQL);
$cant_grupo=0;
while ($grupo_dos=mysqli_fetch_array($grupos_dos)) {
	$grupo_a[$cant_grupo]['id_grupo']=$grupo_dos['idgrupo'];
	$cant_grupo++;
}

$pdf = new PDF();
$pdf->AliasNbPages();
// $pdf->SetTitle($grupos);
$pdf->AddPage('L','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(1,6);
$pdf->SetFont('Arial','B',7);
$pdf->SetFont('');


$SQL="SELECT * FROM provincias";
$provincias = mysqli_query($con, $SQL);

	for ($i=0; $i <= $cant_grupo; $i++) {
		$total_gral_a[$i]=0;
	}

while ($provincia=mysqli_fetch_array($provincias)) {

	$SQL="SELECT * FROM view_registros_gestoria WHERE id_provincia = ".$provincia['id_provincia'].$cadena;
	$tram_provs=mysqli_query($con, $SQL);
	// $pdf->Cell(0,5,$SQL,0,0,'L');
	// $pdf->Ln();
	$cant_tramites_p=mysqli_num_rows($tram_provs);

	for ($i=0; $i <= $cant_grupo; $i++) {
		$total_por_provincia_a[$i]=0;
	}

	if ($cant_tramites_p>0) {
		$pdf->Ln(2);
		$prov=mysqli_fetch_array($tram_provs);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(0,5,' '.(strtoupper($prov['provincia'])),0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',7);
		$pdf->SetFont('');

		$SQL="SELECT * FROM registros_gestoria_localidades WHERE id_provincia = ".$provincia['id_provincia'];
		$localidades = mysqli_query($con, $SQL);
		// $pdf->Cell(0,5,$SQL,0,0,'L');

		while ($localidad = mysqli_fetch_array($localidades)) {

			$SQL="SELECT * FROM view_registros_gestoria WHERE id_localidad = ".$localidad['id_localidad'].$cadena;
			$tram_loc=mysqli_query($con, $SQL);
			// $pdf->Ln();
			// $pdf->Cell(0,5,$SQL,0,0,'L');
			$cant_tramites_l=0;
			$cant_tramites_l=mysqli_num_rows($tram_loc);

			if ($cant_tramites_l>0) {

				$loc=mysqli_fetch_array($tram_loc);
				$pdf->Cell(28,5,' '.(strtoupper($loc['localidad'])),1,0,'L');
				$cant_por_loc=0;

				for ($i=0; $i < $cant_grupo; $i++) {

					$SQL="SELECT * FROM view_registros_gestoria WHERE id_localidad = ".$localidad['id_localidad']." AND id_modelo =".$grupo_a[$i]['id_grupo'].$cadena;
					$tram_loc=mysqli_query($con, $SQL);
					$cant=mysqli_num_rows($tram_loc);
					$pdf->Cell(11,5,$cant,1,0,'C');

					$cant_por_loc=$cant_por_loc+$cant;

					if ($i==1) {
						$pdf->SetFont('Arial','BI',7);
						$pdf->Cell(11,5,$cant_por_loc,1,0,'C');
						$pdf->SetFont('');
					}

					$total_por_provincia_a[$i]=$total_por_provincia_a[$i]+$cant;
					$total_por_provincia_a[$cant_grupo]=$total_por_provincia_a[$cant_grupo]+$cant;

					$total_gral_a[$i]=$total_gral_a[$i]+$cant;
					$total_gral_a[$cant_grupo]=$total_gral_a[$cant_grupo]+$cant;
				}

				$pdf->Cell(11,5,$cant_por_loc,1,0,'C');//total por localidad
				$pdf->Ln();

			}//fin if cantidad de tramites por localidad
		}// fin while localidades


			$pdf->Ln(2);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(28,5,'Total '.(strtoupper($prov['provincia'])),1,0,'C');
			for ($i=0; $i <= $cant_grupo; $i++) {
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(11,5,$total_por_provincia_a[$i],1,0,'C');
					if ($i==1) {
						$pdf->SetFont('Arial','BI',7);
						$pdf->Cell(11,5,$total_por_provincia_a[$i-1]+$total_por_provincia_a[$i],1,0,'C');
						$pdf->SetFont('');
					}
			}
			$pdf->Ln();
	}// fin if  cantidad tramites provincia

}//fin while provincias


$pdf->Ln();
$pdf->SetFont('Arial','B',7);
$pdf->Cell(28,5,'TOTAL GRAL ',1,0,'C');
for ($i=0; $i <= $cant_grupo; $i++) {
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(11,5,$total_gral_a[$i],1,0,'C');

		if ($i==1) {
			$pdf->SetFont('Arial','BI',7);
			$pdf->Cell(11,5,$total_gral_a[$i-1]+$total_gral_a[$i],1,0,'C');
			$pdf->SetFont('');
		}
}


$pdf->Output('Stock.pdf','I');
$pdf->close();

 ?>
