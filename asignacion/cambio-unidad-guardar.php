<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="SELECT * FROM asignaciones WHERE nro_unidad = ".$id_nro_uno;
$unidades_uno=mysqli_query($con, $SQL);
$uni_uno=mysqli_fetch_array($unidades_uno);

$id_modelo=$uni_uno['id_modelo'];
$unidad_a['color_uno']=$uni_uno['color_uno'];
$unidad_a['color_dos']=$uni_uno['color_dos'];
$unidad_a['color_tres']=$uni_uno['color_tres'];
$unidad_a['id_asesor']=$uni_uno['id_asesor'];
$unidad_a['cliente']=$uni_uno['cliente'];
$unidad_a['fec_reserva']=$uni_uno['fec_reserva'];
$unidad_a['pagado']=$uni_uno['pagado'];
$unidad_a['hora']=$uni_uno['hora'];
$unidad_a['estado_reserva']=$uni_uno['estado_reserva'];
$unidad_a['reservada']=$uni_uno['reservada'];
$unidad_a['fec_cancelacion']=$uni_uno['fec_cancelacion'];
$unidad_a['cancelada']=$uni_uno['cancelada'];
$unidad_a['id_sucursal']=$uni_uno['id_sucursal'];
$unidad_a['reventa']=$uni_uno['reventa'];


$SQL="SELECT * FROM asignaciones WHERE nro_unidad = ".$id_nro_dos;
$unidades_dos=mysqli_query($con, $SQL);
$uni_dos=mysqli_fetch_array($unidades_dos);

$SQL="UPDATE asignaciones SET ";

//$SQL.=" pagado = ".$uni_dos["pagado"].", ";

$SQL.=" reventa = ".$uni_dos["reventa"].", ";

if ($uni_dos["estado_reserva"]!=0 AND $uni_dos["estado_reserva"]!='' AND $uni_dos["estado_reserva"]!=null) {
	$SQL.=" estado_reserva = ".$uni_dos["estado_reserva"].", ";
}else{
	$SQL.=" estado_reserva = 0 ,";
}

if ($uni_dos["fec_reserva"]!='' AND $uni_dos["fec_reserva"]!=null) {
	$SQL.=" fec_reserva = '".$uni_dos["fec_reserva"]."', ";
}else{
	$SQL.=" fec_reserva = null ,";
}

if ($uni_dos["fec_cancelacion"]!='' AND $uni_dos["fec_cancelacion"]!=null) {
	$SQL.=" fec_cancelacion = '".$uni_dos["fec_cancelacion"]."', ";
}else{
	$SQL.=" fec_cancelacion = null ,";
}

$SQL.=" fec_limite = null ,";


if ($uni_dos["id_sucursal"]!=0 AND $uni_dos["id_sucursal"]!='' AND $uni_dos["id_sucursal"]!=null) {
	$SQL.=" id_sucursal = ".$uni_dos["id_sucursal"].", ";
}else{
	$SQL.=" id_sucursal = 0 ,";
}


if ($uni_dos["cancelada"]!=0 AND $uni_dos["cancelada"]!='' AND $uni_dos["cancelada"]!=null) {
	$SQL.=" cancelada = ".$uni_dos["cancelada"].", ";
}else{
	$SQL.=" cancelada = 0 ,";
}


if ($uni_dos["hora"]!='' AND $uni_dos["hora"]!=null) {
	$SQL.=" hora = '".$uni_dos["hora"]."', ";
}else{
	$SQL.=" hora = null ,";
}

$SQL.=" cliente = '".$uni_dos["cliente"]."',";


if ($uni_dos["id_asesor"]!=0 AND $uni_dos["id_asesor"]!='' AND $uni_dos["id_asesor"]!=null) {
	$SQL.=" id_asesor = ".$uni_dos["id_asesor"].", ";
}else{
	$SQL.=" id_asesor = 0 ,";
}

if ($uni_dos["color_uno"]!=0 AND $uni_dos["color_uno"]!='' AND $uni_dos["color_uno"]!=null) {
	$SQL.=" color_uno = ".$uni_dos["color_uno"].", ";
}else{
	$SQL.=" color_uno = 0 ,";
}

if ($uni_dos["color_dos"]!=0 AND $uni_dos["color_dos"]!='' AND $uni_dos["color_dos"]!=null) {
	$SQL.=" color_dos = ".$uni_dos["color_dos"].", ";
}else{
	$SQL.=" color_dos = 0 ,";
}

if ($uni_dos["color_tres"]!=0 AND $uni_dos["color_tres"]!='' AND $uni_dos["color_tres"]!=null) {
	$SQL.=" color_tres = ".$uni_dos["color_tres"].", ";
}else{
	$SQL.=" color_tres = 0 ,";
}

if ($uni_dos["reservada"]!=0 AND $uni_dos["reservada"]!='' AND $uni_dos["reservada"]!=null) {
	$SQL.=" reservada = ".$uni_dos["reservada"].", ";
}else{
	$SQL.=" reservada = 0 ,";
}

$SQL.=" guardado = 1 ";
$SQL.=" WHERE nro_unidad =".$id_nro_uno;
mysqli_query($con, $SQL);



$SQL="UPDATE asignaciones SET ";

//$SQL.=" pagado = ".$unidad_a["pagado"].", ";

$SQL.=" estado_reserva = ".$unidad_a["estado_reserva"].",";

$SQL.=" reventa = ".$unidad_a["reventa"].",";

if ($unidad_a["fec_reserva"]!='' AND $unidad_a["fec_reserva"]!=null) {
	$SQL.=" fec_reserva = '".$unidad_a["fec_reserva"]."', ";
}else{
	$SQL.=" fec_reserva = null ,";
}

if ($unidad_a["fec_cancelacion"]!='' AND $unidad_a["fec_cancelacion"]!=null) {
	$SQL.=" fec_cancelacion = '".$unidad_a["fec_cancelacion"]."', ";
}else{
	$SQL.=" fec_cancelacion = null ,";
}
$SQL.=" fec_limite = null ,";

$SQL.=" id_sucursal = ".$unidad_a["id_sucursal"].",";
$SQL.=" cancelada = ".$unidad_a["cancelada"].",";

if ($unidad_a["hora"]!='' AND $unidad_a["hora"]!=null) {
	$SQL.=" hora = '".$unidad_a["hora"]."', ";
}else{
	$SQL.=" hora = null ,";
}

$SQL.=" cliente = '".$unidad_a["cliente"]."',";
$SQL.=" id_asesor = ".$unidad_a["id_asesor"].",";

if ($unidad_a["color_uno"]!='' AND $unidad_a["color_uno"]!=null) {
	$SQL.=" color_uno = ".$unidad_a["color_uno"].", ";
}else{
	$SQL.=" color_uno = 0 ,";
}

if ($unidad_a["color_dos"]!='' AND $unidad_a["color_dos"]!=null) {
	$SQL.=" color_dos = ".$unidad_a["color_dos"].", ";
}else{
	$SQL.=" color_dos = 0 ,";
}

if ($unidad_a["color_tres"]!='' AND $unidad_a["color_tres"]!=null) {
	$SQL.=" color_tres = ".$unidad_a["color_tres"].", ";
}else{
	$SQL.=" color_tres = 0 ,";
}

$SQL.=" reservada = ".$unidad_a["reservada"].",";
$SQL.=" guardado = 1 ";
$SQL.=" WHERE nro_unidad =".$id_nro_dos;
mysqli_query($con, $SQL);

$modelo_activo = $id_modelo;

include ('contenido_relleno.php');

 ?>