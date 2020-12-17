<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
@session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../login");
	//ademas salgo de este script
	exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Asignación 0km</title>

<!-- 	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> -->
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.ico" />
	<link rel="stylesheet" href="css/roquesystem.css">
	<link rel="stylesheet" href="css/menu-secundario-dos.css">
	<!-- 	<link href="https://file.myfontastic.com/6CRLECjnYdYKU5BvcK7cQA/icons.css" rel="stylesheet"> -->
<link href="css/iconos.css" rel="stylesheet">
	<script src="alertas_query/sweetalert-dev.js"></script>
	<!-- <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script> -->
	<script src="js/jquery-2.1.3.min.js"></script>
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="css/estilo_app.css">
	<script scr="js/menu-secundario.js"></script>
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="print" href="css/stock_p.css">

</head>
<body>

	<div class="lienzo-unidad">
	</div>
	<?php include('en_proceso/en_proceso.php'); ?>
	<div class="zona-cabecera ancho-100">
		<div class="cabecera">
			<div class="cabecera-izquierda">
				<div class="menu-icon">
					<label class="icon-menu" for="icono-menu"><span class="icon-mini-menu"></span></label>
					<input type="checkbox" name="icono-menu" id="icono-menu">
				</div>
				<div class="zona-logo-ppal">
					<img class="logo-ppal" src="imagenes/logodyv_c.png" alt="">
				</div>
			</div>
			<div class="cabecera-derecha">
				<div class="zona-usuario">
					<div class="nombre-usuario">
						<span><?php echo $_SESSION["usuario"]; ?></span>
						<input type="hidden" id="id_usuario" value="<?php echo $_SESSION["id"]; ?>">
					</div>
				</div>
				<div class="zona-img-toyota">
					<img class="img-toyota" src="imagenes/logo_toyota.png" alt="">
				</div>
			</div>
		</div>

	</div>
	<div class="menu-lateral menu-lateral-scroll">
			<?php include('menu-lateral.php'); ?>
	</div>
	<div class="zona-menu-secundario">
		<?php include('menu_secundario.php'); ?>
	</div>

	<div class="zona-contenido">
		<div class="contenido-principal">
			<?php include('contenido_relleno.php'); ?>
		</div>
	</div>




	<div id="mensaje_respuesta"></div>

</body>
</html>