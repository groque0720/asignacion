<?php
/*
 * Genera (o devuelve) el token de encuesta de una unidad usada entregada.
 * Deja el resultado en $salida. Requiere $con + helpers eu_* + constantes EU_*.
 *
 * Entrada POST: id = asignaciones_usados.id_unidad
 * Salida: { ok, token, link, completada, ya_existia, encuesta }  ó  { ok:false, error }
 */

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { $salida = ['ok' => false, 'error' => 'Unidad inválida']; return; }

// La unidad debe estar entregada (la vista ya filtra entregado=1)
$u = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_unidad FROM view_asignaciones_usados_entregadas
     WHERE id_unidad = $id AND borrar = 0 AND guardado = 1 LIMIT 1"));
if (!$u) { $salida = ['ok' => false, 'error' => 'La unidad no figura como entregada']; return; }

// Encuesta activa
$enc = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_encuesta, nombre FROM encu_encuestas WHERE activa = 1 AND baja = 0 LIMIT 1"));
if (!$enc) { $salida = ['ok' => false, 'error' => 'No hay una encuesta activa. Activá una en Configurar.']; return; }
$id_encuesta = (int)$enc['id_encuesta'];

// ¿Ya hay token para esta unidad?
$tk = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_token, token, completada FROM encu_tokens WHERE id_asignacion = $id LIMIT 1"));

$ya_existia = false;
if ($tk) {
    $token = $tk['token'];
    $completada = (int)$tk['completada'];
    $ya_existia = true;
} else {
    // Generar token único
    do {
        $token  = eu_generar_token($id);
        $tokene = mysqli_real_escape_string($con, $token);
        $dup = mysqli_num_rows(mysqli_query($con,
            "SELECT 1 FROM encu_tokens WHERE token = '$tokene' LIMIT 1"));
    } while ($dup > 0);

    $tokene = mysqli_real_escape_string($con, $token);
    $ok = mysqli_query($con,
        "INSERT INTO encu_tokens (token, id_asignacion, id_encuesta)
         VALUES ('$tokene', $id, $id_encuesta)");
    if (!$ok) { $salida = ['ok' => false, 'error' => 'No se pudo generar el token: ' . mysqli_error($con)]; return; }
    $completada = 0;
}

$salida = [
    'ok'         => true,
    'token'      => $token,
    'link'       => EU_BASE_URL . '?t=' . $token,
    'completada' => $completada,
    'ya_existia' => $ya_existia,
    'encuesta'   => eu_utf8($enc['nombre']),
];
