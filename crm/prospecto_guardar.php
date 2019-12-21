<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);


$SQL="SELECT id_estado_cliente FROM prospectos_clientes WHERE id = ".$_POST['id_cliente'];
$clientes=mysqli_query($con, $SQL);
$cliente = mysqli_fetch_array($clientes);

if ($cliente['id_estado_cliente']==1) {
	$SQL="UPDATE prospectos_clientes SET id_estado_cliente = 2 WHERE id =".$_POST['id_cliente'];
	mysqli_query($con, $SQL);
}


$SQL="UPDATE prospectos_seguimientos SET guardado_de_prospecto = 1 WHERE id_prospecto =".$_POST['id'];
mysqli_query($con, $SQL);


$SQL=" UPDATE prospectos SET ";
$SQL .=" id_cliente = ".$_POST['id_cliente'].", ";
$SQL .=" id_sucursal = ".$_POST['id_sucursal'].", ";
$SQL .=" fecha_alta = '".$_POST['fecha_alta']."' , ";
$SQL .=" id_usuario = '".$_POST['id_usuario']."' , ";
$SQL .=" id_modelo_tpa = ".$_POST['id_modelo_tpa']." , ";
$SQL .=" id_modelo = ".$_POST['id_modelo']." , " ;
$SQL .=" id_version = ".$_POST['id_version']." , ";
$SQL .=" id_modo_acercamiento = ".$_POST['id_modo_acercamiento']." ,";
$SQL .=" id_canal_acercamiento = ".$_POST['id_canal_acercamiento']." ,";
$SQL .=" id_ponderacion = '".$_POST['id_ponderacion']."' , ";

if ($_POST["fecha_cierre"]!='') {
	$SQL.=" fecha_cierre = '".$_POST["fecha_cierre"]."', ";
	$SQL .=" cerrado = 1 , ";
}else{
	$SQL.=" fecha_cierre = null ,";
	$SQL .=" cerrado = 0 , ";
}

$SQL .=" id_motivo_cierre = ".$_POST['id_motivo_cierre']." ,";
$SQL .=" observacion = '".trim($_POST['observacion'])."' ,";
$SQL .=" guardado = 1 ";
$SQL .=" WHERE id = ".$_POST['id'];

mysqli_query($con, $SQL);

// $prospecto['id'] = $_POST['id_prospecto'];
 
include('prospectos.php');

mysqli_close($con);

 ?>

