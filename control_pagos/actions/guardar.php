<?php
/*
 * Guarda la edición de una reserva desde el modal del módulo Control de Pagos.
 * Deja el resultado en $salida; el endpoint guardar.php lo emite como JSON.
 *
 * Equivale a ventas/web/control_pagos_clientes_edit.php pero:
 *   - fechas vacías => NULL (evita error 1292 con sql_mode estricto)
 *   - reporta errores (no falla en silencio)
 *
 * Requiere: $con (config_app.php).
 */

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
    $salida = ["ok" => false, "error" => "Reserva inválida"];
    return;
}
if ($nrou < 300) {
    $salida = ["ok" => false, "error" => "Número de unidad inválido (debe ser ≥ 300)"];
    return;
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
    $salida = ["ok" => false, "error" => mysqli_error($con)];
    return;
}

$salida = ["ok" => true];
