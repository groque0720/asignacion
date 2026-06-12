<?php
/*
 * Endpoint JSON de listado del módulo TPA Planes Avanzados.
 * Wrapper fino: bootstrap + helpers + motor de datos (actions/listar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $userId + $puedeEditar
require __DIR__ . '/funciones/consulta.php';  // tpa_*
require __DIR__ . '/actions/listar.php';      // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
