<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	$opcion=$_GET["opcion"];
	$elegido=$_GET["elegido"];
	$id_pregunta=$_GET["id"];


	//-----------------cambio de formato

	if ($opcion=="act_sirespuesta") {
		$SQL="UPDATE encuestas_preguntas SET";
		$SQL.=" si_respuesta = ".$elegido;
		$SQL.=" WHERE id_pregunta = ".$id_pregunta;

		echo $elegido;
	};

	if ($opcion=="act_prox_preg") {
		$SQL="UPDATE encuestas_preguntas SET";
		$SQL.=" proxima_pregunta = '".$elegido."'";
		$SQL.=" WHERE id_pregunta = ".$id_pregunta;

		echo $elegido;
	};
		mysqli_query($con, $SQL);


?>


<?php mysqli_close($con); ?>