<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="UPDATE registros_gestoria_clientes SET estado = ".$estado." WHERE id_cliente_gestoria =".$id_cli;
mysqli_query($con, $SQL);

?>

