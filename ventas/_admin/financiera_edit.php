<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE financieras SET financiera='".$_POST["financiera"]."', activo=".$_POST["activo"]." WHERE idfinanciera =".$_POST["idfinanciera"];
mysqli_query($con, $SQL);
header("Location: financieras.php");

?>
