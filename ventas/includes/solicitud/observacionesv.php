<fieldset>

	<div class="fila" style="margin-bottom:3px;">	
					
			<div style="width: 20%; text-align: center;">
				<label>Precio:</label>
				<select id="tipoprecio" name="tipoprecio" style="width:90px" required>
					<option value=""></option>
					<option value="abierto" <?php  if ($reserva['tipoprecio'] == "abierto") { echo "selected"; } ?>>Abierto</option>
					<option value="fijo" <?php  if ($reserva['tipoprecio'] == "fijo") { echo "selected"; } ?>>Fijo</option>
				</select>
			</div>
			<div style="width: 20%; text-align: center;">
				<label>Factura:</label>
				<input type="text" id="factura" name="factura" size="5" style=" padding-left: 5px;"value="<?php echo $reserva["factura"]; ?>" required>
			</div>

		
			<label>Entrega mes:</label>
			<select name="mesentrega" id="mesentrega" required>
				<option value=""></option>
				<option value="P.A." <?php  if ($reserva['mesentrega'] == "P.A.") { echo "selected"; } ?>>P.A.</opcion>
				<option value="Enero" <?php  if ($reserva['mesentrega'] == "Enero") { echo "selected"; } ?>>Enero</opcion>
				<option value="Febrero" <?php  if ($reserva['mesentrega'] == "Febrero") { echo "selected"; } ?>>Febrero</opcion>
				<option value="Marzo" <?php  if ($reserva['mesentrega'] == "Marzo") { echo "selected"; } ?>>Marzo</opcion>	
				<option value="Abril" <?php  if ($reserva['mesentrega'] == "Abril") { echo "selected"; } ?>>Abril</opcion>
				<option value="Mayo" <?php  if ($reserva['mesentrega'] == "Mayo") { echo "selected"; } ?>>Mayo</opcion>
				<option value="Junio" <?php  if ($reserva['mesentrega'] == "Junio") { echo "selected"; } ?>>Junio</opcion>	
				<option value="Julio" <?php  if ($reserva['mesentrega'] == "Julio") { echo "selected"; } ?>>Julio</opcion>
				<option value="Agosto" <?php  if ($reserva['mesentrega'] == "Agosto") { echo "selected"; } ?>>Agosto</opcion>
				<option value="Septiembre" <?php  if ($reserva['mesentrega'] == "Septiembre") { echo "selected"; } ?>>Septiembre</opcion>	
				<option value="Octubre" <?php  if ($reserva['mesentrega'] == "Octubre") { echo "selected"; } ?>>Octubre</opcion>
				<option value="Noviembre" <?php  if ($reserva['mesentrega'] == "Noviembre") { echo "selected"; } ?>>Noviembre</opcion>
				<option value="Diciembre" <?php  if ($reserva['mesentrega'] == "Diciembre") { echo "selected"; } ?>>Diciembre</opcion>		
			</select>
			<span>del</span>
			<select name="anoentrega" id="anoentrega" required>
				<option value=""></option>
				<option value="P.A." <?php  if ($reserva['anoentrega'] == "P.A.") { echo "selected"; } ?>>P.A.</opcion>
				<option value="2014" <?php  if ($reserva['anoentrega'] == "2014") { echo "selected"; } ?>>2014</opcion>
				<option value="2015" <?php  if ($reserva['anoentrega'] == "2015") { echo "selected"; } ?>>2015</opcion>
				<option value="2016" <?php  if ($reserva['anoentrega'] == "2016") { echo "selected"; } ?>>2016</opcion>		
			</select>
		
		<span>Seg&uacute;n disponibilidad de F&aacute;brica.</span>
	</div>

	
	<hr>
	Observaciones.
	<div id="observaciones" style="text-align: center;">
		<textarea id="observacion" name="observacion" rows="4" style="width: 97%; font-size:1em;" ><?php echo $reserva['observacion']." ".$reserva['obsanulada']  ?></textarea>
	</div>

	<?php 
		if ($reserva['modificaciones']!="" OR $reserva['modificaciones']!=null) {?>
			Historial de Modificaciones.
			<div id="observaciones" style="padding: 5px; border: 1px solid #C0C0C0; font-size: .9em">
				<?php echo $reserva['modificaciones']; ?>
				<textarea style="display: none" id="obs_cambio_a" name="obs_cambio_a" rows="4" style="width: 97%; font-size:1em;" ><?php echo $reserva['modificaciones'] ?></textarea>
			</div>		
	 <?php  }?>

</fieldset>