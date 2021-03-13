<fieldset>

	<div class="fila" style="margin-bottom:3px;">

			<div style="width: 20%; text-align: center;">
				<label>Precio:</label>
				<select id="tipoprecio" name="tipoprecio" style="width:70px" required>
					<option value=""></option>
					<option value="abierto" <?php  if ($reserva['tipoprecio'] == "abierto") { echo "selected"; } ?>>Abierto</option>
					<option value="fijo" <?php  if ($reserva['tipoprecio'] == "fijo") { echo "selected"; } ?>>Fijo</option>
				</select>
			</div>
			<div style="width: 20%; text-align: center;">
				<label>Factura:</label>
				<input type="text" id="factura" name="factura" size="1" style=" padding-left: 2px;"value="<?php echo $reserva["factura"]; ?>" required>
			</div>
	</div>
	<div class="fila" style="margin-bottom:3px; display: flex; justify-content: space-between;">
		<div style="width: 45%">
			<label>Entrega mes:</label>
			<select name="mesentrega" id="mesentrega" required>
				<option value=""></option>
				<option value="P.A." <?php  if ($reserva['mesentrega'] == "P.A.") { echo "selected"; } ?>>P.A.</opcion>
				<option value="Enero" <?php  if ($reserva['mesentrega'] == "Enero" ) { echo "selected"; } ?>>Enero</opcion>
				<option value="Enero/Febrero" <?php  if ($reserva['mesentrega'] == "Enero/Febrero") { echo "selected"; } ?>>Enero/Febrero</opcion>
				<option value="Febrero" <?php  if ($reserva['mesentrega'] == "Febrero" ) { echo "selected"; } ?>>Febrero</opcion>
				<option value="Febrero/Marzo" <?php  if ($reserva['mesentrega'] == "Febrero/Marzo") { echo "selected"; } ?>>Febrero/Marzo</opcion>
				<option value="Marzo" <?php  if ($reserva['mesentrega'] == "Marzo" ) { echo "selected"; } ?>>Marzo</opcion>
				<option value="Marzo/Abril" <?php  if ($reserva['mesentrega'] == "Marzo/Abril") { echo "selected"; } ?>>Marzo/Abril</opcion>
				<option value="Abril" <?php  if ($reserva['mesentrega'] == "Abril" ) { echo "selected"; } ?>>Abril</opcion>
				<option value="Abril/Mayo" <?php  if ($reserva['mesentrega'] == "Abril/Mayo") { echo "selected"; } ?>>Abril/Mayo</opcion>
				<option value="Mayo" <?php  if ($reserva['mesentrega'] == "Mayo" ) { echo "selected"; } ?>>Mayo</opcion>
				<option value="Mayo/Junio" <?php  if ($reserva['mesentrega'] == "Mayo/Junio" ) { echo "selected"; } ?>>Mayo/Junio</opcion>
				<option value="Junio" <?php  if ($reserva['mesentrega'] == "Junio" ) { echo "selected"; } ?>>Junio</opcion>
				<option value="Junio/Julio" <?php  if ($reserva['mesentrega'] == "Junio/Julio") { echo "selected"; } ?>>Junio/Julio</opcion>
					<option value="Julio" <?php  if ($reserva['mesentrega'] == "Julio" ) { echo "selected"; } ?>>Julio</opcion>
				<option value="Julio/Agosto" <?php  if ($reserva['mesentrega'] == "Julio/Agosto") { echo "selected"; } ?>>Julio/Agosto</opcion>
					<option value="Agosto" <?php  if ($reserva['mesentrega'] == "Agosto" ) { echo "selected"; } ?>>Agosto</opcion>
				<option value="Agosto/Septiembre" <?php  if ($reserva['mesentrega'] == "Agosto/Septiembre") { echo "selected"; } ?>>Agosto/Septiembre</opcion>
					<option value="Septiembre" <?php  if ($reserva['mesentrega'] == "Septiembre" ) { echo "selected"; } ?>>Septiembre</opcion>
				<option value="Septiembre/Octubre" <?php  if ($reserva['mesentrega'] == "Septiembre/Octubre") { echo "selected"; } ?>>Septiembre/Octubre</opcion>
				<option value="Octubre" <?php  if ($reserva['mesentrega'] == "Octubre" ) { echo "selected"; } ?>>Octubre</opcion>
				<option value="Octubre/Noviembre" <?php  if ($reserva['mesentrega'] == "Octubre/Noviembre") { echo "selected"; } ?>>Octubre/Noviembre</opcion>
				<option value="Noviembre" <?php  if ($reserva['mesentrega'] == "Noviembre" ) { echo "selected"; } ?>>Noviembre</opcion>
				<option value="Noviembre/Diciembre" <?php  if ($reserva['mesentrega'] == "Noviembre/Diciembre") { echo "selected"; } ?>>Noviembre/Diciembre</opcion>
				<option value="Diciembre" <?php  if ($reserva['mesentrega'] == "Diciembre" ) { echo "selected"; } ?>>Diciembre</opcion>
				<option value="Diciembre/Enero" <?php  if ($reserva['mesentrega'] == "Diciembre/Enero") { echo "selected"; } ?>>Diciembre/Enero</opcion>
			</select>

			<span>del</span>
			<select name="anoentrega" id="anoentrega" required>
				<option value="P.A." <?php  if ($reserva['anoentrega'] == "P.A.") { echo "selected"; } ?>>P.A.</opcion>
				<option value=""></option>
					<?php if ($reserva['enviada']>=1 and false) { ?>
						<option value="2015" <?php  if ($reserva['anoentrega'] == "2015") { echo "selected"; } ?>>2015</opcion>
						<option value="2016" <?php  if ($reserva['anoentrega'] == "2016") { echo "selected"; } ?>>2016</opcion>
                        <option value="2016/2017" <?php  if ($reserva['anoentrega'] == "2016/2017") { echo "selected"; } ?>>2016/2017</opcion>
						<option value="2017" <?php  if ($reserva['anoentrega'] == "2017") { echo "selected"; } ?>>2017</opcion>
						<option value="2017/2018" <?php  if ($reserva['anoentrega'] == "2017/2018") { echo "selected"; } ?>>2017/2018</opcion>
						<option value="2018" <?php  if ($reserva['anoentrega'] == "2018") { echo "selected"; } ?>>2018</opcion>
						<option value="2018/2019" <?php  if ($reserva['anoentrega'] == "2018/2019") { echo "selected"; } ?>>2018/2019</opcion>
						<option value="2019" <?php  if ($reserva['anoentrega'] == "2019") { echo "selected"; } ?>>2019</opcion>
						<option value="2019/2020" <?php  if ($reserva['anoentrega'] == "2019/2020") { echo "selected"; } ?>>2019/2020</opcion>
					<?php } ?>
						<option value="2020" <?php  if ($reserva['anoentrega'] == "2020") { echo "selected"; } ?>>2020</opcion>
						<option value="2020/2021" <?php  if ($reserva['anoentrega'] == "2020/2021") { echo "selected"; } ?>>2020/2021</opcion>
						<option value="2021" <?php  if ($reserva['anoentrega'] == "2021") { echo "selected"; } ?>>2021</opcion>
						<option value="2021/2022" <?php  if ($reserva['anoentrega'] == "2021/2022") { echo "selected"; } ?>>2021/2022</opcion>
						<option value="2022" <?php  if ($reserva['anoentrega'] == "2022") { echo "selected"; } ?>>2022</opcion>
			</select>
		</div>
		<div style="width: 55%">
			<label>Fecha estimada:</label>
			<?php
				$fechaControl = "2020-02-21";
				if ($reserva["fecres"]  >= $fechaControl ) { ?>

					<input type="date" id="fecest" name="fecest" size="10" value="<?php echo $reserva["fecest"]; ?>" required>
				<?php } else { ?>
					<input type="date" id="fecest" name="fecest" size="10" value="" disabled>
			 <?php } ?>
			 <span>Seg&uacute;n disponibilidad de F&aacute;brica.</span>

		</div>
	</div>

	<hr>
	Observaciones.
	<div id="observaciones" style="text-align: center;">
		<textarea id="observacion" name="observacion" rows="4" style="width: 97%; font-size:1em;" ><?php echo $reserva['observacion']." ".$reserva['obsanulada']  ?></textarea>
	</div>



	<?php


		if ($reserva['modificaciones']!="" OR $reserva['modificaciones']!=null) {?>
			Historial de Modificaciones.
			<div id="observaciones" style="padding: 5px; border: 1px solid #C0C0C0; font-size: .9em;">
				<?php echo $reserva['modificaciones']; ?>
			</div>
	 <?php  }?>

	 <textarea style="display: none" id="obs_cambio_a" name="obs_cambio_a" rows="4" style="width: 97%; font-size:1em;" ><?php echo $reserva['modificaciones'] ?></textarea>

</fieldset>