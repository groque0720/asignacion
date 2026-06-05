<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
extract($_POST);

$usuario = mysqli_real_escape_string($con, $usuario);
$contrasena = mysqli_real_escape_string($con, $contraseña);

$SQL="SELECT * FROM usuarios WHERE activo = 1 AND usuario = '".$usuario."' AND clave = '".$contrasena."'";
$result=mysqli_query($con, $SQL);

//echo $contrasena;

	$campo=mysqli_fetch_array($result);

if (!empty($campo['usuario'])) {
	@session_start();
	$_SESSION["autentificado"]= "SI";
	$_SESSION["id"]=$campo['idusuario'];
	$_SESSION["usuario"]=$campo['nombre'];
	$_SESSION["idperfil"]=$campo['idperfil'];
	$_SESSION["idsuc"]=$campo['idsucursal'];
	$_SESSION["es_gerente"]=$campo['es_gerente'];
	$_SESSION["id_negocio"]=$campo['id_negocio'];

$SQL="INSERT INTO sesiones (id_usuario, nombre, fecha, hora, latitud, longitud, ip) VALUES (".$_SESSION["id"].",'".$_SESSION["usuario"]."','".date("Y-m-d")."','".date( 'H:i:s')."','','','".$ip_user."')";
mysqli_query($con, $SQL);

// Usuarios que, al loguearse, son redirigidos al dashboard contable.
$usuariosDashboard = [
	11, 13, 14, 15, 16, 28, 31, 36, 37, 41,
	45, 47, 51, 56, 57, 59, 65, 66, 68, 71,
	72, 79, 82, 83, 87, 89, 91, 93, 94, 96, 101, 102,
	104, 106, 111, 116, 117, 119, 120, 121, 124, 125,
	132, 135, 136, 138, 139, 144, 146, 147, 163,
];

if (in_array((int)$campo['idusuario'], $usuariosDashboard, true) or $campo['id_negocio'] == 2) {
		echo '<script>	window.location.href = "../dashboard/index.php";</script>';
	}else{


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
	if ($campo['idperfil']==18) {
		echo '<script>	window.location.href = "../ventas/web/noticias.php";</script>';
	}
	if ($campo['idperfil']==19) {
		echo '<script>	window.location.href = "../ventas/web/recursos_dyv_toyota.php";</script>';
	}
	if ($campo['idperfil']==20) {
		echo '<script>	window.location.href = "../ventas/web/recepcion.php";</script>';
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
	if ($campo['idperfil']==24) {
		echo '<script>	window.location.href = "../asignacion";</script>';
	}
	}
	 mysqli_close($con);
	}else{

		$SQL="INSERT INTO sesiones (id_usuario, nombre, fecha, hora, latitud, longitud, ip) VALUES (999,'NO EXITO - ".$usuario." - ".$contrasena."','".date("Y-m-d")."','".date( 'H:i:s')."','','','".$ip_user."')";
		mysqli_query($con, $SQL);

		echo '<script>	swal("ERROR AL VALIDAR ACCESO", "Por favor verifique que sus datos sean correctos", "error");
				$("#usuario").facus();
			</script>';

	}




 ?>