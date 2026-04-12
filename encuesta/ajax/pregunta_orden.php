<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_pregunta = isset($_POST['id_pregunta']) ? (int)$_POST['id_pregunta'] : 0;
$id_encuesta = isset($_POST['id_encuesta']) ? (int)$_POST['id_encuesta'] : 0;
$accion      = isset($_POST['accion'])      ? $_POST['accion']           : '';

if ($id_pregunta <= 0 || $id_encuesta <= 0) { echo "Parámetros inválidos."; exit(); }

$res = mysqli_query($con, "SELECT nro_orden FROM enc_preguntas WHERE id_pregunta = $id_pregunta AND id_encuesta = $id_encuesta AND baja = 0");
if (mysqli_num_rows($res) == 0) { echo "Pregunta no encontrada."; exit(); }
$preg     = mysqli_fetch_array($res);
$nro_actual = (int)$preg['nro_orden'];

if ($accion == 'subir') {
	$nro_target = $nro_actual - 1;
	if ($nro_target < 1) { echo "ok"; exit(); }
} elseif ($accion == 'bajar') {
	$nro_target = $nro_actual + 1;
} else {
	echo "Acción inválida."; exit();
}

// Buscar la pregunta que ocupa el lugar destino
$res2 = mysqli_query($con, "SELECT id_pregunta FROM enc_preguntas WHERE id_encuesta = $id_encuesta AND nro_orden = $nro_target AND baja = 0");
if (mysqli_num_rows($res2) == 0) { echo "ok"; exit(); } // ya está en el extremo
$otra = mysqli_fetch_array($res2);
$id_otra = (int)$otra['id_pregunta'];

// Intercambiar órdenes
mysqli_query($con, "UPDATE enc_preguntas SET nro_orden = $nro_actual WHERE id_pregunta = $id_otra");
mysqli_query($con, "UPDATE enc_preguntas SET nro_orden = $nro_target WHERE id_pregunta = $id_pregunta");

echo "ok";
?>
