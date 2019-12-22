<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = $_POST["abuscar"];

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");


$SQL="UPDATE reservas SET estadopago = 3, cancelada = 1 WHERE idreserva = ". $datoBuscar;
$res = mysqli_query($con, $SQL);

 mysqli_close($con);
 echo "Reserva Cancelada!";
?>
