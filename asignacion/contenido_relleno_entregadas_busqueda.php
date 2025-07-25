  <?php
  set_time_limit(1800); // Aumentamos a 30 minutos
  ini_set('memory_limit', '1024M'); // Aumentamos a 1GB
  ini_set('max_execution_time', 1800); // 30 minutos también aquí
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
	// $fec_ini = $año_desde.'/'.$mes_desde.'/01';
	// $fec_fin = $año_hasta.'/'.$mes_hasta.'/'.$ultimo_dia_mes_hasta;

	
	$fec_ini = sprintf('%04d-%02d-01', $año_desde, $mes_desde);
    $fec_fin = sprintf('%04d-%02d-%02d', $año_hasta, $mes_hasta, $ultimo_dia_mes_hasta);
	
	$cadena .= " fec_entrega >= '$fec_ini' AND fec_entrega <= '$fec_fin' ";

// $SQL="SELECT * FROM view_asignaciones_entregadas WHERE ".$cadena." ORDER BY fec_entrega DESC";


$SQL = "SELECT
asignaciones.id_unidad AS id_unidad,
asignaciones.año AS año,
asignaciones.nro_orden AS nro_orden,
asignaciones.interno AS interno,
asignaciones.chasis AS chasis,
asignaciones.cliente AS cliente,
usuarios.nombre AS asesor,
asignaciones.id_mes AS id_mes,
asignaciones.id_sucursal AS id_sucursal,
asignaciones.fec_reserva AS fec_reserva,
asignaciones.id_asesor AS id_asesor,
asignaciones.nro_unidad AS nro_unidad,
asignaciones.fec_arribo AS fec_arribo,
asignaciones.fec_despacho AS fec_despacho,
asignaciones.id_ubicacion AS id_ubicacion,
asignaciones.pagado AS pagado,
asignaciones.fec_entrega AS fec_entrega,
asignaciones.id_grupo,
asignaciones.id_modelo,
asignaciones.id_color,
asignaciones.servicio_conectado
FROM
((((asignaciones))
JOIN usuarios ON ((asignaciones.id_asesor = usuarios.idusuario))))
where ((`asignaciones`.`entregada` = 1) and (`asignaciones`.`borrar` = 0) and (`asignaciones`.`guardado` = 1) and (`asignaciones`.`fec_entrega` >= '".$fec_ini."') and (`asignaciones`.`fec_entrega` <= '".$fec_fin."') )
order by `asignaciones`.`fec_entrega` desc";

$unidades = mysqli_query($con, $SQL);
if (!$unidades) {
    echo "Error en la consulta: " . mysqli_error($con);
    exit;
}
include('contenido_relleno_entregadas_cuerpo.php');
 ?>
