<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
@session_start();

extract($_POST);

$idsuc=$_SESSION["idsuc"];


if ($idsuc != '1' ) {
	$cadena=" AND id_sucursal <> 1";
}else{
	$cadena=" AND id_sucursal = 1 ";
}

$SQL="SELECT max(leg) as max_num FROM registros_gestoria WHERE guardado=1 ". $cadena; 
$bus_nro=mysqli_query($con, $SQL);
if (empty($bus_nro)) {$prox_nro=1;}else{ $prox_nro_a=mysqli_fetch_array($bus_nro); $prox_nro=$prox_nro_a['max_num'];}

if (is_null($prox_nro)) {
	$prox_nro = 1;
}else{
	$prox_nro ++;
}

$SQL="UPDATE registros_gestoria SET leg = ".$prox_nro.", nro_leg = '".$suc_res."-".$prox_nro."', guardado = 1 WHERE id_reg_gestoria =".$id_reg;
mysqli_query($con, $SQL);


echo $prox_nro;

?>

