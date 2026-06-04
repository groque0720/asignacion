<?php
/*
 * Bootstrap del módulo Control de Pagos: conexión + sesión + autenticación.
 * Reemplaza el bloque session_start + auth que estaba duplicado en
 * index.php, data.php, guardar.php, excel.php y pdf.php.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del include para que,
 * si no hay sesión, responda 401 JSON en vez de redirigir al login.
 */

include __DIR__ . "/../funciones/func_mysql.php";
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    if (isset($AUTH_FAIL) && $AUTH_FAIL === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(["error" => "No autenticado"]);
    } else {
        header("Location: ../login");
    }
    exit();
}

$userId      = $_SESSION["id"]      ?? null;
$userName    = $_SESSION["usuario"] ?? null;
// Perfiles con permiso de edición (mismos que usaba el modal de index.php).
// Además, usuarios puntuales habilitados por id (excepciones fuera de perfil).
$usuariosEdita = [119, 120];
$puedeEditar = in_array((int)($_SESSION['idperfil'] ?? 0), [1, 2, 9, 14], true)
            || in_array((int)($userId ?? 0), $usuariosEdita, true);
