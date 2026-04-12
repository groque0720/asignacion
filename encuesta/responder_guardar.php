<?php
// Procesa y guarda las respuestas de la encuesta pública
include_once("funciones/func_mysql.php");
conectar();

$token       = isset($_POST['token'])       ? trim($_POST['token'])           : '';
$id_token    = isset($_POST['id_token'])    ? (int)$_POST['id_token']         : 0;
$id_encuesta = isset($_POST['id_encuesta']) ? (int)$_POST['id_encuesta']      : 0;
$resp_json   = isset($_POST['respuestas_json']) ? $_POST['respuestas_json']   : '';

if ($token === '' || $id_token <= 0) {
	header("Location: expirada.php"); exit();
}

// Validar token
$token_esc = mysqli_real_escape_string($con, $token);
$res_tok = mysqli_query($con, "SELECT * FROM enc_tokens WHERE token = '$token_esc' AND id_token = $id_token AND completada = 0 LIMIT 1");
if (mysqli_num_rows($res_tok) == 0) {
	header("Location: expirada.php?tipo=completada"); exit();
}
$tok_data = mysqli_fetch_array($res_tok);
$id_asignacion = (int)$tok_data['id_asignacion'];

// Decodificar respuestas
$respuestas = json_decode($resp_json, true);
if (!is_array($respuestas) || empty($respuestas)) {
	header("Location: responder.php?t=" . urlencode($token) . "&error=empty"); exit();
}

// Cargar preguntas de la encuesta
$SQL_preg = "SELECT * FROM enc_preguntas WHERE id_encuesta = $id_encuesta AND baja = 0 ORDER BY nro_orden ASC";
$res_preg = mysqli_query($con, $SQL_preg);
$preguntas = [];
while ($p = mysqli_fetch_array($res_preg)) {
	$preguntas[$p['id_pregunta']] = $p;
	// Cargar opciones
	$preguntas[$p['id_pregunta']]['opciones'] = [];
	if (in_array($p['tipo_pregunta'], [3, 4])) {
		$res_op = mysqli_query($con, "SELECT * FROM enc_opciones WHERE id_pregunta = {$p['id_pregunta']} AND baja = 0");
		while ($op = mysqli_fetch_array($res_op)) {
			$preguntas[$p['id_pregunta']]['opciones'][$op['id_opcion']] = $op;
		}
	}
}

// ── Insertar enc_respuestas (cabecera) ──────────────────────
$SQL_resp = "INSERT INTO enc_respuestas (id_token, id_asignacion, id_encuesta)
			 VALUES ($id_token, $id_asignacion, $id_encuesta)";
if (!mysqli_query($con, $SQL_resp)) {
	// Si ya existe (doble submit), redirigir a gracias
	header("Location: gracias.php"); exit();
}
$id_respuesta = mysqli_insert_id($con);

// ── Procesar cada pregunta ──────────────────────────────────
$valores_promedio = []; // array de valores para calcular promedio

foreach ($preguntas as $id_p => $preg) {
	$tipo    = (int)$preg['tipo_pregunta'];
	$pondera = (int)$preg['pondera'];

	$dato = isset($respuestas[$id_p]) ? $respuestas[$id_p] : null;
	$omitida = ($dato === null || (isset($dato['omitida']) && $dato['omitida']));

	$resp_valor = null;
	$resp_texto = null;
	$mostrada   = $omitida ? 0 : 1;

	if (!$omitida) {
		if ($tipo == 1) {
			// Escala 1-10
			$resp_valor = isset($dato['valor']) ? round(min(10, max(1, (float)$dato['valor'])), 2) : null;

		} elseif ($tipo == 2) {
			// Si/No: valor 1 = sí (10), 0 = no (0)
			if (isset($dato['valor'])) {
				$resp_valor = ((int)$dato['valor'] == 1) ? 10.0 : 0.0;
			}

		} elseif ($tipo == 3) {
			// Selección múltiple: (seleccionadas / total) * 10
			$opciones_sel = isset($dato['opciones']) ? (array)$dato['opciones'] : [];
			$total_ops    = count($preg['opciones']);
			if ($total_ops > 0) {
				$cant_sel    = count($opciones_sel);
				$resp_valor  = round(($cant_sel / $total_ops) * 10, 2);
			} else {
				$resp_valor = null;
				$pondera    = 0;
			}

		} elseif ($tipo == 4) {
			// Lista si/no: no pondera
			$resp_valor = null;
			$pondera    = 0;

		} elseif ($tipo == 5) {
			// Texto libre
			$resp_texto = isset($dato['texto']) ? trim($dato['texto']) : '';
			$pondera    = 0;
		}
	}

	// Insertar detalle
	$rv_sql = ($resp_valor !== null) ? $resp_valor : 'NULL';
	$rt_sql = ($resp_texto !== null) ? "'".mysqli_real_escape_string($con, $resp_texto)."'" : 'NULL';
	$SQL_det = "INSERT INTO enc_respuestas_detalle (id_respuesta, id_pregunta, respuesta_valor, respuesta_texto, mostrada)
				VALUES ($id_respuesta, $id_p, $rv_sql, $rt_sql, $mostrada)";
	mysqli_query($con, $SQL_det);
	$id_detalle = mysqli_insert_id($con);

	// Insertar opciones si aplica
	if (!$omitida && $tipo == 3) {
		$opciones_sel = isset($dato['opciones']) ? (array)$dato['opciones'] : [];
		foreach ($opciones_sel as $id_op) {
			$id_op = (int)$id_op;
			if (isset($preg['opciones'][$id_op])) {
				mysqli_query($con, "INSERT INTO enc_respuestas_opciones (id_detalle, id_opcion, valor_elegido) VALUES ($id_detalle, $id_op, 1)");
			}
		}
	}

	if (!$omitida && $tipo == 4) {
		$opciones_lsn = isset($dato['opciones']) ? (array)$dato['opciones'] : [];
		foreach ($opciones_lsn as $item) {
			if (!isset($item['id'])) continue;
			$id_op  = (int)$item['id'];
			$val_op = isset($item['val']) ? (int)$item['val'] : 0;
			if (isset($preg['opciones'][$id_op])) {
				mysqli_query($con, "INSERT INTO enc_respuestas_opciones (id_detalle, id_opcion, valor_elegido) VALUES ($id_detalle, $id_op, $val_op)");
			}
		}
	}

	// Acumular para promedio
	if (!$omitida && $pondera && $resp_valor !== null) {
		$valores_promedio[] = $resp_valor;
	}
}

// ── Calcular promedio ───────────────────────────────────────
$promedio = null;
if (count($valores_promedio) > 0) {
	$promedio = round(array_sum($valores_promedio) / count($valores_promedio), 2);
}

// ── Actualizar cabecera con promedio ────────────────────────
$prom_sql = ($promedio !== null) ? $promedio : 'NULL';
mysqli_query($con, "UPDATE enc_respuestas SET resultado_promedio = $prom_sql WHERE id_respuesta = $id_respuesta");

// ── Marcar token como completado ───────────────────────────
mysqli_query($con, "UPDATE enc_tokens SET completada = 1, fecha_respuesta = NOW() WHERE id_token = $id_token");

// ── Actualizar asignacion con_encuesta = 2 ─────────────────
mysqli_query($con, "UPDATE asignaciones SET con_encuesta = 2 WHERE id_unidad = $id_asignacion");

header("Location: gracias.php");
exit();
?>
