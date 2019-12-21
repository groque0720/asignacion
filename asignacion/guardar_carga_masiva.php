<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

if ($estado_tasa) {
	$estado=1;
}else{
	$estado=0;
}

$SQL="SELECT MAX(nro_unidad) as nro_unidad FROM asignaciones";
$unidades=mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($unidades);
$nro = (int)$unidad['nro_unidad'] + 1;

if ($reserva_gerencia) {

	for ($i=0; $i < $cantidad; $i++) { 

		$SQL="INSERT INTO asignaciones (nro_unidad, id_negocio, id_mes, año, id_grupo, id_modelo, estado_tasa, guardado, reservada, cliente, fec_reserva, id_asesor)
		 VALUES ($nro, 1, $id_mes, $año, $id_grupo, $id_modelo, $estado, 1, 1, 'RESERVADA EFV','".date("Y-m-d")."', 2)";
		mysqli_query($con, $SQL);
		$nro++;	
	}

} else {

	for ($i=0; $i < $cantidad; $i++) { 

		$SQL="INSERT INTO asignaciones (nro_unidad, id_negocio, id_mes, año, id_grupo, id_modelo, estado_tasa, guardado)
		 VALUES ($nro, 1, $id_mes, $año, $id_grupo, $id_modelo, $estado, 1)";
		mysqli_query($con, $SQL);
		$nro++;	
	}

}

$modelo_activo = $id_modelo;

$SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES($modelo_activo,'".date("Y-m-d")."')";
mysqli_query($con, $SQL);

include ('contenido_relleno.php');

?>