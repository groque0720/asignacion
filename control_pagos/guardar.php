<?php
/*
 * Endpoint POST: guarda la edición de una reserva desde el modal.
 * Wrapper fino: bootstrap + acción (actions/guardar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con
require __DIR__ . '/actions/guardar.php';      // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida);
mysqli_close($con);
