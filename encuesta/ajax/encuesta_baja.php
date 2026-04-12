<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_encuesta = isset($_POST['id_encuesta']) ? (int)$_POST['id_encuesta'] : 0;
if ($id_encuesta <= 0) { echo "ID inválido."; exit(); }

// Solo se puede dar de baja si no está activa
$res = mysqli_query($con, "SELECT activa FROM enc_encuestas WHERE id_encuesta = $id_encuesta AND baja = 0");
if (mysqli_num_rows($res) == 0) { echo "Encuesta no encontrada."; exit(); }
$enc = mysqli_fetch_array($res);
if ($enc['activa'] == 1) { echo "No se puede eliminar una encuesta activa. Activá otra primero."; exit(); }

mysqli_query($con, "UPDATE enc_encuestas SET baja = 1 WHERE id_encuesta = $id_encuesta");
echo "ok";
?>
