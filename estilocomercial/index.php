<?php 
@session_start();

//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../index.php");
	//ademas salgo de este script
	exit();
}	

 ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Derka y Vargas S. A.</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/dyv.ico" />
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/login.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" href="css/themes/red/pace-theme-center-simple.css">
	<script src="js/login.js"></script>
</head>
<body class="desarroll">
	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>DERKA Y VARGAS S. A.</h1>
			Sistema de Gestión Integral Concesionario Oficial Toyota			
		</div>
		<div class="ed-item centrar-texto cuadro_login web-25 mobil-100">
			<form id="form_login" action="web/login_validar.php" method="POST" >
				<input type="text" id="usuario" name="usuario" placeholder="Usuario" title="Introduce tu Usuario" required>
				<input type="password" id="contraseña" name="contraseña" placeholder="Contraseña" title="Introduce tu Contraseña" required>
				<input type="submit" name="enviar" value="Iniciar Sesión" placeholder="Contraseña" class="btn_enviar">
			</form>
		</div>
	</div>
	<div id="invalido" class="invalido centrar-texto">
		
	</div>
</body>
</html>