<?php
/*
 * Endpoint JSON: genera/devuelve el token de encuesta de una unidad usada.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';     // sesión + auth (401/403 JSON) + $con
require __DIR__ . '/funciones/consulta.php';     // helpers eu_*
require __DIR__ . '/actions/token.php';          // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
