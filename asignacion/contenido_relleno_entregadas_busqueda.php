  <?php 
  include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$cadena='';

	if ($mes_desde!=0) {
		$cadena.=" MONTH(fec_entrega) >=".$mes_desde;
	}
	if ($año_desde!='' AND $año_desde>=2016) {
		$cadena.=" AND YEAR(fec_entrega) >=".$año_desde;
	}
	if ($mes_hasta!=0) {
		$cadena.=" OR (MONTH(fec_entrega) <=".$mes_hasta;
	}
	if ($año_hasta!='' AND $año_hasta>=2016) {
		$cadena.=" AND YEAR(fec_entrega) <=".$año_hasta.") ";
	}
$SQL="SELECT * FROM asignaciones WHERE entregada = 1 AND".$cadena." ORDER BY fec_entrega DESC";
//echo $SQL;
$unidades = mysqli_query($con, $SQL);
include('contenido_relleno_entregadas_cuerpo.php');
 ?>
