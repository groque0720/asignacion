<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

// $SQL="DELETE FROM recepcion WHERE id_recepcion = ".$id_recepcion;
// mysqli_query($con, $SQL);

$SQL=" UPDATE recepcion SET ";
$SQL .=" id_sucursal = ".$_POST['id_sucursal']." , ";
$SQL .=" fecha = '".$_POST['fecha']."' , ";
$SQL .=" hora = '".$_POST['hora']."' , " ;
$SQL .=" id_acercamiento = ".$_POST['id_acercamiento']." , ";
$SQL .=" motivo_no_compra = ".$_POST['motivo_no_compra']." , ";
$SQL .=" cliente = '".trim($_POST['cliente'])."' ,";
$SQL .=" telefono = '".trim($_POST['telefono'])."' ,";
$SQL .=" mail = '".trim($_POST['mail'])."' ,";
$SQL .=" id_provincia = ".$_POST['id_provincia']." ,";
$SQL .=" id_localidad = ".$_POST['id_localidad']." ,";
$SQL .=" id_grupo = ".$_POST['id_grupo']." ,";
$SQL .=" id_modelo = ".$_POST['id_modelo']." ,";
$SQL .=" id_asesor = ".$_POST['id_asesor']." ,";
$SQL .=" derivado = ".$_POST['derivado']." ,";
$SQL .=" visto = ".$_POST['visto']." ,";
$SQL .=" carga_registro = ".$_POST['carga_registro']." ,";
$SQL .=" seguimiento = ".$_POST['seguimiento']." ,";
// $SQL .=" vendido = ".$_POST['vendido']." ,";
if ($_POST['id_perfil']!='3') {
	$SQL .=" observacion = '".trim($_POST['observacion'])."' , ";
}else{
	$SQL .=" observacion_asesor = '".trim($_POST['observacion_asesor'])."' , ";	
}
$SQL .=" terminado = ".$_POST['terminado'].", ";
$SQL .=" guardado = 1 ";
$SQL .=" WHERE id_recepcion = ".$_POST['id_recepcion'];

mysqli_query($con, $SQL);

if ($_POST['id_perfil']!='3') {
	include('recepcion_cuerpo.php');
}else{
	include('recepcion_cuerpo_asesores.php');
}


mysqli_close($con);

 ?>
