<?php
/*
 * Guarda la edición de una reserva desde el modal del módulo Control de Pagos.
 * Equivale a ventas/web/control_pagos_clientes_edit.php pero:
 *   - fechas vacías => NULL (evita error 1292 con sql_mode estricto)
 *   - responde JSON y reporta errores (no falla en silencio)
 */
header('Content-Type: application/json; charset=utf-8');
@session_start();
include("funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "No autenticado"]);
    exit;
}

// Fecha vacía / "null" / 0 => NULL real.
function fechaSQL($con, $v) {
    $v = trim((string)$v);
    if ($v === '' || strtolower($v) === 'null' || $v === '0') return "NULL";
    return "'".mysqli_real_escape_string($con, $v)."'";
}
function txt($con, $v) {
    return mysqli_real_escape_string($con, (string)$v);
}

$id   = isset($_POST['id'])   ? (int)$_POST['id']   : 0;
$nrou = isset($_POST['nrou']) ? (int)$_POST['nrou'] : 0;

if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "Reserva inválida"]);
    exit;
}
if ($nrou < 300) {
    echo json_encode(["ok" => false, "error" => "Número de unidad inválido (debe ser ≥ 300)"]);
    exit;
}

$SQL = "UPDATE reservas SET ".
    "interno = '".txt($con, $_POST['nroint'] ?? '')."', ".
    "nrounidad = '".txt($con, $_POST['nrou'] ?? '')."', ".
    "llego = ".fechaSQL($con, $_POST['fecarr'] ?? '').", ".
    "fechacanc = ".fechaSQL($con, $_POST['feccan'] ?? '').", ".
    "fechaentrega = ".fechaSQL($con, $_POST['fecent'] ?? '').", ".
    "nroorden = '".txt($con, $_POST['no'] ?? '')."', ".
    "obscanc = '".txt($con, $_POST['obs'] ?? '')."' ".
    "WHERE idreserva = ".$id;

if (!mysqli_query($con, $SQL)) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => mysqli_error($con)]);
    exit;
}

echo json_encode(["ok" => true]);
mysqli_close($con);
