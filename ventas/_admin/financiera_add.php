<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");
$SQL="INSERT INTO financieras(financiera, activo) VALUES ('".$_POST["financiera"]."', 1)";

mysqli_query($con, $SQL);
header("Location: financieras.php");

?>