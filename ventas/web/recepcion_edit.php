<?php
 include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL = "UPDATE regrecepcion SET ";
$SQL .="sector ='".$_POST["sector"]."', ";
$SQL .="cliente = '".$_POST["cliente"]."',";
$SQL .="acercamiento = '".$_POST["acercamiento"]."', ";
$SQL .="telefono ='".$_POST["telefono"]."', ";
$SQL .="asesor ='".$_POST["asesor"]."', ";
$SQL .="email ='".$_POST["email"]."', ";
$SQL .="seguimiento = ".$_POST["seguimiento"].", ";
$SQL .="observacion ='".$_POST["observacion"]."' ";
$SQL .="WHERE idcontacto ='".$_POST["idcontacto"]."'";

echo $_POST["idcontacto"];

mysqli_query($con, $SQL);

?>
