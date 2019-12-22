<?php
// include ("../include/security.php");

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");
extract($_POST);



$SQL="SELECT * FROM usuarios WHERE activo = 1 AND usuario = '$usuario' AND clave = '$contrasena'";

$result=mysqli_query($con, $SQL);

$campo=mysqli_fetch_array($result);

if (!empty($campo['usuario'])) {
	@session_start();
	$_SESSION["autentificado"]= "SI";
	$_SESSION["id"]=$campo['idusuario'];
	$_SESSION["usuario"]=$campo['nombre'];
	$_SESSION["idperfil"]=$campo['idperfil'];
	$_SESSION["idsuc"]=$campo['idsucursal'];

	if ($campo['idperfil']==1) {
		header("Location: ../_admin/admin.php");
	}
	if ($campo['idperfil']==3) {
		header("Location: asesores.php");
	}
	if ($campo['idperfil']==2) {
		header("Location: administracion.php");
	}
	if ($campo['idperfil']==8) {
		header("Location: pagos_clientes.php");
	}
	if ($campo['idperfil']==10) {
		header("Location: control_pagos_clientes.php");
	}
	if ($campo['idperfil']==17) {
		header("Location: control_pagos_clientes.php");
	}
	if ($campo['idperfil']==9) {
		header("Location: control_pagos_clientes.php");
	}
	if ($campo['idperfil']==11) {
		header("Location: creditos.php");
	}
	if ($campo['idperfil']==12) {
		header("Location: remesas.php");
	}
	if ($campo['idperfil']==13) {
		header("Location: ../_admin/precios_admin.php");
	}
	if ($campo['idperfil']==14) {
		header("Location: control_reservas.php");
	}
	if ($campo['idperfil']==15) {
		header("Location: reportes.php");
	}
	if ($campo['idperfil']==16) {
		header("Location: control_reservas.php");
	}
	if ($campo['idperfil']==18) {
		header("Location: noticias.php");
	}
	if ($campo['idperfil']==19) {
		header("Location: recursos_dyv_toyota.php");
	}
	if ($campo['idperfil']==20) {
		header("Location: recepcion.php");
	}

	 mysqli_close($con);

} else {
	echo '<script language = javascript>
	alert("Usuario o Contrase\u00f1a incorrecta, por favor verifique.")
	self.location = "../index.php"
	</script>';
}

?>