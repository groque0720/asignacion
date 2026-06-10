<?php
/*
 * Endpoint JSON: estado de cuenta de un cliente (resumen + pagos + lookups).
 * Wrapper fino: bootstrap + helpers + acción (actions/cuenta_datos.php).
 * IDrecord = idcliente (igual que el módulo viejo).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';     // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';     // ec_datos / ec_lookups
require __DIR__ . '/actions/cuenta_datos.php';   // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
