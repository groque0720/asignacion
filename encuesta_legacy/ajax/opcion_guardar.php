<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_pregunta  = isset($_POST['id_pregunta'])  ? (int)$_POST['id_pregunta']                          : 0;
$texto_opcion = isset($_POST['texto_opcion']) ? trim(mysqli_real_escape_string($con, $_POST['texto_opcion'])) : '';

if ($id_pregunta <= 0) { echo "Pregunta inválida."; exit(); }
if ($texto_opcion === '') { echo "El texto es obligatorio."; exit(); }

// Verificar que la pregunta existe y es de tipo 3 o 4
$res = mysqli_query($con, "SELECT tipo_pregunta FROM enc_preguntas WHERE id_pregunta = $id_pregunta AND baja = 0");
if (mysqli_num_rows($res) == 0) { echo "Pregunta no encontrada."; exit(); }
$preg = mysqli_fetch_array($res);
if (!in_array($preg['tipo_pregunta'], [3, 4])) { echo "Este tipo de pregunta no admite opciones."; exit(); }

// Calcular nro_orden
$res_max  = mysqli_query($con, "SELECT MAX(nro_orden) AS max_ord FROM enc_opciones WHERE id_pregunta = $id_pregunta AND baja = 0");
$row_max  = mysqli_fetch_array($res_max);
$nro_ord  = ($row_max['max_ord'] !== null) ? $row_max['max_ord'] + 1 : 1;

$SQL = "INSERT INTO enc_opciones (id_pregunta, texto_opcion, nro_orden)
		VALUES ($id_pregunta, '$texto_opcion', $nro_ord)";
if (mysqli_query($con, $SQL)) {
	echo "ok:" . mysqli_insert_id($con);
} else {
	echo "Error: " . mysqli_error($con);
}
?>
