<?php

session_start();

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="INSERT INTO regrecepcion(";
$SQL .="idusuario,";
$SQL .="fecha,";
$SQL .="sector,";
$SQL .="cliente,";
$SQL .="acercamiento,";
$SQL .="telefono,";
$SQL .="asesor,";
$SQL .="email,";
$SQL .="seguimiento,";
$SQL .="observacion)";
$SQL .=" VALUES(";
$SQL .="'".$_POST["idusuario"]."',";
$SQL .="'".$_POST["fecha"]."',";
$SQL .="'".$_POST["sector"]."',";
$SQL .="'".$_POST["cliente"]."',";
$SQL .="'".$_POST["acercamiento"]."',";
$SQL .="'".$_POST["telefono"]."',";
$SQL .="'".$_POST["asesor"]."',";
$SQL .="'".$_POST["email"]."',";
$SQL .="".$_POST["seguimiento"].",";
$SQL .="'".$_POST["observacion"]."')";

mysqli_query($con, $SQL);


$rs = mysql_query("SELECT MAX(idcontacto) AS id FROM regrecepcion");
if ($row = mysql_fetch_row($rs)) {
$id_contacto = trim($row[0]);
}

mysqli_close($con);

 ?>



