<?php

$SQL = "SELECT
	tpa_planes_modelos.id AS modelo_id, 
	tpa_planes_modelos.modelo AS modelo
FROM
	tpa_planes_modelos
WHERE
	tpa_planes_modelos.id = $modelo_activo_id
";


$modelo_x_get = mysqli_query($con, $SQL);

if ($modelo_x_get) {
    // Obtener el primer registro
    $modelo_activo_first = mysqli_fetch_assoc($modelo_x_get);
	$modelo_activo_nombre = $modelo_activo_first['modelo'];

    // Verificar si el registro fue obtenido
    if ($modelo_activo_nombre) {
        // Aquí puedes acceder a los campos del primer registro
        
        // echo "ID del primer modelo: " . $modelo_id . "<br>";
        // echo "Nombre del primer modelo: " . $modelo_nombre;
    } else {
        echo "No se encontraron registros.";
    }
} else {
    echo "Error en la consulta: " . mysqli_error($con);
}

?>