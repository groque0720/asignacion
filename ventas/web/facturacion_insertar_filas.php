<?php

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");



$SQL="SELECT * FROM codigos WHERE detalle= '".$_POST['det_l']."'";
$cod=mysqli_query($con, $SQL);
$codigo=mysqli_fetch_array($cod);


if ($_POST['det_l'] != "#" and $_POST['det_l'] != "##" ) {
	$SQL="INSERT INTO facturas_lineas (idcodigo, codigo, idfactura, detalle, adjunto, movimiento , monto) VALUES ('".$codigo['idcodigo']."','".$codigo['codigo']."','".$_POST['idfact']."','".$codigo['detalle']."','".$_POST['det_ad']."','".$codigo['movimiento']."',".$_POST['monto'].")";
mysqli_query($con, $SQL);
}else {
	$SQL="INSERT INTO facturas_lineas (idcodigo, codigo, idfactura, detalle, adjunto, movimiento) VALUES ('".$codigo['idcodigo']."','".$codigo['codigo']."','".$_POST['idfact']."','".$codigo['detalle']."','".$_POST['det_ad']."','".$codigo['movimiento']."')";
mysqli_query($con, $SQL);
}

 include("facturacion_altamodbaja_sol.php");
  mysqli_close($con);
 ?>


