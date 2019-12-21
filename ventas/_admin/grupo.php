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

			<p>Editar Grupo</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM grupos WHERE idgrupo=".$_GET["IDrecord"];
			$grupos=mysqli_query($con, $SQL);
			$grup=mysqli_fetch_array($grupos);
			?>

			<form id="form_suc" name="form_suc" method="POST" action="grupo_edit.php" autocomplete="off">
				<input type="hidden" name="idgrupo" id="idgrupo" value="<?php echo $grup['idgrupo'];?>">
				<label>Sucursal:</label><br>
				<input type"text" name="grupo" id="grupo" value="<?php echo $grup['grupo'];?>"><br>
				<label>Posici&oacute;n:</label><br>
				<input type"text" name="posicion" id="posicion" value="<?php echo $grup['posicion'];?>" required><br>

				<label>Activo</label><br>

				<select name="activo">

					<?php
						if ($grup["activo"]=="1") { ?>

						<option value="1" selected>Si</option>
						<option value="0" >No</option>

					<?php	} else { ?>
							<option value="1" >Si</option>
							<option value="0" selected>No</option>

					<?php }
					 ?>
				</select>

					  <br>


				<hr>
				<input type="Submit" Value="Guardar">
			</form>


		</article>

	</section>

</div>

</body>