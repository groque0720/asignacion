<div id="cliente">

	<fieldset>


		<div class="fila">

			<div style="width: 47%">
				<label>Ap. y Nom.:</label>
				<input type="text" id="nombre" name="nombre" value="<?php echo $clientes['nombre']; ?>" size="42" required>
			</div>

			<div style="width: 16%; text-align: center;">
				<label>Sexo:</label>
				<select id="sexo" name="sexo" style="width:90px" >
					<option value=""></option>
					<option value="masculino" <?php  if ($clientes['sexo'] == "masculino") { echo "selected"; } ?>>Masculino</option>
					<option value="femenino" <?php  if ($clientes['sexo'] == "femenino") { echo "selected"; } ?>>Femenino</option>
				</select>
			</div>

			<div style="width: 27%; font-size: 1em !important; float:right !important;">
				<label>Fec. Nac.:</label>
				<input type="date" id="fecnac" name="fecnac" value="<?php echo $clientes["fecnac"]; ?>">

			</div>

		</div>

		<div class="fila">

				<div style="width: 11%;">
					<label>Edad:</label>
					<input type="text" id="edad" name="edad" size="2" value="<?php echo $clientes["edad"]; ?>">
				</div>


				<div style="width: 15%;">
					<label>Tipo Doc.:</label>
					<select id="tipodoc" name="tipodoc" style="width:55px" >
						<option value=""></option>
						<option value="DNI" <?php  if ($clientes['tipodoc'] == "DNI") { echo "selected"; } ?>>D.N.I</option>
						<option value="LC" <?php  if ($clientes['tipodoc'] == "LC") { echo "selected"; } ?>>L.C.</option>
						<option value="LE" <?php  if ($clientes['tipodoc'] == "LE") { echo "selected"; } ?>>L.E.</option>
						<option value="PAS" <?php  if ($clientes['tipodoc'] == "PAS") { echo "selected"; } ?>>PAS.</option>
					</select>
				</div>
				<div style="width: 20%;">
					<label>Nro.:</label>
					<input type="text" id="nrodoc" name="nrodoc" size="10" value="<?php echo $clientes["nrodoc"]; ?>">
				</div>
				<div style="width: 21%;">
					<label>Cuil / Cuit:</label>
					<input type="text" id="cuil" name="cuil" size="10" value="<?php echo $clientes["cuil"]; ?>">
				</div>
				<div style="width: 29%; float:right !important; text-align: right;">
					<label>Ocupaci&oacute;n:</label>
					<?php

						if ($reserva["idreserva"]<=4299) { ?>
							<input type="text" id="ocupacion" name="ocupacion" size="19" value="<?php echo $clientes["ocupacion"]; ?>" required>
						<?php }else{  ?>




					<select id="ocupacion" name="ocupacion" style="width:70%" required>
					<option value="">-</option>
						<?php
						$SQL="SELECT * FROM profesiones";
						$ocupacion=mysqli_query($con, $SQL);
						while ($ase=mysqli_fetch_array($ocupacion)) { ?>
						<option value="<?php echo $ase['profesion'];?>" <?php  if ($clientes["ocupacion"] == $ase['profesion']) { echo "selected"; } ?>><?php echo $ase['profesion'];?> </option>
						<?php }  ?>
					</select>
					<?php } ?>
					<!-- <input type="text" id="ocupacion" name="ocupacion" size="19" value="<?php echo $clientes["ocupacion"]; ?>" required> -->
				</div>
		</div>

		<div class="fila">

				<div style="width: 50%;">
					<label>Direcci&oacute;n:</label>
					<input type="text" id="direccion" name="direccion" size="47" value="<?php echo $clientes["direccion"]; ?>" required>
				</div>
				<div style="width: 23%;">
					<label>Loc.:</label>
					<input type="text" id="localidad" name="localidad" size="16" value="<?php echo $clientes["localidad"]; ?>" required>
				</div>
				<div style="width: 22%; float:right !important; text-align: right;" >
					<label>Prov.:</label>
					<!-- <input type="text" id="provincia" name="provincia" size="15" value="<?php echo $clientes["provincia"]; ?>" required> -->
					<select id="provincia" name="provincia" style="width:72%" required>
						<option value=""></option>
						<?php
						$SQL="SELECT * FROM provincias ORDER BY provincia";
						$provincias=mysqli_query($con, $SQL);
						while ($ase=mysqli_fetch_array($provincias)) { ?>
						<option value="<?php echo $ase['provincia'];?>" <?php  if ($clientes["provincia"] == $ase['provincia']) { echo "selected"; } ?>><?php echo $ase['provincia'];?> </option>
						<?php }  ?>
					</select>

				</div>


		</div>

		<div class="fila">

				<div style="width: 50%;">
					<label>E-mail:</label>
					<input type="email" id="mail" name="mail" size="50" value="<?php echo $clientes["mail"]; ?>">
				</div>
				<div style="width: 23%;">
					<label>Tel.:</label>
					<input type="text" id="tfijo" name="tfijo" size="18" value="<?php echo $clientes["tfijo"]; ?>" >
				</div>
				<div style="width: 23%; float:right !important; text-align: right;">
					<label>Cel.:</label>
					<input type="text" id="tcelu" name="tcelu" size="18" value="<?php echo $clientes["tcelu"]; ?>" required>
				</div>

		</div>


		<div class="fila">
				<div style="width: 25%; text-align: left;">
					<label>Estado Civil:</label>
					<select id="estadocivil" name="estadocivil" style="width:100px" >
						<option value=""></option>
						<option value="Soltero" <?php  if ($clientes['estadocivil'] == "Soltero") { echo "selected"; } ?>>Soltero</option>
						<option value="Casado" <?php  if ($clientes['estadocivil'] == "Casado") { echo "selected"; } ?>>Casado</option>
						<option value="Divorciado" <?php  if ($clientes['estadocivil'] == "Divorciado") { echo "selected"; } ?>>Divorciado</option>
						<option value="Viudo" <?php  if ($clientes['estadocivil'] == "Viudo") { echo "selected"; } ?>>Viudo</option>
					</select>
				</div>
				<div style="width: 25%; text-align: center;">
					<label>Grupo Familiar:</label>
					<select id="grupofamiliar" name="grupofamiliar" style="width:100px" >
						<option value=""></option>
						<option value="En Pareja" <?php  if ($clientes['grupofamiliar'] == "En Pareja") { echo "selected"; } ?>>En Pareja</option>
						<option value="Solo" <?php  if ($clientes['grupofamiliar'] == "Solo") { echo "selected"; } ?>>Solo</option>
					</select>
				</div>
				<div style="width: 25%; text-align: center;">
					<label>Cant. Hijos:</label>
					<select id="canthijos" name="canthijos" style="width:50px" >
						<option value=""></option>
						<option value="0" <?php  if ($clientes['canthijos'] == "0") { echo "selected"; } ?>>0</option>
						<option value="1" <?php  if ($clientes['canthijos'] == "1") { echo "selected"; } ?>>1</option>
						<option value="2" <?php  if ($clientes['canthijos'] == "2") { echo "selected"; } ?>>2</option>
						<option value="+2" <?php  if ($clientes['canthijos'] == "+2") { echo "selected"; } ?>>+2</option>
					</select>
				</div>

				<div style="width: 20%; float:right; text-align: right; margin-right: 5px;">
					<label>Cliente Toyota:</label>
					<select id="ctoyota" name="ctoyota" style="width:50px" required>
						<option value=""></option>
						<option value="Si" <?php  if ($clientes['ctoyota'] == "Si") { echo "selected"; } ?>>Si</option>
						<option value="No" <?php  if ($clientes['ctoyota'] == "No") { echo "selected"; } ?>>No</option>
					</select>
				</div>
		</div>

		<div class="fila">

				<div style="width: 50%; float:float; text-align: left; margin-right: 5px;">
					<label style="color:red">Preferencia de Contacto:</label>
					<select id="prefcontacto" name="prefcontacto" style="width:200px" required>
						<option value=""></option>
						<option value="Teléfono" <?php  if ($clientes['prefcontacto'] == "Teléfono") { echo "selected"; } ?>>Teléfono</option>
						<option value="E-mail" <?php  if ($clientes['prefcontacto'] == "E-mail") { echo "selected"; } ?>>E-mail</option>
					</select>
				</div>

		</div>


				</fieldset>

		</div>
