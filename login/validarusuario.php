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



if ($campo['idusuario'] == 144 or $campo['idusuario'] == 139 or $campo['idusuario'] == 138 or  $campo['idusuario'] == 136 or $campo['idusuario'] == 135 or $campo['idusuario'] == 132 or $campo['idusuario'] == 13 or $campo['idusuario'] == 47 or $campo['idusuario'] == 37 or $campo['idusuario'] == 117 OR $campo['idusuario'] == 125 OR $campo['idusuario'] == 28 OR $campo['idusuario'] == 120 OR $campo['idusuario'] == 124 OR $campo['idusuario'] == 119 or $campo['idusuario'] == 116 or $campo['idusuario'] == 106 or $campo['idusuario'] == 31 or $campo['idusuario'] == 102 or $campo['idusuario'] == 101 or $campo['idusuario'] == 93 or $campo['idusuario'] == 91 or $campo['idusuario'] == 66 or $campo['idusuario'] == 89 or $campo['idusuario'] == 87 or $campo['idusuario'] == 15 or $campo['id_negocio'] == 2 or $campo['idusuario']==45 or $campo['idusuario']==41 or $campo['idusuario']==72 or $campo['idusuario']==79 or $campo['idusuario']==57 or $campo['idusuario']==71 or $campo['idusuario']==68 or $campo['idusuario']==111 or $campo['idusuario']==59 or $campo['idusuario']==83 or $campo['idusuario']==11 or $campo['idusuario']==14 or $campo['idusuario']==82 or $campo['idusuario']==16 or $campo['idusuario']==104 or $campo['idusuario']==56 or $campo['idusuario']==51 or $campo['idusuario']==36) {
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