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

			<p>Nueva Sucursal</p>
			<hr>
			
			<form id="form_suc" name="form_suc" method="POST" action="sucursal_add.php" autocomplete="off">
				<input type="hidden" name="idsucursal" id="idsucursal" >
				<label>Sucursal:</label><br>
				<input type"text" name="sucursal" id="sucursal" ><br>
				<label>Sucursal Resumido:</label><br>
				<input type"text" name="sucres" id="sucres" ><br>
				

				
				</select>
				<hr>
				<input type="Submit" Value="Guardar">
			</form>

		</article>

	</section>

</div>	
		
</body>

</html>
          