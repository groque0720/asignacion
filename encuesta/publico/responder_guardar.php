<?php
/*
 * Guarda las respuestas de la encuesta pública 0km, calcula el promedio ponderado,
 * marca el token como completado Y actualiza asignaciones.con_encuesta = 2.
 * Responde JSON { ok } / { ok:false, error }.
 *
 * Ponderación:
 *  tipo 1 escala → valor directo 1-10  |  tipo 2 sí/no → sí=10, no=0
 *  tipo 3 múltiple → (seleccionadas/total)*10  |  tipo 4 lista sí/no → no pondera
 *  tipo 5 texto → no pondera  |  omitidas → mostrada=0, no entran al promedio
 */
require __DIR__ . '/bootstrap_publico.php';
require __DIR__ . '/../funciones/consulta.php';   // enc_set_audit (para el trigger de auditoría)
header('Content-Type: application/json; charset=utf-8');

$token       = isset($_POST['token'])           ? trim($_POST['token'])      : '';
$id_token    = isset($_POST['id_token'])        ? (int)$_POST['id_token']    : 0;
$id_encuesta = isset($_POST['id_encuesta'])     ? (int)$_POST['id_encuesta'] : 0;
$resp_json   = isset($_POST['respuestas_json']) ? $_POST['respuestas_json']  : '';

if ($token === '' || $id_token <= 0) { echo json_encode(['ok' => false, 'error' => 'Sesión inválida']); exit(); }

$te = mysqli_real_escape_string($con, $token);
$rt = mysqli_query($con, "SELECT id_asignacion, id_encuesta FROM enc_tokens
                          WHERE token = '$te' AND id_token = $id_token AND completada = 0 LIMIT 1");
if (!$rt || mysqli_num_rows($rt) === 0) { echo json_encode(['ok' => false, 'error' => 'Esta encuesta ya fue respondida o el enlace no es válido.']); exit(); }
$tok = mysqli_fetch_assoc($rt);
$id_asignacion = (int)$tok['id_asignacion'];
$id_encuesta   = (int)$tok['id_encuesta'];

$respuestas = json_decode($resp_json, true);
if (!is_array($respuestas)) $respuestas = [];

// Cargar preguntas + opciones de la encuesta
$preguntas = [];
$rp = mysqli_query($con, "SELECT * FROM enc_preguntas WHERE id_encuesta = $id_encuesta AND baja = 0 ORDER BY nro_orden ASC");
while ($p = mysqli_fetch_assoc($rp)) {
    $p['opciones'] = [];
    if (in_array((int)$p['tipo_pregunta'], [3, 4], true)) {
        $ro = mysqli_query($con, "SELECT id_opcion FROM enc_opciones WHERE id_pregunta = {$p['id_pregunta']} AND baja = 0");
        while ($o = mysqli_fetch_assoc($ro)) $p['opciones'][(int)$o['id_opcion']] = true;
    }
    $preguntas[(int)$p['id_pregunta']] = $p;
}

// Cabecera de respuesta (UNIQUE en id_token evita doble submit)
if (!mysqli_query($con, "INSERT INTO enc_respuestas (id_token, id_asignacion, id_encuesta)
                         VALUES ($id_token, $id_asignacion, $id_encuesta)")) {
    echo json_encode(['ok' => true]); exit();   // ya existía → tratado como completada
}
$id_respuesta = mysqli_insert_id($con);

$valores = [];
foreach ($preguntas as $id_p => $preg) {
    $tipo    = (int)$preg['tipo_pregunta'];
    $pondera = (int)$preg['pondera'];
    $dato    = isset($respuestas[$id_p]) ? $respuestas[$id_p] : null;
    $omitida = ($dato === null);

    $resp_valor = null; $resp_texto = null; $mostrada = $omitida ? 0 : 1;

    if (!$omitida) {
        if ($tipo == 1) {
            $resp_valor = isset($dato['valor']) ? round(min(10, max(1, (float)$dato['valor'])), 2) : null;
        } elseif ($tipo == 2) {
            if (isset($dato['valor'])) $resp_valor = ((int)$dato['valor'] === 1) ? 10.0 : 0.0;
        } elseif ($tipo == 3) {
            $sel = isset($dato['opciones']) ? (array)$dato['opciones'] : [];
            $tot = count($preg['opciones']);
            if ($tot > 0) $resp_valor = round((count($sel) / $tot) * 10, 2);
            else { $resp_valor = null; $pondera = 0; }
        } elseif ($tipo == 4) {
            $resp_valor = null; $pondera = 0;
        } elseif ($tipo == 5) {
            $resp_texto = isset($dato['texto']) ? trim($dato['texto']) : '';
            $pondera = 0;
        }
    }

    $rv = ($resp_valor !== null) ? $resp_valor : 'NULL';
    $rtx = ($resp_texto !== null) ? "'" . mysqli_real_escape_string($con, $resp_texto) . "'" : 'NULL';
    mysqli_query($con, "INSERT INTO enc_respuestas_detalle (id_respuesta, id_pregunta, respuesta_valor, respuesta_texto, mostrada)
                        VALUES ($id_respuesta, $id_p, $rv, $rtx, $mostrada)");
    $id_detalle = mysqli_insert_id($con);

    if (!$omitida && $tipo == 3) {
        foreach ((array)($dato['opciones'] ?? []) as $idop) {
            $idop = (int)$idop;
            if (isset($preg['opciones'][$idop]))
                mysqli_query($con, "INSERT INTO enc_respuestas_opciones (id_detalle, id_opcion, valor_elegido) VALUES ($id_detalle, $idop, 1)");
        }
    }
    if (!$omitida && $tipo == 4) {
        foreach ((array)($dato['opciones'] ?? []) as $item) {
            if (!isset($item['id'])) continue;
            $idop = (int)$item['id'];
            $val  = isset($item['val']) ? (int)$item['val'] : 0;
            if (isset($preg['opciones'][$idop]))
                mysqli_query($con, "INSERT INTO enc_respuestas_opciones (id_detalle, id_opcion, valor_elegido) VALUES ($id_detalle, $idop, $val)");
        }
    }

    if (!$omitida && $pondera && $resp_valor !== null) $valores[] = $resp_valor;
}

$promedio = count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) : null;
$ps = ($promedio !== null) ? $promedio : 'NULL';
mysqli_query($con, "UPDATE enc_respuestas SET resultado_promedio = $ps WHERE id_respuesta = $id_respuesta");
mysqli_query($con, "UPDATE enc_tokens SET completada = 1, fecha_respuesta = NOW() WHERE id_token = $id_token");

// Marcar la unidad como encuesta completada (con_encuesta = 2). Setear @vars de auditoría primero.
enc_set_audit($con);
mysqli_query($con, "UPDATE asignaciones SET con_encuesta = 2 WHERE id_unidad = $id_asignacion");

echo json_encode(['ok' => true]);
