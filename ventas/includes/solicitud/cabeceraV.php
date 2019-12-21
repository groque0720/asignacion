<fieldset>
	
	<div id="cabecera" >
		<div id="asesor" style="width:30%; float: left;">
			<label style="margin: 5px;">Nro: </label><span style="font-weight: bold; margin: 0 10px 0 0;"><?php echo $reserva["idreserva"]; ?></span>
			Asesor: <spam style="font-size: 1.3em; font-style:italic; font-weight: bold;">
			<?php echo $usuario['nombre']; ?>
						 </spam>
		</div>

		<div style="width: 30%; float: left; text-align: center;" >
			<div id="asesor">
				<label>Venta: </label>
				<select id="venta" name="venta" required>
					<option value=""></option>
					<option value="Convencional" <?php  if ($reserva["venta"] == "Convencional") { echo "selected"; } ?>>Convencional</option>
					<option value="Reventa" <?php  if ($reserva['venta'] == "Reventa") { echo "selected"; } ?>>Reventa</option>
					<option value="Plan Empleado" <?php  if ($reserva['venta'] == "Plan Empleado") { echo "selected"; } ?>>Plan Empleado</option>
					<option value="Especial" <?php  if ($reserva['venta'] == "Especial") { echo "selected"; } ?>>Especial</option>
					<option value="Plan de Ahorro" <?php  if ($reserva['venta'] == "Plan de Ahorro") { echo "selected"; } ?>>Plan de Ahorro</option>
				</select>
			</div>	
		</div>

		<div style="width: 40%; float:left; text-align: right;">
			<label>Fecha.:</label>
			<input type="date" id="fecres" name="fecres" value="<?php echo $reserva["fecres"]; ?>" required>
			<?php if ($reserva['fecult']!="") { ?>Ult. Act. <?php echo cambiarformatofecha($reserva['fecult']);} ?> 
			<input type="date" id="fecult" name="fecult" style="display: none;" value="<?php echo $reserva["fecult"]; ?>" >
			
		</div>

	</div>	

</fieldset>