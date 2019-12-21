<?php
	include("funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");



	if ("alta_evento"==$_POST["operacion"]) {
		$nro=1;

		$rs = mysql_query("SELECT MAX(nro) AS nro FROM eventos WHERE activo = 1");
		if ($row = mysql_fetch_row($rs)) {
			$nro = trim($row[0])+1;
		}
		$SQL="INSERT INTO eventos (nro, activo) VALUES ($nro, '0')";
		mysqli_query($con, $SQL);
		$rs = mysql_query("SELECT MAX(id_evento) AS id FROM eventos");
		if ($row = mysql_fetch_row($rs)) {
			echo trim($row[0]);
		}
	}

	if ("Eliminar_imagen"==$_POST["operacion"]) {
		// $SQL="UPDATE imagenes SET (activo='0') WHERE id_img=".$_POST["id_img"];
		$url = $_POST["url"];
		unlink($url);
		$SQL="DELETE FROM imagenes WHERE id_img=".$_POST["id_img"];
		mysqli_query($con, $SQL);
	}

	if ("editar"==$_POST["operacion"]) {

	$SQL = "UPDATE eventos SET";
	$SQL .=" titulo ='".$_POST["titulo"]."', ";
	$SQL .=" ubicacion ='".$_POST["ubicacion"]."', ";
	$SQL .=" fecha_inicio ='".$_POST["fecha_inicio"]."', ";
	$SQL .=" fecha_fin ='".$_POST["fecha_fin"]."', ";
	$SQL .=" asistentes ='".$_POST["asistentes"]."', ";
	$SQL .=" contactos ='".$_POST["contactos"]."', ";
	$SQL .=" ventas ='".$_POST["ventas"]."', ";
	$SQL .=" detalle ='".$_POST["detalle"]."', ";
	$SQL .=" activo = 1, ";
	$SQL .=" negocio ='".$_POST["negocio"]."' ";
	$SQL .=" WHERE id_evento =".$_POST["id_evento"];
	mysqli_query($con, $SQL);

	$SQL="UPDATE imagenes SET activo=1 WHERE id_evento=".$_POST["id_evento"];
	mysqli_query($con, $SQL);

	header("Location: index.php");
	}


	mysqli_close($con);

 ?>