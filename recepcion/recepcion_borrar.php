<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM recepcion WHERE id_recepcion = ".$id_recepcion;
mysqli_query($con, $SQL);

 ?>