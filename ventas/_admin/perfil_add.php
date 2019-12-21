<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO perfiles(perfil, activo) VALUES ('".$_POST["perfil"]."', 1)";

mysqli_query($con, $SQL);
header("Location: perfiles.php");

?>