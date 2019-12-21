<table class="tabla_p">
	<thead>
		<tr>

			<td width="2%">N.U.</td>
			<td width="4%">Asig. Mes</td>
			<td width="2%">Asig. A&ntilde;o</td>
			<td width="5%">Nro Orden</td>
			<td width="3%">Interno</td>
			<td width="10%">Modelo</td>
			<td width="4%">Fecha Playa</td>
			<td width="4%">Fecha Arribo</td>
			<td width="2%">Se Pag&oacute;</td>
			<td width="4%">Costo</td>
			<td width="4%">Saldo</td>
			<td width="4%">Cancelado</td>
			<td width="4%">Cancela</td>
			<td width="8%">Cliente</td>
			<td width="4%">Asesor</td>

		</tr>
	</thead>


</table>

<table>
<tbody>
	<?php
	$dia="-";
	while ($reg=mysqli_fetch_array($res)) {

		$SQL="SELECT
				Sum(lineas_detalle.monto) AS totalop,
				reservas.fechacanc AS fechacanc,
				reservas.estadopago AS estadopago,
				reservas.idreserva AS idreserva
				FROM
				reservas
				Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
				WHERE
				lineas_detalle.movimiento =  1 AND
				reservas.nrounidad = ".  $reg["nrounidad"];
		$toto=mysqli_query($con, $SQL);
		$totaldet=mysqli_fetch_array($toto);

		$SQL="SELECT Sum(pagos_lineas.monto) AS pagado FROM pagos_lineas WHERE idreserva = ".$totaldet["idreserva"];
		$res_p=mysqli_query($con, $SQL);
		if (empty($res_p)) {$reg_pagos["pagado"]=0;}else{$reg_pagos = mysqli_fetch_array($res_p);}

		if ($reg_pagos["pagado"] == "" || $reg_pagos["pagado"] == null) {
			$reg_pagos["pagado"] =0;
		}


	?>

	<?php if ($reg["sepago"] != -1 || $totaldet["estadopago"] != 3) { ?>

		<?php if ((($idfiltro==1  || $idfiltro==4) && $dia==$reg["fechaplaya"]) || ($idfiltro==3 && $dia==$reg["fechaarribo"]) ) { ?>

			<tr>
				<td width="2%"><?php echo $reg["nrounidad"] ?></td>
				<td width="4%"><?php echo $reg["asigmes"] ?></td>
				<td width="2%"><?php echo $reg["asigano"] ?></td>
				<td width="5%"><?php echo $reg["nroorden"] ?></td>
				<td width="3%"><?php echo $reg["interno"] ?></td>
				<td width="10%"><?php echo $reg["modelo"] ?></td>
				<td width="4%"><?php echo $reg["fechaplaya"] ?></td>
				<td width="4%"><?php echo $reg["fechaarribo"] ?></td>
				<td width="2%"><?php if ($reg["sepago"] == "-1") {echo "Si";}else{ echo "No";}?></td>

				<td class ="li" width="4%"><?php  if ($reg["sepago"] != "-1") {echo number_format($reg["costo"], 2, ',','.');}else{ echo "-";}  ?></td>


				<td class="li" width="4%" ><?php echo number_format(($totaldet["totalop"] - $reg_pagos["pagado"]), 2, ',','.'); ?></td>
				<td width="4%" style="<?php if ($totaldet["estadopago"]==3) {echo "background:#28FF28";};?>">
								<?php if ($totaldet["estadopago"]==3) {
									echo "Cancelado";
								}else{ echo "No";} ?>
								</td>
				<td width="4%">
					<?php if ($totaldet["fechacanc"]!="" AND $totaldet["fechacanc"]!=0 AND $totaldet["fechacanc"]!= null) {
								echo cambiarformatofecha($totaldet["fechacanc"]);
							}else{ echo "SF";} ?></td>
				<td class="ld" width="8%" style="<?php if ($reg["confirmada"]==0) {echo "background:#FFC4C4";};?>"><?php echo $reg["cliente"] ?></td>
				<td width="4%"><?php echo $reg["asesor"] ?></td>
			</tr>
			<?php }else{ ?>
				</tbody>
				</table>
				<?php
				if ($idfiltro==1 || $idfiltro ==4 ) {
					$dia=$reg["fechaplaya"];
				}
				if ($idfiltro==3) {
					$dia=$reg["fechaarribo"];
				}

				if ($idfiltro==2) {
					$dia=$reg["fechacanc"];
				}


				 ?>
				<br>
				<table>
					<tbody>
						<tr>
							<td width="2%"><?php echo $reg["nrounidad"] ?></td>
							<td width="4%"><?php echo $reg["asigmes"] ?></td>
							<td width="2%"><?php echo $reg["asigano"] ?></td>
							<td width="5%"><?php echo $reg["nroorden"] ?></td>
							<td width="3%"><?php echo $reg["interno"] ?></td>
							<td width="10%"><?php echo $reg["modelo"] ?></td>
							<td width="4%"><?php echo $reg["fechaplaya"] ?></td>
							<td width="4%"><?php echo $reg["fechaarribo"] ?></td>
							<td width="2%"><?php if ($reg["sepago"] == "-1") {echo "Si";}else{ echo "No";}?></td>

							<td class ="li" width="4%"><?php  if ($reg["sepago"] != "-1") {echo number_format($reg["costo"], 2, ',','.');}else{ echo "-";}  ?></td>

							<td class="li" width="4%"><?php echo number_format(($totaldet["totalop"] - $reg_pagos["pagado"]), 2, ',','.');?></td>
							<td width="4%" style="<?php if ($totaldet["estadopago"]==3) {echo "background:#28FF28";};?>">
								<?php if ($totaldet["estadopago"]==3) {
									echo "Cancelado";
								}else{ echo "No";} ?>
								</td>
							<td width="4%">
							<?php if ($totaldet["fechacanc"]!="" AND $totaldet["fechacanc"]!=0 AND $totaldet["fechacanc"]!= null) {
								echo cambiarformatofecha($totaldet["fechacanc"]);
							}else{ echo "SF";} ?></td>
							<td class="ld" width="8%" style="<?php if ($reg["confirmada"]==0) {echo "background:#FFC4C4";};?>"><?php echo $reg["cliente"] ?></td>
							<td width="4%"><?php echo $reg["asesor"] ?></td>
						</tr>
			<?php } //Cierre de que si cumple de que las fechas son iguales?>
		<?php } //Cierre de si cumplen de que alguno no esta cancelado (Toyota - Cliente?>
	<?php }  mysqli_close($con);// cierre de iteración?>
		</tbody>
		</table>


