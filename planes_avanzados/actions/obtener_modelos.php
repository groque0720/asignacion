<?php 
    $SQL="SELECT
	tpa_planes_versiones.id, 
	tpa_planes_modelos.modelo, 
	tpa_planes_versiones.version
FROM
	tpa_planes_modelos
	INNER JOIN
	tpa_planes_versiones
	ON 
		tpa_planes_modelos.id = tpa_planes_versiones.modelo_id
WHERE
	tpa_planes_modelos.activo = 1 AND
	tpa_planes_versiones.activo = 1
ORDER BY
	tpa_planes_modelos.posicion ASC, 
	tpa_planes_versiones.posicion ASC";
    
	$modelos=mysqli_query($con, $SQL);
?>