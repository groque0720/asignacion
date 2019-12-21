<?php 

$mes_act = $mes;

	if ($mes==1) {
		$mes_ant = 12;
		$ano_ant = $ano - 1;
	}else{
		$mes_ant= $mes - 1;
		$ano_ant = $ano;
	}

	$SQL="SELECT * FROM meses WHERE idmes = ".$mes_ant;
	$meses = mysqli_query($con, $SQL);
	$nombre_mes=mysqli_fetch_array($meses);

?>

<div class="flexible justificar fila_indices">

	<div class="ventas_mes_anterior ancho-30 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
			Desempeño en Ventas <?php echo $nombre_mes['mes'].' '.$ano_ant; ?>
		</div>

		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="7%"> 
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead>
				<tr>
					<td>Modelos</td>
					<td>Objetivos</td>
					<td>Boletos</td>
					<td>% Cumpl.</td>
				</tr>
			</thead>
			<tbody>

				<?php 
					$SQL="SELECT * FROM ect_modelos WHERE activo= 1 AND es_usado = 0 ORDER BY posicion";
					$modelos=mysqli_query($con, $SQL);

					$total_obj=0;
					$total_vtas=0;

					while ($modelo=mysqli_fetch_array($modelos)) {

						$SQL="SELECT * FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 2 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);

						$total_obj = $total_obj + $ventas['objetivo'];
						$total_vtas = $total_vtas + $ventas['cumple'];

					 ?>


							<tr>
								<td class="celda-espacio-left"><?php echo $modelo['modelo']; ?></td>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

				<?php } ?>

					<tr class="negrita fondo-gris-1" >
						<td class="celda-espacio-left">TOTAL 0 KM</td>
						<td class="centrar-texto"><?php echo $total_obj; ?></td>
						<td class="centrar-texto"><?php echo $total_vtas ?></td>
						<td class="derecha-texto"><?php   if ($total_obj!=0) { echo number_format($total_vtas*100/$total_obj,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					<?php 

						$SQL="SELECT * FROM ect_modelos WHERE activo= 1 AND es_usado = 1 ORDER BY posicion";
						$modelos=mysqli_query($con, $SQL);
						$modelo=mysqli_fetch_array($modelos);

						$SQL="SELECT * FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 2 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes_ant." AND ano = ".$ano_ant;
						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);
						$total_obj = $total_obj + $ventas['objetivo'];
						$total_vtas = $total_vtas + $ventas['cumple'];
					 ?>

							<tr>
								<td class="celda-espacio-left"><?php echo $modelo['modelo']; ?></td>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					<tr class="negrita fondo-gris-1">
						<td class="celda-espacio-left">TOTAL</td>
						<td class="centrar-texto"><?php echo $total_obj; ?></td>
						<td class="centrar-texto"><?php echo $total_vtas ?></td>
						<td class="derecha-texto"><?php   if ($total_obj!=0) { echo number_format($total_vtas*100/$total_obj,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>


			</tbody>

		</table>
	</div>
			
	<div class="ventas_mes_anterior ancho-20 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
			Acumulado de Ventas Año <?php echo $ano_ant; ?>
		</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead>
				<tr>
					<td>Objetivos</td>
					<td>Boletos</td>
					<td>% Cumpl.</td>
				</tr>
			</thead>
			<tbody>

				<?php 
					$SQL="SELECT * FROM ect_modelos WHERE activo= 1 AND es_usado = 0 ORDER BY posicion";
					$modelos=mysqli_query($con, $SQL);

					$total_obj_acum=0;
					$total_vtas_acum=0;

					$cant_mod=0;
					while ($modelo=mysqli_fetch_array($modelos)) { 

						$modelos_a[$cant_mod]['mod_res'] = $modelo['mod_res'];

						$SQL="SELECT Sum(objetivo) AS objetivo, Sum(cumple) AS cumple	FROM ect_objetivos_cumplimiento WHERE 	id_tipo_objetivo = 2 AND id_asesor =". $id_asesor." AND id_mes <= ".$mes_ant." AND ano = ".$ano_ant." AND ect_objetivos_cumplimiento.id_modelo = ".$modelo['id'];

						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);

						$modelos_a[$cant_mod]['objetivo'] = $ventas['objetivo'];
						$modelos_a[$cant_mod]['ventas'] = $ventas['cumple'];

						$total_obj_acum = $total_obj_acum + $ventas['objetivo'];
						$total_vtas_acum = $total_vtas_acum + $ventas['cumple'];

					 ?>
							<tr>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

				<?php $cant_mod++; } ?>

					<tr class="negrita fondo-gris-1">
						<td class="centrar-texto"><?php echo $total_obj_acum; ?></td>
						<td class="centrar-texto"><?php echo $total_vtas_acum ?></td>
						<td class="derecha-texto"><?php   if ($total_obj_acum!=0) { echo number_format($total_vtas_acum*100/$total_obj_acum,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					<?php 

						$SQL="SELECT * FROM ect_modelos WHERE activo= 1 AND es_usado = 1 ORDER BY posicion";
						$modelos=mysqli_query($con, $SQL);
						$modelo=mysqli_fetch_array($modelos);

						$SQL="SELECT Sum(objetivo) AS objetivo, Sum(cumple) AS cumple	FROM 	ect_objetivos_cumplimiento WHERE 	id_tipo_objetivo = 2 AND id_asesor =". $id_asesor." AND id_mes <= ".$mes_ant." AND ano = ".$ano_ant." AND ect_objetivos_cumplimiento.id_modelo = ".$modelo['id'];

						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);
						$total_obj_acum = $total_obj_acum + $ventas['objetivo'];
						$total_vtas_acum = $total_vtas_acum + $ventas['cumple'];
					 ?>

							<tr>
								<td class="centrar-texto">  <?php echo $ventas['objetivo']; ?></td>
								<td class="centrar-texto"> <?php echo $ventas['cumple']; ?></td>
								<td class="derecha-texto"> <?php   if ($ventas['objetivo']!=0) { echo number_format($ventas['cumple']*100/$ventas['objetivo'],2)." %";}else{ echo '0,00 %';};?></td>
							</tr>

					<tr style="border: none;"><td style="border: none;"></td></tr>
					<tr class="negrita fondo-gris-1">
						<td class="centrar-texto"><?php echo $total_obj_acum; ?></td>
						<td class="centrar-texto"><?php echo $total_vtas_acum ?></td>
						<td class="derecha-texto"><?php   if ($total_obj_acum!=0) { echo number_format($total_vtas_acum*100/$total_obj_acum,2)." %";}else{ echo '0,00 %';};?></td>
					</tr>


			</tbody>

		</table>
	</div>

	<div class="zona_grafico_ventas ancho-35 centrar-texto">
		<div  id="grafico_ventas_acumuladas" style="width:100%; height: 100%; margin: 0 auto" ></div>
	</div>
	

	<div class="ancho-15">
		<div class="centrar-texto centrar-caja negrita">

		<?php 
				$SQL="SELECT * FROM meses WHERE idmes = ".$mes;
		$meses = mysqli_query($con, $SQL);
		$nombre_mes=mysqli_fetch_array($meses);
		 ?>
			Objetivos <?php echo $nombre_mes['mes'].' '.$ano; ?>
		</div>

		<table class="ancho-90 margen-arriba-5">
		<colgroup>
			<col width="33.33%"> 
			<col width="33.33%">
			<col width="33.33%">
		</colgroup>
		<thead>
			<tr>
				<td>Ventas</td>
				<td>Prosp.</td>
				<td>TD</td>
			</tr>
		</thead>
		<tbody>

			<?php 
				$SQL="SELECT * FROM ect_modelos WHERE es_usado = 0 ORDER BY posicion";
				$modelos=mysqli_query($con, $SQL);

				$tot_obj_vtas = 0;
				$tot_obj_pres = 0;
				$tot_obj_td = 0;

				while ($modelo=mysqli_fetch_array($modelos)) { 

					//id tipo de objetivos 1= presupuesto, 2 = ventas, 3 Test Drive;

				$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 2 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
				$res=mysqli_query($con, $SQL);
				$obj_venta_a=mysqli_fetch_array($res);

				$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 1 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
				$res=mysqli_query($con, $SQL);
				$obj_pres_a=mysqli_fetch_array($res);

				$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 3 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
				$res=mysqli_query($con, $SQL);
				$obj_td_a=mysqli_fetch_array($res);

				$tot_obj_vtas = $tot_obj_vtas + $obj_venta_a['objetivo'];
				$tot_obj_pres = $tot_obj_pres + $obj_pres_a['objetivo'];
				$tot_obj_td = $tot_obj_td + $obj_td_a['objetivo'];

					?>
				<tr>
					<td class="centrar-texto"><?php echo $obj_venta_a['objetivo']; ?></td>
					<td class="centrar-texto"><?php echo $obj_pres_a['objetivo']; ?></td>
					<td class="centrar-texto"><?php echo $obj_td_a['objetivo']; ?></td>
				</tr>
			<?php } ?>

				<tr class="negrita fondo-gris-1">
					<td class="centrar-texto"><?php echo $tot_obj_vtas; ?></td>
					<td class="centrar-texto"><?php echo $tot_obj_pres; ?></td>
					<td class="centrar-texto"><?php echo $tot_obj_td; ?></td>
				</tr>

				<tr style="border: none;"><td style="border: none;"></td></tr>
				<tr>

				<?php 
				
					$SQL="SELECT * FROM ect_modelos WHERE es_usado = 1 ORDER BY posicion";
					$modelos=mysqli_query($con, $SQL);
					$modelo = mysqli_fetch_array($modelos);

					$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 2 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
					$res=mysqli_query($con, $SQL);
					$obj_venta_a=mysqli_fetch_array($res);

					$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 1 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
					$res=mysqli_query($con, $SQL);
					$obj_pres_a=mysqli_fetch_array($res);

					$SQL="SELECT objetivo FROM ect_objetivos_cumplimiento WHERE id_tipo_objetivo = 3 AND id_modelo = ". $modelo['id']." AND id_asesor = ".$id_asesor." AND id_mes = ".$mes." AND ano = ".$ano;
					$res=mysqli_query($con, $SQL);
					$obj_td_a=mysqli_fetch_array($res);

					$tot_obj_vtas = $tot_obj_vtas + $obj_venta_a['objetivo'];
					$tot_obj_pres = $tot_obj_pres + $obj_pres_a['objetivo'];
					$tot_obj_td = $tot_obj_td + $obj_td_a['objetivo'];

				 ?>
				 <tr>
					<td class="centrar-texto"><?php echo $obj_venta_a['objetivo'];?></td>
					<td class="centrar-texto"><?php echo $obj_pres_a['objetivo']; ?></td>
					<td class="centrar-texto"><?php echo $obj_td_a['objetivo']; ?></td>
				</tr>

				<tr style="border: none;"><td style="border: none;"></td></tr>
				<tr class="negrita fondo-gris-1">
					<td class="centrar-texto"><?php echo $tot_obj_vtas; ?></td>
					<td class="centrar-texto"><?php echo $tot_obj_pres; ?></td>
					<td class="centrar-texto"><?php echo $tot_obj_td; ?></td>
				</tr>
				</tbody>
			</table>
	</div>



</div>
   


<script src="js/highcharts/highcharts.js"></script>
<!--  <script src="js/html2canvas.js" type="text/javascript"></script> -->
<!-- <script src="js/highcharts/exporting.js"></script> -->

<script>
Highcharts.chart('grafico_ventas_acumuladas', {
	chart: {
        type: 'column',
        renderTo: 'zona_grafico_ventas'
    },
    title: {
        text: 'Objetivos Vs. Boletos - Año '+<?php echo $ano_ant; ?>,
         style: {
            color: 'red',
            fontSize:'15px',
            fontWeight: 'bold'
        }
    },
    xAxis: {
        categories: [

        <?php 
						for ($i=0; $i < $cant_mod; $i++) {  
							echo "'".$modelos_a[$i]['mod_res']."',";
						}
        ?>
            
        ],
        crosshair: true
    },

    yAxis: {
    	// visible: false,
        min: 0,
        title: {
            text: ' '
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:6px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
    		 series: {
            // colorByPoint: true
        	},
        column: {
        	//valores en cada punto del grafico
	        	dataLabels: {
	                enabled: true
	            },
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Obj.',
        data: [<?php 
						for ($i=0; $i < $cant_mod; $i++) {  
							echo $modelos_a[$i]['objetivo'].",";
						}
        ?>]

    }, {
        name: 'Vtas',
        data: [<?php 
						for ($i=0; $i < $cant_mod; $i++) {  
							echo $modelos_a[$i]['ventas'].",";
						}
        ?>]

    }]
});
</script>

