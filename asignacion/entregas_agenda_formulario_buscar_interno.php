<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="SELECT * FROM view_asignaciones WHERE interno = ".$interno;
$res=mysqli_query($con, $SQL);
$cant=mysqli_num_rows($res);

if ($cant > 0) {
	$unidad=mysqli_fetch_array($res);
	$arr = array('cantidad' => 1, 'cancelada' => $unidad['cancelada'], 'nro_unidad' => $unidad['nro_unidad'], 'cliente' => $unidad['cliente'], 'grupo' => $unidad['grupo'], 'modelo' => $unidad['modelo'], 'asesor' => $unidad['asesor'], 'id_asesor' => $unidad['id_asesor']);
	
}else{
	$arr = array('cantidad' => 0);
}
echo json_encode($arr);
 ?>

