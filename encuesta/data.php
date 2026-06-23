<?php
/*
 * Endpoint JSON paginado del módulo Encuesta · 0km (lista de entregas).
 * Wrapper fino: bootstrap + helpers + motor de datos (actions/listar.php).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';     // sesión + auth (401/403 JSON) + $con
require __DIR__ . '/funciones/consulta.php';     // helpers enc_*
require __DIR__ . '/actions/listar.php';         // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
