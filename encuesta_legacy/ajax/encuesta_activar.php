<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_encuesta = isset($_POST['id_encuesta']) ? (int)$_POST['id_encuesta'] : 0;
if ($id_encuesta <= 0) { echo "ID inválido."; exit(); }

// Verificar que existe
$res = mysqli_query($con, "SELECT id_encuesta FROM enc_encuestas WHERE id_encuesta = $id_encuesta AND baja = 0");
if (mysqli_num_rows($res) == 0) { echo "Encuesta no encontrada."; exit(); }

// Desactivar todas
mysqli_query($con, "UPDATE enc_encuestas SET activa = 0");
// Activar la seleccionada
mysqli_query($con, "UPDATE enc_encuestas SET activa = 1 WHERE id_encuesta = $id_encuesta");

echo "ok";
?>
