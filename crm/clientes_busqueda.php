<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$cantidad = 1000;

if ($_SESSION["es_gerente"]==1) {

	$SQL="SELECT * FROM crm_clientes_buscar WHERE (celular LIKE '%" . $abuscar . "%' OR telefono LIKE '%" . $abuscar . "%' OR nombre LIKE '%" . $abuscar . "%' OR localidad LIKE '%" . $abuscar . "%') AND id_estado_cliente >= 2 AND guardado = 1 ORDER BY nombre DESC LIMIT $cantidad";
	$clientes=mysqli_query($con, $SQL);

}else{
	$SQL="SELECT * FROM crm_clientes_buscar WHERE (celular LIKE '%" . $abuscar . "%' OR telefono LIKE '%" . $abuscar . "%' OR nombre LIKE '%" . $abuscar . "%' OR localidad LIKE '%" . $abuscar . "%') AND id_estado_cliente >= 2 AND guardado = 1 AND id_usuario = {$_SESSION["id"]} ORDER BY nombre DESC LIMIT $cantidad";
	$clientes=mysqli_query($con, $SQL);
}
include('clientes_contenido.php');

 ?>