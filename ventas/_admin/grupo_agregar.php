<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login - Derka y Vargas S. A.</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>

<body>

<?php include("includes/admin_header.php"); ?>


<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<p>Nuevo Grupo</p>
			<hr>
			
			<form id="form_suc" name="form_suc" method="POST" action="grupo_add.php" autocomplete="off">
				<input type="hidden" name="idgrupo" id="idgrupo" >
				<label>Grupo:</label><br>
				<input type"text" name="grupo" id="grupo" required><br>
				<label>Posici&oacute;n:</label><br>
				<input type"text" name="posicion" id="posicion" required>

				<hr>
				<input type="Submit" Value="Guardar">
			</form>

		</article>

	</section>

</div>	
		
</body>

</html>
          