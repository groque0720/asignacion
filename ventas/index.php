<?php
header('Location: ../index.php');
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>Derka y Vargas</title>
    <link rel="stylesheet" type="text/css" href="css/estiloindex.css">
    <link rel="shortcut icon" type="image/x-icon" href="dyv.ico" />
    <meta charset="iso-8859-1">
</head>

<body>

	<div id="agrupar">
		<header id="encabezado">
			<div id="imagenlogodyv">
				<img id="logodyv" src="imagenes/logodyv.png" alt="Derka y Vargas S. A.">
			</div>
			<div id="imagentoyota">
				<img id="logotoyota" src="imagenes/logotoyota.png" alt="Toyota Argentina">
			</div>
		</header>
		<nav id="menu"></nav>
		<section id="contenido">
			
			<div id="cuadrologin">
				<form action="web/validar.php" method="POST" name="form_login" id="form_login">
					<table id="login">
						<tr><td align="right"></td><td><input type="text" id="usuario" placeholder="Usuario" name="usuario" size="40" required autofocus></td></tr>
						<tr><td align="right"></td><td><input type="password" id="contrasena" placeholder="Contrase&ntilde;a" size="40" name="contrasena" required></td></tr>
						<tr><td></td><td align="right"><input type="submit" id="enviar" value="Ingresar"></td></tr>
					</table>	
				</form>
			<div>

		</section>
		<footer id="pie"></footer>
	</div>
		
</body>
 </html>
          
       