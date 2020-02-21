<fieldset>

	<div id="cabecera" >
		<div id="asesor" style="width:35%; float: left;">
			<label style="margin: 2px;">Nro: </label><span style="font-weight: bold; margin: 0 10px 0 0;"><?php echo $reserva["idreserva"]; ?></span>
			Asesor: <spam style="font-size: 1.3em; font-style:italic; font-weight: bold;">
			<?php echo $usuario['nombre']; ?>
						 </spam>
		</div>

		<div style="width: 20%; float: left; text-align: center;" >
			<div id="asesor">
				<label>Venta:</label>
				<select id="venta" name="venta" required>
					<option value=""></option>
					<option value="Convencional" <?php  if ($reserva["venta"] == "Convencional") { echo "selected"; } ?>>Convencional</option>
					<option value="Usado Certificado" <?php  if ($reserva["venta"] == "Usado Certificado") { echo "selected"; } ?>>Usado Certificado</option>
					<option value="Reventa" <?php  if ($reserva['venta'] == "Reventa") { echo "selected"; } ?>>Reventa</option>
					<option value="Plan Dueño" <?php  if ($reserva['venta'] == "Plan Dueño") { echo "selected"; } ?>>Plan Dueño</option>
					<option value="Plan Empleado" <?php  if ($reserva['venta'] == "Plan Empleado") { echo "selected"; } ?>>Plan Empleado</option>
					<option value="Especial" <?php  if ($reserva['venta'] == "Especial") { echo "selected"; } ?>>Especial</option>
					<option value="Plan de Ahorro" <?php  if ($reserva['venta'] == "Plan de Ahorro") { echo "selected"; } ?>>Plan de Ahorro</option>
					<option value="Plan Adjudicado" <?php  if ($reserva['venta'] == "Plan Adjudicado") { echo "selected"; } ?>>Plan Adjudicado</option>
					<option value="Reg. Discapacidad" <?php  if ($reserva['venta'] == "Reg. Discapacidad") { echo "selected"; } ?>>Reg. Discapacidad</option>

				</select>
			</div>
		</div>

		<div style="width: 40%; float:right; text-align: right; margin-left:15px;">
			<label>Fecha:</label>
			<input type="date" id="fecres" name="fecres" size="10" value="<?php echo $reserva["fecres"]; ?>" required disabled>
			<input type="time" id="fectime" name="fectime" step="1" value="<?php echo $reserva["hora"]; ?>" required disabled>
		</div>

		<div style="width: 35%; float:right;; text-align: right; margin: 5px;">
			<?php if ($reserva['fecult']!="") { ?>Fecha.:Ult. Act. <?php echo cambiarformatofecha($reserva['fecult']);} ?>
			<input type="date" id="fecult" name="fecult" style="display: none;" value="<?php echo $reserva["fecult"]; ?>" >

		</div>

	</div>

</fieldset>