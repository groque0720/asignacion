<?php
include ("../includes/security.php");?>
<?php
	set_time_limit(300);
	include("../funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL = "SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL); 

	$codigo=$_GET["codigo"];



	while ($modelo=mysqli_fetch_array($modelos)) {


		$SQL = "SELECT * FROM listaprecio WHERE idmodelo = ".$modelo['idmodelo'] ." AND activo = 1";
		$listas = mysqli_query($con, $SQL);
		$lista=mysqli_fetch_array($listas);

		if ($codigo == 3) {
			echo ($modelo['idmodelo'].' '.$modelo['modelo'].' '.$lista['pl'])."<br>";
		}


		if ($codigo == 1) {

			$SQL = "UPDATE lineas_detalle
							INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
							SET monto = ".$lista['pl']." 
							WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 1";
			mysqli_query($con, $SQL);
		}

		if ($codigo == 2) {
			$SQL = "UPDATE lineas_detalle
					INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
					SET monto = ".$lista['flete']." 
					WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 2";
			mysqli_query($con, $SQL);
		}


		if ($codigo == 3) {
			$SQL = "UPDATE lineas_detalle
					INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
					SET monto = ".$lista['trans']." 
					WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 3";
			mysqli_query($con, $SQL);
		}

	}

	if ($codigo == 1) {
	header("Location: /ventas/_admin/precios_actualizar_reservas.php?codigo=2");
	}

	if ($codigo == 2) {
	header("Location: /ventas/_admin/precios_actualizar_reservas.php?codigo=3");
	}

// exit();
 
?>