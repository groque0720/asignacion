<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") {
	echo json_encode(['ok' => false, 'msg' => 'Sin autorización.']); exit();
}
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) {
	echo json_encode(['ok' => false, 'msg' => 'Sin autorización.']); exit();
}
include_once("funciones/func_mysql.php");
conectar();

$id_asignacion = isset($_POST['id_asignacion']) ? (int)$_POST['id_asignacion'] : 0;
if ($id_asignacion <= 0) {
	echo json_encode(['ok' => false, 'msg' => 'ID de unidad inválido.']); exit();
}

// Verificar que la unidad existe y fue entregada
$SQL = "SELECT id_unidad, con_encuesta FROM asignaciones
		WHERE id_unidad = $id_asignacion AND entregada = 1 AND borrar = 0 AND guardado = 1
		LIMIT 1";
$res = mysqli_query($con, $SQL);
if (mysqli_num_rows($res) == 0) {
	echo json_encode(['ok' => false, 'msg' => 'Unidad no encontrada o no entregada.']); exit();
}
$unidad = mysqli_fetch_array($res);

// Si ya tiene encuesta completada
if ($unidad['con_encuesta'] == 2) {
	echo json_encode(['ok' => false, 'msg' => 'Esta unidad ya tiene una encuesta completada.']); exit();
}

// Verificar token existente (por si ya fue generado)
$SQL_tok = "SELECT token FROM enc_tokens WHERE id_asignacion = $id_asignacion LIMIT 1";
$res_tok  = mysqli_query($con, $SQL_tok);
if (mysqli_num_rows($res_tok) > 0) {
	$row = mysqli_fetch_array($res_tok);
	$link = BASE_URL_ENCUESTA . '?t=' . $row['token'];
	echo json_encode(['ok' => true, 'link' => $link, 'token' => $row['token']]); exit();
}

// Verificar que hay encuesta activa
$SQL_enc = "SELECT id_encuesta FROM enc_encuestas WHERE activa = 1 AND baja = 0 LIMIT 1";
$res_enc  = mysqli_query($con, $SQL_enc);
if (mysqli_num_rows($res_enc) == 0) {
	echo json_encode(['ok' => false, 'msg' => 'No hay ninguna encuesta activa. Configurá una en la sección Configurar Encuesta.']); exit();
}
$encuesta = mysqli_fetch_array($res_enc);
$id_encuesta = (int)$encuesta['id_encuesta'];

// Generar token único
do {
	$token = generarToken($id_asignacion);
	$check = mysqli_query($con, "SELECT id_token FROM enc_tokens WHERE token = '".mysqli_real_escape_string($con, $token)."' LIMIT 1");
} while (mysqli_num_rows($check) > 0);

// Insertar token
$token_esc = mysqli_real_escape_string($con, $token);
$SQL_ins = "INSERT INTO enc_tokens (token, id_asignacion, id_encuesta)
			VALUES ('$token_esc', $id_asignacion, $id_encuesta)";
if (!mysqli_query($con, $SQL_ins)) {
	echo json_encode(['ok' => false, 'msg' => 'Error al generar el token: ' . mysqli_error($con)]); exit();
}

// Actualizar asignaciones.con_encuesta = 1 (pendiente)
mysqli_query($con, "UPDATE asignaciones SET con_encuesta = 1 WHERE id_unidad = $id_asignacion");

$link = BASE_URL_ENCUESTA . '?t=' . $token;
echo json_encode(['ok' => true, 'link' => $link, 'token' => $token]);
?>
