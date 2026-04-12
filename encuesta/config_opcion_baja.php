<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { echo "Sin autorización."; exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { echo "Sin autorización."; exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_opcion = isset($_POST['id_opcion']) ? (int)$_POST['id_opcion'] : 0;
if ($id_opcion <= 0) { echo "ID inválido."; exit(); }

mysqli_query($con, "UPDATE enc_opciones SET baja = 1 WHERE id_opcion = $id_opcion");
echo "ok";
?>
