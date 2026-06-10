<?php
/*
 * Endpoint JSON: detalle de una celda (para el modal).
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';      // auth (401 JSON) + $con + $UPLOADS_URL
require __DIR__ . '/funciones/consulta.php';     // $US_ESTADOS / us_estados_lista
require __DIR__ . '/actions/celda.php';          // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
