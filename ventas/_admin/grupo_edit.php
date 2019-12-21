<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE grupos SET grupo='".$_POST["grupo"]."',
posicion=".$_POST["posicion"]." ,
activo=".$_POST["activo"]."
WHERE idgrupo =".$_POST["idgrupo"];
mysqli_query($con, $SQL);
header("Location: grupos.php");
?>