<fieldset>
			<div style="text-align:center; font-weight: bold;">Unidad Usada Como parte de Pago y/o Venta particular del cliente</div>		

	<div id="entrega_usado">
		<div class="fila">	
				
					<div style="width: 22%">
						<label>Marca:</label>
						<input type="text" id="marcau" name="marcau" size="13" value="<?php echo $usadoe['marca']; ?>" >
					</div>


					<div style="width: 21%; text-align: center;">
						<label>Tipo:</label>
						<input type="text" id="tipou" name="tipou" size="13" value="<?php echo $usadoe['tipo']; ?>" >
					</div>

					<div style="width: 54%; float:right !important;">
						<label>Modelo:</label>
						<input type="text" id="modelou" name="modelou"  size="50" value="<?php echo $usadoe['modelo']; ?>" >
						
					</div>

					</div>


					<div class="fila">	
					
					<div style="width: 20%">
						<label>Color:</label>
						<input type="text" id="coloru" name="coloru" size="10" value="<?php echo $usadoe['color']; ?>" >
					</div>


					<div style="width: 16%; text-align: center;">
						<label>A&ntilde;o:</label>
						<input type="text" id="aniou" name="aniou" size="2" maxlength="4" value="<?php echo $usadoe['anio']; ?>" >
					</div>

					<div style="width: 20%; text-align: center; ">
						<label>Dominio:</label>
						<input style="text-transform:uppercase;" type="text" id="dominio" name="dominio" value="<?php echo $usadoe['dominio']; ?>" size="5" maxlength="7" >
					</div>

					<div style="width: 20%; text-align: center;">
						<label>KM:</label>
						<input type="text" id="km" name="km" size="10" value="<?php echo $usadoe['km']; ?>" >
					</div>

					<div style="width: 20%; text-align: center; float:right !important;" >
						<label>Info:</label>
						<input type="text" id="info" name="info" size="10"  value="<?php echo $usadoe['info']; ?>" >
					</div>

					

				</div>



				</div>
</fieldset>
