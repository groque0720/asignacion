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

			<a href="tipo_agregar.php">Nuevo Tipo</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM tipos WHERE activo=1 ORDER BY tipo";
			$tipos=mysqli_query($con, $SQL);
			?>

			<table id="tabla" rules="all">
				<tr style="text-align:center;background: #ccc;"><td width="200px">Tipos de Veh&iacute;culos</td><td width="100px">Opciones</td></tr>

			<?php
				while($suc=mysqli_fetch_array($tipos)) { ?>

				<tr><td> <?php echo $suc["tipo"]?> </td> <td><a href="tipo.php?IDrecord=<?php echo $suc["idtipo"];?>"><img src="imagenes/editar.png" width="20px"></a></td></tr>

				<?php } ?>


			</table>

		</article>

	</section>

</div>

</body>

</html>

