<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_nivel = isset($_POST['id_nivel']) ? (int)$_POST['id_nivel'] : 0;
if ($id_nivel <= 0) { echo "Parámetro inválido."; exit(); }

$ok = mysqli_query($con, "DELETE FROM enc_niveles WHERE id_nivel = $id_nivel");
echo $ok ? "ok" : "Error: " . mysqli_error($con);
?>
