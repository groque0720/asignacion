<?php
/*
 * Endpoint JSON (POST multipart): sube adjuntos a una celda (sin tocar estado).
 * Wrapper fino: bootstrap + acción.
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';        // auth + $con + $puedeEditar + $UPLOADS_DIR + $UPLOADS_URL + $userName
require __DIR__ . '/funciones/consulta.php';       // us_guardar_adjuntos()
require __DIR__ . '/actions/subir_archivos.php';   // construye $salida

header('Content-Type: application/json; charset=utf-8');
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
