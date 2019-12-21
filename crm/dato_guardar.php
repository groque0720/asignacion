<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

// $SQL="DELETE FROM recepcion WHERE id_recepcion = ".$id_recepcion;
// mysqli_query($con, $SQL);

$SQL=" UPDATE prospectos_clientes SET ";
$SQL .=" nombre = '".$_POST['nombre']."' , ";

if ($_POST["fec_nac"]!='') {
	$SQL.=" fec_nac = '".$_POST["fec_nac"]."', ";
}else{
	$SQL.=" fec_nac = null ,";
}
$SQL .=" id_usuario = ".$_POST['id_usuario']." , " ;
$SQL .=" id_sexo = ".$_POST['id_sexo']." , " ;
$SQL .=" nro_doc = '".$_POST['nro_doc']."' , ";
$SQL .=" cuil_cuit = '".trim($_POST['cuil_cuit'])."' ,";
$SQL .=" id_provincia = ".trim($_POST['id_provincia'])." ,";
$SQL .=" id_localidad = ".trim($_POST['id_localidad'])." ,";
$SQL .=" direccion = '".$_POST['direccion']."' ,";
$SQL .=" telefono = '".$_POST['telefono']."' ,";
$SQL .=" celular = '".$_POST['celular']."' ,";
$SQL .=" email = '".$_POST['email']."' ,";
$SQL .=" id_ocupacion = ".$_POST['id_ocupacion']." ,";
$SQL .=" id_pref_contacto = ".$_POST['id_pref_contacto']." ,";
$SQL .=" es_dato = ".$_POST['es_dato']." ,";
$SQL .=" id_estado_civil = ".$_POST['id_estado_civil']." ,";
$SQL .=" id_grupo_familiar = ".$_POST['id_grupo_familiar']." ,";
$SQL .=" cant_hijos = '".$_POST['cant_hijos']."' ,";
$SQL .=" alta_desde_prospecto = ".$_POST['alta_desde_prospecto']." ,";
$SQL .=" id_prospecto_alta = ".$_POST['id_prospecto_alta']." ,";
$SQL .=" observacion = '".trim($_POST['observacion'])."' , ";
$SQL .=" guardado = 1 ";
$SQL .=" WHERE id = ".$_POST['id'];

mysqli_query($con, $SQL);

if ($id_prospecto=='') { 
	include('datos.php');
}

mysqli_close($con);

 ?>
