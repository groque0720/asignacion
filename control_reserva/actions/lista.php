<?php
/*
 * Listado paginado de reservas para data.php. Deja el resultado en $salida.
 * Requiere: $con (config_app.php) y cr_lista (funciones/consulta.php).
 */

$q    = isset($_GET['q'])    ? trim($_GET['q'])           : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per  = isset($_GET['per'])  ? (int)$_GET['per']          : 20;   // el original paginaba de a 20
if ($per < 1)   $per = 20;
if ($per > 200) $per = 200;
$offset = ($page - 1) * $per;

$r = cr_lista($con, $q, $per, $offset);
if (isset($r['error'])) { http_response_code(500); $salida = ["error" => $r['error']]; return; }

$salida = [
    'ok'    => true,
    'page'  => $page,
    'per'   => $per,
    'total' => $r['total'],
    'pages' => $per > 0 ? (int)ceil($r['total'] / $per) : 1,
    'rows'  => $r['rows'],
];
