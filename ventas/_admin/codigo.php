<!DOCTYPE html>
<html lang="es">
<head>
    <title>C&oacute;digos</title>
     <link rel="stylesheet" type="text/css" href="css/admincss.css">
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     <script src="../js/jquery-1.9.1.js"></script>
     <script language="javascript">

	$(document).ready(function(){


				if (""==$("#movimiento").val()) {
		 		$("#esdescuento").val("");
				$("#escredito").val();
				$("#filaabajo").hide(0);
				$("#descuento").hide(0);
				$("#esdescuento").removeAttr("required");
				$("#escredito").removeAttr("required");

		  		};
		 		if ("1"==$("#movimiento").val()) {
		 		$("#escredito").val("");
				$("#filaabajo").hide(0);
				$("#descuento").show(0);
				$("#esdescuento").attr("required", "true");
				$("#escredito").removeAttr("required");

		  		};
		  		if ("2"==$("#movimiento").val()) {
		  		$("#esdescuento").val("");
				$("#filaabajo").show(0);
				$("#creditos").hide(0);
				$("#descuento").hide(0);
				$("#esdescuento").removeAttr("required");
				$("#escredito").attr("required", "true");
				};


				if (""==$("#escredito").val()) {
				$("#creditos").hide(0);
				$("#tipocredito").removeAttr("required");
				$("#financiera").removeAttr("required");
				};
		 		if ("1"==$("#escredito").val()) {
				$("#creditos").show(0);
				$("#tipocredito").attr("required", "true");
				$("#financiera").attr("required", "true");

		  		};
		  		if ("0"==$("#escredito").val()) {
				$("#creditos").hide(0);
				$("#tipocredito").removeAttr("required");
				$("#financiera").removeAttr("required");
		  		};







			$("#movimiento").change(function ()
		 	{
		 		if (""==$(this).val()) {
		 		$("#esdescuento").val("");
		 		$("#tipocredito").val("");
				$("#escredito").val("");
				$("#financiera").val("");
				$("#filaabajo").hide(250);
				$("#descuento").hide(250);
				$("#esdescuento").removeAttr("required");
				$("#escredito").removeAttr("required");

		  		};
		 		if ("1"==$(this).val()) {
		 		$("#esdescuento").val("");
		 		$("#tipocredito").val("");
				$("#escredito").val("");
				$("#financiera").val("");
				$("#filaabajo").hide(250);
				$("#descuento").show(250);
				$("#esdescuento").attr("required", "true");
				$("#escredito").removeAttr("required");
				$("#tipocredito").removeAttr("required");
				$("#financiera").removeAttr("required");

		  		};
		  		if ("2"==$(this).val()) {
		  		$("#esdescuento").val("");
		 		$("#tipocredito").val("");
				$("#escredito").val("");
				$("#financiera").val("");
				$("#filaabajo").show(250);
				$("#creditos").hide(0);
				$("#descuento").hide(250);
				$("#esdescuento").removeAttr("required");
				$("#escredito").attr("required", "true");
				};

  			 });

			$("#escredito").change(function ()
		 	{
		 		if (""==$(this).val()) {
		 		$("#tipocredito").val("");
				$("#financiera").val("");
				$("#creditos").hide(250);
				$("#tipocredito").removeAttr("required");
				$("#financiera").removeAttr("required");
				$("#esdescuento").removeAttr("required");
				};
		 		if ("1"==$(this).val()) {
		 		$("#tipocredito").val("");
				$("#financiera").val("");
				$("#creditos").show(250);
				$("#tipocredito").attr("required", "true");
				$("#financiera").attr("required", "true");
				$("#esdescuento").removeAttr("required");

		  		};
		  		if ("0"==$(this).val()) {
		  		$("#tipocredito").val("");
				$("#financiera").val("");
				$("#creditos").hide(250);
				$("#tipocredito").removeAttr("required");
				$("#financiera").removeAttr("required");
				$("#esdescuento").removeAttr("required");
		  		};

  			 });
	})

  	</script>



</head>

<body>

	<?php include("includes/admin_header.php"); ?>

<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<p>Editar C&oacute;digo</p>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();

			$SQL="SELECT * FROM codigos WHERE idcodigo=".$_GET['IDrecord'];
			$codigos=mysqli_query($con, $SQL);
			$codigo=mysqli_fetch_array($codigos);
			$SQL="SELECT * FROM financieras WHERE activo = 1 ORDER BY financiera";
			$financieras=mysqli_query($con, $SQL);

			$SQL="SELECT * FROM tipos_creditos WHERE activo = 1 ORDER BY tipocredito";
			$creditos = mysqli_query($con, $SQL);

			?>

			<form id="form_suc" name="form_suc" method="POST" action="codigo_edit.php" autocomplete="off">
				<input type="hidden" name="idcodigo" id="idcodigo" value="<?php echo $codigo['idcodigo'];?>">

				<label>Movimiento</label><br>
				<select id="movimiento" name="movimiento" required>
					<option value=""></option>
					<option value="1" <?php  if ($codigo["movimiento"] == 1) { echo "selected"; } ?>>Detalle a Pagar</option>
					<option value="2" <?php  if ($codigo["movimiento"] == 2) { echo "selected"; } ?>>Forma de Pago</option>
				</select><br>

				<label>Detalle:</label><br>
				<input type"text" name="detalle" id="detalle" size="50" value="<?php echo $codigo['detalle'];?>" required><br>
				<hr>

				<div id="descuento">
				<label>Es Descuento?</label><br>
				<select id="esdescuento" name="esdescuento" >
					<option value=""></option>
					<option value="1" <?php  if ($codigo["descuento"] == 1) { echo "selected"; } ?>>Si</option>
					<option value="0" <?php  if ($codigo["descuento"] == 0) { echo "selected"; } ?>>No</option>
				</select><br>
				<hr>
				</div>

				<div id="filaabajo">

				<label>Es Cr&eacute;dito?</label><br>
				<select id="escredito" name="escredito" >
					<option value=""></option>
					<option value="1" <?php  if ($codigo["credito"] == 1) { echo "selected"; } ?>>Si</option>
					<option value="0" <?php  if ($codigo["credito"] == 0) { echo "selected"; } ?>>No</option>
				</select><br>

					<div id="creditos">
						<hr>
					<label>Tipo de Cr&eacute;dito:</label><br>
					<select id="tipocredito" name="tipocredito" >
					<option value=""></option>
					<?php
					while ($credito=mysqli_fetch_array($creditos)) { ?>
					<option value="<?php echo $credito['idtipocredito']; ?>" <?php  if ($credito["idtipocredito"] == $codigo["tipocredito"]) { echo "selected"; } ?>> <?php echo $credito["tipocredito"]?> </option>
					<?php } ?>
					<select><br>
					<label>Financiera:</label><br>
					<select id="financiera" name="financiera" >
					<option value=""></option>
					<?php
					while ($financiera=mysqli_fetch_array($financieras)) { ?>
					<option value="<?php echo $financiera['idfinanciera']; ?>" <?php  if ($financiera["idfinanciera"] == $codigo["financiera"]) { echo "selected"; } ?>> <?php echo $financiera["financiera"]?> </option>
					<?php } ?>
					<select><br>
					</div>
				<hr>
				</div>







				<input type="Submit" Value="Guardar">
			</form>




		</article>

	</section>

</div>
</body>
 </html>
