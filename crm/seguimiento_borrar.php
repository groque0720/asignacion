<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM prospectos_seguimientos WHERE id = ".$id_seguimiento;
mysqli_query($con, $SQL);

?>
