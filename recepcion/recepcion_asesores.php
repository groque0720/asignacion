<?php

@session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../login");
	//ademas salgo de este script
	exit();
}

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Recepción DYV S. A.</title>
	
	<script src="alertas_query/sweetalert-dev.js"></script>
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="js/app.js"></script>
	
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="css/estilo_app.css">
	<link rel="stylesheet" href="css/roquesystem.css">
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="stylesheet" href="css/roquesystem.css">
	<link rel="stylesheet" href="css/menu-secundario-dos.css">
	<link href="https://file.myfontastic.com/6CRLECjnYdYKU5BvcK7cQA/icons.css" rel="stylesheet">

	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />

</head>
<body>
	<?php include('en_proceso/en_proceso.php'); ?>
	<div class="zona-cabecera ancho-100">
		<div class="cabecera">
			<div class="cabecera-izquierda">
				<div class="menu-icon">
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
	<div class="zona-menu-secundario">
		<?php //include('menu_secundario_asesor.php'); ?>
	</div>

	<div class="zona-contenido">
		<div class="contenido-principal">
			<?php include('recepcion_cuerpo_asesores.php'); ?>
		</div>
	</div>
<div class="zona_ver_mas">
	<span id="boton_ver_mas" class="icon-search cursor-pointer" data-usu="recepcion" data-ini="0" data-cantidad="<?php echo $cantidad; ?>"> Ver más</span>
	<img id="imagen_carga" src="imagenes/cargando.gif" alt="">

</div>

	<div class="lienzo-formulario">
	</div>

	<div id="mensaje_respuesta"></div>

	
</body>
</html>
