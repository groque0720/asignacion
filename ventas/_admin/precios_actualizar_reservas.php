<?php
include ("../includes/security.php");?>
<?php
	set_time_limit(300);
	include("../funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL = "SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL); 

	while ($modelo=mysqli_fetch_array($modelos)) {


		$SQL = "SELECT * FROM listaprecio WHERE idmodelo = ".$modelo['idmodelo'] ." AND activo = 1";
		$listas = mysqli_query($con, $SQL);
		$lista=mysqli_fetch_array($listas);
		echo ($modelo['idmodelo'].' '.$modelo['modelo'].' '.$lista['pl'])."<br>";

		$SQL = "UPDATE lineas_detalle
						INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
						SET monto = ".$lista['pl']." 
						WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 1";
		mysqli_query($con, $SQL);

		$SQL = "UPDATE lineas_detalle
				INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
				SET monto = ".$lista['flete']." 
				WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 2";
		mysqli_query($con, $SQL);

		$SQL = "UPDATE lineas_detalle
				INNER JOIN reservas_actualizacion_precios AS reservas ON lineas_detalle.idreserva = reservas.idreserva
				SET monto = ".$lista['trans']." 
				WHERE reservas.idmodelo = ".$modelo['idmodelo']." AND lineas_detalle.idcodigo = 3";
		mysqli_query($con, $SQL);

	}
 
?>