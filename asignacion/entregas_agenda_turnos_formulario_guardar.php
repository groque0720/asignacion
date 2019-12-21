<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$SQL="SELECT * FROM agenda_entregas_horarios WHERE id = $id_turno";
$horas=mysqli_query($con, $SQL);
$hora = mysqli_fetch_array($horas);

$SQL="INSERT INTO agenda_entregas_turnos (fecha, id_horario, nro_unidad, id_sucursal) VALUES ('$fecha', $id_turno, $nro_unidad, $id_sucursal)";
mysqli_query($con, $SQL);


$SQL="UPDATE asignaciones SET ";
	$SQL.=" fec_pedido = '$fecha', ";
	$SQL.=" hora_pedido = '".$hora['hora_inicial']."'";
	$SQL .=" WHERE nro_unidad = ".$nro_unidad;
	mysqli_query($con,$SQL);


//echo $SQL;
// echo $hora['hora_inicial']. ' '.$nro_unidad;
// UPDATE asignaciones SET fec_pedido = '2017-02-11', hora_pedido = '09:00:00' WHERE id_unidad = 11990
include('entregas_agenda_contenido_relleno.php');

 ?>
