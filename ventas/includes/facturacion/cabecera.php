<fieldset>
	
	<div id="cabecera" >
		<div id="asesor" style="width:32%; float: left;">
			Promotor: <spam style="font-size: 1.3em; font-style:italic; font-weight: bold;">
			<?php echo $usuario['nombre']; ?>
						 </spam>
		</div>

		<div style="width: 32%; float: left; text-align: center;" >
			<div id="asesor">
				<label>Venta: </label>
				<select id="venta" name="venta" 	>
					<option value=""></option>
					<option value="Directa" <?php  if ($reserva['venta'] == "Directa") { echo "selected"; } ?>>Directa</option>
					<option value="Re Venta" <?php  if ($reserva['venta'] == "Re Venta") { echo "selected"; } ?>>Re Venta</option>
					<option value="Plan Empleado" <?php  if ($reserva['venta'] == "Plan Empleado") { echo "selected"; } ?>>Plan Empleado</option>
					<option value="Licitacion" <?php  if ($reserva['venta'] == "Licitacion") { echo "selected"; } ?>>Licitaci&oacute;n</option>
				</select>
			</div>	
		</div>

		<div style="width: 32%; float:right; text-align: right;">
			<label>Fecha.:</label>
			<input type="date" id="fecres" name="fecres" value="<?php echo $reserva["fecres"]; ?>">
		</div>
	</div>	

</fieldset>