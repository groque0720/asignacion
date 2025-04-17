<?php
// Consulta para obtener todos los planes de todos los modelos para una situación específica
$sql_planes_all = "SELECT
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
WHERE
    tpa_planes_avanzados.situacion_id = $situacionId
ORDER BY
    tpa_planes_modelos.posicion ASC, 
    tpa_planes_versiones.posicion ASC";

// Ejecutar la consulta
$planes_todos_modelos = mysqli_query($con, $sql_planes_all);

// Verificar si hay errores en la consulta
if (!$planes_todos_modelos) {
    die("Error en la consulta: " . mysqli_error($con));
}

// Verificar cuántas filas retornó
$num_rows = mysqli_num_rows($planes_todos_modelos);
?>
