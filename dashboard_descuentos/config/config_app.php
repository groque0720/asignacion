<?php
/*
 * Bootstrap del módulo Dashboard · Descuentos (0km entregados).
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $userName, $perfil).
 * Dashboard de SOLO LECTURA: acceso = cualquier usuario autenticado.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';   // auth + conexión: $con, $userId, $userName, $perfil
