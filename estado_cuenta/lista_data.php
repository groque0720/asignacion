<?php
/*
 * Lista de clientes activos (versión moderna de ventas/web/pagos_clientes.php).
 * JSON paginado. Cada fila enlaza al estado de cuenta (index.php?IDrecord=idcliente).
 */
header('Content-Type: application/json; charset=utf-8');
@session_start();
include("funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$suc  = isset($_GET['suc'])  ? (int)$_GET['suc']            : (int)($_SESSION['idsuc'] ?? 0);
$q    = isset($_GET['q'])    ? trim($_GET['q'])             : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page'])   : 1;
$per  = isset($_GET['per'])  ? (int)$_GET['per']            : 25;
if ($per < 1)   $per = 25;
if ($per > 200) $per = 200;
$offset = ($page - 1) * $per;
$qe = mysqli_real_escape_string($con, $q);

$W = "r.anulada <> 1 AND r.enviada >= '1'";
if ($suc > 0) $W .= " AND u.idsucursal = ".$suc;
if ($q !== '') {
    $W .= " AND (c.nombre LIKE '%$qe%' OR c.nrodoc LIKE '%$qe%' OR u.nombre LIKE '%$qe%'".
          " OR r.idreserva LIKE '%$qe%' OR r.nrounidad LIKE '%$qe%' OR r.interno LIKE '%$qe%')";
}

$FROM = "FROM reservas r
    INNER JOIN usuarios u ON u.idusuario = r.idusuario
    INNER JOIN clientes c ON c.idcliente = r.idcliente";

$filas = (int)mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) n $FROM WHERE $W"))['n'];

$sql = "SELECT r.idreserva, r.idcliente, r.idcredito, r.compra, r.detalleu,
               r.estadopago, r.cancelada,
               c.nombre AS cliente, u.nombre AS asesor,
               g.grupo AS grupo, m.modelo AS modelo,
               cr.estado AS credito_estado
        $FROM
        LEFT JOIN grupos   g  ON g.idgrupo   = r.idgrupo
        LEFT JOIN modelos  m  ON m.idmodelo  = r.idmodelo
        LEFT JOIN creditos cr ON cr.idcredito = r.idcredito
        WHERE $W
        ORDER BY c.nombre ASC, u.nombre ASC
        LIMIT $per OFFSET $offset";
$res = mysqli_query($con, $sql);
if (!$res) { http_response_code(500); echo json_encode(["error"=>mysqli_error($con)]); exit; }

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    if ($r['compra'] === 'Nuevo') {
        $g = ($r['grupo']  && $r['grupo']  !== '--') ? $r['grupo']  : '';
        $m = ($r['modelo'] && $r['modelo'] !== '--') ? $r['modelo'] : '';
        $modelo = trim($g.' '.$m);
    } else {
        $modelo = (string)$r['detalleu'];
    }
    $rows[] = [
        'idreserva'      => (int)$r['idreserva'],
        'idcliente'      => (int)$r['idcliente'],
        'idcredito'      => (int)$r['idcredito'],
        'compra'         => $r['compra'],
        'asesor'         => $r['asesor'],
        'cliente'        => $r['cliente'],
        'modelo'         => $modelo,
        'estadopago'     => is_null($r['estadopago']) ? 0 : (int)$r['estadopago'],
        'cancelada'      => (int)$r['cancelada'],
        'credito_estado' => is_null($r['credito_estado']) ? 0 : (int)$r['credito_estado'],
    ];
}

echo json_encode([
    'ok'    => true,
    'page'  => $page,
    'per'   => $per,
    'total' => $filas,
    'pages' => $per > 0 ? (int)ceil($filas / $per) : 1,
    'rows'  => $rows,
], JSON_UNESCAPED_UNICODE);
mysqli_close($con);
