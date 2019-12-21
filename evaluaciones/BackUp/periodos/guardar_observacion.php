<?php 

	include("../z_comun/funciones/funciones.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	if ($tipo_evaluacion=='auto') {
		$SQL="UPDATE evaluacion_usuario_calificacion SET observacion_auto = '".$texto."' WHERE id_evaluacion_usuario_calificacion = ".$id;
	}else{
		$SQL="UPDATE evaluacion_usuario_calificacion SET observacion_superior = '".$texto."' WHERE id_evaluacion_usuario_calificacion = ".$id;		
	}


	mysqli_query($con, $SQL);

 ?>
