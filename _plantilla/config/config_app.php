<?php
/*
 * Bootstrap del módulo.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $perfil).
 * Acá sólo se calcula el permiso de edición, específico de cada módulo.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';

// AJUSTAR: perfiles / usuarios con permiso de edición en este módulo.
$usuariosEdita = [];
$puedeEditar = in_array($perfil, [1, 2], true) || in_array($userId, $usuariosEdita, true);
