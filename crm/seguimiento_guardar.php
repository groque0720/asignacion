<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

// $SQL="DELETE FROM recepcion WHERE id_recepcion = ".$id_recepcion;
// mysqli_query($con, $SQL);

$SQL=" UPDATE prospectos_seguimientos SET ";
$SQL .=" id_prospecto = ".$_POST['id_prospecto'].", ";
$SQL .=" fec_contacto = '".$_POST['fec_contacto']."' , ";
$SQL .=" id_tipo_contacto = ".$_POST['id_tipo_contacto']." , ";
$SQL .=" hora = '".$_POST['hora']."' , " ;
$SQL .=" realizado = ".$_POST['realizado']." , ";
if (isset($_POST['fec_realizado'])) {
	$SQL .=" fec_realizado = '".$_POST['fec_realizado']."' ,";
}
if (isset($_POST['id_resultado'])) {
	$SQL .=" id_resultado = ".$_POST['id_resultado']." ,";
}
$SQL .=" observacion = '".trim($_POST['observacion'])."' ,";
$SQL .=" guardado = 1 ";
$SQL .=" WHERE id = ".$_POST['id'];

mysqli_query($con, $SQL);

$prospecto['id'] = $_POST['id_prospecto'];

include('prospecto_formulario_seguimiento.php');


mysqli_close($con);

 ?>

