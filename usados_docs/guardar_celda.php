<?php
/**
 * guardar_celda.php — Guarda el estado de una celda y registra en historial.
 * POST · Devuelve JSON.
 */
require_once '_init.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_unidad   = isset($_POST['id_unidad'])   ? (int)$_POST['id_unidad']              : 0;
$id_item     = isset($_POST['id_item'])     ? (int)$_POST['id_item']                : 0;
$estado      = isset($_POST['estado'])      ? (int)$_POST['estado']                 : 0;
$observacion = isset($_POST['observacion']) ? trim($_POST['observacion'])            : '';

if (!$id_unidad || !$id_item || !in_array($estado, [0, 1, 2, 3])) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

// ── Procesar archivo adjunto ────────────────────────────────────────────────
$archivo = null;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['archivo'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    if ($file['size'] > $max_size) {
        echo json_encode(['ok' => false, 'error' => 'El archivo supera los 2 MB permitidos']);
        exit;
    }

    $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $tipos_permitidos)) {
        echo json_encode(['ok' => false, 'error' => 'Tipo de archivo no permitido. Solo PDF e imágenes (JPG, PNG, GIF, WEBP).']);
        exit;
    }

    $ext_map = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/gif'       => 'gif',
        'image/webp'      => 'webp',
    ];
    $ext     = $ext_map[$mime];
    $archivo = $id_unidad . '_' . $id_item . '_' . time() . '.' . $ext;

    $uploads_dir = __DIR__ . '/uploads/';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploads_dir . $archivo)) {
        echo json_encode(['ok' => false, 'error' => 'Error al guardar el archivo en el servidor']);
        exit;
    }
}

// ── Estado anterior (para historial) ───────────────────────────────────────
$r       = mysqli_query($con, "SELECT estado FROM usados_docs_seguimiento
    WHERE id_unidad = $id_unidad AND id_item = $id_item");
$actual  = mysqli_fetch_assoc($r);
$estado_anterior = $actual ? (int)$actual['estado'] : null;

// ── Upsert seguimiento ──────────────────────────────────────────────────────
$obs_esc  = mysqli_real_escape_string($con, $observacion);
$arch_val = $archivo ? "'" . mysqli_real_escape_string($con, $archivo) . "'" : 'NULL';

// Columnas/valores para INSERT
$insert_cols = "id_unidad, id_item, estado, id_usuario, observacion";
$insert_vals = "$id_unidad, $id_item, $estado, $id_usuario, '$obs_esc'";
if ($archivo !== null) {
    $insert_cols .= ", archivo";
    $insert_vals .= ", " . $arch_val;
}

// SET para ON DUPLICATE KEY UPDATE
$update_sets = "estado = $estado, id_usuario = $id_usuario, observacion = '$obs_esc'";
if ($archivo !== null) {
    $update_sets .= ", archivo = " . $arch_val;
}

$sql = "INSERT INTO usados_docs_seguimiento ($insert_cols)
        VALUES ($insert_vals)
        ON DUPLICATE KEY UPDATE $update_sets";

if (!mysqli_query($con, $sql)) {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar: ' . mysqli_error($con)]);
    exit;
}

// ── Insertar en historial ───────────────────────────────────────────────────
$estado_ant_sql = $estado_anterior !== null ? $estado_anterior : 'NULL';
$arch_hist      = $archivo ? "'" . mysqli_real_escape_string($con, $archivo) . "'" : 'NULL';

mysqli_query($con, "INSERT INTO usados_docs_historial
    (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion, archivo)
    VALUES
    ($id_unidad, $id_item, $estado_ant_sql, $estado, $id_usuario, NOW(), '$obs_esc', $arch_hist)");

// ── Respuesta ───────────────────────────────────────────────────────────────
$nuevo = $ESTADOS[$estado];
echo json_encode([
    'ok'       => true,
    'estado'   => $estado,
    'label'    => $nuevo['label'],
    'icon'     => $nuevo['icon'],
    'class'    => $nuevo['class'],
    'archivo'  => $archivo !== null,
]);
