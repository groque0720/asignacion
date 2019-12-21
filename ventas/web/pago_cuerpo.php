

		<table id="tabla_p" rules="all" border="1" style="width: 100%;">
			<thead>
				<tr>
					<td >Nro</td>
					<td width="10%">Fecha</td>
					<td width="12%">Tipo</td>
					<td width="12%">Modo</td>
					<td width="12%">Financiera</td>
					<td width="12%">Nro Rec.</td>
					<td width="12%">Monto</td>
					<td width="20%">Observaci&oacute;n</td>
					<td width="6%">Editar</td>
				</tr>
			</thead>

			<tbody>

			<?php
			$SQL="SELECT * FROM pagos_lineas WHERE idreserva =".$nrores;
			$pagos_lineas= mysqli_query($con, $SQL);
			$nrolinea = 1;
			$pagado=0;
			while ($lineas=mysqli_fetch_array($pagos_lineas)) {
				$pagado=$pagado+$lineas["monto"];

			$SQL="SELECT * FROM financieras WHERE idfinanciera = '".$lineas["financiera"]."'";
			$financieras = mysqli_query($con, $SQL);
			if (empty($financieras)) {$financiera["financiera"]="";}else{$financiera = mysqli_fetch_array($financieras);}

			$SQL="SELECT * FROM pagos_tipos WHERE idtipopago = '".$lineas["tipo"]."'";
			$tipospagos = mysqli_query($con, $SQL);
			if (empty($tipospagos)) {$tipopago["tipopago"]="";}else{$tipopago = mysqli_fetch_array($tipospagos);}

			$SQL="SELECT * FROM pagos_modos WHERE idpagomodo = '".$lineas["modo"]."'";
			$pagosmodos = mysqli_query($con, $SQL);
			if (empty($pagosmodos)) {$modo["modo"]="";}else{$modo = mysqli_fetch_array($pagosmodos);}
			?>

				<tr style="text-align:center;">
					<td><?php echo $lineas["idpago"]; ?> </td>
					<td><?php echo cambiarformatofecha($lineas["fecha"]); ?> </td>
					<td><?php echo $tipopago["tipopago"]; ?></td>
					<td><?php echo $modo["modo"]; ?></td>
					<td><?php echo $financiera["financiera"];?></td>
					<td><?php echo $lineas["nrorecibo"]; ?></td>
					<td style="text-align: right; padding-right: 5px;"><?php echo number_format($lineas["monto"], 2, ',','.'); ?></td>
					<td><?php echo $lineas["obs"]; ?></td>
					<td>
						<a class="editar_f"  href="#" data-id="<?php echo $nrolinea;?>"><img src="../imagenes/editar.png"  width="20px"></a>
						<a class="eliminar_f" href="#" data-id="<?php echo $lineas["idpago"];?>"><img src="../imagenes/eliminar.png"  width="20px"></a>
					</td>
				</tr>
			<?php $nrolinea++;  }

			$SQL="SELECT sum(monto) as total FROM lineas_detalle WHERE movimiento = 1 and idreserva =".$nrores;
			$result=mysqli_query($con, $SQL);
			if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;

			$debe =  $total_op["total"] - $pagado;
			?>
			</tbody>

		</table>

