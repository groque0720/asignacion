<?php 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);


$SQL="SELECT * FROM view_recepcion WHERE (
sucursal LIKE '%" . $abuscar . "%' OR
acercamiento LIKE '%" . $abuscar . "%' OR 
localidad LIKE '%" . $abuscar . "%' OR
provincia LIKE '%" . $abuscar . "%' OR 
asesor LIKE '%" . $abuscar . "%' OR
cliente LIKE '%" . $abuscar . "%') ORDER BY fecha DESC";


$recepcions=mysqli_query($con, $SQL);
include('recepcion_cuerpo_contenido.php'); 
?>
