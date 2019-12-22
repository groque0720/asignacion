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
			$this->Cell(100,5,utf8_decode('REPORTE DE GESTORIA DETALLE'),0,0,'C');
			$this->Cell(0,5,cambiarFormatoFecha(date('Y-m-d')).' - '. strftime("%H:%M"),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
			// $this->Cell(0,5,utf8_decode('Sucursal :'.$sucursal.' Período :'.cambiarFormatoFecha($_GET['desde']).' al '.cambiarFormatoFecha($_GET['hasta']).' Inscriptas :'.$patente) ,0,0,'C');
			$this->Cell(45,5,utf8_decode('Sucursal: '.$sucursal),0,0,'C');
			$this->Cell(100,5,utf8_decode('Período: '.cambiarFormatoFecha($_GET['desde']).' al '.cambiarFormatoFecha($_GET['hasta'])),0,0,'C');
			$this->Cell(0,5,utf8_decode('Inscriptas: '.$patente),0,0,'C');
			$this->Ln();
			$this->Cell(0,0,'',1,0,'C');
			$this->Ln();
		}


		$this->SetFont('Arial','I',7);
		$this->SetFont('');
		$this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,0,'R');
		$this->Ln();

		$this->Cell(10,5,'Nro',1,0,'C');
		$this->Cell(30,5,'Fecha',1,0,'C');
		$this->Cell(70,5,'Cliente',1,0,'C');
		$this->Cell(50,5,utf8_decode('Modelo - Versión'),1,0,'C');
		$this->Cell(30,5,'Asesor',1,0,'C');
		$this->Ln();
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

$SQL="SELECT * FROM grupos WHERE activo = 1 AND posicion > 0 AND cerokilometro = 1 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL);
$grupos_dos=mysqli_query($con, $SQL);
$cant_grupo=0;
while ($grupo_dos=mysqli_fetch_array($grupos_dos)) {
	$grupo_a[$cant_grupo]['id_grupo']=$grupo_dos['idgrupo'];
	$cant_grupo++;
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetTitle($grupos);
$pdf->AddPage('P','A4');
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(184, 184, 184);
$pdf->SetAutoPageBreak(true,6);
$pdf->SetFont('Arial','B',7);
$pdf->SetFont('');
$nro=0;

$SQL="SELECT * FROM provincias";
$provincias = mysqli_query($con, $SQL);

	for ($i=0; $i <= $cant_grupo; $i++) {
		$total_gral_a[$i]=0;
	}

while ($provincia=mysqli_fetch_array($provincias)) {

	$SQL="SELECT * FROM view_registros_gestoria WHERE compra = 1 AND id_provincia = ".$provincia['id_provincia'].$cadena;
	$tram_provs=mysqli_query($con, $SQL);
	// $pdf->Cell(0,5,$SQL,0,0,'L');
	// $pdf->Ln();
	$cant_tramites_p=mysqli_num_rows($tram_provs);

	for ($i=0; $i <= $cant_grupo; $i++) {
		$total_por_provincia_a[$i]=0;
	}

	if ($cant_tramites_p>0) {
		$prov=mysqli_fetch_array($tram_provs);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,5,' '.utf8_decode(strtoupper($prov['provincia'])).' ('.$cant_tramites_p.')',0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',7);
		$pdf->SetFont('');

		$SQL="SELECT * FROM registros_gestoria_localidades WHERE id_provincia = ".$provincia['id_provincia'];
		$localidades = mysqli_query($con, $SQL);
		// $pdf->Cell(0,5,$SQL,0,0,'L');

		while ($localidad = mysqli_fetch_array($localidades)) {

			$SQL="SELECT * FROM view_registros_gestoria WHERE compra = 1 AND id_localidad = ".$localidad['id_localidad'].$cadena." ORDER BY fec_ins, fec_rec_gestoria ASC";
			$tram_loc=mysqli_query($con, $SQL);
			$cant_localidad = mysqli_num_rows($tram_loc);
			$tramite_aux= mysqli_fetch_array($tram_loc);

			if ($cant_localidad>0) {
				$pdf->SetFont('Arial','B',7.5);
				$pdf->Cell(0,5,'    '.utf8_decode(strtoupper($tramite_aux['localidad'])).' ('.$cant_localidad.')',1,0,'C');
				$pdf->SetFont('Arial','B',7);
				$pdf->SetFont('');
				$pdf->Ln();
			}
			$tram_loc=mysqli_query($con, $SQL);
			while ($tramite= mysqli_fetch_array($tram_loc)) {
				if ($tramite['fec_ins']!=null AND $tramite['fec_ins']!=null) {
					$fecha = $tramite['fec_ins'];
				}else{
					$fecha = $tramite['fec_rec_gestoria'];
				}
				$nro++;
				$pdf->Cell(10,5,$nro,1,0,'C');
				$pdf->Cell(30,5,cambiarFormatoFecha($fecha),1,0,'C');
				$pdf->Cell(70,5,' '.utf8_decode(strtoupper($tramite['nombre'])),1,0,'L');
				$pdf->Cell(50,5,' '.utf8_decode(strtoupper($tramite['modelo'].' '.$tramite['version'])),1,0,'L');
				$pdf->Cell(30,5,' '.utf8_decode(strtoupper($tramite['asesor'])),1,0,'L');
				$pdf->Ln();
			}
			if ($cant_localidad>0) {
				$pdf->Ln();
			}
		}// fin while localidades

	}// fin if  cantidad tramites provincia

}//fin while provincias


$pdf->Output('Stock.pdf','I');
$pdf->close();

 ?>
