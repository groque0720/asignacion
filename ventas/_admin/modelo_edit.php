<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE modelos SET idgrupo='".$_POST["idgrupo"]."', idtipo='".$_POST["idtipo"]."', modelo='".$_POST["modelo"]."', posicion='".$_POST["posicion"]."', activo=".$_POST["activo"]." WHERE idmodelo =".$_POST["idmodelo"];
mysqli_query($con, $SQL);
header("Location: modelos.php");

?>