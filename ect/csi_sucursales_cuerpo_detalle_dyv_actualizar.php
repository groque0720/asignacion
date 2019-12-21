
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="UPDATE ect_csi_sucursales_dyv_detalle SET puntaje =  {$valor}  WHERE id =  {$id} ";
	mysqli_query($con, $SQL);

 ?>


