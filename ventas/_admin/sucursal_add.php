<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO sucursales(sucursal, sucres, activo) VALUES ('".$_POST["sucursal"]."', '".$_POST["sucres"]."', 1)";

mysqli_query($con, $SQL);

header("Location: sucursales.php");

?>