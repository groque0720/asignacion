<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM prospectos_seguimientos WHERE guardado_de_prospecto = 0 AND id_prospecto = ".$id_prospecto;
mysqli_query($con, $SQL);

?>
