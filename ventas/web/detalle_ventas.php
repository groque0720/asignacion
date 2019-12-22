<!DOCTYPE html>
<html lang="es">
<head>
    <title>Detalle de Ventas</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
     <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_sol_p.css">
     <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		$(".boton").click(function(event) {
		document.location.href ="detalle_ventas_resumen.php";
		});

	});
</script>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>


		<section id="seccion">

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="alta_sol" style="width: 40%; float: left;">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Lista de Boletos Realizados</span>
				</div>
				<div id="alta_sol" style="width: 10%; float: right;">
					<input type="button" class="boton" value="Resumen" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>
			<div id="cuerpo_asesor">

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			//mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT * FROM sucursales ORDER BY sucursal";
			$suc=mysqli_query($con, $SQL);

			while ($sucursal=mysqli_fetch_array($suc)) { ?>
				<h3><?php echo $sucursal['sucursal']; ?></h3>
				<?php

				$SQL="SELECT
				reservas.fecres AS fecha,
				usuarios.nombre AS asesor,
				grupos.grupo AS grupo,
				modelos.modelo AS modelo,
				clientes.nombre AS cliente,
				clientes.provincia AS provincia,
				reservas.idreserva AS idreserva,
				sucursales.sucursal AS sucursal
				FROM reservas
				Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
				Inner Join modelos ON reservas.idmodelo = modelos.idmodelo
				Inner Join clientes ON clientes.idcliente = reservas.idcliente
				Inner Join grupos ON reservas.idgrupo = grupos.idgrupo
				Inner Join sucursales ON usuarios.idsucursal = sucursales.idsucursal
				WHERE  month(reservas.fecres) = 11 and reservas.anulada=0 AND sucursales.sucursal ='".$sucursal['sucursal']."'
				ORDER BY sucursal ASC, asesor ASC";
				$res=mysqli_query($con, $SQL);?>

				<table rules="all" border="1" style="width:100%;">

					<thead>
					<tr>
						<td width="6%">Fecha</td>
						<td width="10%">Asesor</td>
						<td width="20%">Unidad</td>
						<td width="20%">Cliente</td>
						<td width="7%">Provincia</td>
						<td width="3%">Ver</td>
					</tr>
					</thead>

				<?php
				$cuenta=0;
				while ($reserva=mysqli_fetch_array($res)) {
				$cuenta=$cuenta + 1;?>

				<tr>
					<td style="text-align:center;"><?php echo cambiarformatofecha($reserva['fecha']); ?></td>
					<td style="text-align:center;"><?php echo $reserva['asesor']; ?></td>
					<td><?php echo $reserva['grupo']." ".$reserva['modelo']; ?></td>
					<td><?php echo $reserva['cliente']; ?></td>
					<td><?php echo $reserva['provincia']; ?></td>
					<td>
					<a href="reserva.php?IDrecord=<?php echo $reserva['idreserva']; ?>"><img src="../imagenes/editar.png" width="20px"></a>
					</td>
				</tr>
				<?php }?>
				</table>
				<br>
				<?php
				$SQL="SELECT
						reservas.idreserva AS idreserva,
						reservas.compra,
						reservas.detalleu AS detalle,
						reservas.coloru AS color,
						reservas.aniou AS anio,
						reservas.dominiou AS dominio,
						reservas.internou AS interno,
						usuarios.nombre AS asesor,
						reservas.fecres AS fecha,
						clientes.nombre AS cliente,
						sucursales.sucursal AS sucursal,
						clientes.provincia AS provincia
						FROM
						reservas
						Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
						Inner Join clientes ON reservas.idcliente = clientes.idcliente
						Inner Join sucursales ON usuarios.idsucursal = sucursales.idsucursal
						WHERE
						reservas.anulada =  '0' AND
						reservas.compra =  'Usado'
						AND sucursales.sucursal ='".$sucursal['sucursal']."'
						ORDER BY
						detalle ASC";
						$usados=mysqli_query($con, $SQL);?>

				<table rules="all" border="1" style="width:100%;">
				 	<?php
				$cuenta_u=0;
				while ($usado=mysqli_fetch_array($usados)) {
				$cuenta_u=$cuenta_u + 1;?>

				<tr>
					<td width="6%" style="text-align:center;"><?php echo cambiarformatofecha($usado['fecha']); ?></td>
					<td width="10%" style="text-align:center;"><?php echo $usado['asesor']; ?></td>
					<td width="20%"><?php echo $usado['detalle']." - ".$usado['color']." - ".$usado['dominio']; ?></td>
					<td width="20%"><?php echo $usado['cliente']; ?></td>
					<td width="7%"><?php echo $usado['provincia']; ?></td>
					<td width="3%">
					<a href="reserva.php?IDrecord=<?php echo $usado['idreserva']; ?>"><img src="../imagenes/editar.png" width="20px"></a>
					</td>
				</tr>
				<?php }?>
				</table>


				<div style="text-align:right; color: red;">
				<span>Cantidad de unidades vendidas en "<?php echo $sucursal['sucursal']; ?>" : </span>
				<span style="font-size: 1.3em;"><?php echo $cuenta + $cuenta_u ?> </span>
				</div>
				<?php } ?>
			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
