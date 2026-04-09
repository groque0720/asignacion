<?php
/**
 * eliminar_archivo.php — Elimina un archivo adjunto (actual o de historial).
 * POST · Devuelve JSON.
 */
require_once '_init.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$tipo      = $_POST['tipo']      ?? '';
$id_unidad = (int)($_POST['id_unidad'] ?? 0);
$id_item   = (int)($_POST['id_item']   ?? 0);
$id_hist   = (int)($_POST['id_hist']   ?? 0);

if (!$id_unidad || !$id_item || !in_array($tipo, ['actual', 'historial'])) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

$uploads_dir = __DIR__ . '/uploads/';

if ($tipo === 'actual') {
    $r   = mysqli_query($con, "SELECT archivo, estado FROM usados_docs_seguimiento
        WHERE id_unidad = $id_unidad AND id_item = $id_item");
    $row = mysqli_fetch_assoc($r);

    if (!$row || !$row['archivo']) {
        echo json_encode(['ok' => false, 'error' => 'No hay archivo actual']);
        exit;
    }

    $archivo      = $row['archivo'];
    $estado_actual = (int)$row['estado'];
    $arch_esc      = mysqli_real_escape_string($con, $archivo);

    mysqli_query($con, "UPDATE usados_docs_seguimiento SET archivo = NULL
        WHERE id_unidad = $id_unidad AND id_item = $id_item");

    mysqli_query($con, "INSERT INTO usados_docs_historial
        (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion)
        VALUES ($id_unidad, $id_item, $estado_actual, $estado_actual, $id_usuario, NOW(),
                '[Archivo eliminado: $arch_esc]')");

    @unlink($uploads_dir . $archivo);

    echo json_encode(['ok' => true]);

} elseif ($tipo === 'historial') {
    if (!$id_hist) {
        echo json_encode(['ok' => false, 'error' => 'ID de historial requerido']);
        exit;
    }

    $r   = mysqli_query($con, "SELECT h.archivo, s.estado
        FROM usados_docs_historial h
        LEFT JOIN usados_docs_seguimiento s
            ON s.id_unidad = h.id_unidad AND s.id_item = h.id_item
        WHERE h.id = $id_hist AND h.id_unidad = $id_unidad AND h.id_item = $id_item");
    $row = mysqli_fetch_assoc($r);

    if (!$row || !$row['archivo']) {
        echo json_encode(['ok' => false, 'error' => 'No hay archivo en ese registro']);
        exit;
    }

    $archivo       = $row['archivo'];
    $estado_actual = (int)($row['estado'] ?? 0);
    $arch_esc      = mysqli_real_escape_string($con, $archivo);

    mysqli_query($con, "UPDATE usados_docs_historial SET archivo = NULL WHERE id = $id_hist");

    mysqli_query($con, "INSERT INTO usados_docs_historial
        (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion)
        VALUES ($id_unidad, $id_item, $estado_actual, $estado_actual, $id_usuario, NOW(),
                '[Archivo eliminado: $arch_esc]')");

    @unlink($uploads_dir . $archivo);

    echo json_encode(['ok' => true]);
}
