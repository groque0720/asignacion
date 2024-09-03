<?php


$SQL = "SELECT
	tpa_planes_avanzados.*, 
	tpa_plan_situaciones.situacion, 
	tpa_modalidades.modalidad, 
	usuarios.nombre AS usuario_venta, 
	tpa_planes_versiones.version, 
	tpa_planes_modelos.modelo
FROM
	tpa_planes_avanzados
	INNER JOIN
	tpa_plan_situaciones
	ON 
		tpa_planes_avanzados.situacion_id = tpa_plan_situaciones.id
	INNER JOIN
	tpa_modalidades
	ON 
		tpa_planes_avanzados.modalidad_id = tpa_modalidades.id
	LEFT JOIN
	usuarios
	ON 
		tpa_planes_avanzados.usuario_venta_id = usuarios.idusuario
	INNER JOIN
	tpa_planes_versiones
	ON 
		tpa_planes_avanzados.version_id = tpa_planes_versiones.id
	INNER JOIN
	tpa_planes_modelos
	ON 
		tpa_planes_versiones.modelo_id = tpa_planes_modelos.id
	 
ORDER BY
	tpa_plan_situaciones.orden ASC, 
	tpa_planes_modelos.posicion ASC, 
	tpa_planes_versiones.posicion ASC";
    
$planes_avanzados = mysqli_query($con, $SQL);



?>

<!-- WHERE
	MONTH (tpa_planes_avanzados.fecha_reserva) = MONTH ( NOW( )) -->