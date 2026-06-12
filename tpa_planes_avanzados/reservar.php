<?php
/*
 * Endpoint POST: reserva un plan libre / edita la reserva propia.
 * Wrapper fino: bootstrap + helpers + acción (actions/reservar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $userId
require __DIR__ . '/funciones/consulta.php';  // tpa_*
require __DIR__ . '/actions/reservar.php';    // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
