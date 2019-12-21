<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$cantidad = 50;

// $SQL="SELECT * FROM prospectos WHERE guardado = 1 ORDER BY fecha_carga DESC LIMIT $cantidad";
// $prospectos=mysqli_query($con, $SQL);

if ($_SESSION["es_gerente"]==1) {

	$SQL="SELECT * FROM view_prospectos_seguimientos WHERE guardado = 1 AND realizado = 0 ORDER BY fec_contacto LIMIT $cantidad";
	$seguimientos=mysqli_query($con, $SQL);

}else{

	$SQL="SELECT * FROM view_prospectos_seguimientos WHERE guardado = 1 AND realizado = 0 AND id_usuario = {$_SESSION["id"]} ORDER BY fec_contacto LIMIT $cantidad";
	$seguimientos=mysqli_query($con, $SQL);

}

include('agenda_contacto_contenido.php');

 ?>