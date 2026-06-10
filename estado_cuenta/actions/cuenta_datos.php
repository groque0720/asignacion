<?php
/*
 * Arma el estado de cuenta de un cliente para data.php.
 * Deja el resultado en $salida; el endpoint lo emite como JSON.
 *
 * Requiere: $con (config_app.php) y ec_datos/ec_lookups (funciones/consulta.php).
 */

$idcliente = isset($_GET['idcliente']) ? (int)$_GET['idcliente'] : 0;
if ($idcliente <= 0) { $salida = ["error" => "Cliente inválido"]; return; }

$d = ec_datos($con, $idcliente);
if ($d === null) { $salida = ["error" => "No se encontró reserva para el cliente"]; return; }

$d['ok']      = true;
$d['lookups'] = ec_lookups($con);   // catálogos del formulario de pagos
$salida = $d;
