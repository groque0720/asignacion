<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE perfiles SET perfil='".$_POST["perfil"]."', activo=".$_POST["activo"]." WHERE idperfil =".$_POST["idperfil"];
mysqli_query($con, $SQL);

header("Location: perfiles.php");

?>