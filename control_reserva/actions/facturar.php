<?php
/*
 * Prepara la facturación de una reserva. Deja el resultado en $salida.
 * Réplica de ventas/web/facturacion_cargar.php: si la factura de la reserva aún no
 * tiene líneas, copia las líneas de la reserva (lineas_detalle → facturas_lineas).
 * Devuelve la URL de la pantalla de facturación legacy, a la que redirige el front.
 *
 * Requiere: $con y $puedeControlar (config_app.php).
 */

if (!$puedeControlar) {
    http_response_code(403);
    $salida = ["ok" => false, "error" => "No tenés permiso para facturar"];
    return;
}

$idres = (int)($_POST['idres'] ?? 0);
if ($idres <= 0) { $salida = ["ok" => false, "error" => "Reserva inválida"]; return; }

$f = mysqli_fetch_assoc(mysqli_query($con, "SELECT idfactura FROM reservas WHERE idreserva = ".$idres));
$idfactura = $f ? (int)$f['idfactura'] : 0;

// ¿La factura ya tiene líneas cargadas? Si no, copiarlas desde la reserva.
$cant = (int)mysqli_fetch_assoc(mysqli_query($con,
    "SELECT COUNT(*) n FROM facturas_lineas WHERE idfactura = ".$idfactura))['n'];

if ($cant === 0) {
    $lin = mysqli_query($con, "SELECT * FROM lineas_detalle WHERE idreserva = ".$idres);
    if ($lin) {
        while ($l = mysqli_fetch_assoc($lin)) {
            mysqli_query($con,
                "INSERT INTO facturas_lineas (idcodigo, codigo, idfactura, detalle, adjunto, movimiento, monto)
                 VALUES (
                    '".mysqli_real_escape_string($con, (string)$l['idcodigo'])."',
                    '".mysqli_real_escape_string($con, (string)$l['codigo'])."',
                    ".$idfactura.",
                    '".mysqli_real_escape_string($con, (string)$l['detalle'])."',
                    '".mysqli_real_escape_string($con, (string)$l['adjunto'])."',
                    '".mysqli_real_escape_string($con, (string)$l['movimiento'])."',
                    ".(float)$l['monto']."
                 )");
        }
    }
}

$salida = ["ok" => true, "url" => "../ventas/web/facturacion.php?IDrecord=".$idres];
