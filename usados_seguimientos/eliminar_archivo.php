<?php
/*
 * Endpoint JSON (POST): elimina un adjunto (tabla nueva o archivo legacy).
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';          // auth + $con + $puedeEditar + $UPLOADS_DIR
require __DIR__ . '/actions/eliminar_archivo.php';   // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
