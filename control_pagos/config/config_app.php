<?php
/*
 * Bootstrap del módulo Control de Pagos.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $perfil).
 * Acá sólo se calcula el permiso de edición, que es específico de este módulo.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';   // auth + conexión: expone $con, $userId, $userName, $perfil

// Perfiles con permiso de edición (mismos que usaba el modal de index.php).
// Además, usuarios puntuales habilitados por id (excepciones fuera de perfil).
$usuariosEdita = [119, 120];
$puedeEditar = in_array($perfil, [1, 2, 9, 14], true)
            || in_array($userId, $usuariosEdita, true);
