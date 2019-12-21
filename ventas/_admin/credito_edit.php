<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE tipos_creditos SET tipocredito='".$_POST["tipocredito"]."', activo=".$_POST["activo"]." WHERE idtipocredito =".$_POST["idtipocredito"];
header("Location: creditos.php");    
?>