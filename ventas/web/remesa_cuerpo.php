<table rules="all" border="1">
					<thead>
						<tr>
							<td width="3%">Nro</td>
							<td width="3%">Unidad</td>
							<td width="6%">Asesor</td>
							<td width="13%">Cliente</td>
							<td width="6%">Fecha</td>
							<td width="21%">Modelo</td>
							<td width="9%">Tablero Control</td>
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
									<td style="text-align:center;"><?php if ($reserva['fecped']!="") { echo cambiarformatofecha($reserva['fecped']);} ?> </td>
									<td>
										<?php if ($reserva['compra']=="Nuevo") {
											if ($grupo['grupo']!="") { echo $grupo['grupo']." ";}
											if ($modelo['modelo']!="") { echo $modelo['modelo'];}
										} else

											echo $reserva['detalleu'];?>
									</td>
									<td>

										<a target="_blank" href="reserva.php?IDrecord=<?php echo $reserva['idreserva']; ?>" style="style" >

											<?php if ($reserva['enviada']==0) { ?>
											<img src="../imagenes/editar.png" title="Reserva Sin Enviar" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==1) { ?>
												<img src="../imagenes/editar_e.png" title="Reserva Enviada" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==2) { ?>
											<img src="../imagenes/reserva_act.png" title="Reserva Actualizada" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==3) { ?>
											<img src="../imagenes/reserva_obs.png" title="Reserva Observada" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==4) { ?>
											<img src="../imagenes/reserva_vista.png" title="Reserva OK" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==5) { ?>
											<img src="../imagenes/reserva_ok.png" title="Reserva OK" width="20px"></a>
											<?php } ?>

										<a target="_blank" href="facturacion.php?IDrecord=<?php echo $reserva['idreserva']; ?>">

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




										<?php if ($reserva['estado'] == 1) {?>
											<img src="../imagenes/recibido.png" width="15px">
										<?php } ?>
										<?php if ($reserva['estado'] == 2) {?>
											<img src="../imagenes/recibido.png" width="15px">
											<img src="../imagenes/lupa.png" width="20px">
										<?php } ?>
										<?php if ($reserva['estado'] == 3) {?>
											<img src="../imagenes/recibido.png" width="15px">
											<img src="../imagenes/ok.png" width="15px">

										<?php } ?>









										<!-- <img src="../imagenes/lupa.png" width="15px">

										<img src="../imagenes/ok.png" width="15px">
										<img src="../imagenes/pago.png" width="15px"> -->

									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>