
<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	//determino que movimiento viene del formulario para realizar la acción.
	$mov=$_POST["mov"];

	if ($mov == 1 ) {
		$SQL="INSERT INTO encuestas (encuesta, detalle, activo) VALUES";
		$SQL.=" ('".$_POST["encuesta"]."','".$_POST["detalle"]."',".$_POST["activo"].")";
	}

	if ($mov==2) {
		$SQL="UPDATE encuestas SET";
		$SQL.=" encuesta = '".$_POST["encuesta"]."',";
		$SQL.=" detalle = '".$_POST["detalle"]."',";
		$SQL.=" activo = ".$_POST["activo"].",";
		$SQL.=" baja = ".$_POST["baja"];
		$SQL.=" WHERE id_encuesta = ".$_POST["id_encuesta"];
	}

		mysqli_query($con, $SQL);

	mysqli_close($con);


?>