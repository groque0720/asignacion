<?php


include("../funciones/func_mysql.php");
conectar();

$SQL="INSERT INTO grupos(grupo, posicion, activo) VALUES ".
"('".$_POST["grupo"]."',".$_POST["posicion"].", 1)";

mysqli_query($con, $SQL);
echo "Los Datos fueron guardados..";
header("Location: grupos.php");

?>
