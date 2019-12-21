<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO modelos(idgrupo, idtipo, modelo, posicion, activo) VALUES (".$_POST["idgrupo"].", ".$_POST["idtipo"].", '".$_POST["modelo"]."', ".$_POST["posicion"].", 1)";

mysqli_query($con, $SQL);
header("Location: modelos.php");

?>