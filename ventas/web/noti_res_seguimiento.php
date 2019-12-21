<?php
	include("../funciones/func_mysql.php");
	conectar();
	$SQL="SELECT * FROM notificaciones WHERE idreserva = ".$_GET["id"]." AND idusuario = ".$_GET["idusu"]." ORDER BY idnotificaciones DESC";
	$noti=mysqli_fetch_array(mysql_query($SQL));
	$res=mysqli_query($con, $SQL);
?>

<div style="border:2px solid blue; padding: 10px; margin: 5px;">

	<div style="width:49%; display:inline-block;">
		<strong>Cliente:</strong> <?php echo $noti["cliente"];?>
	</div>

	<div style="width:49%; display:inline-block;">
		<strong>Asesor:</strong> <?php echo $noti["asesor"];?>
	</div>

	<hr>

	<div style="width:20%; display:inline-block;">
		<strong>Nro Reserva:</strong> <?php echo $noti["idreserva"];?>
	</div>
	<div style="width:20%; display:inline-block;">
		<strong>Compra:</strong> <?php echo $noti["compra"];?>
	</div>
	<div style="width:20%; display:inline-block;">
		<strong>Interno:</strong> <?php if ($noti["interno"]!=0) { echo $noti["interno"];}else{echo "-";}?>
	</div>
	<div style="width:35%; display:inline-block;">
		<strong>Modelo:</strong> <?php echo $noti["modelo"];?>
	</div>
</div>


<table rules="all" border="1" id="tabla_res" style="width:100%">

		<thead>
			<tr>
				<td width="10%">Fecha</td>
				<td width="5%">Hora</td>
				<td width="10%">Notificación</td>
				<td width="50%">Observación</td>
				<td width="5%">Panel</td>
			</tr>
		</thead>

		<tbody>
			<?php $nrofila=0; ?>
			<?php while ($noti_res=mysqli_fetch_array($res)) { ?>
			<?php $nrofila = $nrofila + 1; ?>
			<tr class="<?php if ($noti_res["visto"]==0) { echo "negrita";} ?>">

				<td ><?php echo cambiarformatofecha($noti_res['fechanot']);?> </td>
				<td ><?php echo cambiarformatohora($noti_res['hora']);?></td>
				<td ><?php
						$tipo = $noti_res["tiponot"];

						switch($tipo) {
							case 1:
							echo 'Reserva Nueva';break;
							case 2:
							echo 'Modificación';break;
							case 3:
							echo 'Anulación';break;
							case 4:
							echo 'Facturación';break;
							case 5:
							echo 'Créditos';break;
							case 6:
							echo 'Cancelación';break;
						};
				 ?>
				</td>
				<td ><?php echo $noti_res["Obs"];?></td>
				<td>
					<?php

						switch($tipo) {
							case 1: ?>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir" target="_blank"></a>
							<?php ; break;
							case 2:?>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir" target="_blank"></a>
							<?php ;break;
							case 3:?>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir" target="_blank"></a>
							<?php ;break;
							case 4:?>
							<a href="facturacion.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir_factura" target="_blank"></a>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reservas" id="ir" target="_blank"></a>
							<?php ;break;
							case 5:?>
							<a href="credito.php?IDrecord=<?php echo $noti_res['idcredito']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir_credito" target="_blank"></a>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reservas" id="ir" target="_blank"></a>
							<?php ;break;
							case 6:?>
							<a href="pago.php?IDrecord=<?php echo $noti_res['idpago']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir_pago" target="_blank"></a>
							<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reservas" id="ir" target="_blank"></a>
							<?php ;break;
						};

					 ?>
					</td>

			</tr>

			<?php } ?>
		</tbody>

	</table>

<?php mysqli_close($con); ?>