<?php
/*
 * Detalle de una celda: estado, observación, meta y lista unificada de adjuntos.
 * Deja el resultado en $salida; celda.php lo emite como JSON.
 *
 * Adjuntos (tipos, igual que en el módulo viejo):
 *   'adjunto'  → fila de usados_docs_archivos (id real)        [borrado normal]
 *   'actual'   → archivo legacy en usados_docs_seguimiento     [id = 0]
 * Requiere: $con, $UPLOADS_URL, $US_ESTADOS.
 */

$id_unidad = isset($_GET['id_unidad']) ? (int)$_GET['id_unidad'] : 0;
$id_item   = isset($_GET['id_item'])   ? (int)$_GET['id_item']   : 0;

if (!$id_unidad || !$id_item) {
    $salida = ['ok' => false, 'error' => 'Parámetros inválidos'];
    return;
}

// Ítem (nombre/descripcion para el encabezado del modal).
$ri   = mysqli_query($con, "SELECT nombre, descripcion FROM usados_docs_items WHERE id_item = $id_item");
$item = mysqli_fetch_assoc($ri);
if (!$item) {
    $salida = ['ok' => false, 'error' => 'Ítem no encontrado'];
    return;
}

// Seguimiento actual.
$rs  = mysqli_query($con, "SELECT s.*, u.nombre AS nombre_usuario
    FROM usados_docs_seguimiento s
    LEFT JOIN usuarios u ON s.id_usuario = u.idusuario
    WHERE s.id_unidad = $id_unidad AND s.id_item = $id_item");
$seg = mysqli_fetch_assoc($rs);

$estado      = $seg ? (int)$seg['estado'] : 0;
$observacion = $seg['observacion'] ?? '';
$arch_legacy = $seg['archivo'] ?? null;

// Lista unificada de adjuntos.
$adjuntos = [];

$ra = mysqli_query($con, "SELECT a.id, a.archivo, a.fecha, u.nombre
    FROM usados_docs_archivos a
    LEFT JOIN usuarios u ON a.id_usuario = u.idusuario
    WHERE a.id_unidad = $id_unidad AND a.id_item = $id_item
    ORDER BY a.fecha DESC");
while ($row = mysqli_fetch_assoc($ra)) {
    $adjuntos[] = [
        'tipo'   => 'adjunto',
        'id'     => (int)$row['id'],
        'nombre' => $row['archivo'],
        'url'    => $UPLOADS_URL . rawurlencode($row['archivo']),
        'meta'   => trim(($row['nombre'] ?? 'Desconocido') . ($row['fecha'] ? ', ' . date('d/m/y H:i', strtotime($row['fecha'])) : '')),
    ];
}
if ($arch_legacy) {
    $adjuntos[] = [
        'tipo'   => 'actual',
        'id'     => 0,
        'nombre' => $arch_legacy,
        'url'    => $UPLOADS_URL . rawurlencode($arch_legacy),
        'meta'   => trim(($seg['nombre_usuario'] ?? 'Desconocido') . (!empty($seg['updated_at']) ? ', ' . date('d/m/y H:i', strtotime($seg['updated_at'])) : '')),
    ];
}

$meta = '';
if ($seg) {
    $meta = trim(($seg['nombre_usuario'] ?? 'Desconocido')
        . (!empty($seg['updated_at']) ? ' — ' . date('d/m/Y H:i', strtotime($seg['updated_at'])) : ''));
}

$salida = [
    'ok'          => true,
    'id_unidad'   => $id_unidad,
    'id_item'     => $id_item,
    'item'        => $item['nombre'],
    'estado'      => $estado,
    'observacion' => $observacion,
    'meta'        => $meta,
    'adjuntos'    => $adjuntos,
];
