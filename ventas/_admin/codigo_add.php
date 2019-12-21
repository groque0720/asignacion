<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO codigos(detalle, movimiento, descuento, credito, tipocredito, financiera, activo) VALUES ";
$SQL .= "('".$_POST["detalle"]."','".$_POST["movimiento"]."','".$_POST["esdescuento"]."','".$_POST["escredito"]."','".$_POST["tipocredito"]."','".$_POST["financiera"]."', 1)";

//echo $SQL;

mysqli_query($con, $SQL);
header("Location: codigos.php");
?>