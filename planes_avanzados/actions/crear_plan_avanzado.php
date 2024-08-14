<?php

include("../funciones/func_mysql.php");

conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

$userId= $_SESSION["id"];
$planId = $_POST['planUuId'];

function convertirNumero($monto) {
    $monto = trim($monto);
    $monto = str_replace(['$', ' ','.'],'',$monto);
    $monto = str_replace(',','.',$monto);
    return (float)preg_replace('/[^0-9.]/', '', $monto);
}

function generarUUID() {
    // Genera un UUID versión 4
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    return $uuid;
}

$modelo_id = $_POST['modelo'];
$modalidad_id = $_POST['modalidad'];
$grupo_orden = $_POST['grupo_orden'];
$cuotas_pagadas_cantidad = $_POST['cuotas_pagadas_cantidad'];
$cuotas_pagadas_monto = convertirNumero($_POST['cuotas_pagadas_monto']);
$costo = convertirNumero($_POST['costo']);
$plus = convertirNumero($_POST['plus']);
$venta = convertirNumero($_POST['venta']);
$cuota_promedio = convertirNumero($_POST['cuota_promedio']);
$valor_unidad = convertirNumero($_POST['valor_unidad']);
$estado_id = $_POST['estado'];
$usuario_venta_id = $_POST['usuario_venta_id'];
$monto_reserva = convertirNumero($_POST['monto_reserva']);
$cliente = $_POST['cliente'];


if ($_POST['planUuId']!='') {
    $planUuId = $_POST['planUuId'];
    $SQL = "UPDATE tpa_planes_avanzados SET ";
    $SQL .= "modelo_id = '$modelo', ";
    $SQL .= "modalidad_id = '$modalidad', ";
    $SQL .= "grupo_orden = '$grupo_orden', ";
    $SQL .= "cuotas_pagadas_cantidad = '$cuotas_pagadas_cantidad', ";
    $SQL .= "cuotas_pagadas_monto = $cuotas_pagadas_monto, ";
    $SQL .= "costo = $costo, ";
    $SQL .= "plus = $plus, ";
    $SQL .= "venta = $venta, ";
    $SQL .= "cuota_promedio = $cuota_promedio, ";
    $SQL .= "valor_unidad = $valor_unidad, ";
    $SQL .= "usuario_venta_id = $usuario_venta_id, ";
    $SQL .= "monto_reserva = $monto_reserva, ";
    $SQL .= "cliente = '$cliente', ";
    $SQL .= "estado_id = $estado_id ";
    $SQL .= "WHERE uuid = '".$planUuId."'";
} else {
    $uuid = generarUUID();
    $SQL = "INSERT INTO tpa_planes_avanzados ( ";
    $SQL .= "uuid, modelo_id, modalidad_id, grupo_orden, cuotas_pagadas_cantidad, cuotas_pagadas_monto, ";
    $SQL .= "costo, plus, venta, cuota_promedio, valor_unidad ";
    $SQL .= ") VALUES ( ";
    $SQL .= " '$uuid', '$modelo', '$modalidad', '$grupo_orden', '$cuotas_pagadas_cantidad', $cuotas_pagadas_monto, ";
    $SQL .= " $costo, $plus, $venta, $cuota_promedio, $valor_unidad";
    $SQL .= ")";
}

echo $SQL;


$result = mysqli_query($con, $SQL);

if (!$result) {
    die("Error al insertar datos: " . mysqli_error($con));
}


mysqli_close($con);
header("Location: ../");





// $SQL = "INSERT INTO tpa_planes_avanzados ( ";
// $SQL .= "modelo_id, modalidad_id, grupo_orden, cuotas_pagadas_cantidad, cuotas_pagadas_monto, ";
// $SQL .= "costo, plus, venta, cuota_promedio, valor_unidad ";
// $SQL .= ") VALUES ( ";
// $SQL .= "'$modelo_id', '$modalidad_id', '$grupo_orden', '$cuotas_pagadas_cantidad', $cuotas_pagadas_monto, ";
// $SQL .= "$costo, $plus, $venta, $cuota_promedio, $valor_unidad ";
// $SQL .= ")";

// // Luego, ejecuta la consulta
// $result = mysqli_query($con, $SQL);

// if (!$result) {
//     die("Error al insertar datos: " . mysqli_error($con));
// }


// mysqli_close($con);
// header("Location: ../");
?>