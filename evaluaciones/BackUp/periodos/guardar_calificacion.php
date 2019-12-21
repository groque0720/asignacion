<?php 

	include("../z_comun/funciones/funciones.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	if ($tipo_evaluacion=='auto') {
		$SQL="UPDATE evaluacion_usuario_calificacion SET calificacion_autoevaluacion = ".$valor." WHERE id_evaluacion_usuario_calificacion = ".$id;
	}else{
		$SQL="UPDATE evaluacion_usuario_calificacion SET calificacion_superior = ".$valor." WHERE id_evaluacion_usuario_calificacion = ".$id;		
	}


	mysqli_query($con, $SQL);

 ?>
