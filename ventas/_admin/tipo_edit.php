<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE tipos SET tipo='".$_POST["tipo"]."', activo=".$_POST["activo"]." WHERE idtipo =".$_POST["idtipo"];
mysqli_query($con, $SQL);
header("Location: tipos.php");

?>