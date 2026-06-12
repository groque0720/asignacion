<?php
/*
 * Bootstrap del módulo TPA Planes Avanzados.
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $userName, $perfil).
 * Acá sólo se calculan los permisos, específicos de este módulo.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 */

require __DIR__ . '/../../comun/bootstrap.php';   // auth + conexión: expone $con, $userId, $userName, $perfil

// El módulo viejo decide "admin" por id de usuario (no por perfil). Se respeta tal cual
// para no cambiar quién ve columnas financieras / crea planes / exporta.
//   56 Mauro Vargas · 81 Santiago Galiano · 11 Admin · 144
$usuariosAdmin = [56, 81, 11, 144];
$puedeEditar = in_array($userId, $usuariosAdmin, true);

// Usuario EFV (variante de exportación propia en el módulo viejo).
$esEFV = ($userId === 47);
