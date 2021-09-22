<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);


if (isset($estado_tasa)) {
	$estado=1;
}else{
	$estado=0;
}

if (isset($no_disponible)) {
	$no_disponible_=1;
}else{
	$no_disponible_=0;
}


$SQL="SELECT MAX(nro_unidad) as nro_unidad FROM asignaciones";
$unidades=mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($unidades);
$nro = (int)$unidad['nro_unidad'] + 1;

if (isset($reserva_gerencia)) {

	for ($i=0; $i < $cantidad; $i++) {

		$SQL="INSERT INTO asignaciones (nro_unidad, id_negocio, id_mes, año, id_grupo, id_modelo, estado_tasa, guardado, reservada, cliente, fec_reserva, id_asesor, no_disponible)
		 VALUES ($nro, 1, $id_mes, $año, $id_grupo, $id_modelo, $estado, 1, 1, 'RESERVADA EFV','".date("Y-m-d")."', 2,$no_disponible_)";
		mysqli_query($con, $SQL);
		$nro++;
	}

}else {
	if (trim($reserva_cliente) != '') {
		$reservada = 1;
		$fec_reserva =  ', fec_reserva';
		$fecha = ", '".date("Y-m-d")."'";
	}else{
		$reservada = 0;
		$fec_reserva =  '';
		$fecha = '';
	}
	for ($i=0; $i < $cantidad; $i++) {

		$SQL="INSERT INTO asignaciones (nro_unidad, id_negocio, id_mes, año, id_grupo, id_modelo, estado_tasa, guardado, no_disponible,id_asesor,cliente, reservada ".$fec_reserva.")
		 VALUES ($nro, 1, $id_mes, $año, $id_grupo, $id_modelo, $estado, 1, $no_disponible_, $id_asesor, '$reserva_cliente',$reservada ".$fecha.")";
		mysqli_query($con, $SQL);
		$nro++;
	}

}

$modelo_activo = $id_modelo;

$SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES($modelo_activo,'".date("Y-m-d")."')";
mysqli_query($con, $SQL);

include ('contenido_relleno.php');

?>