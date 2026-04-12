<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_area = isset($_POST['id_area']) ? (int)$_POST['id_area'] : 0;
if ($id_area <= 0) { echo "Área inválida."; exit(); }

// Desasignar el área de las preguntas que la usen (no las borra, solo queda sin área)
mysqli_query($con, "UPDATE enc_preguntas SET id_area = NULL WHERE id_area = $id_area");

if (mysqli_query($con, "DELETE FROM enc_areas WHERE id_area = $id_area")) {
	echo "ok";
} else {
	echo "Error: " . mysqli_error($con);
}
?>
