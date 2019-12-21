<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="DELETE FROM prospectos WHERE id = ".$id_prospecto;
mysqli_query($con, $SQL);

if ($id_prospecto == $id_prospecto_alta AND $alta_desde_prospecto==1) {
	$SQL="DELETE FROM prospectos_clientes WHERE id = ".$id_cliente;
	mysqli_query($con, $SQL);
}

$SQL="DELETE FROM prospectos_seguimientos WHERE id_prospecto = ".$id_prospecto;
mysqli_query($con, $SQL);

?>
