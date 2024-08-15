<?php

$SQL = "SELECT
	tpa_planes_modelos.id AS modelo_id, 
	tpa_planes_modelos.modelo AS modelo
FROM
	tpa_planes_avanzados
	INNER JOIN
	tpa_planes_versiones
	ON 
		tpa_planes_avanzados.version_id = tpa_planes_versiones.id
	INNER JOIN
	tpa_planes_modelos
	ON 
		tpa_planes_versiones.modelo_id = tpa_planes_modelos.id
WHERE
	tpa_planes_avanzados.situacion_id <> 4
GROUP BY
	tpa_planes_modelos.id
ORDER BY
	tpa_planes_modelos.posicion ASC";


$modelos_activos = mysqli_query($con, $SQL);
$modelos_activos_ = mysqli_query($con, $SQL);


if ($modelos_activos_) {
    // Obtener el primer registro
    $primer_modelo = mysqli_fetch_assoc($modelos_activos_);

    // Verificar si el registro fue obtenido
    if ($primer_modelo) {
        // Aquí puedes acceder a los campos del primer registro
        $modelo_id = $primer_modelo['modelo_id'];
        $modelo_nombre = $primer_modelo['modelo'];
        
        // echo "ID del primer modelo: " . $modelo_id . "<br>";
        // echo "Nombre del primer modelo: " . $modelo_nombre;
    } else {
        echo "No se encontraron registros.";
    }
} else {
    echo "Error en la consulta: " . mysqli_error($con);
}

?>