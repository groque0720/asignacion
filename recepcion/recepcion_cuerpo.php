<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$cantidad = 23;

$SQL="SELECT * FROM recepcion WHERE guardado = 1 AND id_sucursal = ".$_SESSION["idsuc"]." ORDER BY fecha DESC LIMIT $cantidad";
$recepcions=mysqli_query($con, $SQL);

include('recepcion_cuerpo_contenido.php');

 ?>