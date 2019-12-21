<?php 

	include("../z_comun/funciones/funciones.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	if ($tipo_evaluacion=='auto') {
		$SQL="UPDATE evaluaciones_usuarios SET terminado_autoevaluacion = 1 WHERE id_evaluacion_usuario = ".$id_evaluacion_usuario;
	}else{
		$SQL="UPDATE evaluaciones_usuarios SET terminado_superior = 1 WHERE id_evaluacion_usuario = ".$id_evaluacion_usuario;		
	}


	mysqli_query($con, $SQL);

 ?>
