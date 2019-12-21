<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Usuarios - Derka y Vargas S. A.</title>
    <link rel="stylesheet" type="text/css" href="css/admincss.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="../js/jquery-1.9.1.js"></script>
     <script language="javascript">

	$(document).ready(function(){

		$("#menu_lateral").hide();

		if ($("#idperfil").val()==1) {
 			$("#menu_lateral").show();
 		};


	});

  	</script>

</head>

<body>

<?php include("includes/admin_header.php"); ?>

<input id="idperfil" name="idperfil" type="hidden" value="<?php echo $_SESSION["idperfil"]; ?>">
<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<p>Editar Precio de Modelo</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();

			$SQL="SELECT * FROM modelos WHERE idmodelo=".$_GET["idmodelo"];
			$mod=mysqli_query($con, $SQL);
			$modelos=mysqli_fetch_array($mod);


			$SQL="SELECT * FROM listaprecio WHERE idprecio =".$_GET['IDrecord'];
			$precios=mysqli_query($con, $SQL);
			$precio=mysqli_fetch_array($precios);

			?>

			<form id="form_mod" name="form_mod" method="POST" action="precio_edit.php" autocomplete="off">
				<input type="hidden" name="idprecio" id="idprecio" value="<?php echo $_GET['IDrecord'];?>">


				<label>Modelo:</label></br>
				<input type"text" name="modelo" id="modelo" size="50" value="<?php echo $modelos['modelo'];?>" readonly="readonly"><br>

				<hr>

				<label >Moneda:</label>
				<select  id="moneda" name="moneda" required>
					<option value="$" <?php  if ($precio["moneda"] == "$") { echo "selected"; } ?>>Pesos</option>
					<option value="U$S" <?php  if ($precio["moneda"] != "$") { echo "selected"; } ?>>Dolares</option>
				</select><br>


				<label>Precio de Lista:</label>
				<input type"number" name="pl" id="pl" autocomplete="off" value="<?php echo number_format($precio['pl'], 2, '.','');?>" ><br>

				<label>Flete y 01:</label>
				<input type"number" name="flete" id="flete"  autocomplete="off" value="<?php echo number_format($precio['flete'], 2, '.','');?>" ><br>

				<label>Transferencia e Inscripci&oacute;n:</label>
				<input type"number" name="trans" id="trans" autocomplete="off" value="<?php echo number_format($precio['trans'], 2, '.','');?>" ><br>

				<hr>

				<label>Neto:</label>
				<input type"number" name="neto" id="neto" autocomplete="off" value="<?php echo number_format($precio['neto'], 2, '.','');?>" ><br>

				<label>I.V.A.:</label>
				<input type"number" name="iva" id="iva" autocomplete="off" value="<?php echo number_format($precio['iva'], 2, '.','');?>" ><br>

				<label>SubTotal:</label>
				<input type"number" name="subtotal" id="subtotal"autocomplete="off"  value="<?php echo number_format($precio['subtotal'], 2, '.','');?>" ><br>

				<label>Impuesto Interno:</label>
				<input type"number" name="impuesto" id="impuesto"  autocomplete="off" value="<?php echo number_format($precio['impuesto'], 2, '.','');?>" ><br>

				<hr>

				<label>Activo:</label>
				<select name="activo">

					<?php
						if ($precio["activo"]=="1") { ?>

						<option value="1" selected>Si</option>
						<option value="0" >No</option>

					<?php	} else { ?>
							<option value="1" >Si</option>
							<option value="0" selected>No</option>

					<?php }
					 ?>
				</select>


				<hr>

				<input type="Submit" Value="Guardar">
			</form>









		</article>

	</section>

</div>

</body>

</html>

