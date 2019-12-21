<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE notificaciones SET";
$SQL .="  visto =  1  WHERE idnotificaciones = '".$_POST["id"]."'";
mysqli_query($con, $SQL);
 mysqli_close($con);
?>