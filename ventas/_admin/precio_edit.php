<?php

include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="UPDATE listaprecio SET ";
$SQL .= " pl = ".$_POST["pl"].", ";
$SQL .= " flete = ".$_POST["flete"].", ";
$SQL .= " trans = ".$_POST["trans"].", ";
$SQL .= " neto = ".$_POST["neto"].", ";
$SQL .= " iva = ".$_POST["iva"].", ";
$SQL .= " subtotal = ".$_POST["subtotal"].", ";
$SQL .= " impuesto = ".$_POST["impuesto"].", ";
$SQL .= " activo = ".$_POST["activo"].", ";
$SQL .= " moneda = '".$_POST["moneda"]."' ";
$SQL .= " WHERE idprecio =".$_POST["idprecio"];


mysqli_query($con, $SQL);
header("Location: precios.php");
// ?>