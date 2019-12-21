<div class="zona-tabla-70">
	<hr>
	<table class="tabla-default">
		<thead>
			<tr>
				<td width="2%">Nro</td>
				<td width="90%">Preguntas</td>
			</tr>
		</thead>

		<?php
			$SQL="SELECT * FROM cuestionarios_respuestas WHERE id_cuestionario =".$id_cuestionario." ORDER BY nro_pregunta ASC";
			$res=mysqli_query($con, $SQL);
			$cant_preg=0;

			while ($preg=mysqli_fetch_array($res)) { $cant_preg=$preg["nro_pregunta"]; ?>
			<tr class="<?php echo "nro_".$preg["nro_pregunta"]; ?>">
				<td class="<?php echo "celda_".$preg["nro_pregunta"]; ?> ?>"><div class="centrar-texto"><?php echo $preg["nro_pregunta"]; ?></div></td>
				<?php
					$SQL="SELECT * FROM encuestas_preguntas WHERE id_pregunta=".$preg["id_pregunta"];
					$res_preg=mysqli_query($con, $SQL);
					$res_p=mysqli_fetch_array($res_preg);
				 ?>
				<td>
					<div class="pregunta_cues">
						<?php echo $res_p["pregunta"]; ?>
					</div>
					<div>
						<?php
							$SQL="SELECT * FROM cuestionarios_respuestas_lineas WHERE id_respuesta_cuestionario =".$preg["id_respuesta_cuestionario"];
							$res_linea=mysqli_query($con, $SQL);
							$rcount=mysql_num_rows($res_linea)/2;
							 ?>

							<?php if ($res_p["id_formato_respuesta"]==4) {?>
							<div class="content_radio">
								<?php
								$band=0;
								 while ($res_l=mysqli_fetch_array($res_linea)) { if ($res_l["respuesta"]==1) { $check=1;}?>

								<div class="item_radio">
								<input type="radio" class="pipa input_radio" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" name="<?php echo $preg["id_respuesta_cuestionario"]; ?>" value="<?php echo $res_l["id_cuestionario_respuestas_lineas"] ?>" <?php if ($res_l["respuesta"]==1) { echo "checked"; $band=1;} ?> ><?php echo $res_l["linea_tipo_respuesta"]; ?>
								</div>
								<?php } ?>
								 <div class="item_radio">
								<input type="radio" class="pipa input_radio" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" name="<?php echo $preg["id_respuesta_cuestionario"]; ?>" value="0" <?php if ($band==0) { echo "checked";} ?>>No contesta
								</div>
							</div>
							<?php } ?>

							<?php if ($res_p["id_formato_respuesta"]==1) {?>

								<select name="1" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" class="<?php echo "pipa pregunta_".$preg["id_respuesta_cuestionario"]; ?>" >
									<option value=""></option>
								<?php while ($res_l=mysqli_fetch_array($res_linea)) { ?>
									<option value="<?php echo $res_l["id_cuestionario_respuestas_lineas"] ?>"><?php echo $res_l["linea_tipo_respuesta"]; ?></option>
								<?php } ?>
								</select>
							<?php } ?>

							<?php if ($res_p["id_formato_respuesta"]==2) { $cont_reg=0;?>
							<div class="ed-container">
								<?php while ($res_l=mysqli_fetch_array($res_linea)) { $cont_reg=$cont_reg+1;?>
									<?php if ($cont_reg<=$rcount): ?>
										<div class="ed-item caja web-50">
											<input name="2" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" class="pipa imp_check" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" type="checkbox" value="<?php echo $res_l["id_cuestionario_respuestas_lineas"] ?>" <?php if ($res_l["respuesta"]==1) { echo "checked";} ?>><?php echo $res_l["linea_tipo_respuesta"]; ?>
										</div>
									<?php endif ?>
									<?php if ($cont_reg>$rcount): ?>
										<div class="ed-item caja web-50">
											<input name="2" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" class="pipa imp_check" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" type="checkbox" value="<?php echo $res_l["id_cuestionario_respuestas_lineas"] ?>" <?php if ($res_l["respuesta"]==1) { echo "checked";} ?>><?php echo $res_l["linea_tipo_respuesta"]; ?>
										</div>
									<?php endif ?>
								<?php } ?>
							</div>
							<?php } ?>

							<?php if ($res_p["id_formato_respuesta"]==3) {?>
	<!-- 											<div>
									<input class="input_tabla_resp" type="text" id="w" name="w">
								</div> -->
							<?php } ?>

							<div>
								<?php if ($res_p["id_formato_respuesta"]!=3){ ?>
									<div>
										<span class="obs_preg">Observaciones</span>
									</div>
								<?php } ?>
								<textarea data-form="<?php echo $res_p["id_formato_respuesta"]; ?>" data-si="<?php echo $preg["si_respuesta"]."-".$preg["proxima_pregunta"]."-".$res_l["id_linea_tipo_respuesta"]; ?>" data-preg="<?php echo $preg["nro_pregunta"]; ?>" class="pipa_ta textarea_tabla" name="3" data-nro="<?php echo $preg["id_respuesta_cuestionario"]; ?>" cols="" rows="2"><?php echo nl2br($preg["observacion"]); ?></textarea>

							</div>

					</div>

					</td>
			</tr>

			<?php } ?>
		<tbody>


		</tbody>
	</table>
<input type="hidden" id="cant_preg" value="<?php echo $cant_preg; ?>">
</div>