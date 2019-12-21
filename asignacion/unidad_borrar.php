<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();

// $SQL="DELETE FROM asignaciones WHERE id_unidad = ".$id_unidad;
// mysqli_query($con, $SQL); 

$SQL="UPDATE asignaciones SET ";
$SQL.=" borrar = 1, ";
$SQL.=" fecha_borrado = '".date('Y-m-d')."', ";
$SQL.=" hora_borrado = '".date('H:i:s')."', ";
$SQL.=" usuario_borrado = '".$_SESSION["usuario"]."' ";
$SQL.=" WHERE id_unidad =".$id_unidad;
mysqli_query($con, $SQL);

?>
