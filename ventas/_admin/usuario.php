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

			<p>Editar Datos del usuario</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM usuarios WHERE idusuario=".$_GET["IDrecord"];
			$usuario=mysqli_query($con, $SQL);
			$usu=mysqli_fetch_array($usuario);
			$SQL="SELECT * FROM sucursales";
			$sucursales=mysqli_query($con, $SQL);
			$SQL="SELECT * FROM perfiles";
			$perfiles=mysqli_query($con, $SQL);


			?>

			<form id="form_suc" name="form_suc" method="POST" action="usuario_edit.php" autocomplete="off">
				<input type="hidden" name="idusuario" id="idusuario" value="<?php echo $usu['idusuario'];?>">
				<label>Nombre:</label><br>
				<input type"text" name="nombre" id="nombre" value="<?php echo $usu['nombre'];?>" size="50"><br>
				<label>Usuario:</label><br>
				<input type"text" name="usuario" id="usuario" value="<?php echo $usu['usuario'];?>" size="50"><br>
				<label>Clave:</label><br>
				<input type"text" name="clave" id="clave" value="<?php echo $usu['clave'];?>"><br>
				<label>Perfil:</label><br>
				<select name="idperfil">
				<?php
				while ($perfil=mysqli_fetch_array($perfiles)) : ?>
				<option value="<?php echo $perfil['idperfil']; ?>"  <?php  if ($perfil["idperfil"] == $usu["idperfil"]) { echo "selected"; } ?> > <?php echo $perfil["perfil"]?> </option>
				<?php endwhile?>
				<select><br>
				<label>Sucursal:</label><br>
				<select name="idsucursal">
				<?php
				while ($suc=mysqli_fetch_array($sucursales)) { ?>
				<option value="<?php echo $suc['idsucursal']; ?>"  <?php  if ($suc["idsucursal"] == $usu["idsucursal"]) { echo "selected"; } ?> > <?php echo $suc["sucursal"]?> </option>
				<?php } ?>
				<select><br>

				<label>Activo</label><br>

				<select name="activo">

					<?php
						if ($usu["activo"]=="1") { ?>

						<option value="1" selected>Si</option>
						<option value="0" >No</option>

					<?php	} else { ?>
							<option value="1" >Si</option>
							<option value="0" selected>No</option>

					<?php }
					 ?>
				</select><br>
				<hr>






				<input type="Submit" Value="Guardar">
			</form>









		</article>

	</section>

</div>

</body>

</html>

