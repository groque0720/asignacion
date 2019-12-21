<div class="zona-tabla ancho-90 s-100" style="overflow-y: scroll; padding-bottom: 0;">
	<div class="margen-auto ancho-100 s-100">
	 	<h1>Evaluaciones del Personal</h1>
	 	<br>
	 </div>
		<table class="ancho-95">
			<colgroup>
				<col width="10%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead >
				<tr>
					<td rowspan="2">Nombre</td>
					<?php
						$SQL="SELECT titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td colspan="2"><?php echo $item['titulo']; ?></td>
						<?php } ?>
					<td colspan="2">Resultado</td>
				</tr>
				<tr>
					<?php
						$SQL="SELECT id_item, titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td>Auto</td>
							<td>Superior</td>

							<?php
								$array[$item['id_item']]['auto']=0;
								$array[$item['id_item']]['sup']=0;
							 ?>

						<?php } ?>
							<td>Auto</td>
							<td>Superior</td>
				</tr>
			</thead>
		</table>
</div>
<div class="zona-tabla ancho-90 s-100" style="height: 400px; overflow-y: auto; overflow-x: hidden;">
		<table class="ancho-95" style="margin-top: -84px;">
			<colgroup>
				<col width="10%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead style="height: 0px;">
				<tr>
					<td rowspan="2">Nombre</td>
					<?php
						$SQL="SELECT titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td colspan="2"><?php echo $item['titulo']; ?></td>
						<?php } ?>
					<td colspan="2">Resultado</td>
				</tr>
				<tr>
					<?php
						$SQL="SELECT id_item, titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td>Auto</td>
							<td>Superior</td>

							<?php
								$array[$item['id_item']]['auto']=0;
								$array[$item['id_item']]['sup']=0;
							 ?>

						<?php } ?>
							<td>Auto</td>
							<td>Superior</td>
				</tr>
			</thead>
			<tbody>

				<?php
					$SQL="SELECT id_usuario, nombre, id_evaluacion_usuario FROM view_evaluaciones_usuarios WHERE id_evaluacion = ".$id_evaluacion.' ORDER BY nombre';//." LIMIT 1";
					$empleados=mysqli_query($con, $SQL);
					$cant_emp=0;

					while ($emple=mysqli_fetch_array($empleados)) { $cant_emp++;?>

						<tr>
							<td><?php echo $emple['nombre']; ?></td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item";
								$items=mysqli_query($con, $SQL);
								$cant_item=0;
								$auto=0;
								$sup=0;
								// $array_valores=[];
								while ($item=mysqli_fetch_array($items)) { $cant_item++;

									$SQL="SELECT calificacion_autoevaluacion as auto, calificacion_superior as sup FROM evaluacion_con_valor_item WHERE id_usuario =".$emple['id_usuario']." AND id_evaluacion =". $id_evaluacion." AND id_item = ".$item['id_item'];
									$resultados=mysqli_query($con, $SQL);
									while ($resul=mysqli_fetch_array($resultados)) { ?>
										<td class="centrar-texto"><?php echo $resul['auto']; $auto=$auto+$resul['auto'];?></td>
										<td class="centrar-texto" style="background: #F1F1F1; color: black;"><?php echo $resul['sup']; $sup=$sup+$resul['sup']; ?></td>

										<?php
											$array[$cant_item]['auto']=$array[$cant_item]['auto'] + $resul['auto'];
											$array[$cant_item]['sup']=$array[$cant_item]['sup'] + $resul['sup'];
										 ?>



									<?php } ?>

							<?php } ?>
									<td class="centrar-texto" style="font-weight: bold; color: red;">
										<a style="color:red;" target="_blank" href="<?php  echo 'evaluacion.php?id='.$emple['id_evaluacion_usuario'].'&e=auto'; ?>"><?php echo number_format($auto/$cant_item,1) ?></a>

										</td>
									<td class="centrar-texto" style="font-weight: bold; color: red; background: #EFEDED;">
										<a  style="color:red;" target="_blank" href="<?php  echo 'evaluacion.php?id='.$emple['id_evaluacion_usuario'].'&e=sup'; ?>"><?php echo number_format($sup/$cant_item,1); ?></a>

									</td>

						</tr>
				<?php } ?>
					<tr class="">
						<td style="border: none;padding: 3px;"></td>
					</tr>

						<tr class="" style="font-weight: bold;">
							<td>Total por Item</td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item";
								$items=mysqli_query($con, $SQL);
								$cant_item=0;
								$auto=0;
								$sup=0;
								while ($item=mysqli_fetch_array($items)) { $cant_item++;?>
										<td class="centrar-texto"><?php echo number_format($array[$cant_item]['auto']/$cant_emp,2);
										 $auto=$auto+$array[$cant_item]['auto']/$cant_emp;?></td>
										<td class="centrar-texto" style="background: #F1F1F1; color: black;"><?php echo number_format($array[$cant_item]['sup']/$cant_emp,2); $sup=$sup+$array[$cant_item]['sup']/$cant_emp;?></td>
								<?php } ?>
									<td style="color:red;" class="centrar-texto" style="font-weight: bold; color: black;"><?php echo number_format($auto/$cant_item,1) ?></td>
									<td style="color:red;" class="centrar-texto" style="font-weight: bold; color: black; background-color: #EFEDED;"><?php echo number_format($sup/$cant_item,1); ?></td>

						</tr>

			</tbody>
		</table>


</div>