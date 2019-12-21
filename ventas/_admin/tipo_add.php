<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO tipos(tipo, activo) VALUES ('".$_POST["tipo"]."', 1)";

mysqli_query($con, $SQL);
header("Location: tipos.php");

?>