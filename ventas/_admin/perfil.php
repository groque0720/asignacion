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

			<p>Editar Datos del Perfil</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM perfiles WHERE idperfil=".$_GET["IDrecord"];
			$perfiles=mysqli_query($con, $SQL);
			$suc=mysqli_fetch_array($perfiles);
			?>

			<form id="form_suc" name="form_suc" method="POST" action="perfil_edit.php" autocomplete="off">
				<input type="hidden" name="idperfil" id="idperfil" value="<?php echo $suc['idperfil'];?>">
				<label>Perfil:</label><br>
				<input type"text" name="perfil" id="perfil" value="<?php echo $suc['perfil'];?>"><br>

				<label>Activo</label><br>

				<select name="activo">

					<?php
						if ($suc["activo"]=="1") { ?>

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

</html>

