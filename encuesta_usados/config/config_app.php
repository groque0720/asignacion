<?php
/*
 * Bootstrap del módulo Encuesta · Usados.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $userName, $perfil).
 * Acá se cargan las constantes del módulo y se calculan los permisos propios.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../config.php';                 // constantes EU_*
require __DIR__ . '/../../comun/bootstrap.php';     // auth + conexión: $con, $userId, $userName, $perfil

// ── Acceso al módulo (perfil habilitado) ────────────────────────────────────
$tieneAcceso = in_array($perfil, EU_PERFILES, true)
            || in_array($userId, EU_USUARIOS_CONFIG, true);

if (!$tieneAcceso) {
    if (isset($AUTH_FAIL) && $AUTH_FAIL === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(["error" => "Sin permiso para este módulo"]);
    } else {
        header("Location: ../asignacion");          // vuelve al sistema
    }
    exit();
}

// Quién puede configurar la encuesta (preguntas/áreas/niveles)
$puedeConfigurar = in_array($perfil, [1, 14], true) || in_array($userId, EU_USUARIOS_CONFIG, true);

// Quién puede generar links de encuesta (cualquiera con acceso al panel)
$puedeEditar = true;
