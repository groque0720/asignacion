<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_area = isset($_POST['id_area']) ? (int)$_POST['id_area'] : 0;
$nombre  = isset($_POST['nombre'])  ? trim($_POST['nombre'])  : '';
$color   = isset($_POST['color'])   ? trim($_POST['color'])   : '#607d8b';

if ($nombre === '') { echo "El nombre del área es obligatorio."; exit(); }

// Validar que el color sea un hex válido (#RRGGBB)
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) $color = '#607d8b';

$nombre_esc = mysqli_real_escape_string($con, $nombre);
$color_esc  = mysqli_real_escape_string($con, $color);

if ($id_area == 0) {
	$res_max = mysqli_query($con, "SELECT MAX(nro_orden) AS max_ord FROM enc_areas");
	$row_max = mysqli_fetch_array($res_max);
	$nro_orden = ($row_max['max_ord'] !== null) ? $row_max['max_ord'] + 1 : 1;

	$SQL = "INSERT INTO enc_areas (nombre, color, nro_orden)
	        VALUES ('$nombre_esc', '$color_esc', $nro_orden)";
	if (mysqli_query($con, $SQL)) {
		echo "ok:" . mysqli_insert_id($con);
	} else {
		echo "Error: " . mysqli_error($con);
	}
} else {
	$SQL = "UPDATE enc_areas SET nombre = '$nombre_esc', color = '$color_esc'
	        WHERE id_area = $id_area";
	if (mysqli_query($con, $SQL)) {
		echo "ok:$id_area";
	} else {
		echo "Error: " . mysqli_error($con);
	}
}
?>
