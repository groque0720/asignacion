<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();
$id_asesor = $_SESSION["id"];

$cantidad=25;

$SQL="SELECT * FROM recepcion WHERE derivado = 1 AND  id_asesor =".$id_asesor." AND guardado = 1 ORDER BY fecha DESC LIMIT $cantidad";
$recepcions=mysqli_query($con, $SQL);

include('recepcion_cuerpo_contenido.php');

 ?>