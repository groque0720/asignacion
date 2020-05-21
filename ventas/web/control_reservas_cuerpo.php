</script>
<table rules="all" border="1">
					<thead>
						<tr>
							<td width="3%">Nro</td>
							<td width="3%">Unidad</td>
							<td width="6%">Asesor</td>
							<td width="19%">Cliente</td>
							<td width="6%">Fecha</td>
							<td width="19%">Modelo</td>
							<td width="10%"> Control</td>
						</tr>

					</thead>

					<tbody>
							<?php while ($reserva=mysqli_fetch_array($res)) {

								$SQL="SELECT * FROM facturas WHERE idfactura=".$reserva['idfactura'];
								$fact=mysqli_query($con, $SQL);
								if (empty($fact)) {$factura['nombre']="";}else{ $factura=mysqli_fetch_array($fact);}


								$SQL="SELECT * FROM grupos WHERE idgrupo=".$reserva['idgrupo'];
									$gru=mysqli_query($con, $SQL);
									if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

									$SQL="SELECT * FROM modelos WHERE idmodelo=".$reserva['idmodelo'];
									$mod=mysqli_query($con, $SQL);
									if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}
								?>
								<tr <?php if ($reserva['anulada']==1) { echo 'class=anulada';} ?>>
									<td style="text-align:center;"><?php echo $reserva['idreserva']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['compra']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['asesor']; ?></td>
									<td><?php echo $reserva['cliente']; ?></td>
									<td style="text-align:center;"><?php if ($reserva['fecres']!="") { echo cambiarformatofecha($reserva['fecres']);} ?> </td>
									<td>
										<?php if ($reserva['compra']=="Nuevo") {
											if ($grupo['grupo']!="") { echo $grupo['grupo']." ";}
											if ($modelo['modelo']!="") { echo $modelo['modelo'];}
										}else{echo $reserva['detalleu'];}?>
									</td>
									<td>

										<a href="reserva.php?IDrecord=<?php echo $reserva['idreserva']; ?>"><img src="../imagenes/editar.png" width="20px"></a>



											<?php if ($reserva['enviada']==1) { ?>
												<img src="../imagenes/enviada.png" title="Reserva Enviada" width="20px">
											<?php } ?>
											<?php if ($reserva['enviada']==0) { ?>
											<img src="../imagenes/editar.png" title="Reserva Sin Enviar" width="20px">
											<?php } ?>
											<?php if ($reserva['enviada']==2) { ?>
											<img src="../imagenes/actualizada.png" title="Reserva Actualizada" width="20px">
											<?php } ?>
											<?php if ($reserva['enviada']==3) { ?>
											<img src="../imagenes/observada.png" title="Reserva observada" width="20px">
											<?php } ?>
											<?php if ($reserva['enviada']==4) { ?>
											<img src="../imagenes/visto.png" title="Reserva Vista" width="20px">
											<?php } ?>
											<?php if ($reserva['enviada']==5) { ?>
											<img src="../imagenes/ok.png" title="Reserva Aprobada" width="20px">
											<?php } ?>

											<a href="pago.php?IDrecord=<?php echo $reserva['idcliente']; ?>">
											<?php if (is_null($reserva['estadopago']) OR $reserva['estadopago']==0 ) {?>
											<img src="../imagenes/pesosb.png" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['estadopago']==1) {?>
											<img src="../imagenes/pagos_i.png" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['estadopago']==2) {?>
											<img src="../imagenes/pagos_m.png" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['estadopago']==3) {?>
											<img src="../imagenes/pagos_ok.png" width="20px"></a>
											<?php } ?>

											&nbsp;&nbsp;&nbsp;
											<?php
												if (($usuario_id==56 or $usuario_id==11) and $reserva['anulada']==0) { ?>
											<!-- facturar -->
												<a class="facturar" href="#" data-id="<?php echo $reserva['idreserva']; ?>">
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
												<!-- anular -->
												<a href="#" class="anular_reserva" name="anular_r" data-id="<?php echo $reserva['idreserva'];?>"><img src="../imagenes/eliminar.png" width="20px"></a>
											<?php }
											 ?>





										<!-- <img src="../imagenes/lupa.png" width="15px">

										<img src="../imagenes/ok.png" width="15px">
										<img src="../imagenes/pago.png" width="15px"> -->

									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>