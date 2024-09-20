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

$version_id = $_POST['version_id'];
$modalidad_id = $_POST['modalidad'];
$grupo_orden = $_POST['grupo_orden'];
$situacion_id = $_POST['situacion_id'];
$cuotas_pagadas_cantidad = $_POST['cuotas_pagadas_cantidad'];
$cuotas_pagadas_monto = convertirNumero($_POST['cuotas_pagadas_monto']);
$costo = convertirNumero($_POST['costo']);
$plus = convertirNumero($_POST['plus']);
$venta = convertirNumero($_POST['venta']);
$cuota_promedio = convertirNumero($_POST['cuota_promedio']);
$valor_unidad = convertirNumero($_POST['valor_unidad']);
$precio_final = convertirNumero($_POST['precio_final']);
$estado_id = $_POST['estado'];
$usuario_venta_id = $_POST['usuario_venta_id'];
$monto_reserva = convertirNumero($_POST['monto_reserva']);
$derecho_adjudicacion = convertirNumero($_POST['derecho_adjudicacion']);
$integracion = convertirNumero($_POST['integracion']);
$situacionIdActual = $_POST['situacionIdActual'];
$cliente = $_POST['cliente'];
$sexo = $_POST['sexo'];
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
$edad = $_POST['edad'];
$dni = $_POST['dni'];
$cuil = $_POST['cuil'];
$direccion = $_POST['direccion'];
$localidad = $_POST['localidad'];
$provincia = $_POST['provincia'];
$email = $_POST['email'];
$celular = $_POST['celular'];
$observaciones = $_POST['observaciones'];

if ($fecha_nacimiento) {
    $fecha_nacimiento = "'".$fecha_nacimiento."'";
} else {
    $fecha_nacimiento = "NULL";
}


if ($_POST['planUuId']!='') {
    $planUuId = $_POST['planUuId'];
    $SQL = "UPDATE tpa_planes_avanzados SET ";
    $SQL .= "version_id = '$version_id', ";
    $SQL .= "modalidad_id = '$modalidad', ";
    $SQL .= "grupo_orden = '$grupo_orden', ";
    $SQL .= "situacion_id = $situacion_id, ";
    $SQL .= "cuotas_pagadas_cantidad = '$cuotas_pagadas_cantidad', ";
    $SQL .= "cuotas_pagadas_monto = $cuotas_pagadas_monto, ";
    $SQL .= "integracion = $integracion, ";
    $SQL .= "derecho_adjudicacion = $derecho_adjudicacion, ";
    $SQL .= "costo = $costo, ";
    $SQL .= "plus = $plus, ";
    $SQL .= "venta = $venta, ";
    $SQL .= "precio_final = $precio_final, ";
    $SQL .= "cuota_promedio = $cuota_promedio, ";
    $SQL .= "valor_unidad = $valor_unidad, ";
    $SQL .= "usuario_venta_id = $usuario_venta_id, ";
    $SQL .= "monto_reserva = $monto_reserva, ";
    $SQL .= "observaciones = '$observaciones', ";
    $SQL .= "cliente = '$cliente', ";
    $SQL .= "sexo = '$sexo', ";
    $SQL .= "fecha_nacimiento = $fecha_nacimiento, ";
    $SQL .= "edad = '$edad', ";
    $SQL .= "dni = '$dni', ";
    $SQL .= "cuil = '$cuil', ";
    $SQL .= "direccion = '$direccion', ";
    $SQL .= "localidad = '$localidad', ";
    $SQL .= "provincia = '$provincia', ";
    $SQL .= "email = '$email', ";
    $SQL .= "celular = '$celular', ";
    $SQL .= "estado_id = $estado_id ";
    $SQL .= "WHERE uuid = '".$planUuId."'";
} else {
    $uuid = generarUUID();
    $SQL = "INSERT INTO tpa_planes_avanzados ( ";
    $SQL .= "uuid, version_id, modalidad_id,  grupo_orden, situacion_id, cuotas_pagadas_cantidad, cuotas_pagadas_monto, ";
    $SQL .= "costo, plus, venta, cuota_promedio, valor_unidad, precio_final, derecho_adjudicacion, integracion, observaciones ";
    $SQL .= ") VALUES ( ";
    $SQL .= " '$uuid', '$version_id', '$modalidad', '$grupo_orden', $situacion_id, '$cuotas_pagadas_cantidad', $cuotas_pagadas_monto, ";
    $SQL .= " $costo, $plus, $venta, $cuota_promedio, $valor_unidad, $precio_final, $derecho_adjudicacion, $integracion, '$observaciones' ";
    $SQL .= ")";
}

echo $SQL;


$result = mysqli_query($con, $SQL);

if (!$result) {
    die("Error al insertar datos: " . mysqli_error($con));
}


mysqli_close($con);
header("Location: ../?situacionId=$situacionIdActual");





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