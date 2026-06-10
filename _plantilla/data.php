<?php
/*
 * Endpoint JSON paginado del módulo.
 * Wrapper fino: bootstrap + helpers + acción (actions/lista.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';   // pl_lista
require __DIR__ . '/actions/lista.php';        // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
