
<fieldset>

	<div id="opcion" name="opcion">

		<label>Compra:</label>
		<select id="compra" name="compra" required>
			<option value="Nuevo" <?php  if ($reserva["compra"] == "Nuevo") { echo "selected"; } ?>>Nuevo</option>
			<option value="Usado" <?php  if ($reserva["compra"] == "Usado") { echo "selected"; } ?>>Usado</option>
		</select>

		<div id="nro_uni" style="display: inline-block; margin-left: 30px">
			<label>Nro Unidad</label>
			<input type="text" name="nrounidad" id="nrounidad" size="7" value="<?php if ($reserva["nrounidad"]!=0) {echo $reserva["nrounidad"];	} ; ?>">
			<span style="color:red">Dato Obligatorio </span>
		</div>

		<div style="float:right;">
			<label>Realizada en Sal&oacute;n ??</label>
			<select id="lugarventa" name="lugarventa" required>
				<option value=""></option>
				<option value="Si" <?php  if ($reserva["lugarventa"] == "Si") { echo "selected"; } ?>>Si</option>
				<option value="No" <?php  if ($reserva["lugarventa"] == "No") { echo "selected"; } ?>>No</option>
<option value="Venta Telefónica" <?php  if ($reserva["lugarventa"] == "Venta Telefónica") { echo "selected"; } ?>>Venta Telefónica</option>
			</select>
		</div>

		<hr>
	</div>

	<div id="unidad">
		<div id="nuevo">

			<div id="lineauno">

				<div id="marcav" style="width:15%;">
					<label>Marca:</label>
					<input type="text" name="marca" id="marca"  value="<?php echo $reserva["marca"] ?>" size="6" >
				</div>
				<div id="tipos" style="width:23%;">
					<label>Tipo:</label>
					<select id="tipo" name="tipo" style="width:80%">
						<option value=""></option>
						<?php
						while ($ase=mysqli_fetch_array($tipos)) { ?>
						<option value="<?php echo $ase['idtipo'];?>" <?php  if ($reserva["idtipo"] == $ase['idtipo']) { echo "selected"; } ?>><?php echo $ase['tipo'];?> </option>
						<?php }  ?>
					</select>
				</div>
				<div id="grupos" name="grupos" style="width:23%;">
					<label>Grupo:</label>
					<select id="grupo" name="grupo" style="width:70%" >
						<?php // buscar el grupo que pertenece la reserva
						    $SQL="SELECT grupos.idgrupo as idgrupo, grupos.grupo as grupo, tipos.idtipo
									FROM (
									grupos
									INNER JOIN modelos ON grupos.idgrupo = modelos.idgrupo
									)
									INNER JOIN tipos ON modelos.idtipo = tipos.idtipo
									GROUP BY idgrupo, grupo, tipos.idtipo
									HAVING (((tipos.idtipo)=".$reserva["idtipo"]."))";
								$grupos=mysqli_query($con, $SQL);

								 while ($grup=mysqli_fetch_array($grupos)) { ?>
										<option value="<?php echo $grup["idgrupo"]; ?>"  <?php  if ($reserva["idgrupo"] == $grup["idgrupo"]) { echo "selected"; } ?> ><?php echo $grup["grupo"];?> </option>
							<?php }  ?>
					</select>
				</div>
				<div id="modelos" name="modelos" style="width:35%;">
					<label>Modelo:</label>
					<select id="modelo" name="modelo" style="width:80%" >
						<?php
							$SQL="SELECT * FROM modelos Where activo = 1 AND idgrupo=".$reserva["idgrupo"]." ORDER BY posicion";
							$modelos=mysqli_query($con, $SQL);
							while ($mod=mysqli_fetch_array($modelos)) { ?>
							<option
								value="<?php echo $mod["idmodelo"]; ?>" <?php  if ($reserva["idmodelo"] == $mod["idmodelo"]) { echo "selected"; } ?> ><?php echo $mod["modelo"]; ?></option>

						 <?php } ?>
					</select>
				</div>
			</div>

			<div id="lineados">

				<div id="color." style="width:20%;">
					<label>Color:</label>
					<input type="text" name="color" id="color"  value="<?php echo $reserva["color"] ?>" style="width:70%;" >
				</div>

				<div id="altuno." style="width:20%;">
					<label>Alt 1:</label>
					<input type="text" name="altuno" id="altuno" value="<?php echo $reserva["altuno"] ?>" style="width:70%;"  >
				</div>
				<div id="altdos." style="width:20%;">
					<label>Alt 2:</label>
					<input type="text" name="altdos" id="altdos" value="<?php echo $reserva["altdos"] ?>" style="width:70%;" >
				</div>
				<div id="interno." style="width:16%;">
					<label>Interno:</label>
					<input type="text" name="interno" id="interno" value="<?php echo $reserva["interno"] ?>" style="width:50%;">
				</div>
				<div id="nroorden." style="width:22%;">
					<label>Nro Orden:</label>
					<input type="text" name="nroorden" id="nroorden"  value="<?php echo $reserva["nroorden"] ?>" style="width:60%;">
				</div>
			</div>

		</div>
		<div id="usado">

			<div id="lineauno">

				<div id="internou." style="width:25%; margin-left: 25px;">
					<label>Interno:</label>
					<input type="text" name="internou" id="internou"  style="width:50%;" value="<?php echo $reserva["internou"] ?>" >
				</div>

				<div id="Detalleu" style="width:70%;">
					<label>Detalle:</label>
					<input type="text" name="detalleu" id="detalleu"  size="70" value="<?php echo $reserva["detalleu"]; ?>">
				</div>


			</div>

			<div id="lineados">


				<div id="coloru" style="width:33%; margin-left: 35px;">
					<label>Color:</label>
					<input type="text" name="colorusa" id="colorusa"  style="width:50%;" value="<?php echo $reserva["coloru"]; ?>"  >
				</div>


				<div id="aniou" style="width:33%;">
					<label>A&ntilde;o:</label>
					<input type="text" name="aniousa" id="aniousa" style="width:30%;" value="<?php echo $reserva["aniou"]; ?>">
				</div>

				<div id="dominiou." style="width:20%;">
					<label>Dominio:</label>
					<input type="text" name="dominiou" id="dominiou" style="width:50%; text-transform:uppercase;" value="<?php echo $reserva["dominiou"] ?>">
				</div>
			</div>
		</div>
	</div>
</fieldset>