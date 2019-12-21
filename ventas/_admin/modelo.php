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

			<p>Editar Datos del Modelo</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM modelos WHERE idmodelo=".$_GET["IDrecord"];
			$mod=mysqli_query($con, $SQL);
			$modelos=mysqli_fetch_array($mod);
			$SQL="SELECT * FROM grupos WHERE activo=1";
			$grupos=mysqli_query($con, $SQL);
			$SQL="SELECT * FROM tipos WHERE activo=1";
			$tipos=mysqli_query($con, $SQL);

			?>

			<form id="form_mod" name="form_mod" method="POST" action="modelo_edit.php" autocomplete="off">
				<input type="hidden" name="idmodelo" id="idmodelo" value="<?php echo $modelos['idmodelo'];?>">

				<label>Tipo:</label></br>
				<select name="idtipo" required>
				<?php
				while($tipo=mysqli_fetch_array($tipos)) { ?>
				<option value="<?php echo $tipo['idtipo']; ?>" <?php  if ($tipo["idtipo"] == $modelos["idtipo"]) { echo "selected"; } ?>><?php echo $tipo["tipo"]?> </option>
				<?php } ?>
				</select></br>

				<label>Grupo:</label></br>
				<select name="idgrupo" required>

				<?php
				while($grup=mysqli_fetch_array($grupos)) { ?>
				<option value="<?php echo $grup['idgrupo']; ?>" <?php  if ($grup["idgrupo"] == $modelos["idgrupo"]) { echo "selected"; } ?>><?php echo $grup["grupo"]?> </option>
				<?php } ?>
				</select></br>

				<label>Modelo:</label></br>
				<input type"text" name="modelo" id="modelo" size="50" value="<?php echo $modelos['modelo'];?>"><br>

				<label>Posici&oacute;n:</label></br>
				<input type"text" name="posicion" id="posicion" value="<?php echo $modelos['posicion'];?>"><br>

				<label>Activo:</label></br>

				<select name="activo">

					<?php
						if ($modelos["activo"]=="1") { ?>

						<option value="1" selected>Si</option>
						<option value="0" >No</option>

					<?php	} else { ?>
							<option value="1" >Si</option>
							<option value="0" selected>No</option>

					<?php }
					 ?>

					</select>
				<hr>

				<input type="Submit" Value="Guardar">
			</form>









		</article>

	</section>

</div>

</body>

</html>

