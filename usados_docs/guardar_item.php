<?php
/**
 * guardar_item.php — Crea o edita un ítem del catálogo.
 * POST · Solo perfiles 1 y 2 · Devuelve JSON.
 */
require_once '_init.php';

header('Content-Type: application/json; charset=utf-8');

if (!$es_admin) {
    echo json_encode(['ok' => false, 'error' => 'Sin permisos']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_item     = isset($_POST['id_item'])     ? (int)$_POST['id_item']               : 0;
$nombre      = isset($_POST['nombre'])      ? trim($_POST['nombre'])                : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion'])           : '';
$posicion    = isset($_POST['posicion'])    ? (int)$_POST['posicion']               : 1;
// Checkbox: presente = 1, ausente = 0
$activo      = isset($_POST['activo'])      ? 1                                     : 0;

if ($nombre === '') {
    echo json_encode(['ok' => false, 'error' => 'El nombre del ítem es requerido']);
    exit;
}

$nombre_esc      = mysqli_real_escape_string($con, $nombre);
$descripcion_esc = mysqli_real_escape_string($con, $descripcion);

if ($id_item > 0) {
    // UPDATE
    $sql = "UPDATE usados_docs_items SET
                nombre      = '$nombre_esc',
                descripcion = '$descripcion_esc',
                posicion    = $posicion,
                activo      = $activo
            WHERE id_item = $id_item";
    $ok  = mysqli_query($con, $sql);
    $new_id = $id_item;
} else {
    // INSERT
    $sql = "INSERT INTO usados_docs_items (nombre, descripcion, posicion, activo)
            VALUES ('$nombre_esc', '$descripcion_esc', $posicion, 1)";
    $ok  = mysqli_query($con, $sql);
    $new_id = (int)mysqli_insert_id($con);
}

if ($ok) {
    echo json_encode(['ok' => true, 'id_item' => $new_id, 'accion' => $id_item > 0 ? 'editado' : 'creado']);
} else {
    echo json_encode(['ok' => false, 'error' => mysqli_error($con)]);
}
