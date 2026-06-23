<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_nivel   = isset($_POST['id_nivel'])    ? (int)$_POST['id_nivel']                  : 0;
$nombre     = isset($_POST['nombre'])      ? trim($_POST['nombre'])                    : '';
$valor_desde = isset($_POST['valor_desde']) ? round((float)$_POST['valor_desde'], 2)  : 0;
$valor_hasta = isset($_POST['valor_hasta']) ? round((float)$_POST['valor_hasta'], 2)  : 10;
$color      = isset($_POST['color'])       ? trim($_POST['color'])                     : '#607d8b';

if ($nombre === '') { echo "El nombre es obligatorio."; exit(); }
if ($valor_desde >= $valor_hasta) { echo "El rango es inválido (desde debe ser menor que hasta)."; exit(); }
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#607d8b';

$nombre = mysqli_real_escape_string($con, $nombre);
$color  = mysqli_real_escape_string($con, $color);

if ($id_nivel > 0) {
    $ok = mysqli_query($con,
        "UPDATE enc_niveles SET nombre='$nombre', valor_desde=$valor_desde,
         valor_hasta=$valor_hasta, color='$color'
         WHERE id_nivel=$id_nivel");
} else {
    $ok = mysqli_query($con,
        "INSERT INTO enc_niveles (nombre, valor_desde, valor_hasta, color)
         VALUES ('$nombre', $valor_desde, $valor_hasta, '$color')");
}

echo $ok ? "ok" : "Error al guardar: " . mysqli_error($con);
?>
