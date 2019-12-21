
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="UPDATE ect_asesores_r_objetivos SET activo =  {$valor}  WHERE id =  {$id} ";
	mysqli_query($con, $SQL);

 ?>


