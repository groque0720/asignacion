<?php

$SQL = "SELECT
	tpa_planes_avanzados.*, 
	tpa_modelos.modelo, 
	tpa_modalidades.modalidad, 
	usuarios.nombre as usuario_venta
FROM
	tpa_planes_avanzados
	INNER JOIN
	tpa_modelos
	ON 
		tpa_planes_avanzados.modelo_id = tpa_modelos.id
	INNER JOIN
	tpa_modalidades
	ON 
		tpa_planes_avanzados.modalidad_id = tpa_modalidades.id
	LEFT JOIN
	usuarios
	ON 
		tpa_planes_avanzados.usuario_venta_id = usuarios.idusuario
ORDER BY
	tpa_modelos.modelo ASC";
    
$planes_avanzados = mysqli_query($con, $SQL);

?>