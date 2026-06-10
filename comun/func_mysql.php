<?php
/*
 * Conexión MySQL compartida por los módulos modernos (comun/bootstrap.php la usa).
 * Idéntica a la func_mysql.php clásica de cada módulo, centralizada en un solo lugar.
 */
include($_SERVER['DOCUMENT_ROOT']."/config/config_mysql.php");

date_default_timezone_set("America/Argentina/Buenos_Aires");

function conectar() {
    global $con;
    $con = mysqli_connect(HOST, USER, PASS) or die("ERROR EN CONEXION:".mysqli_error());
    mysqli_select_db($con, DB) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysqli_error());
    mysqli_query($con, "SET NAMES 'utf8'");
    return $con;
}
