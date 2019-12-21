<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE reservas SET";
$SQL .="  corregir ='' ";
$SQL .=" WHERE idreserva =".$_POST["nrores"];
mysqli_query($con, $SQL);


echo "Reserva OK ";
//header("Location: asesores.php");
 mysqli_close($con);
 ?>