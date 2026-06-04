<?php
/*
 * Endpoint JSON paginado del módulo Control de Pagos.
 * Wrapper fino: bootstrap + helpers + motor de datos (actions/listar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';      // sesión + auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';     // cp_where / cp_calc / cp_modelo_texto
require __DIR__ . '/actions/listar.php';         // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
