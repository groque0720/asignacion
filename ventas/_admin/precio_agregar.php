<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Precios Unidades</title>
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

		<?php
		include("../funciones/func_mysql.php");
		conectar();
		//mysql_query("SET NAMES 'utf8'");
		$SQL="SELECT modelos.idmodelo, modelos.idgrupo, modelos.idtipo, modelos.modelo, modelos.posicion, grupos.grupo, modelos.activo FROM modelos , grupos WHERE modelos.idgrupo =  grupos.idgrupo and modelos.activo = 1 ORDER BY grupos.posicion, modelos.posicion ASC";
		$modelos=mysqli_query($con, $SQL);?>

		<article id="contenido">

			<p>Cargar Precios a Modelo Nuevo</p>
			<hr>

			<form id="form_mod" name="form_mod" method="POST" action="precio_add.php" autocomplete="off">


				<label>Veh&iacute;culo:</label></br>
				<select id="idmodelo" name="idmodelo" required>
					<option value=""></option>
					<?php
					while ($mod=mysqli_fetch_array($modelos)) { ?>
					<option value="<?php echo $mod['idmodelo'];?>"><?php echo $mod['grupo']." - ".$mod['modelo'];?> </option>
					<?php }  ?>
				</select>
				<hr>



				<label >Moneda:</label>
				<select  id="moneda" name="moneda" required>
					<option value="$">Pesos</option>
					<option value="U$S">Dolares</option>
				</select><br>


				<label>Precio de Lista:</label>
				<input type"number" name="pl" id="pl" autocomplete="off" value="0" ><hr>

				<label>Flete y 01:</label>
				<input type"number" name="flete" id="flete"  autocomplete="off" value="0" ><br>

				<label>Transferencia e Inscripci&oacute;n:</label>
				<input type"number" name="trans" id="trans" autocomplete="off" value="0" ><br>

				<hr>

				<label>Neto:</label>
				<input type"number" name="neto" id="neto" autocomplete="off" value="0" ><br>

				<label>I.V.A.:</label>
				<input type"number" name="iva" id="iva" autocomplete="off" value="0"><br>

				<label>SubTotal:</label>
				<input type"number" name="subtotal" id="subtotal"autocomplete="off"  value="0" ><br>

				<label>Impuesto Interno:</label>
				<input type"number" name="impuesto" id="impuesto"  autocomplete="off" value="0" ><br>

				<hr>

				<input type="Submit" Value="Guardar">
			</form>









		</article>

	</section>

</div>

</body>

</html>

