<?php
/*
 * Endpoint POST: ABM de pagos del Estado de Cuenta (insertar / editar / eliminar).
 * Wrapper fino: bootstrap + acción (actions/guardar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $puedeEditar
require __DIR__ . '/actions/guardar.php';      // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida);
mysqli_close($con);
