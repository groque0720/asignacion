<?php
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	// idusuario = 56 = Mauro Vargas
	// idusuario = 11 = Ruky Guerra
	// Federico  Rescala = 45 y 51
	// Luis Gutierrez  = 94
	// vargas Fredy = 41

	// $SQL="UPDATE notificaciones SET visto = 1 WHERE idusuario = 56";
	// mysqli_query($con, $SQL);

	$SQL="DELETE FROM notificaciones WHERE idnotificaciones < 1146818";
	mysqli_query($con, $SQL);

	// $SQL="DELETE FROM sesiones WHERE id < 170464";
	// mysqli_query($con, $SQL);

?>