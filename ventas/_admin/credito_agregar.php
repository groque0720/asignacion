<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Derka y Vargas S. A.</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>

<body>

<?php include("includes/admin_header.php"); ?>


<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<p>Nuevo Tipo de Cr&eacute;dito</p>
			<hr>
			
			<form id="form_suc" name="form_suc" method="POST" action="credito_add.php" autocomplete="off">
				<input type="hidden" name="idtipo" id="idtipo" >
				<label>Cr&eacute;dito:</label><br>
				<input type"text" name="credito" id="credito" ><br>
								
				</select>
				<hr>
				<input type="Submit" Value="Guardar">
			</form>

		</article>

	</section>

</div>	
		
</body>

</html>
          