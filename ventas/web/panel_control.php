<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title>Panel de Control</title>
	<link rel="stylesheet" href="../css/estilo_panel.css" />
</head>
<body>

	<?php
		include("../funciones/func_mysql.php");
		conectar();
		mysql_query("SET NAMES 'utf8'");
		$SQL="SELECT sucursal, idsucursal FROM sucursales";
		$sucursales=mysqli_query($con, $SQL);
	?>

	<div class="titulos">
		<h1>Unidades Canceladas en el Mes</h1>
	</div>


	<?php

	$tot_op = 0;

	 while ($sucursal=mysqli_fetch_array($sucursales)) { ?>

		<div class="sub_titulos">
			<h1><?php echo $sucursal["sucursal"]; ?></h1>
		</div>

		<div class="ventas">
			<table>
				<thead>
					<tr>
						<td width="3%">Nro</td>
						<td width="5%">Fecha</td>
						<td width="3%">Compra</td>
						<td width="5%">Asesor</td>
						<td width="12%">Cliente</td>
						<td width="15%">Unidad</td>
						<td width="5%">Mes Ent.</td>
						<td width="5%">A&ntilde;o</td>

					</tr>
				</thead>


				<?php

				 	$SQL="SELECT
					reservas.idreserva AS nrores,
					reservas.compra AS compra,
					reservas.fecres AS fecha,
					usuarios.nombre AS asesor,
					clientes.nombre AS cliente,
					reservas.detalleu AS usado,
					reservas.mesentrega AS mes_ent,
					reservas.anoentrega AS ano_ent,
					sucursales.sucursal AS sucursal,
					sucursales.idsucursal AS idsucursal,
					reservas.idgrupo AS idgrupo,
					reservas.idmodelo AS idmodelo,
					reservas.anulada AS anulado
					FROM
					reservas
					Inner Join clientes ON reservas.idcliente = clientes.idcliente
					Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
					Inner Join sucursales ON usuarios.idsucursal = sucursales.idsucursal
					WHERE
					reservas.compra <>  'null' AND
					reservas.anulada =  'true' AND
					MONTH(reservas.fecres) =  MONTH(CURDATE()) AND
					sucursales.idsucursal =".$sucursal["idsucursal"]."
					ORDER BY asesor ASC, fecha DESC";

					$operaciones = mysqli_query($con, $SQL);
				?>
				<tbody>

					<?php
					$cant_op=0;

					 while ($operacion=mysqli_fetch_array($operaciones)) {

					 	$cant_op = $cant_op + 1;
					 	$tot_op = $tot_op + 1;

					$SQL="SELECT * FROM grupos WHERE idgrupo=".$operacion['idgrupo'];
					$gru=mysqli_query($con, $SQL);
					if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

					$SQL="SELECT * FROM modelos WHERE idmodelo=".$operacion['idmodelo'];
					$mod=mysqli_query($con, $SQL);
					if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}
					?>

					<tr>
						<td class="text_center"><?php echo $operacion["nrores"]; ?></td>
						<td class="text_center"><?php echo cambiarformatofecha($operacion["fecha"]); ?></td>
						<td class="text_center"><?php echo $operacion["compra"]; ?></td>
						<td class="text_center"><?php echo $operacion["asesor"]; ?></td>
						<td width="15%"><?php echo $operacion["cliente"]; ?></td>
						<td width="15%"><?php echo $grupo["grupo"]." ".$modelo["modelo"]." ".$operacion["usado"]; ?></td>
						<td class="text_center"><?php echo $operacion["mes_ent"]; ?></td>
						<td class="text_center"><?php echo $operacion["ano_ent"]; ?></td>

					</tr>
					<?php } ?>

				</tbody>
			</table>
		</div>
		<div class="totales">
			<strong>Operaciones de <?php echo $sucursal["sucursal"] ?> del mes actual es : <?php echo $cant_op; ?> </strong>
		</div>
		<hr>

	<?php } ?>

	<div class="totales gral">
			<strong>Total de Operaciones DERKA Y VARGAS del mes actual es : <?php echo $tot_op; ?> </strong>
		</div>

	<div class="canceladas">
	</div>
</body>
</html>