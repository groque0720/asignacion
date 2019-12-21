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

			<p>Editar Datos de la Sucursal</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM sucursales WHERE idsucursal=".$_GET["IDrecord"];
			$sucursal=mysqli_query($con, $SQL);
			$suc=mysqli_fetch_array($sucursal);
			?>

			<form id="form_suc" name="form_suc" method="POST" action="sucursal_edit.php" autocomplete="off">
				<input type="hidden" name="idsucursal" id="idsucursal" value="<?php echo $suc['idsucursal'];?>">
				<label>Sucursal:</label><br>
				<input type"text" name="sucursal" id="sucursal" value="<?php echo $suc['sucursal'];?>"><br>
				<label>Sucursal Resumido:</label><br>
				<input type"text" name="sucres" id="sucres" value="<?php echo $suc['sucres'];?>"><br>
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

