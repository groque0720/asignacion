<?php
/*
 * Listado paginado de clientes activos para lista_data.php.
 * Deja el resultado en $salida; el endpoint lo emite como JSON.
 *
 * Requiere: $con (config_app.php) y ec_lista (funciones/consulta.php).
 */

$suc  = isset($_GET['suc'])  ? (int)$_GET['suc']          : (int)($_SESSION['idsuc'] ?? 0);
$q    = isset($_GET['q'])    ? trim($_GET['q'])           : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per  = isset($_GET['per'])  ? (int)$_GET['per']          : 25;
if ($per < 1)   $per = 25;
if ($per > 200) $per = 200;
$offset = ($page - 1) * $per;

$r = ec_lista($con, $suc, $q, $per, $offset);
if (isset($r['error'])) { http_response_code(500); $salida = ["error" => $r['error']]; return; }

$salida = [
    'ok'    => true,
    'page'  => $page,
    'per'   => $per,
    'total' => $r['total'],
    'pages' => $per > 0 ? (int)ceil($r['total'] / $per) : 1,
    'rows'  => $r['rows'],
];
