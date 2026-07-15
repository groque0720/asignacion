<?php
include ("includes/security_usuarios.php");

include("../funciones/func_mysql.php");
require_once(__DIR__ . "/../../comun/clave.php");
conectar();

$clave = isset($_POST["clave"]) ? $_POST["clave"] : '';
$err   = '';

if (!clave_valida($clave, $err)) {
	header("Location: usuario_agregar.php?error=".urlencode($err));
	exit;
}

// El usuario nace hasheado y con cambio obligatorio: la clave que elige el
// admin sirve sólo para el primer login.
$SQL="INSERT INTO usuarios(usuario, nombre, clave, idperfil, idsucursal, activo, debe_cambiar_clave) VALUES ".
"('".mysqli_real_escape_string($con, $_POST["usuario"])."',".
"'".mysqli_real_escape_string($con, $_POST["nombre"])."',".
"'".mysqli_real_escape_string($con, clave_hash($clave))."',".
(int) $_POST["idperfil"].",".
(int) $_POST["idsucursal"].",".
(int) $_POST["activo"].",1)";

mysqli_query($con, $SQL);
header("Location: usuarios.php");
?>
