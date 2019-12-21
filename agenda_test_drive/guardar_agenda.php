<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

if (isset($borrar)) {

	$SQL="UPDATE agenda_td_lineas SET cliente = '', telefono = '' ,id_asesor = 1, realizo = 0 WHERE id_linea=".$id_linea;
}else{
		$SQL="UPDATE agenda_td_lineas SET cliente = '".strtoupper($cliente)."', telefono = '".$telefono."' ,id_asesor = ".$id_asesor.", realizo = ".$realizo." WHERE id_linea=".$id_linea;
}
mysqli_query($con, $SQL);

$SQL="SELECT * FROM agenda_td_lineas WHERE id_sucursal = $sucursal AND id_modelo = $modelo AND fecha = '$fecha'";
$res=mysqli_query($con, $SQL);

include("tablero_agenda_cuerpo.php");

?>