<fieldset>
					

			<div id="encuestra">

				
					<div class="fila">	

						<div style="width: 48%; text-align: center;">
							<label>Condici&oacute;n de Pago:</label>
							<select id="condicionpago" name="condicionpago" style="width:60%" required>
								<option value=""></option>
								<option value="Plan de Ahorro" <?php  if ($reserva['condicionpago'] == "Plan de Ahorro") { echo "selected"; } ?>>Plan de Ahorro</option>
								<option value="Contado" <?php  if ($reserva['condicionpago'] == "Contado") { echo "selected"; } ?>>Contado</option>
								<option value="Contado/Financiado" <?php  if ($reserva['condicionpago'] == "Contado/Financiado") { echo "selected"; } ?>>Contado/Financiado</option>
								<option value="Contado/Usado" <?php  if ($reserva['condicionpago'] == "Contado/Usado") { echo "selected"; } ?>>Contado/Usado</option>
								<option value="Contado/Financiado/Usado" <?php  if ($reserva['condicionpago'] == "Contado/Financiado/Usado") { echo "selected"; } ?>>Contado/Financiado/Usado</option>
								<option value="Leasing" <?php  if ($reserva['condicionpago'] == "Leasing") { echo "selected"; } ?>>Leasing</option>
								<option value="Usado x Usado" <?php  if ($reserva['condicionpago'] == "Usado x Usado") { echo "selected"; } ?>>Usado x Usado</option>
								<option value="Sin Seña" <?php  if ($reserva['condicionpago'] == "Sin Seña") { echo "selected"; } ?>>Sin Se&ntilde;a</option>
								<option value="Sin Anticipo de Pago" <?php  if ($reserva['condicionpago'] == "Sin Anticipo de Pago") { echo "selected"; } ?>>Sin Anticipo de Pago</option>
								
							</select>
						</div>

						<div style="width: 48%; float:right !important;">
							<label>Tipo de Compra:</label>
							<select id="tipocompra" name="tipocompra" style="width:60%" required>
								<option value=""></option>
								<option value="1er 0km" <?php  if ($reserva['tipocompra'] == "1er 0km") { echo "selected"; } ?>>1er 0km</option>
								<option value="Adicional" <?php  if ($reserva['tipocompra'] == "Adicional") { echo "selected"; } ?>>Adicional</option>
								<option value="Reemplazo" <?php  if ($reserva['tipocompra'] == "Reemplazo") { echo "selected"; } ?>>Reemplazo</option>
							</select>
						</div>
		
					</div>
					<div class="fila">	

													
						<div class="mr" style="width: 24%">
							<label>Marca:</label>
							<input type="text" id="marcareem" name="marcareem" size="15" value="<?php echo $reserva["marcareem"] ?>">
						</div>

						<div class="mr" style="width: 52%; ">
							<label>Modelo:</label>
							<input type="text" id="modeloreem" name="modeloreem"  size="50"  value="<?php echo $reserva["modeloreem"] ?>">
							
						</div>

						<div class="mr" style="width: 18%; float:right !important;">
							<label>A&ntilde;o:</label>
							<input type="text" id="anioreem" name="anioreem" size="4" value="<?php echo $reserva["anioreem"] ?>">
						</div>
			

						
					</div>

					<div class="fila">	
											
						<div style="width: 97%; text-align: center; border-top: 1px solid #ccc; padding-top: 3px;">
							<label>Motivos de Selecci&oacute;n de Compra</label>
						</div>

					</div>
					<div class="fila" style="margin: 3px;">	
											
							<div style="width: 15%; text-align: center;">
							<label>Confort</label>
							<input type="checkbox" id="confort" name="confort" value="1"  <?php if ($reserva['confort']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 12%; text-align: center;"  >
							<label>Dise&ntilde;o</label>
							<input type="checkbox" id="disenio" name="disenio" value="1" <?php if ($reserva['disenio']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 15%; text-align: center;" >
							<label>Equipamiento</label>
								<input type="checkbox" id="equipamiento" name="equipamiento"  value="1" <?php if ($reserva['equipamiento']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 12%; text-align: center;" >
							<label>Garant&iacute;a</label>
								<input type="checkbox" id="garantia" name="garantia" value="1" <?php if ($reserva['garantia']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 15%; text-align: center;" >
							<label>Marca Toyota</label>
								<input type="checkbox" id="marcatoyota" name="marcatoyota" value="1" <?php if ($reserva['marcatoyota']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 12%; text-align: center;" >
							<label>Precio</label>
								<input type="checkbox" id="precio" name="precio" value="1" <?php if ($reserva['precio']=="1") { echo "checked ";} ?>>
							</div>
							<div style="width: 10%; text-align: center;" >	
							<label>Otra</label>
								<input type="checkbox" id="otra" name="otra" value="1"  <?php if ($reserva['otra']=="1") { echo "checked ";} ?>>
							</div>
										
					</div>

				
					<div class="fila" >	
											
						<div style="width: 35%; padding-top: 3px;">
							<label>Modelos Alternativos (de otras marcas):</label>
						</div>

						<div style="width: 60%; text-align: center;">
							<input type="text" id="modalt" name="modalt" style="width: 100%; text-align: center;" value="<?php echo $reserva['modalt']; ?>">
						</div>
											
					</div>
				







				</div>
				</fieldset>