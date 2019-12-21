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

			<p>Nueva Financiera</p>
			<hr>
			
			<form id="form_suc" name="form_suc" method="POST" action="financiera_add.php" autocomplete="off">
				<input type="hidden" name="idfinanciera" id="idfinanciera" >
				<label>Nombre de la Financiera:</label><br>
				<input type"text" name="financiera" id="financiera" size="60"><br>
								
				</select>
				<hr>
				<input type="Submit" Value="Guardar">
			</form>

		</article>

	</section>

</div>	
		
</body>

</html>
          