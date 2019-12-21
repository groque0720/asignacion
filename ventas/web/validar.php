<?php
// include ("../include/security.php");

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");
extract($_POST);

$usuario=mysql_real_escape_string($usuario);
$contrasena = mysql_real_escape_string($contrasena);

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
		echo '<script>	window.location.href = "../ventas/_admin/admin.php";</script>';
	}
	if ($campo['idperfil']==3) {
		echo '<script>	window.location.href = "../ventas/web/notificaciones_panel.php";</script>';
	}
	if ($campo['idperfil']==2) {

		echo '<script>	window.location.href = "../ventas/web/administracion.php";</script>';
	}
	if ($campo['idperfil']==8) {
		echo '<script>	window.location.href = "../ventas/web/pagos_clientes.php";</script>';
	}
	if ($campo['idperfil']==10) {
		echo '<script>	window.location.href = "../ventas/web/control_pagos_clientes.php";</script>';
	}
	if ($campo['idperfil']==17) {
		echo '<script>	window.location.href = "../ventas/web/control_pagos_clientes.php";</script>';
	}
	if ($campo['idperfil']==9) {
		echo '<script>	window.location.href = "../ventas/web/control_pagos_clientes.php";</script>';
	}
	if ($campo['idperfil']==11) {
		echo '<script>	window.location.href = "../ventas/web/creditos.php";</script>';
	}
	if ($campo['idperfil']==12) {
		echo '<script>	window.location.href = "../ventas/web/remesas.php";</script>';
	}
	if ($campo['idperfil']==13) {
		echo '<script>	window.location.href = "../ventas/_admin/precios_admin.php";</script>';
	}
	if ($campo['idperfil']==14) {
		echo '<script>	window.location.href = "../ventas/web/control_reservas.php";</script>';
	}
	if ($campo['idperfil']==15) {
		echo '<script>	window.location.href = "../ventas/web/reportes.php";</script>';
	}
	if ($campo['idperfil']==16) {
		echo '<script>	window.location.href = "../ventas/web/control_reservas.php";</script>';
	}
	if ($campo['idperfil']==18) {
		echo '<script>	window.location.href = "../ventas/web/noticias.php";</script>';
	}
	if ($campo['idperfil']==19) {
		echo '<script>	window.location.href = "../ventas/web/recursos_dyv_toyota.php";</script>';
	}
	if ($campo['idperfil']==20) {
		echo '<script>	window.location.href = "../ventas/web/recepcion.php";</script>';
	}
	if ($campo['idperfil']==21) {
		echo '<script>	window.location.href = "../estilocomercial/webs/panel.php";</script>';
	}
	if ($campo['idperfil']==22) {
		echo '<script>	window.location.href = "../eventos/index.php";</script>';
	}
	if ($campo['idperfil']==7) {
		echo '<script>	window.location.href = "../gestoria/index.php";</script>';
	}
	if ($campo['idperfil']==5) {
		echo '<script>	window.location.href = "../asignacion";</script>';
	}

	 mysqli_close($con);

} else {
	echo '<script language = javascript>
	alert("Usuario o Contrase\u00f1a incorrecta, por favor verifique.")
	self.location = "../index.php"
	</script>';
}

?>