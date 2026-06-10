<?php
/*
 * Historial de cambios de una celda. Deja el resultado en $salida.
 * Requiere: $con, $UPLOADS_URL, $US_ESTADOS.
 */

$id_unidad = isset($_GET['id_unidad']) ? (int)$_GET['id_unidad'] : 0;
$id_item   = isset($_GET['id_item'])   ? (int)$_GET['id_item']   : 0;

if (!$id_unidad || !$id_item) {
    $salida = ['ok' => false, 'error' => 'Parámetros inválidos'];
    return;
}

$r = mysqli_query($con, "SELECT h.*, u.nombre
    FROM usados_docs_historial h
    LEFT JOIN usuarios u ON h.id_usuario = u.idusuario
    WHERE h.id_unidad = $id_unidad AND h.id_item = $id_item
    ORDER BY h.fecha DESC
    LIMIT 50");

$rows = [];
while ($h = mysqli_fetch_assoc($r)) {
    $ea = ($h['estado_anterior'] !== null) ? ($US_ESTADOS[(int)$h['estado_anterior']] ?? null) : null;
    $en = $US_ESTADOS[(int)$h['estado_nuevo']] ?? null;
    $rows[] = [
        'usuario'      => $h['nombre'] ?? 'Desconocido',
        'fecha'        => date('d/m/Y H:i', strtotime($h['fecha'])),
        'estado_ant'   => $ea ? ['label' => $ea['label'], 'icon' => $ea['icon'], 'class' => $ea['class']] : null,
        'estado_nuevo' => $en ? ['label' => $en['label'], 'icon' => $en['icon'], 'class' => $en['class']] : null,
        'observacion'  => $h['observacion'],
        'archivo'      => $h['archivo'] ? ['nombre' => $h['archivo'], 'url' => $UPLOADS_URL . rawurlencode($h['archivo'])] : null,
    ];
}

$salida = ['ok' => true, 'historial' => $rows];
