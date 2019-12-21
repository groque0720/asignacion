<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Financieras - Derka y Vargas S. A.</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>

<body>

<?php include("includes/admin_header.php"); ?>


<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<a href="financiera_agregar.php">Nueva Financiera</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM financieras WHERE activo = 1 ORDER BY financiera";
			$financieras=mysqli_query($con, $SQL);

			?>

			<table id="tabla" rules="all" >
				<tr style="text-align:center;background: #ccc;">
					<td width="50%">Financieras</td>
					<td width="10%">Opciones</td>
				</tr>

			<?php
				while($fin=mysqli_fetch_array($financieras)) { ?>

				<tr><td> <?php echo $fin["financiera"]?> </td>
				<td><a href="financiera.php?IDrecord=<?php echo $fin["idfinanciera"];?>"><img src="imagenes/editar.png" width="20px"></a></td></tr>

				<?php } ?>

			</table>

		</article>

	</section>

</div>

</body>

</html>

