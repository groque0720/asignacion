<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM prospectos_clientes WHERE id = ".$id_cliente;
mysqli_query($con, $SQL);

?>
