<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_area = isset($_POST['id_area']) ? (int)$_POST['id_area'] : 0;
$accion  = isset($_POST['accion'])  ? $_POST['accion']        : '';

if ($id_area <= 0) { echo "Parámetros inválidos."; exit(); }

$res = mysqli_query($con, "SELECT nro_orden FROM enc_areas WHERE id_area = $id_area");
if (!$res || mysqli_num_rows($res) == 0) { echo "Área no encontrada."; exit(); }
$area       = mysqli_fetch_array($res);
$nro_actual = (int)$area['nro_orden'];

if ($accion == 'subir') {
    $nro_target = $nro_actual - 1;
    if ($nro_target < 1) { echo "ok"; exit(); }
} elseif ($accion == 'bajar') {
    $nro_target = $nro_actual + 1;
} else {
    echo "Acción inválida."; exit();
}

// Buscar el área que ocupa el lugar destino
$res2 = mysqli_query($con, "SELECT id_area FROM enc_areas WHERE nro_orden = $nro_target");
if (mysqli_num_rows($res2) == 0) { echo "ok"; exit(); }
$otra    = mysqli_fetch_array($res2);
$id_otra = (int)$otra['id_area'];

// Intercambiar órdenes
mysqli_query($con, "UPDATE enc_areas SET nro_orden = $nro_actual WHERE id_area = $id_otra");
mysqli_query($con, "UPDATE enc_areas SET nro_orden = $nro_target WHERE id_area = $id_area");

echo "ok";
?>
