<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Usuarios - Derka y Vargas S. A.</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>

<body>

<?php include("includes/admin_header.php"); ?>


<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<p>Agregar Modelo</p>

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM grupos WHERE activo = 1 ORDER BY posicion";
			$grupos=mysqli_query($con, $SQL);
			$SQL="SELECT * FROM tipos WHERE activo=1";
			$tipos=mysqli_query($con, $SQL);

			 ?>


			<hr>

			<form id="form_mod" name="form_mod" method="POST" action="modelo_add.php" autocomplete="off">


				<label>Tipo:</label></br>
				<select name="idtipo" required>
				<option value=""></option>
				<?php
				while($tipo=mysqli_fetch_array($tipos)) { ?>
				<option value="<?php echo $tipo['idtipo']; ?>"><?php echo $tipo["tipo"]?> </option>
				<?php } ?>
				</select></br>

				<label>Grupo:</label></br>
				<select name="idgrupo" required>
					<option value=""></option>

				<?php
				while($grup=mysqli_fetch_array($grupos)) { ?>
				<option value="<?php echo $grup['idgrupo']; ?>"><?php echo $grup["grupo"]?> </option>
				<?php } ?>
				 </select></br>
				 <label>Modelo:</label></br>
				 <input type="text" name="modelo" size="50" required></br>
				 <label>Posici&oacute;n:</label></br>
				 <input type="text" name="posicion" required></br>

				<hr>
				<input type="Submit" Value="Guardar">
			</form>

		</article>

	</section>

</div>

</body>

</html>

