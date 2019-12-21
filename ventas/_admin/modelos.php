<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Modelos</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>

<body>

<?php include("includes/admin_header.php"); ?>


<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<a href="modelo_agregar.php">Nuevo Modelo</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM grupos WHERE activo = 1 ORDER BY posicion";
			$grupos=mysqli_query($con, $SQL);


			while($grup=mysqli_fetch_array($grupos)) {

				echo "<strong>".$grup["grupo"]."</strong>";

			$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo =".$grup["idgrupo"]." ORDER BY posicion" ;
			$modelos=mysqli_query($con, $SQL);

			?>

			<table id="tabla" rules="all" >
				<tr style="text-align:center;background: #ccc;"><td width="400px">Modelos</td><td width="100px">Opciones</td></tr>

			<?php
				while($mod=mysqli_fetch_array($modelos)) { ?>

				<tr><td style="font-size: 0.8em;"> <?php echo $mod["modelo"]?> </td> <td><a href="modelo.php?IDrecord=<?php echo $mod['idmodelo'] ?>"><img src="imagenes/editar.png" width="15px"></a></td></tr>

				<?php } ?>


			</table>

		<br>

			<?php } ?>







		</article>

	</section>

</div>

</body>

</html>

