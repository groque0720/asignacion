<?php
/*
 * Catálogos para los selects de los modales (reservar / crear-editar plan).
 * Deja el resultado en $salida; el endpoint opciones.php lo emite como JSON.
 * Requiere: $con y las funciones tpa_* (funciones/consulta.php).
 */

$salida = [
    'ok'           => true,
    'versiones'    => tpa_versiones($con),
    'modalidades'  => tpa_modalidades($con),
    'estados'      => tpa_estados($con),
    'situaciones'  => tpa_situaciones($con),
    'asesores'     => tpa_asesores($con),
];
