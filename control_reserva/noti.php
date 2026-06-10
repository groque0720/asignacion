<?php
/*
 * Endpoint JSON: contador de notificaciones sin ver del usuario logueado.
 * Equivale a ventas/web/control_res_act_noti.php (que el original recargaba cada 7s).
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // sesión + auth (401 JSON) + $con + $userId
require __DIR__ . '/funciones/consulta.php';   // cr_noti_count

header('Content-Type: application/json; charset=utf-8');
echo json_encode(["ok" => true, "cantidad" => cr_noti_count($con, $userId)]);
mysqli_close($con);
