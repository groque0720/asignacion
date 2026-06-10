<?php
/*
 * Endpoint JSON: historial de cambios de una celda.
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';      // auth + $con + $UPLOADS_URL
require __DIR__ . '/funciones/consulta.php';     // $US_ESTADOS
require __DIR__ . '/actions/historial.php';      // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
