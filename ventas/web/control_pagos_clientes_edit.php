<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE reservas SET";
$SQL .="  interno = '".$_POST["nroint"]."', ";
$SQL .="  nrounidad = '".$_POST["nrou"]."', ";
$SQL .="  llego='".$_POST["fecarr"]."', ";
$SQL .="  fechacanc='".$_POST["feccan"]."', ";
$SQL .="  fechaentrega='".$_POST["fecent"]."', ";
$SQL .="  nroorden='".$_POST["no"]."', ";
$SQL .="  obscanc ='".$_POST["obs"]."' ";
$SQL .=" WHERE idreserva = '".$_POST["id"]."'";
mysqli_query($con, $SQL);
 mysqli_close($con);
?>