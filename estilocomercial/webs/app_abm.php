
<?php

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");

	//determino que movimiento viene del formulario para realizar la acción.
	$mov=$_POST["mov"];

	if ($mov == 1 ) {
		$SQL="INSERT INTO aplicaciones (aplicacion, url, activo) VALUES";
		$SQL.=" ('".$_POST["aplicacion"]."','".$_POST["url"]."',".$_POST["activo"].")";
	}

	if ($mov==2) {
		$SQL="UPDATE aplicaciones SET";
		$SQL.=" aplicacion = '".$_POST["aplicacion"]."',";
		$SQL.=" url = '".$_POST["url"]."',";
		$SQL.=" activo = ".$_POST["activo"].",";
		$SQL.=" baja = ".$_POST["baja"];
		$SQL.=" WHERE id_app = ".$_POST["id_app"];
	}

		mysqli_query($con, $SQL);

	mysqli_close($con);


?>

