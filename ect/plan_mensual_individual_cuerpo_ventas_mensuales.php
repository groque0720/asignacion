
<div class="flexible justificar margen-arriba-10">

	<div class="ancho-30 ">
		<div class="centrar-texto ancho-90 centrar-caja negrita">
			Ventas Mensuales Año <?php echo $ano; ?>
		</div>
		<table class="ancho-90 margen-arriba-5">
			<colgroup>
				<col width="5%"> 
				<col width="5%">
				<col width="5%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead>
				<tr>
					<td >Mes</td>
					<td >Obj.</td>
					<td >Vtas</td>
					<td >%</td>
					<td >TC</td>
				</tr>
			</thead>
			<tbody>
				<?php 
					$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);

					$cant_mes = 0;

					while ($mes=mysqli_fetch_array($meses)) {

						$cumple_presup=0;

						if ($mes_ant == 12) {
							$mes_ant = 1;
						}

						if ($mes['idmes']<=$mes_ant) {

							$SQL="SELECT sum(objetivo) as objetivo, sum(cumple) as cumple FROM ect_objetivos_cumplimiento WHERE ano=".$ano." AND id_mes = ".$mes['idmes']." AND id_asesor = ".$id_asesor." AND id_tipo_objetivo = 2";
							$objetivos = mysqli_query($con, $SQL);
							$objetivo = mysqli_fetch_array($objetivos);

							$obj_vtas = $objetivo['objetivo'];
							$cumple_vtas = $objetivo['cumple'];

							$mes_a[$cant_mes]['mes_res']=$mes['mes_res'];
							$mes_a[$cant_mes]['obj_vtas']=$obj_vtas;
							$mes_a[$cant_mes]['cumple_vtas']=$cumple_vtas;


							$SQL="SELECT sum(cumple) as cumple FROM ect_objetivos_cumplimiento WHERE ano=".$ano." AND id_mes = ".$mes['idmes']." AND id_asesor = ".$id_asesor." AND id_tipo_objetivo = 1";
							$tcs = mysqli_query($con, $SQL);
							$tc = mysqli_fetch_array($tcs);

							$cumple_presup = $tc['cumple'];
							$cant_mes++;

						}else{
							$obj_vtas = '-';
							$cumple_vtas = '-';
						}
					?>

					<tr>
						<td class="celda-espacio-left"><?php echo $mes['mes']; ?></td>
						<td class="centrar-texto"><?php echo $obj_vtas ?></td>
						<td class="centrar-texto"><?php echo $cumple_vtas; ?></td>
						<td class="derecha-texto negrita"><?php if ($obj_vtas!=0) { echo number_format(($cumple_vtas/$obj_vtas*100),2).' %';}else{ echo '0.00 %';} ?></td>
						<td class="derecha-texto negrita"><?php  if ($cumple_presup!=0) { echo number_format(($cumple_vtas/$cumple_presup*100),2).' %'; }else{ echo '0.00 %';} ?></td>
					</tr>

				<?php } ?>

			</tbody>

		</table>
		<div class="centrar-texto negrita margen-arriba-5" style="color: red; font-size: 10px;">TC = Tasa de Cierre </div>
	</div>
	<div class="ancho-40 centrar-texto centrar-caja">
		<div id="grafico_ventas_mensuales" style="width:100%; height: 100%; margin: 0 auto"></div>
	</div>
	<div class="ancho-30 ">
		
		<div class="ancho-90 centrar-caja">
				<div class="centrar-texto centrar-caja negrita">
						Obj. y Vtas Acum. - Sucursal <?php echo $sucursal['sucursal']; ?>
				</div>

				<table class="margen-arriba-5">
				<colgroup>
					<col width="10%"> 
					<col width="5%">
					<col width="5%">
					<col width="5%">

				</colgroup>
				<thead>
					<tr>
						<td>Asesor</td>
						<td>Objetivo</td>
						<td>Ventas</td>
						<td>%</td>
					</tr>
				</thead>
				<tbody>
				<tr style="border: none;"><td style="border: none;"></td></tr>
				<?php 
					$SQL="SELECT * FROM ect_view_asesores_sucursales WHERE id_sucursal_ect = ".$sucursal['id_sucursal'];
					$asesores=mysqli_query($con, $SQL);
					$tot_obj = 0;
					$tot_vtas = 0;
					while ($asesor=mysqli_fetch_array($asesores)) { 

						

						$SQL="SELECT Sum(objetivo) AS objetivo, Sum(cumple) AS cumple FROM ect_objetivos_cumplimiento WHERE 	id_tipo_objetivo = 2 AND id_asesor =". $asesor['id_asesor_ect']." AND id_mes <= ".$mes_ant." AND ano = ".$ano;

						$ventas_objetivos = mysqli_query($con, $SQL);
						$ventas= mysqli_fetch_array($ventas_objetivos);

						$tot_obj = $tot_obj + $ventas['objetivo'];
						$tot_vtas = $tot_vtas + $ventas['cumple'];

						?>
					<tr class="<?php if ($asesor['id_asesor_ect'] == $id_asesor) {
						echo "negrita color-texto-azul-10 fondo-gris-1";
					} ?>">
						<td class="celda-espacio-left"><?php echo $asesor['asesor'] ?></td>
						<td class="centrar-texto"><?php echo $ventas['objetivo']; ?></td>
						<td class="centrar-texto"><?php echo $ventas['cumple']; ?></td>
						<td class="derecha-texto negrita"><?php if ($ventas['objetivo']!=0) { echo number_format(($ventas['cumple']/$ventas['objetivo']*100),2).' %';}else{ echo '0.00 %';} ?></td>
					</tr>
				<?php } ?>


					<tr style="border: none;"><td style="border: none;"></td></tr>
					<tr class="negrita fondo-gris-1">
						<td class="celda-espacio-left">Total</td>
						<td class="centrar-texto"><?php echo $tot_obj; ?></td>
						<td class="centrar-texto"><?php echo $tot_vtas; ?></td>
						<td class="derecha-texto negrita"><?php if ($tot_obj!=0) { echo number_format(($tot_vtas/$tot_obj*100),2).' %';}else{ echo '0.00 %';} ?></td>
					</tr>
					</tbody>
				</table>

		</div>
	</div>



</div>

<!-- <script>
	Highcharts.chart('grafico_ventas_mensuales', {
    chart: {
        zoomType: 'xy'
    },
    title: {
        text: 'Average Monthly Weather Data for Tokyo'
    },
    xAxis: [{
        categories: [
		        <?php 
		        	$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);

					while ($mes = mysqli_fetch_array($meses)) {
						echo "'".$mes['mes_res']."', ";
				 	} ?>
			],
        crosshair: true
    }],
    yAxis: [
    { // Primary yAxis
        labels: {
            format: '{value}°C',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        title: {
            text: 'Temperature',
            style: {
                color: Highcharts.getOptions().colors[2]
            }
        },
        opposite: true,
        visible: false // no visible

    },
     { // Secondary yAxis
        gridLineWidth: 0,
        title: {
            text: ' ',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        }

    }, { // Tertiary yAxis
        gridLineWidth: 0,
        title: {
            text: 'Sea-Level Pressure',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value} mb',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true,
        visible: false
    }],
    tooltip: {
        shared: true
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        x: 80,
        verticalAlign: 'top',
        y: 55,
        floating: true,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    series: [{
        name: 'Objetivo',
        type: 'column',
        yAxis: 1,
        data: [
	        <?php 
				for ($i=0; $i < $cant_mes; $i++) {  
					echo $mes_a[$i]['obj_vtas'].',';
				}
	         ?>
        ],
        tooltip: {
            valueSuffix: ' '
        }

    },  {
        name: 'Ventas',
        type: 'spline',
        data: [

        	<?php 
				for ($i=0; $i < $cant_mes; $i++) {  
					echo $mes_a[$i]['cumple_vtas'].',';
				}
	         ?>
        ],
        tooltip: {
            valueSuffix: ' '
        }
    }]
});
</script> -->

<script>
	Highcharts.chart('grafico_ventas_mensuales', {
    title: {
        text: 'Objetivos Vs Ventas - Mes x Mes - Año '+<?php echo $ano_ant; ?>,
        style: {
            color: 'red',
            fontSize:'15px',
            fontWeight: 'bold'
        }
    },    yAxis: {
    	// visible: false,
        min: 0,
        title: {
            text: ' '
        }
    },
    xAxis: {
        categories: [
        		<?php 
		        	$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);

					while ($mes = mysqli_fetch_array($meses)) {
						echo "'".$mes['mes_res']."', ";
				 	} ?>
        ]
    },
    labels: {
        items: [{
            html: ' ',
            style: {
                left: '50px',
                top: '18px',
                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
            }
        }]
    },
    series: [{
        type: 'column',
        name: 'Objetivos',
        data: [<?php 
				for ($i=0; $i < $cant_mes; $i++) {

					// if ($i<$cant_mes) { 
					echo $mes_a[$i]['obj_vtas'].',';
					// }else{
					// 	echo '0,';
					// }

				}
	         ?>]
    }, {
        type: 'spline',
        name: 'Ventas',
        data: [<?php 
				for ($i=0; $i < $cant_mes; $i++) { 

					// if ($i<$cant_mes) {
						echo $mes_a[$i]['cumple_vtas'].',';
					// }else{
					// 	echo '0,';
					// }
					
				}
	         ?>],
        marker: {
            lineWidth: 2,
            lineColor: Highcharts.getOptions().colors[3],
            fillColor: 'white'
        }
    }]
});


</script>