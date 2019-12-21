<?php

 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");


//echo $_GET["obser"]." ".$_GET["idres"];


$SQL="UPDATE facturas SET";
$SQL .="  estado = 3, ";
$SQL .="  fecfact = '".$_GET["fecfact"]."' ";
$SQL .=" WHERE idfactura =".$_GET["idfact"];
mysqli_query($con, $SQL);
 mysqli_close($con);

header("Location: facturacion.php?IDrecord=".$_GET["nrores"]);

 ?>