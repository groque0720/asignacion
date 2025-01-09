<?php
include("../funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

@session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
    //si no existe, envio a la página de autentificacion
    header("Location: ../login");
    //ademas salgo de este script
    exit();
}

// $allowedOrigins = [
//     'https://derkayvargas.com',
//     // 'https://otraaplicacion.com'
//   ];
  
//   // Obtener el origen de la solicitud
//   $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
  
//   if ($origin && in_array($origin, $allowedOrigins)) {
//     header('Access-Control-Allow-Origin: ' . $origin);
//     header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
//     header('Access-Control-Allow-Headers: Content-Type, Authorization');
//   }


$SQL = "SELECT
    tpa_planes_versiones.version, 
    tpa_planes_avanzados.id, 
    tpa_planes_avanzados.grupo_orden, 
    tpa_modalidades.modalidad, 
    tpa_planes_avanzados.cuotas_pagadas_cantidad, 
    tpa_planes_modelos.modelo, 
    tpa_planes_avanzados.venta
FROM
    tpa_planes_avanzados
    INNER JOIN tpa_modalidades ON tpa_planes_avanzados.modalidad_id = tpa_modalidades.id
    INNER JOIN tpa_planes_versiones ON tpa_planes_avanzados.version_id = tpa_planes_versiones.id
    INNER JOIN tpa_planes_modelos ON tpa_planes_versiones.modelo_id = tpa_planes_modelos.id";

$planes = mysqli_query($con, $SQL);

$resultados = [];
if ($planes) {
    while ($fila = mysqli_fetch_assoc($planes)) {
        $resultados[] = $fila;
    }
}

mysqli_close($con);

// Enviar los datos como JSON
header('Content-Type: application/json');
echo json_encode($resultados);
?>
