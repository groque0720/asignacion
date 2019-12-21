<?php

include("../_seguridad/_seguridad.php");
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

if ($_POST["cad"]!=0) {
	$SQL="SELECT * FROM cuestionarios WHERE activo=1  AND id_estado_cuestionario <> 3 AND id_encuesta=".$_POST["cad"]." ORDER BY fecha_muestra_origen LIMIT 400";
	$res=mysqli_query($con, $SQL);
}else{
	$SQL="SELECT * FROM cuestionarios WHERE activo=1   AND id_estado_cuestionario  <> 3 ORDER BY fecha_muestra_origen LIMIT 400";
	$res=mysqli_query($con, $SQL);
}

 ?>

 <?php include("cuestionario_lista_cuerpo.php"); ?>
 <?php 	mysqli_close($con);	 ?>