<?php
/*
 * Endpoint POST: prepara la facturación de una reserva (versión moderna de
 * ventas/web/facturacion_cargar.php). Copia las líneas de la reserva a la factura
 * y devuelve la URL de la pantalla de facturación, a la que redirige el front.
 * Wrapper fino: bootstrap + acción (actions/facturar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $puedeControlar
require __DIR__ . '/actions/facturar.php';     // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida);
mysqli_close($con);
