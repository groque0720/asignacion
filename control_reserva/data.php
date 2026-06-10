<?php
/*
 * Endpoint JSON paginado: lista de reservas (enviada >= 1).
 * Wrapper fino: bootstrap + helpers + acción (actions/lista.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';   // cr_lista
require __DIR__ . '/actions/lista.php';        // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
