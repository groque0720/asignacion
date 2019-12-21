<?php

$SQL="SELECT * FROM lineas_detalle WHERE movimiento=1 AND idreserva=".$_GET["IDrecord"];
$der=mysqli_query($con, $SQL);


$SQL="SELECT * FROM lineas_detalle WHERE movimiento=2 AND idreserva=".$_GET["IDrecord"];
$izq=mysqli_query($con, $SQL);

  ?>
<script src="../js/editareliminar.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


<div id="derecha">
				<table rules="all" border="1">
					<thead>
					<tr>

						<td  width="58%">Detalle</td>
						<td  width="19%">Monto</td>
						<td  width="13%">Obs.</td>
					</tr>
					</thead>

						<?php
						$acumulado = 0;
						$lineasd=1;
						while ( $fila=mysqli_fetch_array($der)) { ?>
							<tr>

								<td><?php echo $fila['detalle']." ".$fila['adjunto']; ?></td>
								<td style="text-align: right;">
									<?php

										if ($fila['monto']==0) {
											echo " ";
										}else {
											echo number_format($fila['monto'], 2, ',','.');
										}

									  ?>
								</td>
								<td style="text-align:center;">
								<?php if ($fila['codigo']!='4' and $fila['codigo']!='5' ) { ?>
							<a style="margin-right: 6px;" class="editar_f" href="#" data-id="<?php echo $fila["idlinea"];?>"><img src="../imagenes/editar.png"  width="15px"></a>
						<?php } ?>

						<a class="eliminar_f" href="#" data-id="<?php echo $fila["idlinea"];?>"><img src="../imagenes/eliminar.png"  width="15px"></a>

													</td>
							</tr>
							<?php $acumulado = $acumulado + $fila['monto'];
									$lineasd= $lineasd +1 ;
							 } ?>

							 <!-- Rellena las filas del detalle con la cantidad que faltan para llegar  a 14.- -->
							 <?php
							 for ($i=$lineasd; $i <= 13 ; $i++) { ?>
							 	<tr>

								<td></td>
								<td></td>
								<td>.</td>
							</tr>
							 <?php }  ?>

							 <!-- Inserta al final de la tabla la fila del total.- -->
							<tr>

								<td style="text-align: right; font-weight: bold;">Total a Pagar</td>
								<td style="text-align: right; font-weight: bold;"><?php echo number_format($acumulado, 2, ',','.');  ?></td>
								<td>.</td>
							</tr>

			</table>

			</div>
			<div id="izquierda">
				<table rules="all" border="1">
					<thead>
					<tr>

						<td  width="58%">Detalle</td>
						<td  width="19%">Monto</td>
						<td  width="13%">Obs.</td>
					</tr>
					</thead>

						<?php
						$acumulado = 0;
						$lineasi=1;
						while ( $fila=mysqli_fetch_array($izq)) { ?>
							<tr>

								<td><?php echo $fila['detalle']." ".$fila['adjunto']; ?></td>
								<td style="text-align: right;">
									<?php

										if ($fila['monto']==0) {
											echo " ";
										}else {
											echo number_format($fila['monto'], 2, ',','.');
										}

									  ?></td>
								<td style="text-align:center;" >
									<?php if ($fila['codigo']!='4' and $fila['codigo']!='5' ) { ?>
											<a style="margin-right: 6px;" class="editar_f" href="#" data-id="<?php echo $fila["idlinea"];?>"><img src="../imagenes/editar.png"  width="15px"></a>
										<?php } ?>
									<a class="eliminar_f" href="#" data-id="<?php echo $fila["idlinea"];?>"><img src="../imagenes/eliminar.png"  width="15px"></a>
								</td>
							</tr>
							<?php $acumulado = $acumulado + $fila['monto'];
							$lineasi= $lineasi +1 ;
							 } ?>

							<!-- Rellena las filas del detalle con la cantidad que faltan para llegar  a 14.- -->
							 <?php
							 for ($i=$lineasi; $i <= 13 ; $i++) { ?>
							 	<tr>

								<td></td>
								<td></td>
								<td>.</td>
							</tr>
							 <?php }  ?>

							 <!-- Inserta al final de la tabla la fila del total.- -->
							<tr>

								<td style="text-align: right; font-weight: bold;">Total a Pagar</td>
								<td style="text-align: right; font-weight: bold;"><?php echo number_format($acumulado, 2, ',','.');  ?></td>
								<td>.</td>
							</tr>

			</table>
			</div>