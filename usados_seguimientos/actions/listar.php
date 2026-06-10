<?php
/*
 * Motor del grid. Deja el resultado en $salida; data.php lo emite como JSON.
 * Requiere: $con y las funciones us_* (funciones/consulta.php).
 */

$r = us_listar($con);

if (isset($r['error'])) {
    http_response_code(500);
    $salida = ['ok' => false, 'error' => $r['error']];
    return;
}

$salida = [
    'ok'     => true,
    'items'  => $r['items'],
    'usados' => $r['usados'],
    'total'  => $r['total'],
];
