<?php
/*
 * Endpoint POST: anula una reserva (versión moderna de ventas/web/reserva_anular.php).
 * Wrapper fino: bootstrap + acción (actions/anular.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $puedeControlar
require __DIR__ . '/actions/anular.php';       // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida);
mysqli_close($con);
