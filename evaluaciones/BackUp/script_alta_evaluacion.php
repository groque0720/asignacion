<?php 

include('z_comun/seguridad.php');

include_once("z_comun/funciones/funciones.php");
conectar();

$SQL="INSERT INTO evaluaciones SET fecha = '".date('Y-m-d')."', periodo = 'Octubre 2017'";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(id_evaluacion) as id_evaluacion FROM evaluaciones LIMIT 1";
$res=mysqli_query($con, $SQL);

$eval=mysqli_fetch_array($res);

$id_evaluacion=$eval['id_evaluacion'];


$SQL="SELECT * FROM usuarios WHERE activo = 1";
$res=mysqli_query($con, $SQL);

while ($usuario=mysqli_fetch_array($res)) {

	$SQL="INSERT INTO evaluaciones_usuarios SET id_evaluacion =".$id_evaluacion.", id_usuario =". $usuario['id_usuario'];
	mysqli_query($con, $SQL);

	$SQL="SELECT MAX(id_evaluacion_usuario) as id_evaluacion_usuario FROM evaluaciones_usuarios";
	$res_ev=mysqli_query($con, $SQL);

	$eval_usu = mysqli_fetch_array($res_ev);

	$id_evaluacion_usuario = $eval_usu['id_evaluacion_usuario'];


	$SQL="SELECT * FROM evaluacion_item WHERE activo = 1";
	$items=mysqli_query($con, $SQL);

	while ($item=mysqli_fetch_array($items)) {

		$SQL="INSERT INTO evaluacion_usuario_calificacion SET id_evaluacion_usuario =".$id_evaluacion_usuario.", id_item=".$item['id_item'];
		mysqli_query($con, $SQL);
		
	}

echo $usuario['nombre'].'<br>';
} ?>