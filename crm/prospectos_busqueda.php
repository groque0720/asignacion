<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$cantidad = 50;

if ($_SESSION["es_gerente"]==1) {

	$SQL="SELECT * FROM crm_prospectos_buscar WHERE (asesor LIKE '%" . $abuscar . "%' OR cliente LIKE '%" . $abuscar . "%') AND guardado = 1 ORDER BY fecha_carga DESC LIMIT $cantidad";
	$prospectos=mysqli_query($con, $SQL);

}else {

	$SQL="SELECT * FROM crm_prospectos_buscar WHERE guardado = 1 AND id_usuario = {$_SESSION["id"]}  AND cliente LIKE '%" . $abuscar . "%' ORDER BY fecha_carga DESC LIMIT $cantidad";
	$prospectos=mysqli_query($con, $SQL);
}

include('prospectos_contenido.php');

 ?>