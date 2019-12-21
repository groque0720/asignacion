<script>
	$(".admin").click(function(){

		id = $(this).attr('data-id');
		$("#nrounidad").val('');
		$("#interno").val('');
		$("#arribo").val('');
		$("#cancela").val('');
		$("#obs").val('');
		$("#entrega").val('');
		$("#nrofila").val(id);


	  	$('#tabla_p tr').eq(id).each(function () {
	  		var celda = 1;

	  		$(this).find('td').each(function () {
            	if (celda == 1) { $("#idreserva").val($(this).html());};
            	if (celda == 2) { $("#nrounidad").val($(this).html());};
            	if (celda == 3) { $("#interno").val($(this).html());};
            	if (celda == 4) { $("#nroorden").val($(this).html());};

            	if (celda == 14) {
            						var texto = String($(this).html());
            						texto.replace(" ","");

            						if ($(this).html()!= null && $(this).html()!="") {

            							texto.replace(" ","");
            							var fecha = $(this).html();
	            						var fecha_a = fecha.split("-");
	            						var fecha_r = String(fecha_a[2].substring(0,4)+'-'+fecha_a[1]+'-'+fecha_a[0]);
	            						$("#arribo").val(fecha_r);
									};
								};
            	if (celda == 15) {
            						var texto = String($(this).html());
            						texto.replace(" ","");

            						if ($(this).html()!= null && $(this).html()!="") {

            							texto.replace(" ","");
            							var fecha = $(this).html();
	            						var fecha_a = fecha.split("-");
	            						var fecha_r = String(fecha_a[2].substring(0,4)+'-'+fecha_a[1]+'-'+fecha_a[0]);
	            						$("#cancela").val(fecha_r);
									};
								};
            	if (celda == 16) {
            						//texto = $(this).html();
            						//texto.replace(" ","");
            						//var tres= texto.split("|");
            			       		$("#obs").val($(this).html());
            			       	};

              celda++;
            });


        });

		idres=$("#idreserva").val();
		$.ajax({url:"control_pagos_cliente_buscar_nrounidad.php",cache:false,type:"POST",data:{id:idres},success:function(result){
	    $("#nrounidad").val(result);
	    }});


		$("#form").dialog("open");
	});
</script>

<table id="tabla_p">
	<thead>
		<tr>
			<td width="2%">N.R.</td>
			<td width="2%">N.U.</td>
			<td width="3%">Interno</td>
			<td width="4%">Nro Orden</td>
			<td width="5%">Asesor</td>
			<td width="11%">Cliente</td>
			<td width="12%">Modelo</td>
			<td width="4%">Usado</td>
			<td width="5%">Efectivo</td>
			<td width="5%">Credito</td>
			<td width="5%">Leasing</td>
			<td width="5%">Saldo</td>
			<td width="5%">Fec.Res.</td>
			<td width="5%">Lleg&oacute;</td>
			<td width="5%">Cancela</td>
			<td width="13%">Observaci&oacute;n</td>
			<td class="control" width="7%">Estados</td>
			<td width="2%">Adm</td>
		</tr>
	</thead>
	<tbody>

		<?php
		$total_efectivo_gral=0;
				$total_credito_gral=0;
				$total_leasing_gral=0;
				$total_usado_gral=0;
		$nrofila = 1;
		while ($reg=mysqli_fetch_array($res)) { ?>
		<tr>
			<td><?php echo $reg["idreserva"]; ?></td>
			<td><?php echo $reg["nrounidad"]; ?></td>
			<td><?php echo $reg["interno"]; ?></td>
			<td><?php echo $reg["nroorden"]; ?></td>
			<td><?php echo $reg["asesor"]; ?></td>
			<td class="ld"><?php echo $reg["cliente"]; ?></td>


				<?php

					$SQL="SELECT * FROM grupos WHERE idgrupo=".$reg['idgrupo'];
					$gru=mysqli_query($con, $SQL);
					if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

					$SQL="SELECT * FROM modelos WHERE idmodelo=".$reg['idmodelo'];
					$mod=mysqli_query($con, $SQL);
					if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

				?>


			<td class="ld">



				<?php if ($reg['compra']=="Nuevo") {
						if ($grupo['grupo']!="--") { echo $grupo['grupo']." ";}
						if ($modelo['modelo']!="--") { echo $modelo['modelo'];}
					} else{
							echo $reg['detalleu'];
						}?>
			</td>

			<td class="li">
				<?php
				$total_uu=0;
				$b_usado=0;
				$ley_usado="";
				$pago_usado=0;

				$SQL="SELECT SUM(monto) as total FROM pagos_lineas WHERE  modo=6 and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;
				$pago_usado=$total_op["total"];

				$SQL="SELECT SUM(monto) as total FROM lineas_detalle WHERE idcodigo = 51 and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_uu["total"]=0;}else{$total_uu=mysqli_fetch_array($result);$b_usado=1;} ;


				$total_usado=$total_uu["total"]-$pago_usado;
				$total_usado_gral = $total_usado_gral + $total_usado;
				if ($b_usado==1 && $pago_usado>0 ) {
					$ley_usado="(P)  - ";
				}
				echo "<strong>".$ley_usado."</strong>".number_format($total_uu["total"]-$pago_usado, 2, ',','.');
				 ?>

			</td>



			<?php

				$saldo=0;
				$add="";
				$SQL="SELECT SUM(monto) as total FROM lineas_detalle WHERE movimiento = 1 and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;
				$monto_total=$total_op["total"];

				$SQL="SELECT
						sum(lineas_detalle.monto) AS total,
						lineas_detalle.detalle as comentario
						FROM
						lineas_detalle
						Inner Join codigos ON codigos.idcodigo = lineas_detalle.idcodigo
						WHERE
						codigos.tipocredito =  '1' and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);

				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result); $add .=$total_op["comentario"];} ;
				$total_credito=$total_op["total"];

				$SQL="SELECT
						sum(lineas_detalle.monto) AS total,
						lineas_detalle.detalle as comentario
						FROM
						lineas_detalle
						Inner Join codigos ON codigos.idcodigo = lineas_detalle.idcodigo
						WHERE
						codigos.tipocredito =  '3' and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);

				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result); $add .=$total_op["comentario"];} ;
				$total_leasing=$total_op["total"];

				$monto_total_op=$monto_total-$total_op["total"]-$total_credito-$pago_usado;


				$SQL="SELECT SUM(monto) as total FROM pagos_lineas WHERE (modo = 1 or modo=2 or modo = 5 or modo=7 or modo=8 or modo=9)  and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;

				$monto_total_op=$monto_total_op - $total_op["total"]-$total_usado;


				$SQL="SELECT SUM(monto) as total FROM pagos_lineas WHERE (modo = 3) and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;

				$total_credito=$total_credito-$total_op["total"];

				$SQL="SELECT SUM(monto) as total FROM pagos_lineas WHERE (modo = 4) and idreserva =".$reg["idreserva"];
				$result=mysqli_query($con, $SQL);
				if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;

				$total_leasing = $total_leasing - $total_op["total"];

				$saldo=$monto_total_op+$total_credito+$total_leasing+$total_usado;
				$total_efectivo_gral = $total_efectivo_gral + $monto_total_op;
				$total_credito_gral = $total_credito_gral + $total_credito;
				$total_leasing_gral = $total_leasing_gral + $total_leasing;

			 ?>
			<td class="li"><?php echo number_format($monto_total_op, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_credito, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_leasing, 2, ',','.'); ?></td>
			<td class="li" style="<?php if ($saldo==0) {echo "background:#28FF28";};?>"><?php echo number_format($saldo, 2, ',','.'); ?></td>
			<td><?php echo cambiarformatofecha($reg['fecres']); ?></td>
			<td style="<?php if ($reg['llego']!="" && $reg['llego']!=0 && $reg['llego']!=null) {
				date_default_timezone_set('UTC');
				$segundos=strtotime('now') - strtotime($reg['llego']);
				$diferencia_dias=intval($segundos/60/60/24);

				if ($diferencia_dias>10) {
					echo "background:red; font-weight:bold;font-style: italic;";
					}
				}?>"><?php if ($reg['llego']!=" " && $reg['llego']!=0 && $reg['llego']!=null) { echo cambiarformatofecha($reg['llego']);} ?></td>


			<td style="<?php if ($reg['fechacanc']!="" && $reg['fechacanc']!=0 && $reg['fechacanc']!=null) {

				if ($reg['fechacanc']< date("Y-m-d")) {
					echo "background:#FDBA99; font-weight:bold;font-style: italic;";
					}
				}?>"><?php if ($reg['fechacanc']!="" && $reg['fechacanc']!=0 && $reg['fechacanc']!=null) { echo cambiarformatofecha($reg['fechacanc']);} ?></td>
			<td class="ld"><?php echo $reg['obs']; ?></td>
			<td class="control">

				<a href="reserva.php?IDrecord=<?php echo $reg['idreserva']; ?>" style="style" target="_blank">

					<?php if ($reg['enviada']==0) { ?>
					<img src="../imagenes/editar.png" title="Reserva Sin Enviar" width="15px"></a>
					<?php } ?>
					<?php if ($reg['enviada']==1) { ?>
						<img src="../imagenes/editar_e.png" title="Reserva Enviada" width="15px"></a>
					<?php } ?>
					<?php if ($reg['enviada']==2) { ?>
					<img src="../imagenes/reserva_act.png" title="Reserva Actualizada" width="15px"></a>
					<?php } ?>
					<?php if ($reg['enviada']==3) { ?>
					<img src="../imagenes/reserva_obs.png" title="Reserva Observada" width="15px"></a>
					<?php } ?>
					<?php if ($reg['enviada']==4) { ?>
					<img src="../imagenes/reserva_vista.png" title="Reserva vista" width="15px"></a>
					<?php } ?>
					<?php if ($reg['enviada']==5) { ?>
					<img src="../imagenes/reserva_ok.png" title="Reserva Aprobada" width="15px"></a>
					<?php } ?>

					<?php $SQL="SELECT * FROM facturas WHERE idfactura = ".$reg['idfactura'];
					$result=mysqli_query($con, $SQL);
					if (empty($result)){$factura["total"]=0;}else{$factura=mysqli_fetch_array($result);} ;
					 ?>

				<a href="facturacion.php?IDrecord=<?php echo $reg['idreserva']; ?>" target="_blank">

					<?php if ($factura['estado']==0) { ?>
					<img src="../imagenes/cajaregistradora_n.png" title="Sin Facturar" width="15px"></a>
					<?php } ?>
					<?php if ($factura['estado']==1) { ?>
					<img src="../imagenes/cajaregistradora_e.png" title="Facturaci&oacute;n Enviada" width="15px"></a>
					<?php } ?>
					<?php if ($factura['estado']==3) { ?>
					<img src="../imagenes/cajaregistradora_ok.png" title="Facturaci&oacute;n OK" width="15px"></a>
					<?php } ?>
					<?php if ($factura['estado']==2) { ?>
					<img src="../imagenes/cajaregistradora_obs.png" title="Facturaci&oacute;n Observada" width="15px"></a>
					<?php } ?>


					<?php $SQL="SELECT * FROM creditos WHERE idcredito= ".$reg['idcredito'];
					$result=mysqli_query($con, $SQL);
					if (empty($result)){$credito["total"]=0;}else{$credito=mysqli_fetch_array($result);} ;
					 ?>


					<?php if ($credito['estado'] == 0) {?>
						<img src="../imagenes/creditob.png" width="15px">
					<?php } ?>

					<a href="credito.php?IDrecord=<?php echo $credito['idcredito']; ?>" target="_blank">
						<?php if ($credito['estado'] == 20) {?>
						<img src="../imagenes/credito.png" title="Sin Papeles" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 1) {?>
						<img src="../imagenes/credito_r.png" title="Cr&eacute;dito Recibido" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 2) {?>
							<img src="../imagenes/credito_e.png" title="Cr&eacute;dito Enviado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 22) {?>
							<img src="../imagenes/analisis.png" title="Cr&eacute;dito en An&aacute;lisis" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 3) {?>
							<img src="../imagenes/credito_o.png" title="Cr&eacute;dito Observado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 4) {?>
							<img src="../imagenes/credito_no.png" title="Cr&eacute;dito Rechazado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 5) {?>
							<img src="../imagenes/credito_pre.png" title="Cr&eacute;dito Pre-Aprobado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 6) {?>
							<img src="../imagenes/credito_aprobado.png" title="Cr&eacute;dito Aprobado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 66) {?>
							<img src="../imagenes/aprobado_obs.png" title="Cr&eacute;dito Aprobado Observado" width="15px"></a>
						<?php } ?>
						<?php if ($credito['estado'] == 7 || $credito['estado'] == 70) {?>
							<img src="../imagenes/credito_liq.png" title="Cr&eacute;dito Liquidado" width="15spx"></a>
						<?php } ?>

					<a href="pago.php?IDrecord=<?php echo $reg['idcliente']; ?>" target="_blank">

						<?php if (is_null($reg['estadopago']) OR $reg['estadopago']==0 ) {?>
						<img src="../imagenes/pesosb.png" width="15px" title="Sin pagos"></a>
						<?php } ?>
						<?php if ($reg['estadopago']==1) {?>
						<img src="../imagenes/pagos_i.png" width="15px" title="Con Seña"></a>
						<?php } ?>
						<?php if ($reg['estadopago']==2) {?>
						<img src="../imagenes/pagos_m.png" width="15px" title="Pagos a Cuenta"></a>
						<?php } ?>
						<?php if ($reg['estadopago']==3) {?>
						<img src="../imagenes/pagos_ok.png" width="15px" title="Cancelada"></a>
						<?php } ?>

						<?php
							$a=null;
							 if ($reg['llego'] == null OR $reg['llego'] == '' OR $reg['llego'] == 0) {?>
							<img src="../imagenes/auto_b.png" width="15px" title="Sin Arribo">
							<?php }else{ ?>
								<img src="../imagenes/auto_ll.png" width="15px" title="Con Arribo">

						<?php } ?>



			</td>
			<td class="cel_admin"><a class="admin" data-id="<?php echo $nrofila; ?>" href="#"></a></td>
		</tr>
		<?php
		$nrofila ++;
		} ?>
		<tr class="totales">
			<td ></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td >Totales</td>
			<td class="li"><?php echo number_format($total_usado_gral, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_efectivo_gral, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_credito_gral, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_leasing_gral, 2, ',','.'); ?></td>
			<td class="li"><?php echo number_format($total_leasing_gral+$total_credito_gral+$total_efectivo_gral+$total_usado_gral, 2, ',','.'); ?></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td ></td>
			<td class="admin"></td>
		</tr>




	</tbody>
</table>



