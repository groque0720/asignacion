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

			<p>Alta Usuario</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM sucursales";
			$sucursales=mysqli_query($con, $SQL);
			$SQL="SELECT * FROM perfiles ORDER BY perfil";
			$perfiles=mysqli_query($con, $SQL);


			?>


			<form id="form_suc" name="form_suc" method="POST" action="usuario_add.php" autocomplete="off">
				<label>Nombre:</label><br>
				<input type"text" name="nombre" id="nombre" size="50" required><br>
				<label>Usuario:</label><br>
				<input type"text" name="usuario" id="usuario" size="50" required><br>
				<label>Clave:</label><br>
				<input type"text" name="clave" id="clave" required><br>
				<label>Perfil:</label><br>
				<select name="idperfil" required>
					<option value="" seleted></option>
				<?php
				while ($perfil=mysqli_fetch_array($perfiles)) { ?>
				<option value="<?php echo $perfil['idperfil']; ?>"><?php echo $perfil["perfil"]?> </option>
				<?php } ?>
				<select><br>
				<label>Sucursal:</label><br>
				<select name="idsucursal" required>
					<option value="" seleted></option>
				<?php
				while ($suc=mysqli_fetch_array($sucursales)) { ?>
				<option value="<?php echo $suc['idsucursal']; ?>"> <?php echo $suc["sucursal"]?> </option>
				<?php } ?>
				<select><br>

				<label>Activo</label><br>

				<select name="activo">
					<option value="1" selected required>Si</option>
					<option value="0" >No</option>
				</select><br>
				<hr>

				<input type="Submit" Value="Dar Alta">
			</form>









		</article>

	</section>

</div>

</body>

</html>