<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = $_POST["id"];

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="SELECT nrounidad FROM reservas WHERE idreserva = ". $datoBuscar;
$res=mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($res);
echo $unidad['nrounidad'];
 mysqli_close($con);
 ?>