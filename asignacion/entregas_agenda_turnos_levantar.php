<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();


$SQL="DELETE FROM agenda_entregas_turnos WHERE id = ".$id;
mysqli_query($con,$SQL);


$SQL="UPDATE asignaciones SET ";
	$SQL.=" fec_pedido = null, ";
	$SQL.=" hora_pedido = null";
	$SQL .=" WHERE nro_unidad = ".$nro_unidad;
	mysqli_query($con,$SQL);


include('entregas_agenda_contenido_relleno.php');

 ?>
