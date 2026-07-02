<?php
/*
 * Endpoint JSON del Dashboard · Descuentos (0km entregados).
 * Wrapper fino: bootstrap + helpers + acción (actions/datos.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';   // dd_*
require __DIR__ . '/actions/datos.php';        // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
