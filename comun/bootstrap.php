<?php
/*
 * Bootstrap compartido de los módulos modernos (control_pagos, estado_cuenta, …).
 * Conexión + sesión + autenticación. Expone: $con, $userId, $userName, $perfil.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del include para que,
 * si no hay sesión, responda 401 JSON en vez de redirigir al login.
 *
 * El permiso de edición ($puedeEditar) es específico de cada módulo (cambian los
 * perfiles/usuarios habilitados): se calcula en el config/config_app.php del
 * módulo, DESPUÉS de incluir este archivo.
 *
 * Se incluye desde modulo/config/config_app.php con:
 *   require __DIR__ . '/../../comun/bootstrap.php';
 */

require __DIR__ . '/func_mysql.php';
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    if (isset($AUTH_FAIL) && $AUTH_FAIL === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(["error" => "No autenticado"]);
    } else {
        header("Location: ../login");   // relativo a la URL del módulo (asignacion/<modulo>/) -> asignacion/login
    }
    exit();
}

$userId   = (int)($_SESSION["id"] ?? 0);
$userName = $_SESSION["usuario"] ?? null;
$perfil   = (int)($_SESSION['idperfil'] ?? 0);
