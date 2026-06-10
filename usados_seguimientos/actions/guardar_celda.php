<?php
/*
 * Guarda el estado de una celda (estado + observación) y procesa adjuntos múltiples
 * (input name="archivo[]"). Registra el cambio y cada archivo en el historial.
 * Deja el resultado en $salida; guardar_celda.php lo emite como JSON.
 *
 * Requiere: $con, $puedeEditar, $UPLOADS_DIR, $userId, $US_ESTADOS.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $salida = ['ok' => false, 'error' => 'Método no permitido'];
    return;
}
if (empty($puedeEditar)) {
    http_response_code(403);
    $salida = ['ok' => false, 'error' => 'Sin permisos para editar'];
    return;
}

$id_unidad   = isset($_POST['id_unidad'])   ? (int)$_POST['id_unidad']    : 0;
$id_item     = isset($_POST['id_item'])     ? (int)$_POST['id_item']      : 0;
$estado      = isset($_POST['estado'])      ? (int)$_POST['estado']       : 0;
$observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';

if (!$id_unidad || !$id_item || !in_array($estado, [0, 1, 2, 3], true)) {
    $salida = ['ok' => false, 'error' => 'Datos inválidos'];
    return;
}

// Estado anterior (para historial).
$r       = mysqli_query($con, "SELECT estado FROM usados_docs_seguimiento
    WHERE id_unidad = $id_unidad AND id_item = $id_item");
$actual  = mysqli_fetch_assoc($r);
$estado_anterior = $actual ? (int)$actual['estado'] : null;

// Upsert seguimiento (estado + observación; los adjuntos van aparte).
$obs_esc = mysqli_real_escape_string($con, $observacion);
$sql = "INSERT INTO usados_docs_seguimiento (id_unidad, id_item, estado, id_usuario, observacion)
        VALUES ($id_unidad, $id_item, $estado, $userId, '$obs_esc')
        ON DUPLICATE KEY UPDATE estado = $estado, id_usuario = $userId, observacion = '$obs_esc'";
if (!mysqli_query($con, $sql)) {
    http_response_code(500);
    $salida = ['ok' => false, 'error' => 'Error al guardar: ' . mysqli_error($con)];
    return;
}

// Historial del cambio de estado.
$estado_ant_sql = $estado_anterior !== null ? $estado_anterior : 'NULL';
mysqli_query($con, "INSERT INTO usados_docs_historial
    (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion, archivo)
    VALUES ($id_unidad, $id_item, $estado_ant_sql, $estado, $userId, NOW(), '$obs_esc', NULL)");

// ── Adjuntos múltiples ──────────────────────────────────────────────────────
$max_size = 5 * 1024 * 1024; // 5 MB por archivo
$tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$ext_map = [
    'application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png',
    'image/gif' => 'gif', 'image/webp' => 'webp',
];

if (!is_dir($UPLOADS_DIR)) mkdir($UPLOADS_DIR, 0755, true);

$guardados = 0;
$errores   = [];

if (!empty($_FILES['archivo']) && is_array($_FILES['archivo']['name'])) {
    $n     = count($_FILES['archivo']['name']);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    for ($i = 0; $i < $n; $i++) {
        $err = $_FILES['archivo']['error'][$i];
        if ($err === UPLOAD_ERR_NO_FILE) continue;

        $nombre_orig = $_FILES['archivo']['name'][$i];
        if ($err !== UPLOAD_ERR_OK)                         { $errores[] = $nombre_orig . ' (error de subida)'; continue; }
        if ($_FILES['archivo']['size'][$i] > $max_size)     { $errores[] = $nombre_orig . ' (supera 5 MB)';    continue; }

        $mime = finfo_file($finfo, $_FILES['archivo']['tmp_name'][$i]);
        if (!in_array($mime, $tipos_permitidos, true))      { $errores[] = $nombre_orig . ' (tipo no permitido)'; continue; }

        $ext     = $ext_map[$mime];
        $archivo = $id_unidad . '_' . $id_item . '_' . time() . '_' . $i . '.' . $ext;

        if (!move_uploaded_file($_FILES['archivo']['tmp_name'][$i], $UPLOADS_DIR . $archivo)) {
            $errores[] = $nombre_orig . ' (no se pudo guardar)';
            continue;
        }

        $arch_esc = mysqli_real_escape_string($con, $archivo);
        mysqli_query($con, "INSERT INTO usados_docs_archivos
            (id_unidad, id_item, archivo, id_usuario, fecha)
            VALUES ($id_unidad, $id_item, '$arch_esc', $userId, NOW())");
        mysqli_query($con, "INSERT INTO usados_docs_historial
            (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion, archivo)
            VALUES ($id_unidad, $id_item, $estado, $estado, $userId, NOW(), '[Archivo adjuntado]', '$arch_esc')");

        $guardados++;
    }
    finfo_close($finfo);
}

// ¿La celda tiene al menos un archivo ahora? (nuevos + legacy)
$tiene_arch = $guardados > 0;
if (!$tiene_arch) {
    $rc = mysqli_fetch_assoc(mysqli_query($con, "SELECT
        (SELECT COUNT(*) FROM usados_docs_archivos    WHERE id_unidad=$id_unidad AND id_item=$id_item) +
        (SELECT COUNT(*) FROM usados_docs_seguimiento WHERE id_unidad=$id_unidad AND id_item=$id_item AND archivo IS NOT NULL) AS c"));
    $tiene_arch = ((int)$rc['c']) > 0;
}

$nuevo = $US_ESTADOS[$estado];
$salida = [
    'ok'         => true,
    'estado'     => $estado,
    'icon'       => $nuevo['icon'],
    'class'      => $nuevo['class'],
    'tiene_arch' => $tiene_arch,
    'archivos'   => $guardados,
    'errores'    => $errores,
];
