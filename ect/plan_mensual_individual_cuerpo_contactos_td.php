
<div class="flexible justificar fila_indices">

	<div class="ancho-30 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
		<?php 
			$SQL="SELECT * FROM meses WHERE idmes = ".$mes_ant;
			$meses = mysqli_query($con, $SQL);
			$nombre_mes=mysqli_fetch_array($meses);
		 ?>
			Presupuestos <?php echo $nombre_mes['mes'].' '.$ano_ant; ?>
		</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="7%"> 
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead>
				<tr>
					<td>Modelos</td>
					<td>Objetivos</td>
					<td>Presup.</td>
					<td>% Cumpl.</td>
				</tr>
			</thead>
			<tbody>

				<?php 
					$SQL="SELECT * FROM ect_modelos WHERE activo= 1 ORDER BY posicion";
					$modelos=mysqli_query($con, $SQL);

					$total_obj=0;
					$total_presup=0;

					while ($modelo=mysqli_fetch_array($modelos)) {

						$SQL="SELECT * FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 1 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);

						$total_obj = $total_obj + $ventas['objetivo'];
						$total_presup = $total_presup + $ventas['cumple'];

					 ?>


							<tr>
								<td class="celda-espacio-left"><?php echo $modelo['modelo']; ?></td>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

				<?php } ?>
				<tr style="border: none;"><td style="border: none;"></td></tr>

					<tr class="negrita fondo-gris-1" >
						<td class="celda-espacio-left">TOTAL</td>
						<td class="centrar-texto"><?php echo $total_obj; ?></td>
						<td class="centrar-texto"><?php echo $total_presup ?></td>
						<td class="derecha-texto"><?php   if ($total_obj!=0) { echo number_format($total_presup*100/$total_obj,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>

			</tbody>

		</table>
	</div>

	<div class="ancho-25 ">
		
		<div class="ancho-90">
				<div class="centrar-texto centrar-caja negrita">
					Test Drive <?php echo $nombre_mes['mes'].' '.$ano_ant; ?>
				</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="33.33%">
				<col width="33.33%">
				<col width="33.33%">
			</colgroup>
			<thead>
				<tr>
					<td>Objetivos</td>
					<td>TD</td>
					<td>% Cumpl.</td>
				</tr>
			</thead>
			<tbody>

				<?php 
					//$SQL="SELECT * FROM ect_modelos WHERE activo= 1 AND es_usado = 0 ORDER BY posicion";
					$SQL="SELECT * FROM ect_modelos WHERE activo= 1 ORDER BY posicion";
					$modelos=mysqli_query($con, $SQL);

					$total_obj=0;
					$total_td=0;

					while ($modelo=mysqli_fetch_array($modelos)) {

						$SQL="SELECT * FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 3 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);

						$total_obj = $total_obj + $ventas['objetivo'];
						$total_td = $total_td + $ventas['cumple'];

					 ?>


							<tr>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

				<?php } ?>
				<tr style="border: none;"><td style="border: none;"></td></tr>

					<tr class="negrita fondo-gris-1" >
						<td class="centrar-texto"><?php echo $total_obj; ?></td>
						<td class="centrar-texto"><?php echo $total_td ?></td>
						<td class="derecha-texto"><?php   if ($total_obj!=0) { echo number_format($total_td*100/$total_obj,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>


			</tbody>

		</table>
		</div>
	</div>

	<div class="ancho-25 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
			CSI <?php echo $nombre_mes['mes'].' '.$ano_ant; ?>
		</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="30%">
				<col width="40%">
				<col width="30%">
			</colgroup>
			<thead>
				<tr>
					<td>CSI</td>
					<td>Detalle</td>
					<td class="centrar-texto">Puntaje</td>
				</tr>
			</thead>
			<tbody>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					
					<tr>
						<td class="centrar-texto" rowspan="3" >DYV</td>
						<td class="celda-espacio-left">Asesor</td>
						<?php 
							$SQL="SELECT * FROM ect_csi_asesores WHERE id_asesor_ect = ".$id_asesor." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
							$csi_asesores = mysqli_query($con, $SQL);
							$csi_asesor = mysqli_fetch_array($csi_asesores);
						 ?>
						<td class="centrar-texto"><?php echo number_format($csi_asesor['csi'],2); ?></td>
					</tr>

					<tr>
						
						<?php 

							$SQL="SELECT * FROM ect_view_asesores_sucursales WHERE id_asesor_ect = ".$id_asesor;
							$sucursales = mysqli_query($con, $SQL);
							$sucursal = mysqli_fetch_array($sucursales);
							$id_sucursal_ect = $sucursal['id_sucursal_ect'];


							$SQL="SELECT * FROM ect_csi_sucursales WHERE id_sucursal_ect = ".$sucursal['id_sucursal_ect']." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
							$csi_sucursales = mysqli_query($con, $SQL);
							$csi_sucursal = mysqli_fetch_array($csi_sucursales);
						 ?>
						<td class="celda-espacio-left"><?php echo $sucursal['sucursal']; ?></td>

						<td class="centrar-texto"><?php echo number_format($csi_sucursal['csi_dyv'],2); ?></td>
					</tr>
					
					<tr>
						<td class="celda-espacio-left">DYV</td>
						<?php 
							$SQL="SELECT * FROM ect_csi_sucursales WHERE id_sucursal_ect = 5 AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
							$csi_dyvs = mysqli_query($con, $SQL);
							$csi_dyv = mysqli_fetch_array($csi_dyvs);
						 ?>
						<td class="centrar-texto"><?php echo $csi_dyv['csi_dyv']; ?> </td>
					</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
		
					<tr>
						<td class="centrar-texto" rowspan="3">TASA</td>
						<td class="celda-espacio-left"><?php echo $sucursal['sucursal']; ?></td>
						<td class="centrar-texto"><?php echo number_format($csi_sucursal['csi_tasa'],2); ?></td>
					</tr>

					<tr>
						<td class="celda-espacio-left">DYV</td>
						<td class="centrar-texto"><?php echo number_format($csi_dyv['csi_tasa'],2); ?> </td>
					</tr>
					
					<tr>
						<td class="celda-espacio-left">Red Toyota</td>
						<?php 
							$SQL="SELECT * FROM ect_csi_sucursales WHERE id_sucursal_ect = 6 AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
							$csi_tasas = mysqli_query($con, $SQL);
							$csi_tasa = mysqli_fetch_array($csi_tasas);
						 ?>
						<td class="centrar-texto"><?php echo number_format($csi_tasa['csi_tasa'],2); ?></td>
					</tr>

			</tbody>

		</table>
		<div class="centrar-texto negrita margen-arriba-5" style="color: red; font-size: 10px;">Puntaje Ideal >= 96% </div>
	</div>

	<div class="ancho-20 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
			Tasa de Cierre <?php echo $nombre_mes['mes'].' '.$ano_ant; ?>
		</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="5%">
				<col width="5%">

			</colgroup>
			<thead>
				<tr>
					<td>Detalle</td>
					<td class="centrar-texto">Puntaje</td>
				</tr>
			</thead>
			<tbody>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					
					<tr>
						<td class="celda-espacio-left">Asesor</td>
						<td class="centrar-texto">
							<?php if ($total_presup!=0) { echo number_format(($total_vtas/$total_presup)*100,2);	}else{ echo "0.00";	} ?>
								
							</td>
					</tr>
	
					<tr>
						<?php 
							$SQL="SELECT ect_view_asesores_sucursales.id_sucursal_ect, ect_objetivos_cumplimiento.id_mes, ect_objetivos_cumplimiento.ano, Sum(ect_objetivos_cumplimiento.objetivo) AS objetivo, Sum(ect_objetivos_cumplimiento.cumple) AS cumple, ect_view_asesores_sucursales.sucursal FROM ect_objetivos_cumplimiento INNER JOIN ect_view_asesores_sucursales ON ect_view_asesores_sucursales.id_asesor_ect = ect_objetivos_cumplimiento.id_asesor WHERE ano= ".$ano_ant." AND id_mes = ".$mes_ant." AND id_tipo_objetivo = 2 AND id_sucursal_ect = ".$sucursal['id_sucursal_ect'];

							$tc_sucursales=mysqli_query($con, $SQL);
							$tc_sucursal_vtas = mysqli_fetch_array($tc_sucursales);

							$SQL="SELECT ect_view_asesores_sucursales.id_sucursal_ect, ect_objetivos_cumplimiento.id_mes, ect_objetivos_cumplimiento.ano, Sum(ect_objetivos_cumplimiento.objetivo) AS objetivo, Sum(ect_objetivos_cumplimiento.cumple) AS cumple, ect_view_asesores_sucursales.sucursal FROM ect_objetivos_cumplimiento INNER JOIN ect_view_asesores_sucursales ON ect_view_asesores_sucursales.id_asesor_ect = ect_objetivos_cumplimiento.id_asesor WHERE ano= ".$ano_ant." AND id_mes = ".$mes_ant." AND id_tipo_objetivo = 1 AND id_sucursal_ect = ".$sucursal['id_sucursal_ect'];

							$tc_sucursales=mysqli_query($con, $SQL);
							$tc_sucursal_presu = mysqli_fetch_array($tc_sucursales);
						 ?>
						<td class="celda-espacio-left"><?php echo $sucursal['sucursal']; ?></td> 	<?php  // $tc_sucursal_presu['cumple'].' '.$tc_sucursal_vtas['cumple']; ?>
						<td class="centrar-texto"> <?php if ($tc_sucursal_vtas['cumple']!=0) { echo number_format(($tc_sucursal_vtas['cumple']/$tc_sucursal_presu['cumple'])*100,2);	}else{ echo "0.00";	}  ?></td>
					</tr>
					
					<tr>
						<td class="celda-espacio-left">DYV</td>
						<?php 
							$SQL="SELECT ect_view_asesores_sucursales.id_sucursal_ect, ect_objetivos_cumplimiento.id_mes, ect_objetivos_cumplimiento.ano, Sum(ect_objetivos_cumplimiento.objetivo) AS objetivo, Sum(ect_objetivos_cumplimiento.cumple) AS cumple, ect_view_asesores_sucursales.sucursal FROM ect_objetivos_cumplimiento INNER JOIN ect_view_asesores_sucursales ON ect_view_asesores_sucursales.id_asesor_ect = ect_objetivos_cumplimiento.id_asesor WHERE ano= ".$ano_ant." AND id_mes = ".$mes_ant." AND id_tipo_objetivo = 2";

							$tc_sucursales=mysqli_query($con, $SQL);
							$tc_sucursal_vtas = mysqli_fetch_array($tc_sucursales);

							$SQL="SELECT ect_view_asesores_sucursales.id_sucursal_ect, ect_objetivos_cumplimiento.id_mes, ect_objetivos_cumplimiento.ano, Sum(ect_objetivos_cumplimiento.objetivo) AS objetivo, Sum(ect_objetivos_cumplimiento.cumple) AS cumple, ect_view_asesores_sucursales.sucursal FROM ect_objetivos_cumplimiento INNER JOIN ect_view_asesores_sucursales ON ect_view_asesores_sucursales.id_asesor_ect = ect_objetivos_cumplimiento.id_asesor WHERE ano= ".$ano_ant." AND id_mes = ".$mes_ant." AND id_tipo_objetivo = 1"; 

							$tc_sucursales=mysqli_query($con, $SQL);
							$tc_sucursal_presu = mysqli_fetch_array($tc_sucursales);
						 ?>

						<td class="centrar-texto"> <?php if ($tc_sucursal_vtas['cumple']!=0) { echo number_format(($tc_sucursal_vtas['cumple']/$tc_sucursal_presu['cumple'])*100,2);	}else{ echo "0.00";	}  ?></td>
					</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
		</tbody>

		</table>
		<div class="centrar-texto negrita margen-arriba-5" style="color: red; font-size: 10px;">(Tasa de Cierre > 19% y < 25%) </div>
	</div>

</div>