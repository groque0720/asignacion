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
		document.location.href ="cdetalle_ventas_resumen.php";
		});

	});
</script>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>


		<section id="seccion">

			<div class="fila">
				<div style="width: 20%; float: left; margin:10px;">
				<a href="javascript:window.history.back();">&laquo; Volver atrás</a>
				</div>

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="alta_sol" style="width: 60%; float: left;">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Resumen de Boletos Realizados</span>
				</div>


			</div>
			<div id="cuerpo_asesor">

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT * FROM sucursales ORDER BY sucursal";
			$suc=mysqli_query($con, $SQL);
			?>
			<div style="text-align:center; margin: 10px; font-size: 1.3em;">
				Unidades 0KM
			</div>
			<table rules="all" border="1" style="width:100%;">
				<thead>
					<tr>
						<td width="10%"></td>
						<?php while ($sucursal=mysqli_fetch_array($suc)) { ?>
							<td width="8%"><?php echo $sucursal['sucursal']; ?></td>

						<?php $titulo[]=$sucursal['sucursal']; } ?>
						<td width="8%" style="text-align:center;">Totales</td>
					</tr>
				</thead>

			<?php
			$SQL="SELECT * FROM grupos ORDER BY posicion";
			$grupos=mysqli_query($con, $SQL);
			$cont_uno_gral=0;
			$cont_dos_gral=0;
			$cont_tres_gral=0;
			$cont_cuatro_gral=0;
			?>

			<tbody >
			<?php
			while ($grupo=mysqli_fetch_array($grupos)) {
			$cont_uno=0;
			$cont_dos=0;
			$cont_tres=0;
			$cont_cuatro=0;



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
				WHERE  reservas.anulada=0 AND grupos.grupo ='".$grupo['grupo']."'
				ORDER BY sucursal ASC, asesor ASC";
				$res=mysqli_query($con, $SQL);

				while ($reserva=mysqli_fetch_array($res)) {

					if ($reserva['sucursal']==$titulo[0]) {
						$cont_uno=$cont_uno +1;
						$cont_uno_gral =  $cont_uno_gral + 1;
					};
					if ($reserva['sucursal']==$titulo[1]) {
						$cont_dos=$cont_dos +1;
						$cont_dos_gral =  $cont_dos_gral + 1;
					};
					if ($reserva['sucursal']==$titulo[2]) {
						$cont_tres=$cont_tres +1;
						$cont_tres_gral =  $cont_tres_gral + 1;
					};
					if ($reserva['sucursal']==$titulo[3]) {
						$cont_cuatro=$cont_cuatro +1;
						$cont_cuatro_gral =  $cont_cuatro_gral + 1;
					};
					$total_grupo=$cont_uno + $cont_dos + $cont_tres + $cont_cuatro;
				};

				$total_grupo_gral=$cont_uno_gral + $cont_dos_gral + $cont_tres_gral + $cont_cuatro_gral;

				 ?>


					<tr>
						<td width="10%"><?php  echo $grupo['grupo']?> </td>
						<td width="8%" style="text-align:center;"><?php  if ($cont_uno==0) {echo "-";}else{ echo $cont_uno;} ?></td>
						<td width="8%" style="text-align:center;"><?php  if ($cont_dos==0) {echo "-";}else{ echo $cont_dos;} ?></td>
						<td width="8%" style="text-align:center;"><?php  if ($cont_tres==0) {echo "-";}else{ echo $cont_tres;} ?></td>
						<td width="8%" style="text-align:center;"><?php  if ($cont_cuatro==0) {echo "-";}else{ echo $cont_cuatro;} ?></td>
						<td width="8%" style="text-align:center; background: #ccc;"><?php  if ($total_grupo==0) {echo "-";}else{ echo $total_grupo;} ?></td>

					</tr>

			<?php } ?>

					<tr style="background: #ccc;">
						<td width="10%" style="background: #ccc;">Total x Suc</td>
						<td width="8%" style="text-align:center;"><?php echo $cont_uno_gral; ?></td>
						<td width="8%" style="text-align:center;"><?php echo $cont_dos_gral; ?></td>
						<td width="8%" style="text-align:center;"><?php echo $cont_tres_gral; ?></td>
						<td width="8%" style="text-align:center;"><?php echo $cont_cuatro_gral; ?></td>
						<td width="8%" style="text-align:center; color:red; font-size: 1.3em;"><?php echo $total_grupo_gral; ?></td>
					</tr>

			</tbody>
			</table>

				<div style="text-align:right; color: red;">
				<span>Cantidad de unidades 0km vendidas:</span>
				<span style="font-size: 1.3em;"><?php echo $total_grupo_gral; ?> </span>
				</div>
				<hr>
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
				clientes.nombre AS cliente
				FROM
				reservas
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				WHERE
				reservas.anulada =  '0' AND
				reservas.compra =  'Usado'
				ORDER BY
				detalle ASC";
				$usados=mysqli_query($con, $SQL);

			 ?>
			 <div style="text-align:center; margin: 10px; font-size: 1.3em;">
				Unidades Usadas
			</div>

			 <table rules="all" border="1" style="width:100%;">

					<thead>
					<tr>
						<td width="6%">Fecha</td>
						<td width="10%">Asesor</td>
						<td width="6%">Interno</td>
						<td width="30%">Unidad</td>
						<td width="20%">Cliente</td>
					</tr>
					</thead>
					<?php
					$cuenta=0;
					while ($usado=mysqli_fetch_array($usados)) {
					$cuenta=$cuenta + 1;?>

					<tr>
					<td><?php echo cambiarformatofecha($usado['fecha']); ?></td>
					<td><?php echo $usado['asesor']; ?></td>
					<td style="text-align:center;"><?php echo $usado['interno']; ?></td>
					<td><?php echo $usado['detalle']." - Color ".$usado['color']." - Dominio ".$usado['dominio']; ?></td>
					<td><?php echo $usado['cliente']; ?></td>

					</tr>
				<?php }?>
				</table>
				<div style="text-align:right; color: red;">
				<span>Cantidad de unidades usadas vendidas:</span>
				<span style="font-size: 1.3em;"><?php echo $cuenta ?> </span>
				</div>
				<hr>





			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
