<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	ini_set('max_execution_time', 300);

	$fil="";
	$fil_d ="";
	$idfiltro=$_POST["id"];

	if ($_POST["id"] == 1) {
		$fil = " WHERE sepago = 0 AND datosasignacion.nroorden is not null AND datosasignacion.fechaplaya is not null";
		$fil_d = " ORDER BY datosasignacion.fechaplayaf, nroorden";
	}
	if ($_POST["id"] == 3) {
		$fil = " WHERE datosasignacion.fechaarribo is not null AND datosasignacion.fechaplaya is not null";
		$fil_d = " ORDER BY datosasignacion.fechaplayaf";
	}

	if ($_POST["id"] == 4) {
		$fil = "";
		$fil_d = " ORDER BY datosasignacion.fechaarribof";
	}

	if ($_POST["id"] != 2) {

			$SQL="SELECT
			nro_unidad AS nrounidad,
			id_mes AS asigmes,
			año AS asigano,
			interno AS interno,
			nro_orden AS nroorden,
			fec_playa AS fechaplaya,
			fec_arribo AS fechaarribo,
			modelo AS modelo,
			cliente AS cliente,
			asesor AS asesor,
			costo AS costo,
			pagado AS sepago,
			FROM
			view_asignaciones_nopagadas ";

			$res=mysqli_query($con, $SQL);
			include('estado_unidad_cuerpo.php');


		}else{


		// $SQL="SELECT
		// 		datosasignacion.nrounidad AS nrounidad,
		// 		datosasignacion.nroorden AS nroorden,
		// 		datosasignacion.asigmes AS asigmes,
		// 		datosasignacion.asigano AS asigano,
		// 		datosasignacion.interno AS interno,
		// 		datosasignacion.fechaplaya AS fechaplaya,
		// 		datosasignacion.fechaarribo AS fechaarribo,
		// 		datosasignacion.modelo AS modelo,
		// 		datosasignacion.asesor AS asesor,
		// 		datosasignacion.costo AS costo,
		// 		clientes.nombre AS cliente,
		// 		reservas.fechacanc AS fechacanc,
		// 		datosasignacion.sepago AS sepago,
		// 		datosasignacion.confirmada AS confirmada
		// 		FROM
		// 		reservas
		// 		Inner Join clientes ON reservas.idcliente = clientes.idcliente
		// 		Inner Join datosasignacion ON reservas.nrounidad = datosasignacion.nrounidad
		// 		WHERE
		// 		reservas.anulada <> 1  AND reservas.entregada < 3 AND reservas.enviada >= '1' AND
		// 		datosasignacion.fechaarribo IS NOT NULL  AND
		// 		datosasignacion.confirmada =  '-1' AND
		// 		datosasignacion.cancelada =  '0'
		// 		ORDER BY
		// 		fechacanc";
		//
		//
				$SQL="SELECT * FROM view_asignaciones_recursos";
				$res=mysqli_query($con, $SQL);
				include('estado_unidad_cuerpo.php');
		}
 ?>