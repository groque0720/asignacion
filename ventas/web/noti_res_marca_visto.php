<?php
include("../includes/security.php");      // exige login + arranca sesión
include("../funciones/func_mysql.php");
conectar();

$id  = (int) ($_POST["id"] ?? 0);         // corta la SQLi: id es entero
$usu = (int) $_SESSION["id"];             // dueño desde la sesión, no del cliente

$SQL = "UPDATE notificaciones SET visto = 1 WHERE idnotificaciones = $id AND idusuario = $usu";
mysqli_query($con, $SQL);
mysqli_close($con);
?>
