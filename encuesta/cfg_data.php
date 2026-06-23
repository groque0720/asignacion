<?php
/*
 * Endpoint JSON de LECTURA del configurador (encuestas/preguntas/áreas/niveles).
 * ?res=encuestas | areas | niveles | preguntas (&id_encuesta=)
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';   // $con, $perfil, $puedeConfigurar
require __DIR__ . '/funciones/consulta.php';   // enc_utf8

header('Content-Type: application/json; charset=utf-8');
if (!$puedeConfigurar) { http_response_code(403); echo json_encode(['error' => 'Sin permiso']); exit(); }

$res = isset($_GET['res']) ? $_GET['res'] : '';
$out = ['ok' => true];

if ($res === 'encuestas') {
    $q = mysqli_query($con,
        "SELECT e.id_encuesta, e.nombre, e.descripcion, e.mensaje_bienvenida, e.activa, e.fecha_creacion,
                (SELECT COUNT(*) FROM enc_preguntas p WHERE p.id_encuesta = e.id_encuesta AND p.baja = 0) AS nro_preguntas
         FROM enc_encuestas e WHERE e.baja = 0
         ORDER BY e.activa DESC, e.id_encuesta DESC");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $items[] = [
            'id_encuesta'        => (int)$r['id_encuesta'],
            'nombre'             => enc_utf8($r['nombre']),
            'descripcion'        => enc_utf8($r['descripcion']),
            'mensaje_bienvenida' => enc_utf8($r['mensaje_bienvenida']),
            'activa'             => (int)$r['activa'],
            'nro_preguntas'      => (int)$r['nro_preguntas'],
        ];
    }
    $out['items'] = $items;

} elseif ($res === 'areas') {
    $q = mysqli_query($con, "SELECT id_area, nombre, color, nro_orden FROM enc_areas ORDER BY nro_orden ASC, nombre ASC");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $items[] = ['id_area' => (int)$r['id_area'], 'nombre' => enc_utf8($r['nombre']),
                    'color' => $r['color'], 'nro_orden' => (int)$r['nro_orden']];
    }
    $out['items'] = $items;

} elseif ($res === 'niveles') {
    $q = mysqli_query($con, "SELECT id_nivel, nombre, valor_desde, valor_hasta, color FROM enc_niveles ORDER BY valor_desde DESC");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $items[] = ['id_nivel' => (int)$r['id_nivel'], 'nombre' => enc_utf8($r['nombre']),
                    'desde' => (float)$r['valor_desde'], 'hasta' => (float)$r['valor_hasta'], 'color' => $r['color']];
    }
    $out['items'] = $items;

} elseif ($res === 'preguntas') {
    $idE = isset($_GET['id_encuesta']) ? (int)$_GET['id_encuesta'] : 0;
    $enc = mysqli_fetch_assoc(mysqli_query($con,
        "SELECT id_encuesta, nombre, activa FROM enc_encuestas WHERE id_encuesta = $idE AND baja = 0 LIMIT 1"));
    if (!$enc) { echo json_encode(['error' => 'Encuesta inexistente']); exit(); }

    // Áreas (para el select del form)
    $areas = [];
    $qa = mysqli_query($con, "SELECT id_area, nombre, color FROM enc_areas ORDER BY nro_orden ASC");
    while ($a = mysqli_fetch_assoc($qa)) $areas[] = ['id_area' => (int)$a['id_area'], 'nombre' => enc_utf8($a['nombre']), 'color' => $a['color']];

    $q = mysqli_query($con,
        "SELECT p.*, a.nombre AS area_nombre, a.color AS area_color, ref.texto_pregunta AS ref_texto
         FROM enc_preguntas p
         LEFT JOIN enc_areas a ON a.id_area = p.id_area
         LEFT JOIN enc_preguntas ref ON ref.id_pregunta = p.cond_id_preg_ref
         WHERE p.id_encuesta = $idE AND p.baja = 0
         ORDER BY p.nro_orden ASC, p.id_pregunta ASC");
    $items = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $idp = (int)$r['id_pregunta'];
        $ops = [];
        if (in_array((int)$r['tipo_pregunta'], [3, 4], true)) {
            $qo = mysqli_query($con, "SELECT id_opcion, texto_opcion FROM enc_opciones WHERE id_pregunta = $idp AND baja = 0 ORDER BY nro_orden ASC, id_opcion ASC");
            while ($o = mysqli_fetch_assoc($qo)) $ops[] = ['id' => (int)$o['id_opcion'], 'texto' => enc_utf8($o['texto_opcion'])];
        }
        $items[] = [
            'id_pregunta'    => $idp,
            'nro_orden'      => (int)$r['nro_orden'],
            'texto'          => enc_utf8($r['texto_pregunta']),
            'tipo'           => (int)$r['tipo_pregunta'],
            'pondera'        => (int)$r['pondera'],
            'es_observacion' => (int)$r['es_observacion'],
            'id_area'        => $r['id_area'] !== null ? (int)$r['id_area'] : null,
            'area_nombre'    => enc_utf8($r['area_nombre']),
            'area_color'     => $r['area_color'],
            'cond_ref'       => $r['cond_id_preg_ref'] !== null ? (int)$r['cond_id_preg_ref'] : null,
            'cond_op'        => $r['cond_operador'],
            'cond_val'       => $r['cond_valor'],
            'ref_texto'      => enc_utf8($r['ref_texto']),
            'opciones'       => $ops,
        ];
    }
    $out['encuesta'] = ['id_encuesta' => (int)$enc['id_encuesta'], 'nombre' => enc_utf8($enc['nombre']), 'activa' => (int)$enc['activa']];
    $out['areas']    = $areas;
    $out['items']    = $items;

} else {
    $out = ['error' => 'Recurso desconocido'];
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
mysqli_close($con);
