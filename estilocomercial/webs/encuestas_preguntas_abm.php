
<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	//determino que movimiento viene del formulario para realizar la acción.
	$mov=$_POST["mov"];

	if ($mov == 1 ) {
		$SQL="INSERT INTO encuestas_preguntas";
		$SQL.=" (id_encuesta, nro_pregunta, pregunta, id_formato_respuesta, id_tipo_respuesta, si_respuesta,  proxima_pregunta, activo) VALUES";
		$SQL.=" (".$_POST["id_encuesta"].",".$_POST["nro_pregunta"].",'".$_POST["pregunta"]."',".$_POST["id_formato_respuesta"].",".$_POST["id_tipo_respuesta"].",".$_POST["si_respuesta"].",".$_POST["proxima_pregunta"].",".$_POST["activo"].")";
	}

	if ($mov==2) {
		$SQL="UPDATE encuestas_preguntas SET";
		$SQL.=" id_encuesta = ".$_POST["id_encuesta"].",";
		$SQL.=" nro_pregunta = ".$_POST["nro_pregunta"].",";
		$SQL.=" pregunta= '".$_POST["pregunta"]."',";
		$SQL.=" id_formato_respuesta = ".$_POST["id_formato_respuesta"].",";
		$SQL.=" id_tipo_respuesta = ".$_POST["id_tipo_respuesta"].",";
		$SQL.=" si_respuesta = ".$_POST["si_respuesta"].",";
		$SQL.=" proxima_pregunta = '".$_POST["proxima_pregunta"]."',";
		$SQL.=" activo = ".$_POST["activo"].",";
		$SQL.=" baja = ".$_POST["baja"];
		$SQL.=" WHERE id_pregunta = ".$_POST["id_pregunta"];
	}

		mysqli_query($con, $SQL);

	mysqli_close($con);


?>