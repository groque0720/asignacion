<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="UPDATE registros_gestoria_clientes_doc SET estado = ".$valor." WHERE id_doc_cli =".$id_doc;
mysqli_query($con, $SQL);

?>

