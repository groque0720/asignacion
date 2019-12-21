<!DOCTYPE html>
<html lang="es">
<head>
    <title>C&oacute;digos</title>
     <link rel="stylesheet" type="text/css" href="css/admincss.css">
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<body>

	<?php include("includes/admin_header.php"); ?>

<div id="agrupar">

	<?php include("includes/admin_nav.php"); ?>

	<section id="contenedor">

		<article id="contenido">

			<a href="codigo_agregar.php">Nuevo C&oacute;digo</a>
			<hr>

			<?php
			include("../funciones/func_mysql.php");
			conectar();
			$SQL="SELECT * FROM codigos WHERE activo = 1 and movimiento = 1 ORDER BY bloqueado DESC, detalle ASC";
			$codigos=mysqli_query($con, $SQL); ?>

			<spam style="font-size: 1.3em; font-weight: bold;">Detalle a Pagar</spam> <br><br>
			<table id="tabla" rules="all" style="font-size: 0.8em;">
				<tr style="text-align:center;background: #ccc;">
					<td width="35%">Detalle</td>
					<td width="7%">Descuento</td>
					<td width="7%">Cr&eacute;dito</td>
					<td width="17%">Tipo Cr&eacute;dito</td>
					<td width="22%">Financiera</td>
					<td width="6%">Op</td>
				</tr>

				<?php

			while($codigo=mysqli_fetch_array($codigos)) {?>

			<tr >
				<td ><?php echo $codigo['detalle'] ?></td>
				<td style="text-align:center;"><?php if ($codigo['descuento']==1) { echo "Si"; }else{ echo "-";} ?></td>
				<td style="text-align:center;"><?php if ($codigo['credito']==1) { echo "Si"; }else{ echo "-";} ?></td>
				<td style="text-align:center;">
					<?php
					if (is_null($codigo['tipocredito'] )or ($codigo['financiera']==0)) {
						echo "-";
					}else{
						$SQL="SELECT * FROM tipos_creditos WHERE activo = 1 and idtipocredito =".$codigo['tipocredito'];
						$creditos=mysqli_query($con, $SQL);
						$credito=mysqli_fetch_array($creditos);
						echo $credito['tipocredito'];
					}
					?>

				</td>
				<td style="text-align:center;">
					<?php
					if (is_null($codigo['financiera']) or ($codigo['financiera']==0)) {
						echo "-";
					}else{
						$SQL="SELECT * FROM financieras WHERE activo = 1 and idfinanciera=".$codigo['financiera'];
						$financieras=mysqli_query($con, $SQL);
						$financiera=mysqli_fetch_array($financieras);
						echo $credito['financiera'];
					}
					?>
				</td>
				<td style="text-align:center;"><?php if ($codigo['bloqueado']==0) { ?>
					<a href="codigo.php?IDrecord=<?php echo $codigo['idcodigo'] ?>"><img src="imagenes/editar.png" width="15px"></a>
					<?php } ?></td>
			</tr>



				<?php } ?>


			</table>

			<br>
			<?php
			$SQL="SELECT * FROM codigos WHERE activo = 1 and movimiento = 2 ORDER BY detalle";
			$codigos=mysqli_query($con, $SQL); ?>

			<spam style="font-size: 1.3em; font-weight: bold;">Forma de Pago</spam> <br><br>
			<table id="tabla" rules="all" style="font-size: 0.8em;">
				<tr style="text-align:center;background: #ccc;">
					<td width="35%">Detalle</td>
					<td width="7%">Descuento</td>
					<td width="7%">Cr&eacute;dito</td>
					<td width="17%">Tipo Cr&eacute;dito</td>
					<td width="22%">Financiera</td>
					<td width="6%">Op</td>
				</tr>

				<?php

			while($codigo=mysqli_fetch_array($codigos)) {?>

			<tr >
				<td ><?php echo $codigo['detalle'] ?></td>
				<td style="text-align:center;"><?php if ($codigo['descuento']==1) { echo "Si"; }else{ echo "-";} ?></td>
				<td style="text-align:center;"><?php if ($codigo['credito']==1) { echo "Si"; }else{ echo "-";} ?></td>
				<td style="text-align:center;">
					<?php
					if (is_null($codigo['tipocredito'] )or ($codigo['financiera']==0)) {
						echo "-";
					}else{
						$SQL="SELECT * FROM tipos_creditos WHERE activo = 1 and idtipocredito =".$codigo['tipocredito'];
						$creditos=mysqli_query($con, $SQL);
						$credito=mysqli_fetch_array($creditos);
						echo $credito['tipocredito'];
					}
					?>

				</td>
				<td style="text-align:center;">
					<?php
					if (is_null($codigo['financiera'] ) or ($codigo['financiera']==0)) {
						echo "-";
					}else{
						$SQL="SELECT * FROM financieras WHERE activo = 1 and idfinanciera=".$codigo['financiera'];
						$financieras=mysqli_query($con, $SQL);
						$financiera=mysqli_fetch_array($financieras);
						echo $financiera['financiera'];
					}
					?>
				</td>
				<td style="text-align:center;"><?php if ($codigo['bloqueado']==0) { ?>
					<a href="codigo.php?IDrecord=<?php echo $codigo['idcodigo'] ?>"><img src="imagenes/editar.png" width="15px"></a>
					<?php } ?></td>
			</tr>



				<?php } ?>


			</table>


		</article>

	</section>

</div>
</body>
 </html>
