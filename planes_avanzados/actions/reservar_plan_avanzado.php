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

$estado_id = 2;
$usuario_venta_id = $userId;
$monto_reserva = convertirNumero($_POST['monto_reserva']);
$fecha_reserva = $_POST['fecha_reserva'];
$modelo_version_retirar = $_POST['modelo_version_retirar'];
$hora_reserva = $_POST['hora_reserva'];
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

if ($fecha_nacimiento) {
    $fecha_nacimiento = "'".$fecha_nacimiento."'";
} else {
    $fecha_nacimiento = "NULL";
}

$planUuId = $_POST['planUuId'];
$SQL = "UPDATE tpa_planes_avanzados SET ";
$SQL .= "usuario_venta_id = $usuario_venta_id, ";
$SQL .= "monto_reserva = $monto_reserva, ";
$SQL .= "fecha_reserva = '$fecha_reserva', ";
$SQL .= "hora_reserva = '$hora_reserva', ";
$SQL .= "modelo_version_retirar = '$modelo_version_retirar', ";
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