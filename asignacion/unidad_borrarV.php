<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM asignaciones WHERE id_unidad = ".$id_unidad;
mysqli_query($con, $SQL);

?>
