<?php
include ("../includes/security.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Lista de Precios</title>
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

			<a href="precio_agregar.php">Nuevo Precio</a>
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

			<table id="tabla" rules="all" style="font-size: 0.8em;">
				<tr style="text-align:center;background: #ccc;">
					<td width="29%">Modelos</td>
					<td width="9%">Flete-01</td>
					<td width="9%">Transf.-Insc.</td>
					<td width="9%">Neto</td>
					<td width="9%">IVA</td>
					<td width="10%">Subtotal</td>
					<td width="10%">Imp.Interno</td>
					<td width="12%">Precio Lista</td>
					<td width="3%">Op.</td>
				</tr>

			<?php
				while($mod=mysqli_fetch_array($modelos)) {

					$SQL="SELECT * FROM listaprecio WHERE idmodelo = ".$mod['idmodelo']." AND activo = 1";
					$precios=mysqli_query($con, $SQL);
					$afectadas = mysql_num_rows($precios);
					if ($afectadas!=0) {
					$precio=mysqli_fetch_array($precios);
					?>

				<tr style="text-align: right;">
					<td style="text-align: left;"> <?php echo $mod["modelo"]?> </td>
					<td><?php echo number_format($precio["flete"], 2, ',','.')?></td>
					<td><?php echo number_format($precio["trans"], 2, ',','.')?></td>
					<td><?php if ($precio["neto"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["neto"], 2, ',','.');}?> </td>
					<td><?php if ($precio["iva"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["iva"], 2, ',','.');}?></td>
					<td><?php if ($precio["subtotal"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["subtotal"], 2, ',','.');}?></td>
					<td><?php if ($precio["impuesto"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["impuesto"], 2, ',','.');}?></td>

					<td><?php echo $precio["moneda"]." ".number_format($precio["pl"], 2, ',','.')?></td>
					<td><a href="precio.php?IDrecord=<?php echo $precio['idprecio'] ?>&idmodelo=<?php echo $mod['idmodelo']?>"><img src="imagenes/editar.png" width="15px"></a></td>
				</tr>

				<?php }
			} ?>

			</table>

			<br>

			<?php } ?>

		</article>

	</section>

</div>

</body>

</html>

