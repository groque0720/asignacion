<table rules="all" border="1">
					<thead>
						<tr>
							<td width="3%">Nro</td>
							<td width="2%">Unidad</td>
							<td width="8%">Asesor</td>
							<td width="16%">Cliente</td>
							<td width="20%">Modelo</td>
							<td width="6%">Cr&eacute;dito</td>
							<td width="5%">Estado</td>
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

									$SQL="SELECT estado FROM creditos WHERE idcredito =".$reserva['idcredito'];
									$creditos=mysqli_query($con, $SQL);
									$credito=mysqli_fetch_array($creditos);

								?>
								<tr>
									<td style="text-align:center;"><?php echo $reserva['idreserva']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['compra']; ?></td>
									<td style="text-align:center;"><?php echo $reserva['asesor']; ?></td>
									<td><?php echo $reserva['cliente']; ?></td>

									<td>
										<?php if ($reserva['compra']=="Nuevo") {
											if ($grupo['grupo']!="") { echo $grupo['grupo']." ";}
											if ($modelo['modelo']!="") { echo $modelo['modelo'];}
										} else

											echo $reserva['detalleu'];?>
									</td>
									<td style="text-align:center;">
										<?php
											if ($credito['estado']==0) {
											 	echo "-";
											 }

											// if ($credito['estado']!=0) {
											// 	echo "Si";
											// }
										 ?>

										 <a href="credito.php?IDrecord=<?php echo $reserva['idcredito']; ?>" target="_blank">
										<?php if ($credito['estado'] == 20) {?>
											<img src="../imagenes/credito.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 1) {?>
											<img src="../imagenes/recibido.png" width="15px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 2) {?>
											<!-- <img src="../imagenes/recibido.png" width="15px"> -->
											<img src="../imagenes/enviado.png" width="15px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 22) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/credito_analisis.png" width="20px"></a>
										<?php } ?>

										<?php if ($credito['estado'] == 3) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/lupa.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 4) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/rechazado.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 5) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/preok.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 6) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/ok.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 66) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/credito_ap_obs.png" width="20px"></a>
										<?php } ?>
										<?php if ($credito['estado'] == 7 || $credito['estado'] == 70) {?>
											<!-- <img src="../imagenes/recibido.png" width="20px"> -->
											<!-- <img src="../imagenes/enviado.png" width="20px"> -->
											<img src="../imagenes/pago.png" width="20px"></a>
										<?php } ?>



									 </td>

									<td>

										<a href="pago.php?IDrecord=<?php echo $reserva['idcliente']; ?>"><img src="../imagenes/editar.png" width="20px"></a>

										<?php if ($reserva['estadopago'] == 3) {?>
											<div class="ico_cancelada"></div>
										<?php } ?>


									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>