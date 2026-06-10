<?php
/*
 * Lista todos los ítems para el panel admin. Deja el resultado en $salida.
 * Requiere: $con, $esAdmin.
 */

if (empty($esAdmin)) {
    http_response_code(403);
    $salida = ['ok' => false, 'error' => 'Sin permisos'];
    return;
}

$items = [];
$r = mysqli_query($con, "SELECT * FROM usados_docs_items ORDER BY posicion, id_item");
while ($row = mysqli_fetch_assoc($r)) {
    $items[] = [
        'id_item'     => (int)$row['id_item'],
        'nombre'      => $row['nombre'],
        'descripcion' => $row['descripcion'] ?? '',
        'posicion'    => (int)$row['posicion'],
        'activo'      => (int)$row['activo'],
    ];
}

$salida = ['ok' => true, 'items' => $items];
