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

			<a href="grupo_agregar.php">Nuevo Grupo</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM grupos WHERE activo = 1 ORDER BY posicion";
			$grupos=mysqli_query($con, $SQL);
			?>

			<table id="tabla" rules="all">
				<tr style="text-align:center;background: #ccc;"><td width="200px">Grupos</td><td width="100px">Opciones</td></tr>

			<?php
				while($grup=mysqli_fetch_array($grupos)) { ?>

				<tr><td> <?php echo $grup["grupo"]?> </td> <td><a href="grupo.php?IDrecord=<?php echo $grup["idgrupo"];?>"><img src="imagenes/editar.png" width="20px"></a></td></tr>

				<?php } ?>


			</table>










		</article>

	</section>

</div>

</body>

</html>

