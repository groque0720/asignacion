<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
	include ('busqueda_rapida_unidades_cuerpo.php');
?>


