<table rules="all" border="1">
					<thead>
						<tr>
							<td width="3%">Nro</td>
							<td width="3%">Unidad</td>
							<td width="6%">Asesor</td>
							<td width="13%">Cliente</td>
							<td width="6%">Fecha</td>
							<td width="18%">Detalle</td>
							<td width="12%">Tablero Control</td>
						</tr>

					</thead>

					<tbody>

							<?php while ($reserva=mysqli_fetch_array($res)) {

									$SQL="SELECT * FROM usuarios WHERE idusuario=".$reserva['idusuario'];
									$usu=mysqli_query($con, $SQL);
									$usuario=mysqli_fetch_array($usu);

									$SQL="SELECT * FROM clientes WHERE idcliente=".$reserva['idcliente'];
									$cli=mysqli_query($con, $SQL);
									$cliente=mysqli_fetch_array($cli);

									$SQL="SELECT * FROM grupos WHERE idgrupo=".$reserva['idgrupo'];
									$gru=mysqli_query($con, $SQL);
									if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

									$SQL="SELECT * FROM modelos WHERE idmodelo=".$reserva['idmodelo'];
									$mod=mysqli_query($con, $SQL);
									if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

									$SQL="SELECT * FROM facturas WHERE idfactura=".$reserva['idfactura'];
									$fact=mysqli_query($con, $SQL);
									if (empty($fact)) {$factura['nombre']="";}else{ $factura=mysqli_fetch_array($fact);}

									$SQL="SELECT * FROM creditos WHERE idcredito=".$reserva['idcredito'];
									$creditos=mysqli_query($con, $SQL);
									if (empty($creditos)) {$credito['estado']="";}else{ $credito=mysqli_fetch_array($creditos);}

								?>
								<tr>
									<td style="text-align:center;"><?php echo $reserva['idreserva']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['compra']; ?></td>
									<td style="text-align:center;"><?php echo $usuario['nombre']; ?></td>
									<td><?php echo $cliente['nombre']; ?></td>
									<td style="text-align:center;"><?php if ($reserva['fecres']!="") { echo cambiarformatofecha($reserva['fecres']);} ?> </td>
									<td>
										<?php if ($reserva['compra']=="Nuevo") {
											if ($grupo['grupo']!="") { echo $grupo['grupo']." ";}
											if ($modelo['modelo']!="") { echo $modelo['modelo'];}
										} else

											echo $reserva['detalleu'];?>
									</td>
									<td>
										<!-- preparo grafico para reserva -->
										<a href="administracion_reserva.php?IDrecord=<?php echo $reserva['idreserva']; ?>">
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
											<img src="../imagenes/reserva_vista.png" title="Reserva vista" width="20px"></a>
											<?php } ?>
											<?php if ($reserva['enviada']==5) { ?>
											<img src="../imagenes/reserva_ok.png" title="Reserva Aprobada" width="20px"></a>
											<?php } ?>

											<a href="facturacion.php?IDrecord=<?php echo $reserva['idreserva']; ?>">
											<?php if ($factura['estado']==0) { ?>
											<img src="../imagenes/cajaregistradora_n.png" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==1) { ?>
											<img src="../imagenes/cajaregistradora_e.png" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==2) { ?>
											<img src="../imagenes/cajaregistradora_obs.png" width="20px"></a>
											<?php } ?>
											<?php if ($factura['estado']==3) { ?>
											<img src="../imagenes/cajaregistradora_ok.png" width="20px"></a>
											<?php } ?>

											<!-- preparo grafico para Creditos -->
											<?php if ($credito['estado'] == 0) {?>
												<img src="../imagenes/creditob.png" width="20px">
												<?php } ?>
											<a href="credito.php?IDrecord=<?php echo $credito['idcredito']; ?>">
												<?php if ($credito['estado'] == 20) {?>
												<img src="../imagenes/credito.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 1) {?>
												<img src="../imagenes/credito_r.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 2) {?>
													<img src="../imagenes/credito_e.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 22) {?>
													<img src="../imagenes/analisis.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 3) {?>
													<img src="../imagenes/credito_o.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 4) {?>
													<img src="../imagenes/credito_no.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 5) {?>
													<img src="../imagenes/credito_pre.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 6) {?>
													<img src="../imagenes/credito_aprobado.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 66) {?>
													<img src="../imagenes/aprobado_obs.png" width="20px"></a>
												<?php } ?>
												<?php if ($credito['estado'] == 7) {?>
													<img src="../imagenes/credito_liq.png" width="20spx"></a>
												<?php } ?>

												<a href="pago.php?IDrecord=<?php echo $reserva['idreserva']; ?>">
												<?php if (is_null($reserva['estadopago'])) {?>
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

												<?php
												$a=null;
												 if ($reserva['llego'] == null OR $reserva['llego'] == '' OR $reserva['llego'] == 0) {?>
												<img src="../imagenes/auto_b.png" width="20px">
												<?php }else{ ?>
													<img src="../imagenes/auto_ll.png" width="20px">

												<?php } ?>





										<!-- <img src="../imagenes/pesos_ok.png" width="20px">
										<img src="../imagenes/creditob.png" width="20px">
										<img src="../imagenes/corolla_r.png" width="20px">
										<img src="../imagenes/patente_n.png" width="25px" height="20"> -->


									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>

