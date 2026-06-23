<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_pregunta = isset($_POST['id_pregunta']) ? (int)$_POST['id_pregunta'] : 0;
if ($id_pregunta <= 0) { echo "ID inválido."; exit(); }

// Obtener encuesta e id_orden para reordenar
$res = mysqli_query($con, "SELECT id_encuesta, nro_orden FROM enc_preguntas WHERE id_pregunta = $id_pregunta AND baja = 0");
if (mysqli_num_rows($res) == 0) { echo "Pregunta no encontrada."; exit(); }
$preg = mysqli_fetch_array($res);

// Dar de baja
mysqli_query($con, "UPDATE enc_preguntas SET baja = 1 WHERE id_pregunta = $id_pregunta");

// Reordenar el resto
$id_enc = (int)$preg['id_encuesta'];
$nro    = (int)$preg['nro_orden'];
mysqli_query($con, "UPDATE enc_preguntas SET nro_orden = nro_orden - 1
					WHERE id_encuesta = $id_enc AND nro_orden > $nro AND baja = 0");

echo "ok";
?>
