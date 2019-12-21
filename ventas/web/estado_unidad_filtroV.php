<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

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

			datosasignacion.nrounidad AS nrounidad,
			datosasignacion.asigmes AS asigmes,
			datosasignacion.asigano AS asigano,
			datosasignacion.interno AS interno,
			datosasignacion.nroorden AS nroorden,
			datosasignacion.fechaplaya AS fechaplaya,
			datosasignacion.fechaarribo AS fechaarribo,
			datosasignacion.fechaarribof AS fechaarribof,
			datosasignacion.modelo AS modelo,
			datosasignacion.cliente AS cliente,
			datosasignacion.asesor AS asesor,
			datosasignacion.costo AS costo,
			datosasignacion.sepago AS sepago,
			datosasignacion.confirmada AS confirmada
			FROM
			datosasignacion	".$fil."  ".$fil_d;

			$res=mysqli_query($con, $SQL);
			include('estado_unidad_cuerpo.php');


		}else{


		$SQL="SELECT
				datosasignacion.nrounidad AS nrounidad,
				datosasignacion.nroorden AS nroorden,
				datosasignacion.asigmes AS asigmes,
				datosasignacion.asigano AS asigano,
				datosasignacion.interno AS interno,
				datosasignacion.fechaplaya AS fechaplaya,
				datosasignacion.fechaarribo AS fechaarribo,
				datosasignacion.modelo AS modelo,
				datosasignacion.asesor AS asesor,
				datosasignacion.costo AS costo,
				clientes.nombre AS cliente,
				reservas.fechacanc AS fechacanc,
				datosasignacion.sepago AS sepago,
				datosasignacion.confirmada AS confirmada
				FROM
				reservas
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				Inner Join datosasignacion ON reservas.nrounidad = datosasignacion.nrounidad
				WHERE
				reservas.anulada <> 1  AND reservas.entregada < 3 AND reservas.enviada >= '1' AND
				datosasignacion.fechaarribo IS NOT NULL  AND
				datosasignacion.confirmada =  '-1' AND
				datosasignacion.cancelada =  '0'
				ORDER BY
				fechacanc";

				$res=mysqli_query($con, $SQL);
				include('estado_unidad_cuerpo.php');

		}
 ?>