<div class="ancho-100 ">

	<?php 
		if ($cant_meses <= 6) {
			$class='ancho-80 margen-arriba-5';
		}else{
			if ($cant_meses <= 8) {
			$class='ancho-90 margen-arriba-5';
			}else{
				$class='ancho-100 margen-arriba-5';
			}
		}
	 ?>


	<div class="centrar-texto ancho-95 negrita">
		
	</div>

<?php 

$SQL="SELECT * FROM ect_asesores WHERE id_asesor = ".$asesor;
$ect_ases=mysqli_query($con, $SQL);

$reg_enc=mysqli_num_rows($ect_ases);

if ($reg_enc==0 AND $asesor!=0) {
 	echo 'Asesor sin Datos para procesar';
}else{
	$ect_ase = mysqli_fetch_array($ect_ases);
 ?>

		<table class="<?php echo $class; ?>" style="float: left;">
			<colgroup>
				<col width="25%">
				<?php for ($i=0; $i < $cant_meses; $i++) { ?>
					<col width="2%">
					<col width="2%">
				<?php } ?> 

				<col width="5%">
			</colgroup>

			<thead>
				<tr style="border: none;">
					<td>ITEM </td>

					<?php $j = $mes_desde; ?>

					<?php for ($i=0; $i < $cant_meses; $i++) { ?>
					<td><?php echo $mes_a[$j]['mes_res']; if ($j==12) {$j=1;}else{$j++;} ?></td>
					<td style="background: white; color: black;">Dif</td>
					<?php } ?>

					<td>Total</td>
				</tr>
			</thead>
			<tbody>

				
				<?php 
					$SQL="SELECT * FROM ect_csi_item_dyv";
					$items=mysqli_query($con, $SQL);
					$fila=0;

					while ($item = mysqli_fetch_array($items)) { $fila++; $item_acumulado=0;
						 if (($fila % 2)==0) {
						 	$estilo_fila = "border: none; background: #CBF9F9;";
						 }else{
						 	$estilo_fila = "border: none;";
						  } 
						?>
						<tr style="border: none;"><td style="border: none;"></td></tr>

						<tr style="<?php echo $estilo_fila; ?>" >
							<td class="celda-espacio-left"><?php echo $item['detalle']; ?></td>

							<?php for ($i=0; $i < $cant_meses; $i++) { 

								if (($mes_desde+$i)>12) {
									$mes_actual = 1;
									$ano_actual = $ano_desde+1;
								}else{
									$mes_actual=$mes_desde+$i;
									$ano_actual = $ano_desde;
								}

								if ($asesor==0) {
									$SQL="SELECT * FROM ect_csi_sucursales WHERE id_sucursal_ect =".$sucursal." AND id_mes =".$mes_actual." AND ano = ".$ano_actual;
									$csi_sucs = mysqli_query($con, $SQL);
									$csi_suc= mysqli_fetch_array($csi_sucs);

									$csi_mes_a[$i]['csi']=$csi_suc['csi_dyv'];
									$csi_mes_a[$i]['xy']=$csi_suc['csi_dyv']*($i+1);
									$csi_mes_a[$i]['x2']=($i+1)*($i+1);
									$csi_mes_a[$i]['y2']=$csi_suc['csi_dyv']*$csi_suc['csi_dyv'];

									$id_csi_sucursal = $csi_suc['id'];

									$SQL="SELECT * FROM ect_csi_sucursales_dyv_detalle WHERE id_csi_sucursal = ".$id_csi_sucursal." AND id_item_dyv = ". $item['id'];
									$csi_suc_dets=mysqli_query($con, $SQL);
									$csi_suc_det=mysqli_fetch_array($csi_suc_dets);
								}else{


									
										$SQL="SELECT * FROM ect_csi_asesores WHERE id_asesor_ect =".$ect_ase['id']." AND id_mes =".$mes_actual." AND ano = ".$ano_actual;
										$csi_sucs = mysqli_query($con, $SQL);
										$csi_suc= mysqli_fetch_array($csi_sucs);

										$csi_mes_a[$i]['csi']=$csi_suc['csi'];
										$csi_mes_a[$i]['xy']=$csi_suc['csi']*($i+1);
										$csi_mes_a[$i]['x2']=($i+1)*($i+1);
										$csi_mes_a[$i]['y2']=$csi_suc['csi']*$csi_suc['csi'];

										$id_csi_asesor = $csi_suc['id'];

										$SQL="SELECT * FROM ect_csi_asesores_dyv_detalle WHERE id_csi_asesor = ".$id_csi_asesor." AND id_item_dyv = ". $item['id'];
										$csi_suc_dets=mysqli_query($con, $SQL);
										$csi_suc_det=mysqli_fetch_array($csi_suc_dets);
								
								}

								?>

							<td class="centrar-texto"><?php echo number_format($csi_suc_det['puntaje'],1); $item_acumulado = $item_acumulado + $csi_suc_det['puntaje'];  ?></td>

							<?php 
								if ($i>0) {
								 $dif= number_format($csi_suc_det['puntaje'] - $puntaje_anterior,1);
								}else{
									$dif='';
									$puntaje_anterior=0;
								}

								$puntaje_anterior = $csi_suc_det['puntaje'];

								if ($dif>=0) {
									$clase_celda="color:#5B5959; border: none; font-size: 10px;";
								}else{
									$clase_celda="color:#FD0404; border: none; font-size: 10px;";
								}

							 ?>


							<td class="centrar-texto" style="<?php echo $clase_celda; ?>"><?php echo $dif; ?></td>



							<?php } ?>

						<td class="centrar-texto"><?php echo number_format($item_acumulado/$cant_meses,1).' %'; ?> </td>

						</tr>

				<?php } ?>
					<tr style="border: none;"><td style="border: none;"></td></tr>

					<tr style="border: none;" class="negrita fondo-gris-1">
						<td>CSI TOTAL  MES </td>

							<?php $puntaje_anterior=0; $item_gral_acum=0;?>

							<?php for ($i=0; $i < $cant_meses; $i++) { ?>
								<td class="centrar-texto"><?php echo $csi_mes_a[$i]['csi']; $item_gral_acum = $item_gral_acum + $csi_mes_a[$i]['csi'];  ?></td>

								<?php 
								if ($i>0) {
								 $dif= number_format($csi_mes_a[$i]['csi'] - $puntaje_anterior,2);
								}else{
									$dif='';
								}

								$puntaje_anterior = $csi_mes_a[$i]['csi'];

								if ($dif>=0) {
									$clase_celda="color:#5B5959; border: none; font-size: 10px; background:white;";
								}else{
									$clase_celda="color:#FD0404; border: none; font-size: 10px; background:white;";
								}

							 ?>

								<td style="<?php echo $clase_celda; ?>" class="centrar-texto"><?php echo $dif; ?> </td>
							<?php } ?>

						<td class="centrar-texto"><?php echo number_format($item_gral_acum/$cant_meses,1)." %"; ?></td>
					</tr>

			</tbody>

		</table>
	<?php } ?>
</div>

<?php 
		$sum_y = 0;
		$sum_x2 = 0;
		$sum_x=0;
		$sum_xy=0;

	for ($i=0; $i < $cant_meses ; $i++) { 
		$sum_y = $sum_y + $csi_mes_a[$i]['csi'];
		$sum_x2 =$sum_x2 + $csi_mes_a[$i]['x2'];
		$sum_x= $sum_x + ($i+1);
		$sum_xy= $sum_xy + $csi_mes_a[$i]['xy'];
	}


	$a0 = (($sum_y * $sum_x2) - ($sum_x * $sum_xy))/(($cant_meses * $sum_x2)-(($sum_x)*($sum_x)));

	$a1 = (($cant_meses * $sum_xy) - ($sum_x * $sum_y)) / (($cant_meses * $sum_x2) - (($sum_x)*($sum_x)));

	for ($i=0; $i < $cant_meses; $i++) { 
		$csi_mes_a[$i]['tendencia'] = $a0 + ($a1 * ($i+1));
	}

 ?>

<script src="js/highcharts/highcharts.js"></script>

<div id="container" class="<?php echo $class; ?>"></div>
 <script>
 	
 	Highcharts.chart('container', {

    title: {
        text: 'CSI DyV Acumulado Anual'
    },

    subtitle: {
        text: 'Source: thesolarfoundation.com'
    },

    xAxis: {
        categories: [

        	<?php
        	 $j = $mes_desde; 
        	 for ($i=0; $i < $cant_meses; $i++) { 
				echo "'".$mes_a[$j]['mes_res']."',";
				if ($j==12) {
					$j=1;
				}else{
					$j++;
				} 
			} ?>
        ],
        crosshair: true
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        // series: {
        //     pointStart: 2010
        // }
    },

    series: [{
        name: 'CSI',
        data: [
        		<?php
        	  	 	for ($i=0; $i < $cant_meses; $i++) { 
					echo $csi_mes_a[$i]['csi'].",";
					}
				?>
			]
    }, {
        name: 'Tendencia',
        data: [
				<?php
        	  	 	for ($i=0; $i < $cant_meses; $i++) { 
					echo $csi_mes_a[$i]['tendencia'].",";
					}
				?>
         	]
    }]

});
 </script>