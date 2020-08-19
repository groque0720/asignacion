  <?php
  include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$cadena='';

	// if ($mes_desde!=0) {
	// 	$cadena.=" (MONTH(fec_entrega) >=".$mes_desde;
	// }
	// if ($año_desde!='' AND $año_desde>=2016) {
	// 	$cadena.=" AND YEAR(fec_entrega) >=".$año_desde;
	// }
	// if ($mes_hasta!=0) {
	// 	$cadena.=" )AND ((MONTH(fec_entrega) <=".$mes_hasta;
	// }
	// if ($año_hasta!='' AND $año_hasta>=2016) {
	// 	$cadena.=" AND YEAR(fec_entrega) <=".$año_hasta.") )";
	// }


	$ultimo_dia_mes_hasta = cal_days_in_month(CAL_GREGORIAN, $mes_hasta, $año_hasta);
	$fec_ini = $año_desde.'/'.$mes_desde.'/01';
	$fec_fin = $año_hasta.'/'.$mes_hasta.'/'.$ultimo_dia_mes_hasta;

	$cadena .= " fec_entrega >= '$fec_ini' AND fec_entrega <= '$fec_fin' ";


$SQL="SELECT * FROM asignaciones WHERE entregada = 1 AND".$cadena." ORDER BY fec_entrega DESC";
$unidades = mysqli_query($con, $SQL);
include('contenido_relleno_entregadas_cuerpo.php');
 ?>
