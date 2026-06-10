<?php
/*
 * Sube adjuntos a una celda SIN tocar estado/observación (input name="archivo[]").
 * Lo usa el sub-modal de carga: subida inmediata e independiente del botón Guardar.
 * Deja el resultado en $salida; subir_archivos.php lo emite como JSON.
 *
 * Requiere: $con, $puedeEditar, $UPLOADS_DIR, $UPLOADS_URL, $userId, $userName.
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

$id_unidad = isset($_POST['id_unidad']) ? (int)$_POST['id_unidad'] : 0;
$id_item   = isset($_POST['id_item'])   ? (int)$_POST['id_item']   : 0;

if (!$id_unidad || !$id_item) {
    $salida = ['ok' => false, 'error' => 'Datos inválidos'];
    return;
}

// Estado actual de la celda (para el historial del adjunto); 0 si aún no hay fila.
$rs     = mysqli_query($con, "SELECT estado FROM usados_docs_seguimiento
    WHERE id_unidad = $id_unidad AND id_item = $id_item");
$actual = mysqli_fetch_assoc($rs);
$estado = $actual ? (int)$actual['estado'] : 0;

$adj = us_guardar_adjuntos($con, $UPLOADS_DIR, $UPLOADS_URL, $id_unidad, $id_item, $estado, $userId, $userName ?? '');

$tiene_arch = $adj['guardados'] > 0;
if (!$tiene_arch) {
    $rc = mysqli_fetch_assoc(mysqli_query($con, "SELECT
        (SELECT COUNT(*) FROM usados_docs_archivos    WHERE id_unidad=$id_unidad AND id_item=$id_item) +
        (SELECT COUNT(*) FROM usados_docs_seguimiento WHERE id_unidad=$id_unidad AND id_item=$id_item AND archivo IS NOT NULL) AS c"));
    $tiene_arch = ((int)$rc['c']) > 0;
}

$salida = [
    'ok'         => true,
    'guardados'  => $adj['guardados'],
    'nuevos'     => $adj['nuevos'],
    'errores'    => $adj['errores'],
    'tiene_arch' => $tiene_arch,
];
