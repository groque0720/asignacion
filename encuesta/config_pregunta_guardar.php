<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_encuesta   = isset($_POST['id_encuesta'])  ? (int)$_POST['id_encuesta']  : 0;
$id_pregunta   = isset($_POST['id_pregunta'])  ? (int)$_POST['id_pregunta']  : 0;
$texto         = isset($_POST['texto_pregunta']) ? trim($_POST['texto_pregunta']) : '';
$tipo          = isset($_POST['tipo_pregunta']) ? (int)$_POST['tipo_pregunta'] : 1;
$pondera       = isset($_POST['pondera'])       ? 1 : 0;
$id_area       = isset($_POST['id_area']) && (int)$_POST['id_area'] > 0 ? (int)$_POST['id_area'] : 0;
$es_obs        = isset($_POST['es_observacion'])? 1 : 0;
$tiene_cond    = isset($_POST['tiene_condicion'])? 1 : 0;
$cond_preg     = ($tiene_cond && !empty($_POST['cond_id_preg_ref'])) ? (int)$_POST['cond_id_preg_ref'] : 0;
$cond_op       = ($tiene_cond && !empty($_POST['cond_operador'])) ? trim($_POST['cond_operador']) : '';
$cond_val      = ($tiene_cond && isset($_POST['cond_valor'])) ? trim(mysqli_real_escape_string($con, $_POST['cond_valor'])) : '';

if ($id_encuesta <= 0) { echo "Encuesta inválida."; exit(); }
if ($texto === '') { echo "El texto de la pregunta es obligatorio."; exit(); }

// Validar tipo
if (!in_array($tipo, [1,2,3,4,5])) { echo "Tipo de pregunta inválido."; exit(); }

// Tipos 4 y 5 nunca ponderan
if (in_array($tipo, [4, 5])) $pondera = 0;

// Tipo 5: siempre es observación (opcional el checkbox, por defecto sí)
if ($tipo != 5) $es_obs = 0;

// Condición: validar operador
$ops_validos = ['<', '<=', '=', '>=', '>', '!='];
if ($cond_op && !in_array($cond_op, $ops_validos)) { echo "Operador de condición inválido."; exit(); }

$texto_esc    = mysqli_real_escape_string($con, $texto);
$cond_op_esc  = mysqli_real_escape_string($con, $cond_op);
$cond_preg_sql = $cond_preg > 0 ? $cond_preg : 'NULL';
$cond_op_sql  = $cond_op   ? "'$cond_op_esc'" : 'NULL';
$cond_val_sql = ($cond_val !== '') ? "'$cond_val'" : 'NULL';
$id_area_sql  = $id_area > 0 ? $id_area : 'NULL';

if ($id_pregunta == 0) {
	// Nueva pregunta: calcular nro_orden
	$res_max = mysqli_query($con, "SELECT MAX(nro_orden) AS max_orden FROM enc_preguntas WHERE id_encuesta = $id_encuesta AND baja = 0");
	$row_max  = mysqli_fetch_array($res_max);
	$nro_orden = ($row_max['max_orden'] !== null) ? $row_max['max_orden'] + 1 : 1;

	$SQL = "INSERT INTO enc_preguntas
			(id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, id_area, es_observacion,
			 cond_id_preg_ref, cond_operador, cond_valor)
			VALUES ($id_encuesta, $nro_orden, '$texto_esc', $tipo, $pondera, $id_area_sql, $es_obs,
			        $cond_preg_sql, $cond_op_sql, $cond_val_sql)";
	if (mysqli_query($con, $SQL)) {
		echo "ok:" . mysqli_insert_id($con);
	} else {
		echo "Error: " . mysqli_error($con);
	}
} else {
	$SQL = "UPDATE enc_preguntas
			SET texto_pregunta  = '$texto_esc',
			    tipo_pregunta   = $tipo,
			    pondera         = $pondera,
			    id_area         = $id_area_sql,
			    es_observacion  = $es_obs,
			    cond_id_preg_ref = $cond_preg_sql,
			    cond_operador    = $cond_op_sql,
			    cond_valor       = $cond_val_sql
			WHERE id_pregunta = $id_pregunta AND id_encuesta = $id_encuesta AND baja = 0";
	if (mysqli_query($con, $SQL)) {
		echo "ok:$id_pregunta";
	} else {
		echo "Error: " . mysqli_error($con);
	}
}
?>
