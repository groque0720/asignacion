<?php

// include '../config/config_app.php';


$dyv = [];
$base = [
    'sucursal' => $sucursal['sucursal'] ?? 'Derka y Vargas',
    'cantidad_reservas' => 0,
    'monto_reservas' => 0,
    'cantidad_reservas_con_descuento' => 0,
    'monto_reservas_con_descuento' => 0,    
    'porcentaje' => 0,
    'porcentaje_con_descuento' => 0,
];


$dyv['dyv']['año'] = $base;
$dyv['dyv']['mes'] = $base;

$SQL = "SELECT
        sucursales.sucursal
    FROM
        sucursales
    ORDER BY
        sucursales.posicion ASC";

$sucursales = mysqli_query($con, $SQL);

while ($sucursal = mysqli_fetch_array($sucursales)) {

    $dyv[$sucursal['sucursal']]['año'] = $base;
    $dyv[$sucursal['sucursal']]['mes'] = $base;
    $dyv[$sucursal['sucursal']]['asesores'] = [];

}

$SQL = "SELECT
            reservas.idreserva AS `Nro Reserva`,
            reservas.compra AS Venta,
            clientes.nombre AS Cliente,
            reservas.nrounidad AS NroUnidad,
            reservas.interno AS Interno,
            reservas.fecres AS Fecha,
            YEAR ( reservas.fecres ) AS Año,
            MONTH ( reservas.fecres ) AS Mes,
            grupos.grupo AS Modelo,
            usuarios.nombre AS Asesor,
            sucursales.sucursal AS Sucursal,
            lineas_detalle.detalle AS `Código`,
            lineas_detalle.adjunto AS `Det (Código)`,
            lineas_detalle.monto AS Importe 
        FROM
            (
                (
                    (
                        (
                            ( reservas JOIN clientes ON ( ( reservas.idcliente = clientes.idcliente ) ) )
                            JOIN usuarios ON ( ( reservas.idusuario = usuarios.idusuario ) ) 
                        ) 
                    )
                    JOIN grupos ON ( ( reservas.idgrupo = grupos.idgrupo ) ) 
                )
                JOIN lineas_detalle ON ( ( reservas.idreserva = lineas_detalle.idreserva ) ) 
            )
            INNER JOIN sucursales ON usuarios.idsucursal = sucursales.idsucursal 
        WHERE
            (
                YEAR(reservas.fecres) = $año_actual
                AND ( reservas.anulada = 0 ) 
                AND ( reservas.enviada >= 1 ) 
                AND ( lineas_detalle.idcodigo > 0 ) 
            AND lineas_detalle.movimiento = 1 
            )
    ";



$reservas = mysqli_query($con, $SQL);
$nueva = false;
$reserva_numero = null;
$asesores_pass = [];
$mes_pass = [];

while ($reserva = mysqli_fetch_array($reservas)) {

    if ($reserva['Mes'] > $mes_actual) {
        continue; // Si la reserva es mayor al mes actual, la ignoramos
    }

    if($reserva['Nro Reserva'] != $reserva_numero) {
        $nueva = true;
        $reserva_numero = $reserva['Nro Reserva'];
    } else {
        $nueva = false;
    }

    // Derka y Vargas
    $dyv['dyv']['año']['cantidad_reservas'] += $nueva ? 1 : 0;
    $dyv['dyv']['año']['monto_reservas'] += $reserva['Importe'];
    $dyv['dyv']['año']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
    $dyv['dyv']['año']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;
    
    $cant_ = $dyv['dyv']['año']['cantidad_reservas'];
    $cant_descuento = $dyv['dyv']['año']['cantidad_reservas_con_descuento'];
    $dyv['dyv']['año']['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

    $mont_= $dyv['dyv']['año']['monto_reservas'];
    $mont_descuento = $dyv['dyv']['año']['monto_reservas_con_descuento'];
    $dyv['dyv']['año']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;

    // Sucursal
    $dyv[$reserva['Sucursal']]['año']['cantidad_reservas'] += $nueva ? 1 : 0;
    $dyv[$reserva['Sucursal']]['año']['monto_reservas'] += $reserva['Importe'];
    $dyv[$reserva['Sucursal']]['año']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
    $dyv[$reserva['Sucursal']]['año']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;
    
    $cant_ = $dyv[$reserva['Sucursal']]['año']['cantidad_reservas'];
    $cant_descuento = $dyv[$reserva['Sucursal']]['año']['cantidad_reservas_con_descuento'];
    $dyv[$reserva['Sucursal']]['año']['porcentaje'] =  ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

    $mont_= $dyv[$reserva['Sucursal']]['año']['monto_reservas'];
    $mont_descuento = $dyv[$reserva['Sucursal']]['año']['monto_reservas_con_descuento'];
    $dyv[$reserva['Sucursal']]['año']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;


    $asesor = $reserva["Asesor"];
    $sucursal_nombre = $reserva['Sucursal']; // para mantener consistencia en el índice
    
    if (!in_array($asesor, $asesores_pass)) {
        $base = [
            'sucursal' => $sucursal_nombre,
            'cantidad_reservas' => 0,
            'monto_reservas' => 0,
            'cantidad_reservas_con_descuento' => 0,
            'monto_reservas_con_descuento' => 0,    
            'porcentaje' => 0,
            'porcentaje_con_descuento' => 0,
        ];
    
        $dyv[$sucursal_nombre][$asesor]['año'] = $base;
        $dyv[$sucursal_nombre][$asesor]['mes'] = $base;
        $asesores_pass[] = $asesor;
        $dyv[$sucursal_nombre]['asesores'][] = $asesor;
    }
    
    // AÑO
    $dyv[$sucursal_nombre][$asesor]['año']['cantidad_reservas'] += $nueva ? 1 : 0;
    $dyv[$sucursal_nombre][$asesor]['año']['monto_reservas'] += $reserva['Importe'];
    $dyv[$sucursal_nombre][$asesor]['año']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
    $dyv[$sucursal_nombre][$asesor]['año']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

    $cant_ = $dyv[$sucursal_nombre][$asesor]['año']['cantidad_reservas'];
    $cant_descuento = $dyv[$sucursal_nombre][$asesor]['año']['cantidad_reservas_con_descuento'];
    $dyv[$sucursal_nombre][$asesor]['año']['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

    $mont_= $dyv[$sucursal_nombre][$asesor]['año']['monto_reservas'];
    $mont_descuento = $dyv[$sucursal_nombre][$asesor]['año']['monto_reservas_con_descuento'];
    $dyv[$sucursal_nombre][$asesor]['año']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;

    if ($reserva['Mes'] == $mes_actual) {
        $dyv['dyv']['mes']['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv['dyv']['mes']['monto_reservas'] += ($reserva['Importe']);
        $dyv['dyv']['mes']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv['dyv']['mes']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv['dyv']['mes']['cantidad_reservas'];
        $cant_descuento = $dyv['dyv']['mes']['cantidad_reservas_con_descuento'];
        $dyv['dyv']['mes']['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv['dyv']['mes']['monto_reservas'];
        $mont_descuento = $dyv['dyv']['mes']['monto_reservas_con_descuento'];
        $dyv['dyv']['mes']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;


        $dyv[$reserva['Sucursal']]['mes']['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv[$reserva['Sucursal']]['mes']['monto_reservas'] += ($reserva['Importe']);    
        $dyv[$reserva['Sucursal']]['mes']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv[$reserva['Sucursal']]['mes']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv[$reserva['Sucursal']]['mes']['cantidad_reservas'];
        $cant_descuento = $dyv[$reserva['Sucursal']]['mes']['cantidad_reservas_con_descuento'];
        $dyv[$reserva['Sucursal']]['mes']['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv[$reserva['Sucursal']]['mes']['monto_reservas'];
        $mont_descuento = $dyv[$reserva['Sucursal']]['mes']['monto_reservas_con_descuento'];
        $dyv[$reserva['Sucursal']]['mes']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;

        //  Asesor
        $dyv[$sucursal_nombre][$asesor]['mes']['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv[$sucursal_nombre][$asesor]['mes']['monto_reservas'] += ($reserva['Importe']);
        $dyv[$sucursal_nombre][$asesor]['mes']['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv[$sucursal_nombre][$asesor]['mes']['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv[$sucursal_nombre][$asesor]['mes']['cantidad_reservas'];
        $cant_descuento = $dyv[$sucursal_nombre][$asesor]['mes']['cantidad_reservas_con_descuento'];
        $dyv[$sucursal_nombre][$asesor]['mes']['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv[$sucursal_nombre][$asesor]['mes']['monto_reservas'];
        $mont_descuento = $dyv[$sucursal_nombre][$asesor]['mes']['monto_reservas_con_descuento'];
        $dyv[$sucursal_nombre][$asesor]['mes']['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;

    }


    // echo "<pre>";
        $mes_curso = $reserva['Mes'];


        if (!isset($dyv['dyv'][$mes_curso])) {
            $dyv['dyv'][$mes_curso] = $base;
        }
        if (!isset($dyv[$reserva['Sucursal']][$mes_curso])) {
            $dyv[$reserva['Sucursal']][$mes_curso] = $base;
        }
        if (!isset($dyv[$sucursal_nombre][$asesor][$mes_curso])) {
            $dyv[$sucursal_nombre][$asesor][$mes_curso] = $base;
        }

        $dyv['dyv'][$mes_curso]['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv['dyv'][$mes_curso]['monto_reservas'] += $reserva['Importe'];
        $dyv['dyv'][$mes_curso]['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv['dyv'][$mes_curso]['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv['dyv'][$mes_curso]['cantidad_reservas'];
        $cant_descuento = $dyv['dyv'][$mes_curso]['cantidad_reservas_con_descuento'];
        $dyv['dyv'][$mes_curso]['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv['dyv'][$mes_curso]['monto_reservas'];
        $mont_descuento = $dyv['dyv'][$mes_curso]['monto_reservas_con_descuento'];
        $dyv['dyv'][$mes_curso]['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;


        $dyv[$reserva['Sucursal']][$mes_curso]['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv[$reserva['Sucursal']][$mes_curso]['monto_reservas'] += $reserva['Importe'];    
        $dyv[$reserva['Sucursal']][$mes_curso]['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv[$reserva['Sucursal']][$mes_curso]['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv[$reserva['Sucursal']][$mes_curso]['cantidad_reservas'];
        $cant_descuento = $dyv[$reserva['Sucursal']][$mes_curso]['cantidad_reservas_con_descuento'];
        $dyv[$reserva['Sucursal']][$mes_curso]['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv[$reserva['Sucursal']][$mes_curso]['monto_reservas'];
        $mont_descuento = $dyv[$reserva['Sucursal']][$mes_curso]['monto_reservas_con_descuento'];
        $dyv[$reserva['Sucursal']][$mes_curso]['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;

        //  Asesor
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['cantidad_reservas'] += $nueva ? 1 : 0;
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['monto_reservas'] += $reserva['Importe'];
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['cantidad_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? 1 : 0;
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['monto_reservas_con_descuento'] += ($reserva['Importe'] < 0) ? abs($reserva['Importe']) : 0;

        $cant_ = $dyv[$sucursal_nombre][$asesor][$mes_curso]['cantidad_reservas'];
        $cant_descuento = $dyv[$sucursal_nombre][$asesor][$mes_curso]['cantidad_reservas_con_descuento'];
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['porcentaje'] = ($cant_ > 0) ? round(($cant_descuento * 100 / $cant_),2)  : 0;

        $mont_= $dyv[$sucursal_nombre][$asesor][$mes_curso]['monto_reservas'];
        $mont_descuento = $dyv[$sucursal_nombre][$asesor][$mes_curso]['monto_reservas_con_descuento'];
        $dyv[$sucursal_nombre][$asesor][$mes_curso]['porcentaje_con_descuento'] = ($mont_ > 0) ? round(($mont_descuento * 100) / $mont_, 2) : 0;
    // --



}





// header('Content-Type: application/json');
// echo json_encode($dyv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

