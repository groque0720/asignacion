<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();
$SQL="UPDATE usuarios SET usuario='".$_POST["usuario"]."',
clave='".$_POST["clave"]."',
nombre='".$_POST["nombre"]."',
idsucursal=".$_POST["idsucursal"].",
idperfil=".$_POST["idperfil"].",
activo=".$_POST["activo"]."
WHERE idusuario =".$_POST["idusuario"];

mysqli_query($con, $SQL);
header("Location: usuarios.php");
?>