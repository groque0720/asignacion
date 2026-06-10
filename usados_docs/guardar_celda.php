<?php
/**
 * guardar_celda.php — Guarda el estado de una celda y registra en historial.
 * Soporta múltiples archivos adjuntos (input name="archivo[]").
 * POST · Devuelve JSON.
 */
require_once '_init.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_unidad   = isset($_POST['id_unidad'])   ? (int)$_POST['id_unidad']    : 0;
$id_item     = isset($_POST['id_item'])     ? (int)$_POST['id_item']      : 0;
$estado      = isset($_POST['estado'])      ? (int)$_POST['estado']       : 0;
$observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';

if (!$id_unidad || !$id_item || !in_array($estado, [0, 1, 2, 3])) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

// ── Estado anterior (para historial) ───────────────────────────────────────
$r       = mysqli_query($con, "SELECT estado FROM usados_docs_seguimiento
    WHERE id_unidad = $id_unidad AND id_item = $id_item");
$actual  = mysqli_fetch_assoc($r);
$estado_anterior = $actual ? (int)$actual['estado'] : null;

// ── Upsert seguimiento (estado + observación; los archivos van aparte) ──────
$obs_esc = mysqli_real_escape_string($con, $observacion);
$sql = "INSERT INTO usados_docs_seguimiento (id_unidad, id_item, estado, id_usuario, observacion)
        VALUES ($id_unidad, $id_item, $estado, $id_usuario, '$obs_esc')
        ON DUPLICATE KEY UPDATE estado = $estado, id_usuario = $id_usuario, observacion = '$obs_esc'";

if (!mysqli_query($con, $sql)) {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar: ' . mysqli_error($con)]);
    exit;
}

// ── Historial del cambio de estado ──────────────────────────────────────────
$estado_ant_sql = $estado_anterior !== null ? $estado_anterior : 'NULL';
mysqli_query($con, "INSERT INTO usados_docs_historial
    (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion, archivo)
    VALUES
    ($id_unidad, $id_item, $estado_ant_sql, $estado, $id_usuario, NOW(), '$obs_esc', NULL)");

// ── Procesar archivos adjuntos (múltiples) ──────────────────────────────────
$max_size = 5 * 1024 * 1024; // 5 MB por archivo
$tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$ext_map = [
    'application/pdf' => 'pdf',
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'image/gif'       => 'gif',
    'image/webp'      => 'webp',
];

$uploads_dir = __DIR__ . '/uploads/';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}

$guardados = 0;
$errores   = [];

if (!empty($_FILES['archivo']) && is_array($_FILES['archivo']['name'])) {
    $n     = count($_FILES['archivo']['name']);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    for ($i = 0; $i < $n; $i++) {
        $err = $_FILES['archivo']['error'][$i];
        if ($err === UPLOAD_ERR_NO_FILE) continue;

        $nombre_orig = $_FILES['archivo']['name'][$i];

        if ($err !== UPLOAD_ERR_OK) {
            $errores[] = $nombre_orig . ' (error de subida)';
            continue;
        }
        if ($_FILES['archivo']['size'][$i] > $max_size) {
            $errores[] = $nombre_orig . ' (supera 5 MB)';
            continue;
        }

        $mime = finfo_file($finfo, $_FILES['archivo']['tmp_name'][$i]);
        if (!in_array($mime, $tipos_permitidos)) {
            $errores[] = $nombre_orig . ' (tipo no permitido)';
            continue;
        }

        $ext     = $ext_map[$mime];
        $archivo = $id_unidad . '_' . $id_item . '_' . time() . '_' . $i . '.' . $ext;

        if (!move_uploaded_file($_FILES['archivo']['tmp_name'][$i], $uploads_dir . $archivo)) {
            $errores[] = $nombre_orig . ' (no se pudo guardar)';
            continue;
        }

        $arch_esc = mysqli_real_escape_string($con, $archivo);
        mysqli_query($con, "INSERT INTO usados_docs_archivos
            (id_unidad, id_item, archivo, id_usuario, fecha)
            VALUES ($id_unidad, $id_item, '$arch_esc', $id_usuario, NOW())");

        mysqli_query($con, "INSERT INTO usados_docs_historial
            (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion, archivo)
            VALUES ($id_unidad, $id_item, $estado, $estado, $id_usuario, NOW(),
                    '[Archivo adjuntado]', '$arch_esc')");

        $guardados++;
    }

    finfo_close($finfo);
}

// ── ¿La celda tiene al menos un archivo ahora? (nuevos + legacy) ────────────
$tiene_archivos = $guardados > 0;
if (!$tiene_archivos) {
    $rc = mysqli_fetch_assoc(mysqli_query($con, "SELECT
        (SELECT COUNT(*) FROM usados_docs_archivos WHERE id_unidad=$id_unidad AND id_item=$id_item) +
        (SELECT COUNT(*) FROM usados_docs_seguimiento WHERE id_unidad=$id_unidad AND id_item=$id_item AND archivo IS NOT NULL) AS c"));
    $tiene_archivos = ((int)$rc['c']) > 0;
}

// ── Respuesta ───────────────────────────────────────────────────────────────
$nuevo = $ESTADOS[$estado];
echo json_encode([
    'ok'       => true,
    'estado'   => $estado,
    'label'    => $nuevo['label'],
    'icon'     => $nuevo['icon'],
    'class'    => $nuevo['class'],
    'archivos' => $guardados,
    'archivo'  => $tiene_archivos,
    'errores'  => $errores,
]);
