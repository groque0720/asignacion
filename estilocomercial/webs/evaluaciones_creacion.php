<?php

set_time_limit(300);

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");

	extract($_POST);

	$SQL="INSERT INTO evaluaciones (periodo, fecha) VALUES ('$periodo','$fecha')";
	mysqli_query($con, $SQL);

	$rs = mysql_query("SELECT MAX(id_evaluacion) AS id FROM evaluaciones LIMIT 1");
	if ($row = mysql_fetch_row($rs)) {
		$id_evaluacion= trim($row[0]);
	}
	//----- busco asesores
	$SQL="SELECT * FROM usuarios WHERE id_perfil = 2 AND activo = 1";
	$usuarios = mysqli_query($con, $SQL);

	while ( $usu=mysqli_fetch_array($usuarios)) {

		$SQL="INSERT INTO evaluaciones_realizadas (id_evaluacion, id_usuario) VALUES (".$id_evaluacion.",".$usu['id_usuario'].")";
		mysqli_query($con, $SQL);

			$rs = mysql_query("SELECT MAX(id_evaluacion_realizada) AS id FROM evaluaciones_realizadas LIMIT 1");

			if ($row = mysql_fetch_row($rs)) {
				$id_evaluacion_realizada= trim($row[0]);
			}

			$SQL="SELECT * FROM evaluacion_o_preguntas";
			$preguntas=mysqli_query($con, $SQL);

			while ($preg=mysqli_fetch_array($preguntas)) {

				$SQL="INSERT INTO evaluaciones_realizadas_objetivos (id_evaluacion_realizada, id_evaluacion_o, nro_objetivo, ponderacion, objetivo) VALUES";
				$SQL .=" ('".$id_evaluacion_realizada."','".$preg['id_evaluacion_o']."','".$preg['nro_objetivo']."','".$preg['ponderacion']."','".$preg['objetivo']."')";
				mysqli_query($con, $SQL);
			}

			$SQL="SELECT * FROM evaluacion_f_lineas";
			$preguntas=mysqli_query($con, $SQL);

			while ($preg=mysqli_fetch_array($preguntas)) {

				$SQL="INSERT INTO evaluaciones_realizadas_factores (id_evaluacion_realizada, id_evaluacion_f, id_evaluacion_f_linea, factor) VALUES";
				$SQL .=" ('".$id_evaluacion_realizada."','".$preg['id_evaluacion_f']."','".$preg['id_evaluacion_f_lineas']."','".$preg['opcion_factor']."')";
				mysqli_query($con, $SQL);
			}

	}


?>

<?php include("evaluaciones_panel_cuerpo.php") ?>