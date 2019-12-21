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

			<a href="usuario_agregar.php">Nuevo Usuario</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM usuarios WHERE activo = 1 ORDER BY nombre";
			$usuarios=mysqli_query($con, $SQL);

			?>

			<table id="tabla" rules="all">
				<tr style="text-align:center;background: #ccc;">
					<td width="20%">Nombre</td>
					<td width="40%">Usuarios</td>
					<td width="27%">Perfil</td>
					<td width="15%">Sucursal</td>
					<td width="2%">Opciones</td>
				</tr>

			<?php
				while($usu=mysqli_fetch_array($usuarios)) { ?>

				<tr><td> <?php echo $usu["nombre"]?> </td>
					<td> <?php echo $usu["usuario"]?> </td>


					<td>
						<?php
							$SQL="SELECT * FROM perfiles";
							$perfiles=mysqli_query($con, $SQL);

							while ($perfil=mysqli_fetch_array($perfiles)) {
								if ($perfil["idperfil"] ==  $usu["idperfil"]) {
									echo $perfil["perfil"];
								}
							}

						?>
					</td>

					<td>
						<?php
							$SQL="SELECT * FROM sucursales";
							$sucursales=mysqli_query($con, $SQL);

							while ($suc=mysqli_fetch_array($sucursales)) {
								if ($suc["idsucursal"] ==  $usu["idsucursal"]) {
									echo $suc["sucursal"];
								}
							}

						?>
					</td>
				 <td><a href="usuario.php?IDrecord=<?php echo $usu["idusuario"];?>"><img src="imagenes/editar.png" width="20px"></a></td></tr>

				<?php } ?>


			</table>










		</article>

	</section>

</div>

</body>

</html>

