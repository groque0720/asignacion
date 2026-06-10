<?php
/*
 * Bootstrap del módulo Estado de Cuenta.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $perfil).
 * Acá sólo se calcula el permiso de ABM, que es específico de este módulo.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';

// Permiso para ABM de pagos: Tesorería (8) + admins (1,2) + usuarios habilitados
// puntualmente por id (excepciones fuera de perfil, heredadas del módulo viejo).
$usuariosEdita = [119, 120, 87, 28, 11, 94, 96, 14];
$puedeEditar = in_array($perfil, [1, 2, 8], true) || in_array($userId, $usuariosEdita, true);
