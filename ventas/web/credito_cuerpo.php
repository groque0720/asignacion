</script>
<table rules="all" border="1">
					<thead>
						<tr>
							<td width="3%">Nro</td>
							<td width="4%">Unidad</td>
							<td width="8%">Asesor</td>
							<td width="20%">Cliente</td>
							<td width="6%">Fecha</td>
							<td width="18%">Modelo</td>
							<td width="8%">Estado</td>
						</tr>

					</thead>

					<tbody>

							<?php while ($reserva=mysqli_fetch_array($res)) {


								$SQL="SELECT * FROM grupos WHERE idgrupo=".$reserva['idgrupo'];
									$gru=mysqli_query($con, $SQL);
									if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

									$SQL="SELECT * FROM modelos WHERE idmodelo=".$reserva['idmodelo'];
									$mod=mysqli_query($con, $SQL);
									if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}



								?>
								<tr>
									<td style="text-align:center;"><?php echo $reserva['idreserva']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['compra']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['asesor']; ?></td>
									<td><?php echo $reserva['cliente']; ?></td>
									<td style="text-align:center;"><?php if ($reserva['fecres']!="") { echo cambiarformatofecha($reserva['fecres']);} ?> </td>
									<td>
										<?php if ($reserva['compra']=="Nuevo") {
											if ($grupo['grupo']!="") { echo $grupo['grupo']." ";}
											if ($modelo['modelo']!="") { echo $modelo['modelo'];}
										}else{
											echo $reserva['detalleu'];}?>
									</td>
									<td>

										<?php

										$SQL="SELECT estado FROM facturas WHERE idfactura =".$reserva['idfactura'];
										$facturas=mysqli_query($con, $SQL);
										$factura=mysqli_fetch_array($facturas);

										 ?>
										<a href="credito.php?IDrecord=<?php echo $reserva['idcredito']; ?>"><img src="../imagenes/editar.png" width="20px"></a>

										<a href="facturacion.php?IDrecord=<?php echo $reserva['idreserva']; ?>">
											<?php if ($factura['estado']==0) { ?>
											<img src="../imagenes/cajaregistradora_n.png" title="Sin Facturar" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==1) { ?>
											<img src="../imagenes/cajaregistradora_e.png" title="Facturaci&oacute;n Enviada" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==3) { ?>
											<img src="../imagenes/cajaregistradora_ok.png" title="Facturaci&oacute;n OK" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==2) { ?>
											<img src="../imagenes/cajaregistradora_obs.png" title="Facturaci&oacute;n Observada" width="20px"></a>
											<?php } ?>

										<?php if ($reserva['estado'] == 1 OR $reserva['estado'] == 0) {?>
											<img src="../imagenes/recibido.png" width="15px">
										<?php } ?>
										<?php if ($reserva['estado'] == 2) {?>
											<!-- <img src="../imagenes/recibido.png" width="15px"> -->
											<img src="../imagenes/enviado.png" width="15px">
										<?php } ?>
										<?php if ($reserva['estado'] == 22) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/credito_analisis.png" width="20px">
										<?php } ?>

										<?php if ($reserva['estado'] == 3) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/lupa.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 4) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/rechazado.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 5) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/preok.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 6) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/ok.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 66) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/credito_ap_obs.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 70 OR $reserva['estado'] == 7) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/pago.png" width="20px">
										<?php } ?>




										<!-- <img src="../imagenes/lupa.png" width="15px">

										<img src="../imagenes/ok.png" width="15px">
										<img src="../imagenes/pago.png" width="15px"> -->

									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>