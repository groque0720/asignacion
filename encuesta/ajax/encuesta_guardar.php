<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_encuesta        = isset($_POST['id_encuesta'])         ? (int)$_POST['id_encuesta']                                           : 0;
$nombre             = isset($_POST['nombre'])              ? trim(mysqli_real_escape_string($con, $_POST['nombre']))               : '';
$descripcion        = isset($_POST['descripcion'])         ? trim(mysqli_real_escape_string($con, $_POST['descripcion']))          : '';
$mensaje_bienvenida = isset($_POST['mensaje_bienvenida'])  ? trim(mysqli_real_escape_string($con, $_POST['mensaje_bienvenida']))   : '';

if ($nombre === '') { echo "El nombre es obligatorio."; exit(); }

if ($id_encuesta == 0) {
	$SQL = "INSERT INTO enc_encuestas (nombre, descripcion, mensaje_bienvenida)
			VALUES ('$nombre', '$descripcion', '$mensaje_bienvenida')";
} else {
	$SQL = "UPDATE enc_encuestas
			SET nombre = '$nombre',
			    descripcion = '$descripcion',
			    mensaje_bienvenida = '$mensaje_bienvenida'
			WHERE id_encuesta = $id_encuesta AND baja = 0";
}

if (mysqli_query($con, $SQL)) {
	echo "ok";
} else {
	echo "Error al guardar: " . mysqli_error($con);
}
?>
