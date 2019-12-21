<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO usuarios(usuario, nombre, clave, idperfil, idsucursal, activo) VALUES ".
"('".$_POST["usuario"]."','".$_POST["nombre"]."','".$_POST["clave"]."',".$_POST["idperfil"].",".$_POST["idsucursal"].",".$_POST["activo"].")";

mysqli_query($con, $SQL);
header("Location: usuarios.php");
?>