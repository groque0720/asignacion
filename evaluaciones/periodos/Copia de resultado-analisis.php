<div class="margen-auto ancho-95 s-100" style="display: flex; justify-content: space-between; margin-bottom: 10px !important;">
	<div class="">
		<h1>Evaluaciones del Personal</h1>
	</div>
	<div class="ancho-50" style="display: flex; justify-content: space-between;">
		<div class="ancho-30">
			<label for="estado">Estado</label>
			<select name="estado" id="estado">
				<option value="1">Todas</option>
				<option value="2">Termiandas</option>
				<option value="3">No Terminadas</option>
			</select>
		</div>
		<div class="ancho-30">
			<label for="miembro">Evaluación</label>
			<select name="miembro" id="miembro">
				<option value="1">Todas</option>
				<option value="2">Auto</option>
				<option value="3">Superior</option>
			</select>
		</div>
<!-- 		<div class="ancho-30">
			<label for="comentario">Comentarios</label>
			<select name="comentario" id="comentario">
				<option value="1">Si/No</option>
				<option value="2">No</option>
				<option value="3">Si</option>
			</select>
		</div> -->
		<div class="ancho-10">
			<a href="" id="filtrar"><button style="padding: 3px;">Filtrar</button></a>
		</div>
	</div>
 </div>

 <div id="proceso" style="width: 100%; height: 100vh; background: rgba(0,0,0,.1); display: flex; position: fixed; top: 0; left: 0;">
 	<img src="load.gif" alt="" style="width: 60px; height: 60px; margin: auto;">
 </div>

<div id="resultado">

<div class="zona-tabla ancho-95 s-100" style="overflow-y: scroll; padding-bottom: 0;">
	<table class="ancho-95">
		<colgroup>
			<col width="9.6%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">

		</colgroup>
		<thead >
			<tr>
				<td rowspan="2">Nombre</td>
				<?php
					$SQL="SELECT titulo FROM evaluacion_item WHERE activo = 1";
					$items=mysqli_query($con, $SQL);
					while ($item=mysqli_fetch_array($items)) { ?>
						<td colspan="2"><?php echo $item['titulo']; ?></td>
					<?php } ?>
				<td colspan="2">Resultado</td>
			</tr>
			<tr>
				<?php
					$SQL="SELECT id_item, titulo FROM evaluacion_item WHERE activo = 1";
					$items=mysqli_query($con, $SQL);
					while ($item=mysqli_fetch_array($items)) { ?>
						<td>Auto</td>
						<td>Sup.</td>

						<?php
							$array[$item['id_item']]['auto']=0;
							$array[$item['id_item']]['sup']=0;
						 ?>

					<?php } ?>
						<td>Auto</td>
						<td>Sup.</td>
			</tr>
		</thead>
	</table>
</div>


	<div class="zona-tabla ancho-95 s-100" style="height: 600px; overflow-y: auto; overflow-x: hidden;">
		<table class="ancho-95" style="margin-top: -60px;">
			<colgroup>
				<col width="9.6%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
			</colgroup>
			<thead>
				<tr>
					<td rowspan="2">Nombre</td>
					<?php
						$SQL="SELECT titulo FROM evaluacion_item WHERE activo = 1";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td colspan="2"><?php echo $item['titulo']; ?></td>
						<?php } ?>
					<td colspan="2">Resultado</td>
				</tr>
				<tr>
					<?php
						$SQL="SELECT id_item, titulo FROM evaluacion_item WHERE activo = 1";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td>Auto</td>
							<td>Sup.</td>

							<?php
								$array[$item['id_item']]['auto']=0;
								$array[$item['id_item']]['sup']=0;
							 ?>

						<?php } ?>
							<td>Auto</td>
							<td>Sup.</td>
				</tr>
			</thead>
			<tbody>

				<?php
					$SQL="SELECT id_usuario, nombre, id_evaluacion_usuario, superior, sector, sucursal FROM view_evaluaciones_usuarios WHERE id_evaluacion = ".$id_evaluacion." ORDER BY nombre ";//" LIMIT 50";
					$empleados=mysqli_query($con, $SQL);
					$cant_emp=0;

					while ($emple=mysqli_fetch_array($empleados)) { $cant_emp++;?>
						<tr>
							<td><span  style="cursor: pointer;" title="<?php echo  $emple['sector'].' - Sup. '.$emple['superior'].' - '.$emple['sucursal'] ?> "><?php echo $emple['nombre']; ?></span></td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item";
								$items=mysqli_query($con, $SQL);
								$cant_item=0;
								$auto=0;
								$sup=0;
								// $array_valores=[];
								while ($item=mysqli_fetch_array($items)) { $cant_item++;

									$SQL="SELECT calificacion_autoevaluacion as auto, calificacion_superior as sup, observacion_auto, observacion_superior FROM evaluacion_con_valor_item WHERE id_usuario =".$emple['id_usuario']." AND id_evaluacion =". $id_evaluacion." AND id_item = ".$item['id_item'];
									$resultados=mysqli_query($con, $SQL);
									while ($resul=mysqli_fetch_array($resultados)) { ?>
										<td class="centrar-texto" style="">
											<div style="position: relative;"
											>
												<div>
													<?php echo $resul['auto']; $auto=$auto+$resul['auto'];?>
												</div>

												<div style="position: absolute; right: 5px; top: 0">
												<?php
													if ($resul['observacion_auto']!='') { ?>
														<span style="float: right; cursor: pointer;" title="<?php echo $resul['observacion_auto']; ?>"><img style="width: 12px;" src="../z_comun/imagenes/obs.png" alt=""></span>
												<?php }  ?>
												</div>
											</div>
										</td>
										<td class="centrar-texto" style="background: #F1F1F1; color: black;">
											<div style="position: relative;"
											>
												<div>
													<?php echo $resul['sup']; $sup=$sup+$resul['sup']; ?>
												</div>

												<div style="position: absolute; right: 5px; top: 0">
												<?php
													if ($resul['observacion_superior']!='') { ?>
														<span style="float: right; cursor: pointer;" title="<?php echo $resul['observacion_superior']; ?>"><img style="width: 12px;" src="../z_comun/imagenes/obs.png" alt=""></span>
												<?php }  ?>
												</div>
											</div>
											</td>

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






										</td>

						</tr>
				<?php } ?>
					<tr class="">
						<td style="border: none;padding: 3px;"></td>
					</tr>

						<tr class="" style="font-weight: bold;">
							<td>Total por Item</td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item WHERE activo = 1";
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
</div>

<script>
	$("#proceso").hide();

	$("#filtrar").click(function(e){
		e.preventDefault();
			estado=$("#estado").val();
			miembro=$("#miembro").val();
			// comentario=$("#comentario").val();
			id_evaluacion = <?php echo $id_evaluacion; ?>;

			$("#proceso").show();

			$.ajax({
			url:"filtro.php",
			cache:false,
			type:"POST",
			data:{estado,miembro,id_evaluacion,comentario},
			success:function(result){
     			$("#resultado").html(result);
     			$("#proceso").hide();
    		}
    	});
	});
</script>