<?php

 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");


//echo $_GET["obser"]." ".$_GET["idres"];


$SQL="UPDATE reservas SET";
$SQL .="  enviada = 4 ";
$SQL .=" WHERE idreserva =".$_GET["idres"];
mysqli_query($con, $SQL);
 mysqli_close($con);
header("Location: control_reservas.php");

 ?>