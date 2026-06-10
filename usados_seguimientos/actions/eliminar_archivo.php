<?php
/*
 * Elimina un adjunto. Deja el resultado en $salida; el endpoint lo emite como JSON.
 *   tipo 'adjunto' → fila de usados_docs_archivos (id en id_arch)
 *   tipo 'actual'  → archivo legacy en usados_docs_seguimiento.archivo
 * Borra de la DB, deja rastro en el historial y elimina el archivo físico.
 * Requiere: $con, $puedeEditar, $UPLOADS_DIR, $userId.
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

$tipo      = $_POST['tipo'] ?? '';
$id_unidad = (int)($_POST['id_unidad'] ?? 0);
$id_item   = (int)($_POST['id_item']   ?? 0);
$id_arch   = (int)($_POST['id_arch']   ?? 0);

if (!$id_unidad || !$id_item || !in_array($tipo, ['adjunto', 'actual'], true)) {
    $salida = ['ok' => false, 'error' => 'Datos inválidos'];
    return;
}

// Estado actual (para registrar en historial).
$rs            = mysqli_query($con, "SELECT estado FROM usados_docs_seguimiento
    WHERE id_unidad = $id_unidad AND id_item = $id_item");
$segrow        = mysqli_fetch_assoc($rs);
$estado_actual = (int)($segrow['estado'] ?? 0);

if ($tipo === 'adjunto') {
    if (!$id_arch) { $salida = ['ok' => false, 'error' => 'ID de adjunto requerido']; return; }

    $r   = mysqli_query($con, "SELECT archivo FROM usados_docs_archivos
        WHERE id = $id_arch AND id_unidad = $id_unidad AND id_item = $id_item");
    $row = mysqli_fetch_assoc($r);
    if (!$row || !$row['archivo']) { $salida = ['ok' => false, 'error' => 'Adjunto no encontrado']; return; }

    $archivo = $row['archivo'];
    mysqli_query($con, "DELETE FROM usados_docs_archivos WHERE id = $id_arch");

} else { // 'actual' (legacy)
    $r   = mysqli_query($con, "SELECT archivo FROM usados_docs_seguimiento
        WHERE id_unidad = $id_unidad AND id_item = $id_item");
    $row = mysqli_fetch_assoc($r);
    if (!$row || !$row['archivo']) { $salida = ['ok' => false, 'error' => 'No hay archivo actual']; return; }

    $archivo = $row['archivo'];
    mysqli_query($con, "UPDATE usados_docs_seguimiento SET archivo = NULL
        WHERE id_unidad = $id_unidad AND id_item = $id_item");
}

$arch_esc = mysqli_real_escape_string($con, $archivo);
mysqli_query($con, "INSERT INTO usados_docs_historial
    (id_unidad, id_item, estado_anterior, estado_nuevo, id_usuario, fecha, observacion)
    VALUES ($id_unidad, $id_item, $estado_actual, $estado_actual, $userId, NOW(),
            '[Archivo eliminado: $arch_esc]')");

@unlink($UPLOADS_DIR . $archivo);

// ¿Quedan adjuntos en la celda?
$rc = mysqli_fetch_assoc(mysqli_query($con, "SELECT
    (SELECT COUNT(*) FROM usados_docs_archivos    WHERE id_unidad=$id_unidad AND id_item=$id_item) +
    (SELECT COUNT(*) FROM usados_docs_seguimiento WHERE id_unidad=$id_unidad AND id_item=$id_item AND archivo IS NOT NULL) AS c"));

$salida = ['ok' => true, 'tiene_arch' => ((int)$rc['c']) > 0];
