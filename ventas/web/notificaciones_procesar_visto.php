<?php
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE publicaciones_linea SET";
$SQL .="  visto = 1 ";
$SQL .=" WHERE id_publicacion_linea =".$_POST["id"];
mysqli_query($con, $SQL);

mysqli_close($con);

?>