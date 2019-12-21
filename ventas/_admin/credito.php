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

			<p>Editar Datos del Tipo de Cr&eacute;dito</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM tipos_creditos WHERE idtipocredito=".$_GET["IDrecord"];
			$tipos=mysqli_query($con, $SQL);
			$suc=mysqli_fetch_array($tipos);
			?>

			<form id="form_suc" name="form_suc" method="POST" action="credito_edit.php" autocomplete="off">
				<input type="hidden" name="idtipocredito" id="idtipocredito" value="<?php echo $suc['idtipocredito'];?>">
				<label>Tipo:</label><br>
				<input type"text" name="tipocredito" id="tipocredito" value="<?php echo $suc['tipocredito'];?>"><br>

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

