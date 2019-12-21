<table rules="all" border="1" style="width: 100%;">
	<thead>
		<tr>
			<td width="10%">Fecha</td>
			<td width="10%">Estado</td>
			<td width="68%">Observaci&oacute;n</td>
			<td width="7%">Editar</td>
		</tr>
	</thead>

	<tbody>

			<?php
			$SQL="SELECT * FROM creditos_lineas WHERE idcredito =".$_POST['idcredito'];
			$lineas_creditos= mysqli_query($con, $SQL);

			while ($lineas=mysqli_fetch_array($lineas_creditos)) { ?>
			<tr>
			<td><?php echo cambiarformatofecha($lineas["fecha"]); ?> </td>
			<td>
				<select id="estado_l" name="estado_l" disabled>
				<option value="0"></option>
				<option value="1" <?php if ($lineas['estado']==1) {  echo "selected";} ?>>Recibido</option>
				<option value="2" <?php if ($lineas['estado']==2) {  echo "selected";} ?>>Enviado</option>
				<option value="22" <?php if ($lineas['estado']==22) {  echo "selected";} ?>>En An&aacute;lisis</option>
				<option value="3" <?php if ($lineas['estado']==3) {  echo "selected";} ?>>Observado</option>
				<option value="4" <?php if ($lineas['estado']==4) {  echo "selected";} ?>>Rechazado</option>
				<option value="5" <?php if ($lineas['estado']==5) {  echo "selected";} ?>>Pre-Aprobado</option>
				<option value="6" <?php if ($lineas['estado']==6) {  echo "selected";} ?>>Aprobado</option>
				<option value="66" <?php if ($lineas['estado']==66) {  echo "selected";} ?>>Aprobado Obs</option>
				<option value="70" <?php if ($lineas['estado']==7 || $lineas['estado']==70) {  echo "selected";} ?>>Liquidado</option>
				</select>
			</td>
			<td><?php echo $lineas['observacion'] ?> </td>
			<td>
			<a class="editar_f" href="#" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/editar.png"  width="20px"></a>
			<a class="eliminar_f" href="#" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/eliminar.png"  width="20px"></a>
			</td>

			</tr>
			<?php } ?>
			</tbody>
	</table>