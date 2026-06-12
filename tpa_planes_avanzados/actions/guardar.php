<?php
/*
 * Crea (INSERT con UUID nuevo) o edita (UPDATE) un plan. SOLO admin.
 * Deja el resultado en $salida; el endpoint guardar.php lo emite como JSON.
 *
 * Reemplaza crear_plan_avanzado.php del módulo viejo (sin el `echo $SQL` de debug).
 * Requiere: $con, $puedeEditar (config_app.php) y tpa_* (funciones/consulta.php).
 */

if (empty($puedeEditar)) {
    http_response_code(403);
    $salida = ["ok" => false, "error" => "Sin permiso para editar planes."];
    return;
}

function tpa_uuid_v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

// Campos comunes (saneados).
$version_id   = (int)($_POST['version_id'] ?? 0);
$modalidad_id = (int)($_POST['modalidad_id'] ?? 0);
$situacion_id = (int)($_POST['situacion_id'] ?? 1);
$estado_id    = (int)($_POST['estado_id'] ?? 1);
$grupo_orden  = tpa_txt($con, $_POST['grupo_orden'] ?? '');
$cuotas_cant  = (int)($_POST['cuotas_pagadas_cantidad'] ?? 0);

$cuotas_monto = tpa_num($_POST['cuotas_pagadas_monto'] ?? 0);
$costo        = tpa_num($_POST['costo'] ?? 0);
$cesion       = tpa_num($_POST['cesion'] ?? 0);
$plus         = tpa_num($_POST['plus'] ?? 0);
$venta        = tpa_num($_POST['venta'] ?? 0);
$cuota_prom   = tpa_num($_POST['cuota_promedio'] ?? 0);
$valor_unidad = tpa_num($_POST['valor_unidad'] ?? 0);
$integracion  = tpa_num($_POST['integracion'] ?? 0);
$derecho_adj  = tpa_num($_POST['derecho_adjudicacion'] ?? 0);
$precio_final = tpa_num($_POST['precio_final'] ?? 0);
$monto_reserva = tpa_num($_POST['monto_reserva'] ?? 0);
$observaciones = tpa_txt($con, $_POST['observaciones'] ?? '');

// Asesor: puede venir vacío -> NULL.
$usuario_venta_id = (isset($_POST['usuario_venta_id']) && $_POST['usuario_venta_id'] !== '' && $_POST['usuario_venta_id'] !== 'null')
    ? (int)$_POST['usuario_venta_id'] : null;
$usuarioSQL = $usuario_venta_id === null ? 'NULL' : (string)$usuario_venta_id;

// Datos de la reserva / cliente.
$modelo_version_retirar = tpa_txt($con, $_POST['modelo_version_retirar'] ?? '');
$cliente   = tpa_txt($con, $_POST['cliente'] ?? '');
$sexo      = tpa_txt($con, $_POST['sexo'] ?? '');
$edad      = tpa_txt($con, $_POST['edad'] ?? '');
$dni       = tpa_txt($con, $_POST['dni'] ?? '');
$cuil      = tpa_txt($con, $_POST['cuil'] ?? '');
$direccion = tpa_txt($con, $_POST['direccion'] ?? '');
$localidad = tpa_txt($con, $_POST['localidad'] ?? '');
$provincia = tpa_txt($con, $_POST['provincia'] ?? '');
$email     = tpa_txt($con, $_POST['email'] ?? '');
$celular   = tpa_txt($con, $_POST['celular'] ?? '');
$hora_reserva = tpa_txt($con, $_POST['hora_reserva'] ?? '');

if ($version_id <= 0 || $modalidad_id <= 0) {
    $salida = ["ok" => false, "error" => "Versión y modalidad son obligatorias."];
    return;
}

$uuid = isset($_POST['planUuId']) ? trim((string)$_POST['planUuId']) : '';

$sets =
      "version_id = $version_id, "
    . "modalidad_id = $modalidad_id, "
    . "grupo_orden = '$grupo_orden', "
    . "situacion_id = $situacion_id, "
    . "cuotas_pagadas_cantidad = $cuotas_cant, "
    . "cuotas_pagadas_monto = $cuotas_monto, "
    . "integracion = $integracion, "
    . "derecho_adjudicacion = $derecho_adj, "
    . "costo = $costo, "
    . "cesion = $cesion, "
    . "plus = $plus, "
    . "venta = $venta, "
    . "precio_final = $precio_final, "
    . "cuota_promedio = $cuota_prom, "
    . "valor_unidad = $valor_unidad, "
    . "usuario_venta_id = $usuarioSQL, "
    . "monto_reserva = $monto_reserva, "
    . "observaciones = '$observaciones', "
    . "estado_id = $estado_id, "
    // datos de la reserva / cliente
    . "modelo_version_retirar = '$modelo_version_retirar', "
    . "hora_reserva = '$hora_reserva', "
    . "fecha_reserva = "    . tpa_fechaSQL($con, $_POST['fecha_reserva'] ?? '') . ", "
    . "cliente = '$cliente', "
    . "sexo = '$sexo', "
    . "fecha_nacimiento = " . tpa_fechaSQL($con, $_POST['fecha_nacimiento'] ?? '') . ", "
    . "edad = '$edad', "
    . "dni = '$dni', "
    . "cuil = '$cuil', "
    . "direccion = '$direccion', "
    . "localidad = '$localidad', "
    . "provincia = '$provincia', "
    . "email = '$email', "
    . "celular = '$celular'";

if ($uuid !== '') {
    $uuidEsc = mysqli_real_escape_string($con, $uuid);
    $SQL = "UPDATE tpa_planes_avanzados SET $sets WHERE uuid = '$uuidEsc'";
} else {
    $uuid    = tpa_uuid_v4();
    $uuidEsc = mysqli_real_escape_string($con, $uuid);
    $SQL = "UPDATE tpa_planes_avanzados SET $sets WHERE uuid = '$uuidEsc'";
    // INSERT: arrancamos con el uuid y luego seteamos todo con el mismo bloque.
    $ins = mysqli_query($con, "INSERT INTO tpa_planes_avanzados (uuid, situacion_id, estado_id) VALUES ('$uuidEsc', $situacion_id, $estado_id)");
    if (!$ins) { http_response_code(500); $salida = ["ok" => false, "error" => mysqli_error($con)]; return; }
}

if (!mysqli_query($con, $SQL)) {
    http_response_code(500);
    $salida = ["ok" => false, "error" => mysqli_error($con)];
    return;
}

$salida = ["ok" => true, "uuid" => $uuid];
