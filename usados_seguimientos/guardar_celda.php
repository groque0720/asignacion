<?php
/*
 * Endpoint JSON (POST multipart): guarda estado/observación + adjuntos múltiples.
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';      // auth + $con + $puedeEditar + $UPLOADS_DIR
require __DIR__ . '/funciones/consulta.php';     // $US_ESTADOS
require __DIR__ . '/actions/guardar_celda.php';  // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
