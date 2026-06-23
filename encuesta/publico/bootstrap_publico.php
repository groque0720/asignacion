<?php
/*
 * Bootstrap de las páginas PÚBLICAS de la encuesta 0km (responder, gracias,
 * expirada). NO exige login: el acceso lo gobierna el token único de la URL.
 * Sólo abre la conexión y carga las constantes del módulo. Expone $con.
 */
require __DIR__ . '/../config.php';                 // constantes ENCUESTA_*
require __DIR__ . '/../../comun/func_mysql.php';    // define conectar() y setea $con global
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
