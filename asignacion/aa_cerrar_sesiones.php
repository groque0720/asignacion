<?php

	// include("funciones/func_mysql.php");
	// conectar();
	// mysqli_query($con,"SET NAMES 'utf8'");

	$SQL = "SELECT estado FROM asignacion_estado WHERE id = 1";
	$result=mysqli_query($con, $SQL);
	$habilitado=mysqli_fetch_array($result);

	if ($habilitado['estado'] == 0) {

		if($_SESSION["id"] != 71)
		{
			if ($_SESSION["idperfil"] != 14 or $_SESSION["id"] == 14) {
				echo '<script>	window.location.href = "../asignacion/index_.php";</script>';
			}
		}

	}

?>