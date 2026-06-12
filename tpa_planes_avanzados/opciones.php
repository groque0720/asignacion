<?php
/*
 * Endpoint JSON de catálogos (selects de los modales).
 * Wrapper fino: bootstrap + helpers + acción (actions/catalogos.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';  // tpa_*
require __DIR__ . '/actions/catalogos.php';   // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
