<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
// var_dump($_POST);

$SQL="UPDATE asignaciones_usados SET ";
$SQL.=" id_estado = ".$_POST["id_estado"].", ";
$SQL.=" id_estado_certificado = ".$_POST["id_estado_certificado"].", ";
$SQL.=" por = ".$_POST["por"].", ";
$SQL.=" km = ".$_POST["km"].",";

$SQL.=" vehiculo = '".$_POST["vehiculo"]."',";

$SQL.=" año = ".$_POST["año"].",";


$SQL.=" costo_reparacion = ".$_POST["costo_reparacion"].", ";


$SQL.=" toma_mas_impuesto = ".$_POST["toma_mas_impuesto"].", ";
$SQL.=" costo_contable = ".$_POST["costo_contable"].", ";
$SQL.=" precio_venta = ".$_POST["precio_venta"].", ";
$SQL.=" precio_info = ".$_POST["precio_info"].", ";
$SQL.=" transferencia = ".$_POST["transferencia"].", ";

$SQL.=" precio_contado = ".$_POST["precio_contado"].", ";
$SQL.=" precio_0km = ".$_POST["precio_0km"].", ";


$SQL.=" ultimo_dueño = '".$_POST["ultimo_dueño"]."', ";
$SQL.=" asesortoma = ".$_POST["asesortoma"].", ";

$SQL.=" dominio = '".strtoupper($_POST["dominio"])."',";

$SQL.=" interno = ".$_POST["interno"].",";
$SQL.=" color = ".$_POST["id_color"].",";
$SQL.=" id_sucursal = ".$_POST["id_sucursal"].",";

// $SQL.=" hora = '".$_POST["hora"]."',";

if ($_POST["hora"]!='') {
	$SQL.=" hora = '".$_POST["hora"]."', ";
}else{
	$SQL.=" hora = null ,";
}


if ($_POST["fec_recepcion"]!='') {
	$SQL.=" fec_recepcion = '".$_POST["fec_recepcion"]."', ";
}else{
	$SQL.=" fec_recepcion = null ,";
}


if ($_POST["fecha_cancelacion"]!='') {
	$SQL.=" fecha_cancelacion = '".$_POST["fecha_cancelacion"]."', ";
}else{
	$SQL.=" fecha_cancelacion = null ,";
}

if ($_POST["fec_entrega"]!='') {
	$SQL.=" fec_entrega = '".$_POST["fec_entrega"]."', ";
	$SQL.=" entregado = 1 , ";
}else{
	$SQL.=" fec_entrega = null ,";
	$SQL.=" entregado= 0 , ";
}

$SQL.=" nro_remito = '".$_POST["nro_remito"]."', ";


$SQL.=" estado_reserva = ".$_POST["estado_reserva"].",";

if ($_POST["fec_reserva"]!='') {
	$SQL.=" fec_reserva = '".$_POST["fec_reserva"]."', ";
	$SQL.=" reservada = 1, ";
}else{
	$SQL.=" fec_reserva = null ,";
	$SQL.=" reservada = 0, ";
}

$SQL.=" cliente = '".$_POST["cliente"]."',";
$SQL.=" id_asesor = ".$_POST["id_asesor"].",";


$SQL.=" observacion = '".$_POST["observacion"]."',";
$SQL.=" guardado = 1 ";
$SQL.=" WHERE id_unidad =".$_POST['id_unidad'];

mysqli_query($con, $SQL);




// $SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES($modelo_activo,'".date("Y-m-d")."')";
// mysqli_query($con, $SQL);

$cargado="si";

include ('carga_unidades_usados.php');

// if ($text_busqueda!='' AND $text_busqueda!=null ) {
// 	$abuscar=$text_busqueda;
// 	include ('busqueda_rapida_unidades_cuerpo.php');
// }else{
// 	include ('contenido_relleno.php');
// }

 ?>