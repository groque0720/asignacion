<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM view_asignaciones_entregas WHERE id_ubicacion = ".$id_suc." ORDER BY ".$orden;
	$unidades = mysqli_query($con, $SQL);

	include('entregas_contenido_relleno_cuerpo.php'); 

?>


