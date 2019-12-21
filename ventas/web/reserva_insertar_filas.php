<?php

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT * FROM codigos WHERE detalle= '".$_POST['det_l']."'";
$cod=mysqli_query($con, $SQL);
$codigo=mysqli_fetch_array($cod);


if ($_POST['det_l'] != "#" and $_POST['det_l'] != "##" ) {
	$cred=0;
	if ($codigo['credito']==1) {
		$cred=1;

		$SQL="UPDATE creditos SET estado = 20 WHERE idcredito = ".$_POST['nrocred'];
		mysqli_query($con, $SQL);
	}
	$SQL="INSERT INTO lineas_detalle (idcodigo, codigo, idreserva, detalle, adjunto, movimiento, monto, credito) VALUES ('".$codigo['idcodigo']."','".$codigo['codigo']."','".$_POST['nrores']."','".$codigo['detalle']."','".$_POST['det_ad']."','".$codigo['movimiento']."',".$_POST['monto'].", ".$cred.")";
	mysqli_query($con, $SQL);
}else {
	$SQL="INSERT INTO lineas_detalle (idcodigo, codigo, idreserva, detalle, adjunto, movimiento) VALUES ('".$codigo['idcodigo']."','".$codigo['codigo']."','".$_POST['nrores']."','".$codigo['detalle']."','".$_POST['det_ad']."','".$codigo['movimiento']."')";
mysqli_query($con, $SQL);
}

 include("reserva_altamodbaja_sol.php");
  mysqli_close($con);
 ?>


