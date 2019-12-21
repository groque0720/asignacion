<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO listaprecio(idmodelo, moneda, pl, flete, trans, neto, iva, subtotal, impuesto, activo) VALUES ";
$SQL .= "(".$_POST["idmodelo"].",'".$_POST["moneda"]."',".$_POST["pl"].",".$_POST["flete"].",".$_POST["trans"].",".$_POST["neto"].",";
$SQL .= $_POST["iva"].",".$_POST["subtotal"].",".$_POST["impuesto"].",1)";

mysqli_query($con, $SQL);
header("Location: precios.php");
?>