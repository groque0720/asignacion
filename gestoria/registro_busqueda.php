<?php


//conecto con una base de datos
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

if ($det!=null AND $det!='') {

		$SQL="SELECT * FROM view_registros_gestoria WHERE nombre LIKE '%".$det."%' OR nro_leg LIKE '%".$det."%' OR interno LIKE '%".$det."%' OR asesor LIKE '%".$det."%' OR sucursal LIKE '%".$det."%' OR patente LIKE '%".$det."%' OR localidad LIKE '%".$det."%' ORDER BY id_reg_gestoria DESC LIMIT 200";
	$res_reg = mysqli_query($con, $SQL);
	include('lista_tramites.php');

}else{
	$SQL="SELECT * FROM view_registros_gestoria ORDER BY id_reg_gestoria DESC LIMIT 200";
				//$SQL="SELECT * FROM view_registros_gestoria ORDER BY id_reg_gestoria DESC";
	$res_reg = mysqli_query($con, $SQL);
	include('lista_tramites.php');

}

mysqli_close($con); 

?>