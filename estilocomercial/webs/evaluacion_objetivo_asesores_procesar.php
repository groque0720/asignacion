<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");


	extract($_POST);

//  autoevaluacion

	if ($operacion=='carga') {
		$SQL="UPDATE evaluaciones_realizadas_objetivos SET autoevaluacion ='".$autoevaluacion."' WHERE id_evaluacion_objetivo = ".$id;
		mysqli_query($con, $SQL);
	}

	if ($operacion=='terminado_asesor') {

		$SQL="SELECT * FROM evaluaciones_realizadas_objetivos WHERE id_evaluacion_realizada = ".$id;
		$evaluaciones_realizadas = mysqli_query($con, $SQL);

		$tot_eva=0;

		while ($eva=mysqli_fetch_array($evaluaciones_realizadas)) {
			$tot_eva=$tot_eva + ($eva['autoevaluacion']/100*$eva['ponderacion']);
		}

		$SQL="UPDATE evaluaciones_realizadas SET terminado_usuario = 1, puntaje_autoevaluado = $tot_eva WHERE id_evaluacion_realizada = ".$id;
		mysqli_query($con, $SQL);
	}

	// evaluador

	if ($operacion=='carga_evaluador') {
		$SQL="UPDATE evaluaciones_realizadas_objetivos SET evaluacion_sup ='".$autoevaluacion."' WHERE id_evaluacion_objetivo = ".$id;
		mysqli_query($con, $SQL);
	}


	if ($operacion=='terminado_evaluador') {

		$SQL="SELECT * FROM evaluaciones_realizadas_objetivos WHERE id_evaluacion_realizada = ".$id;
		$evaluaciones_realizadas = mysqli_query($con, $SQL);

		$tot_eva=0;

		while ($eva=mysqli_fetch_array($evaluaciones_realizadas)) {
			$tot_eva=$tot_eva + ($eva['evaluacion_sup']/100*$eva['ponderacion']);
		}

		$SQL="UPDATE evaluaciones_realizadas SET terminado_evaluador = 1, puntaje_objetivos = $tot_eva WHERE id_evaluacion_realizada = ".$id;
		mysqli_query($con, $SQL);
	}

	// Evaluación por factores

	if ($operacion=='carga_evaluador_factor') {
		$SQL="UPDATE evaluaciones_realizadas_factores SET evaluacion_sup ='".$evaluacion_sup."' WHERE id_evaluacion_objetivo = ".$id;
		mysqli_query($con, $SQL);
	}


	if ($operacion=='terminado_evaluador_factor') {

		$SQL="SELECT * FROM evaluaciones_realizadas_factores WHERE id_evaluacion_realizada = ".$id;
		$evaluaciones_realizadas = mysqli_query($con, $SQL);

		$tot_eva=0;

		while ($eva=mysqli_fetch_array($evaluaciones_realizadas)) {
			$tot_eva=$tot_eva + $eva['evaluacion_sup'];
		}

		$SQL="UPDATE evaluaciones_realizadas SET terminado_evaluador = 1, puntaje_factores = $tot_eva WHERE id_evaluacion_realizada = ".$id;
		mysqli_query($con, $SQL);
	}



 ?>