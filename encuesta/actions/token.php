<?php
/*
 * Genera (o devuelve) el token de encuesta de una unidad 0km entregada.
 * Deja el resultado en $salida. Requiere $con + helpers enc_* + constantes ENCUESTA_*.
 *
 * Entrada POST: id = asignaciones.id_unidad
 * Salida: { ok, token, link, completada, ya_existia, encuesta }  ó  { ok:false, error }
 *
 * Conserva el comportamiento del módulo viejo: al generar un token nuevo marca
 * asignaciones.con_encuesta = 1 (seteando antes las @vars de auditoría del trigger).
 */

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { $salida = ['ok' => false, 'error' => 'Unidad inválida']; return; }

// La unidad debe estar entregada
$u = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_unidad, con_encuesta FROM asignaciones
     WHERE id_unidad = $id AND entregada = 1 AND borrar = 0 AND guardado = 1 LIMIT 1"));
if (!$u) { $salida = ['ok' => false, 'error' => 'La unidad no figura como entregada']; return; }

// Si ya está completada, no se regenera
if ((int)$u['con_encuesta'] === 2) {
    $salida = ['ok' => false, 'error' => 'Esta unidad ya tiene una encuesta completada.']; return;
}

// ¿Ya hay token para esta unidad? (lo devuelve sin tocar nada)
$tk = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_token, token, completada FROM enc_tokens WHERE id_asignacion = $id LIMIT 1"));
if ($tk) {
    $salida = [
        'ok'         => true,
        'token'      => $tk['token'],
        'link'       => BASE_URL_ENCUESTA . '?t=' . $tk['token'],
        'completada' => (int)$tk['completada'],
        'ya_existia' => true,
    ];
    return;
}

// Encuesta activa
$enc = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_encuesta, nombre FROM enc_encuestas WHERE activa = 1 AND baja = 0 LIMIT 1"));
if (!$enc) {
    $salida = ['ok' => false, 'error' => 'No hay una encuesta activa. Activá una en Configurar.']; return;
}
$id_encuesta = (int)$enc['id_encuesta'];

// Generar token único
do {
    $token  = enc_generar_token($id);
    $tokene = mysqli_real_escape_string($con, $token);
    $dup = mysqli_num_rows(mysqli_query($con,
        "SELECT 1 FROM enc_tokens WHERE token = '$tokene' LIMIT 1"));
} while ($dup > 0);

$tokene = mysqli_real_escape_string($con, $token);
$ok = mysqli_query($con,
    "INSERT INTO enc_tokens (token, id_asignacion, id_encuesta)
     VALUES ('$tokene', $id, $id_encuesta)");
if (!$ok) { $salida = ['ok' => false, 'error' => 'No se pudo generar el token: ' . mysqli_error($con)]; return; }

// Marcar asignaciones.con_encuesta = 1 (pendiente). Setear las @vars de auditoría primero.
enc_set_audit($con);
mysqli_query($con, "UPDATE asignaciones SET con_encuesta = 1 WHERE id_unidad = $id");

$salida = [
    'ok'         => true,
    'token'      => $token,
    'link'       => BASE_URL_ENCUESTA . '?t=' . $token,
    'completada' => 0,
    'ya_existia' => false,
    'encuesta'   => enc_utf8($enc['nombre']),
];
