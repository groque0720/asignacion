<?php

	require_once(__DIR__ . '/funciones/guard_clave.php');
	guard_clave();

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL = "SELECT estado FROM asignacion_estado WHERE id = 1";
	$result=mysqli_query($con, $SQL);
	$habilitado=mysqli_fetch_array($result);

	if ($habilitado['estado'] == 1) {
		$SQL="UPDATE asignacion_estado SET estado = 0";
		mysqli_query($con, $SQL);
		echo "La planilla de Asignación está <span style='color:red;'>**** DESHABILITADA ****</span>";
	}else{
		$SQL="UPDATE asignacion_estado SET estado = 1";
		mysqli_query($con, $SQL);
		echo "La planilla de Asignación está <span style='color:green;'>++++ HABILITADA ++++</span>";
	}
?>