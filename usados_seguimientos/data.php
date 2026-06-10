<?php
/*
 * Endpoint JSON del grid (filas usados × columnas ítems).
 * Wrapper fino: bootstrap + lógica + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';      // auth (401 JSON) + $con
require __DIR__ . '/funciones/consulta.php';     // us_listar / us_*
require __DIR__ . '/actions/listar.php';         // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
