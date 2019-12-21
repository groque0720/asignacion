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
	<title>Agenda Test Drive</title>
	
	<link href="https://file.myfontastic.com/6CRLECjnYdYKU5BvcK7cQA/icons.css" rel="stylesheet">
	<script src="alertas_query/sweetalert-dev.js"></script>
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<script src="js/app.js"></script>
	<script src="js/tablero_agenda.js"></script>
	<link rel="stylesheet" href="css/estilo_ppal.css">
	<link rel="stylesheet" href="css/roquesystem.css">
	<link rel="stylesheet" href="css/menu-secundario-dos.css">

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



	
</head>
<body>
	<?php include('en_proceso/en_proceso.php'); ?>
	<div class="zona-cabecera ancho-100">
		<div class="cabecera">
			<div class="cabecera-izquierda">
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
		<?php include('menu_secundario.php'); ?>
	</div>

	<div class="contenido-ppal ancho-80">
		<?php include ('agenda_cuerpo.php') ?>
	</div>


	<div class="lienzo-form-agendar">
	</div>

	<div id="mensaje_respuesta"></div>

	
</body>
</html>