<?php
/*
 * Estado de Cuenta de un cliente (versión moderna de ventas/web/pago.php).
 * Devuelve JSON con el resumen + el listado de pagos.
 * IDrecord = idcliente (igual que el módulo viejo).
 */
header('Content-Type: application/json; charset=utf-8');
@session_start();
include("funciones/func_mysql.php");
include("_consulta.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$idcliente = isset($_GET['idcliente']) ? (int)$_GET['idcliente'] : 0;
if ($idcliente <= 0) {
    echo json_encode(["error" => "Cliente inválido"]);
    exit;
}

$d = ec_datos($con, $idcliente);
if ($d === null) {
    echo json_encode(["error" => "No se encontró reserva para el cliente"]);
    exit;
}

// Lookups para el formulario de pagos.
function lookup($con, $sql) {
    $out = [];
    $r = mysqli_query($con, $sql);
    if ($r) while ($x = mysqli_fetch_assoc($r)) $out[] = $x;
    return $out;
}
$d['ok'] = true;
$d['lookups'] = [
    'tipos'       => lookup($con, "SELECT idtipopago AS id, tipopago AS nombre FROM pagos_tipos ORDER BY tipopago"),
    'modos'       => lookup($con, "SELECT idpagomodo AS id, modo AS nombre FROM pagos_modos ORDER BY modo"),
    'financieras' => lookup($con, "SELECT idfinanciera AS id, financiera AS nombre FROM financieras WHERE seleccionable = 1 ORDER BY financiera"),
];

echo json_encode($d, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
