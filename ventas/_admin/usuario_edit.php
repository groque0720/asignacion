<?php
include ("../includes/security.php");

include("../funciones/func_mysql.php");
require_once(__DIR__ . "/../../comun/clave.php");
conectar();

$idusuario = (int) $_POST["idusuario"];

// La clave se guarda SIEMPRE hasheada. Vacía = el admin no la está cambiando,
// así que no se toca la que ya tiene (antes el form venía precargado con la
// clave en texto plano y cada guardado la volvía a escribir tal cual).
$clave    = isset($_POST["clave"]) ? $_POST["clave"] : '';
$setClave = '';

if ($clave !== '') {
	$err = '';
	if (!clave_valida($clave, $err)) {
		header("Location: usuario.php?IDrecord=".$idusuario."&error=".urlencode($err));
		exit;
	}
	// Una clave puesta por el admin la conocen dos personas: el dueño de la
	// cuenta tiene que elegir la suya en el próximo login.
	$setClave = "clave = '".mysqli_real_escape_string($con, clave_hash($clave))."', debe_cambiar_clave = 1,";
}

$SQL = "UPDATE usuarios SET
usuario    = '".mysqli_real_escape_string($con, $_POST["usuario"])."',
".$setClave."
nombre     = '".mysqli_real_escape_string($con, $_POST["nombre"])."',
idsucursal = ".(int) $_POST["idsucursal"].",
idperfil   = ".(int) $_POST["idperfil"].",
activo     = ".(int) $_POST["activo"]."
WHERE idusuario = ".$idusuario;

mysqli_query($con, $SQL);
header("Location: usuarios.php");
?>
