<?php
/*
 * Bootstrap del módulo Control de Reservas.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $perfil).
 * Acá sólo se calcula el permiso de control (facturar / anular), específico del módulo.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';

// Facturar / Anular: sólo usuarios habilitados (idéntico al original control_reservas.php → 56 y 11).
$usuariosControl = [56, 11];
$puedeControlar = in_array($userId, $usuariosControl, true);
