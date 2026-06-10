<?php
/*
 * Endpoint JSON (POST): alta o edición de un ítem (columna).
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';       // auth + $con + $esAdmin
require __DIR__ . '/actions/guardar_item.php';    // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
