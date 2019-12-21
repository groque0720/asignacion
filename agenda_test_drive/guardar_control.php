<?php 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="UPDATE agenda_td_lineas SET ok = $valor WHERE id_linea=".$id_linea;
mysqli_query($con, $SQL);



?>