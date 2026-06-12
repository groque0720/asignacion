<?php
/*
 * Lista los planes de la situación + modelo activos (estado opcional).
 * Deja el resultado en $salida; el endpoint data.php lo emite como JSON.
 * Requiere: $con (config_app.php) y las funciones tpa_* (funciones/consulta.php).
 *
 * Sin paginación: los volúmenes por situación/modelo son chicos; el filtro de
 * estado y la búsqueda se resuelven en el cliente sobre estas filas.
 */

list($sit, $modelo, $estado) = tpa_filtros();

$modelosActivos = tpa_modelos_activos($con);

// Si no vino modelo (o no es válido), usar el primer modelo activo.
$idsModelos = array_column($modelosActivos, 'id');
if ($modelo <= 0 || !in_array($modelo, $idsModelos, true)) {
    $modelo = $idsModelos[0] ?? 0;
}

$res = tpa_query_planes($con, $sit, $modelo, $estado);
if (!$res) { http_response_code(500); $salida = ["error" => mysqli_error($con)]; return; }

$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = tpa_map_row($r);

$salida = [
    'ok'             => true,
    'situacionId'    => $sit,
    'modeloActivo'   => $modelo,
    'estado'         => $estado,
    'modelosActivos' => $modelosActivos,
    'userId'         => $userId,
    'rows'           => $rows,
];
