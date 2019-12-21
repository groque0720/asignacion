<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

list($año, $mes, $dia) = explode('/', $fecha);
$fecha= $dia."-".$mes."-".$año;
//$fecha = date('Y-m-d', $fecha);
//echo $fecha;

$SQL="SELECT * FROM agenda_td_lineas WHERE id_sucursal = $sucursal AND id_modelo = $modelo AND fecha = '$fecha'";
$res=mysqli_query($con, $SQL);

$cant=mysqli_num_rows($res);

if ($cant>0) {

	include("tablero_agenda_cuerpo.php");

}else{

	$SQL="SELECT * FROM agenda_td_horarios";
	$horarios = mysqli_query($con, $SQL);

	while ($horario = mysqli_fetch_array($horarios)) { 
		 $SQL="INSERT INTO agenda_td_lineas (id_horario, id_sucursal, id_modelo, fecha) VALUES (".$horario['id_horario'].",".$sucursal.",".$modelo.",'".$fecha."')";
		 // $SQL="INSERT INTO agenda_td_lineas (id_horario) VALUES (".$horario['id_horario'].")";
		mysqli_query($con, $SQL);
	} 

	$SQL="SELECT * FROM agenda_td_lineas WHERE id_sucursal = $sucursal AND id_modelo = $modelo AND fecha = '$fecha'";
	$res=mysqli_query($con, $SQL);

	include("tablero_agenda_cuerpo.php");

}

 ?>