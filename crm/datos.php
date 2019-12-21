<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$cantidad = 50;


if ($_SESSION["es_gerente"]==1) {

	$SQL="SELECT * FROM prospectos_clientes WHERE id_estado_cliente = 1 AND guardado = 1 ORDER BY nombre DESC LIMIT $cantidad";
	$clientes=mysqli_query($con, $SQL);

}else{
	$SQL="SELECT * FROM prospectos_clientes WHERE id_estado_cliente = 1 AND guardado = 1 AND id_usuario = {$_SESSION["id"]} ORDER BY nombre DESC LIMIT $cantidad";
	$clientes=mysqli_query($con, $SQL);
}

include('datos_contenido.php');

?>
