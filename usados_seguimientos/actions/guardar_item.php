<?php
/*
 * Crea o edita un ítem del catálogo. Deja el resultado en $salida.
 * Requiere: $con, $esAdmin.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $salida = ['ok' => false, 'error' => 'Método no permitido'];
    return;
}
if (empty($esAdmin)) {
    http_response_code(403);
    $salida = ['ok' => false, 'error' => 'Sin permisos'];
    return;
}

$id_item     = isset($_POST['id_item'])     ? (int)$_POST['id_item']      : 0;
$nombre      = isset($_POST['nombre'])      ? trim($_POST['nombre'])      : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$posicion    = isset($_POST['posicion'])    ? (int)$_POST['posicion']     : 1;
$activo      = !empty($_POST['activo']) ? 1 : 0;   // checkbox: presente=1

if ($nombre === '') {
    $salida = ['ok' => false, 'error' => 'El nombre del ítem es requerido'];
    return;
}

$nombre_esc      = mysqli_real_escape_string($con, $nombre);
$descripcion_esc = mysqli_real_escape_string($con, $descripcion);

if ($id_item > 0) {
    $sql = "UPDATE usados_docs_items SET
                nombre = '$nombre_esc', descripcion = '$descripcion_esc',
                posicion = $posicion, activo = $activo
            WHERE id_item = $id_item";
    $ok     = mysqli_query($con, $sql);
    $new_id = $id_item;
} else {
    // Alta: siempre activo.
    $sql = "INSERT INTO usados_docs_items (nombre, descripcion, posicion, activo)
            VALUES ('$nombre_esc', '$descripcion_esc', $posicion, 1)";
    $ok     = mysqli_query($con, $sql);
    $new_id = (int)mysqli_insert_id($con);
}

if ($ok) {
    $salida = ['ok' => true, 'id_item' => $new_id, 'accion' => $id_item > 0 ? 'editado' : 'creado'];
} else {
    http_response_code(500);
    $salida = ['ok' => false, 'error' => mysqli_error($con)];
}
