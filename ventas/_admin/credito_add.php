<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO tipos_creditos(tipocredito, activo) VALUES ('".$_POST["credito"]."', 1)";

mysqli_query($con, $SQL);
header("Location: creditos.php");

?>