<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE sucursales SET sucursal='".$_POST["sucursal"]."', sucres='".$_POST["sucres"]."', activo=".$_POST["activo"]." WHERE idsucursal =".$_POST["idsucursal"];
mysqli_query($con, $SQL);

header("Location: sucursales.php");

?>