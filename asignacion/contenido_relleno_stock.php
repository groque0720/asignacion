<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

 ?>

<table class="listado_gestoria print_stock" WORD-BREAK:BREAK-ALL>
	<colgroup>
			<col width="20%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
	</colgroup>
	<thead>
		<?php 
			$SQL="SELECT * FROM meses ORDER BY idmes";
			$meses = mysqli_query($con, $SQL);
			$i=1;
			$m_a = (int)date("n");
			$a_a=(int)date("y");
			while ($mes=mysqli_fetch_array($meses)) {
				$mes_a[$i]['mesres']=$mes['mes_res'];
				$i++;
			}
		 ?>

		<tr>

			<td rowspan="2">Modelos</td>
				<?php 
					for ($i=0; $i < 9 ; $i++) {
						if ($m_a>12) {
							$m_a=$m_a-12;
							$a_a++;
						}
				?>
			<td colspan="2"><?php echo $mes_a[$m_a]['mesres'].' '.$a_a; ?></td>
				<?php $m_a++;} ?>
			<td colspan="2">Total</td>
		</tr>

		<tr>
			<?php 
			for ($i=0; $i < 10; $i++) { ?>
				<td>Stock</td>
				<td>Asig.</td>
			<?php }  ?>
		</tr>

	</thead>
	<tbody class="lista-unidades">
	
	<?php

		$ant_cant_modelo_t=0; //reinicio contador de asiganciones libre general
		$SQL="SELECT * FROM grupos WHERE posicion > 0 AND activo = 1 ORDER BY posicion";
		$grupos = mysqli_query($con, $SQL);
		while ($grupo=mysqli_fetch_array($grupos)) { ?>

			<tr >
				<td colspan="21" class="titulo-stock-modelo"><?php echo $grupo['grupo']; ?></td>
			</tr>

		<?php
			$SQL="SELECT * FROM modelos WHERE posicion > 0 AND activo = 1 AND idgrupo = ".$grupo['idgrupo']." ORDER BY posicion";
			$modelos=mysqli_query($con, $SQL);
			$t_m_m=0;
			$t_m_t_r=0;
			//reinicio  contadores (modelos mes)
			for ($i=0; $i < 9; $i++) { 
				$stock_total_mes[$i]['cant']=0;
			}
			for ($i=0; $i < 9; $i++) { 
				$stock_total_mes_r[$i]['cant']=0;
			}
				$ant_cant_modelo_m=0; //reinicio contador de asiganciones libre por modelo
				while ($modelo=mysqli_fetch_array($modelos)) {
					
					$m_a = (int)date("n");
					$a_a=(int)date("Y");
					$t_m_t=0; //total modelo tasa
					$ant_cant_modelo = 0;
					//Empieza FOR para Asignacion TASA
					for ($i=0; $i < 9 ; $i++) { 
						if ($m_a>12) {
							$m_a=$m_a-12;
							$a_a++;
						}
						$SQL="SELECT * FROM view_stock_tasa WHERE id_mes = $m_a AND año = $a_a AND id_modelo = ".$modelo['idmodelo'];
						$cantidad=mysqli_query($con, $SQL);
						$cant=mysqli_fetch_array($cantidad);
						$stock_tasa[$i]['cant']=$cant['cantidad'];
						$stock_total_mes[$i]['cant']=$stock_total_mes[$i]['cant']+$stock_tasa[$i]['cant'];//totales mesuales x modelos(grupos)
						$t_m_m=$t_m_m +$stock_tasa[$i]['cant'];
						$t_m_t=$t_m_t+$stock_tasa[$i]['cant'];
						$stock_gral_mes[$i]['cant']=$stock_gral_mes[$i]['cant']+$stock_tasa[$i]['cant'];
						$t_g_t=$t_g_t + +$stock_tasa[$i]['cant'];
						$m_a++;
					}

					$m_a = (int)date("n");
					$a_a=(int)date("Y");
					$t_m_r=0; //total modelo tasa
					for ($i=0; $i < 9 ; $i++) { 
						if ($m_a>12) {
							$m_a=$m_a-12;
							$a_a++;
						}

						//busco las unidades libres anteriores para suparle al stock del mes actual
						if ($i==0) {
							$SQL="SELECT SUM(cantidad) AS cantidad FROM view_stock_libre_anteriores WHERE id_mes < $m_a AND id_modelo = ".$modelo['idmodelo'];
							$cantidad=mysqli_query($con, $SQL);
							$cant=mysqli_fetch_array($cantidad);
							$ant_cant_modelo = $cant['cantidad'];
							$ant_cant_modelo_m = $ant_cant_modelo_m + $cant['cantidad'];
							$ant_cant_modelo_t = $ant_cant_modelo_t + $cant['cantidad'];
						}

						$SQL="SELECT * FROM view_stock_reservas WHERE id_mes = $m_a AND año = $a_a AND id_modelo = ".$modelo['idmodelo'];
						$cantidad=mysqli_query($con, $SQL);
						$cant=mysqli_fetch_array($cantidad);
						$stock_reserva[$i]['cant']=$cant['cantidad'];
						$stock_total_mes_r[$i]['cant']=$stock_total_mes_r[$i]['cant']+$stock_reserva[$i]['cant'];//totales mesuales x modelos(grupos)
						$t_m_r=$t_m_r + $stock_reserva[$i]['cant'];
						$t_m_t_r = $t_m_t_r+$stock_reserva[$i]['cant'];
						$stock_gral_mes_r[$i]['cant']=$stock_gral_mes_r[$i]['cant']+$stock_reserva[$i]['cant'];
						$t_g_r=$t_g_r +$stock_reserva[$i]['cant'];
						$m_a++;
					}
				
		?>
		<tr>
				<td class="stock-modelo"><?php echo $modelo['modelo']; ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[0]['cant'] - $stock_reserva[0]['cant'] + $ant_cant_modelo; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[0]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[1]['cant'] - $stock_reserva[1]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[1]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[2]['cant'] - $stock_reserva[2]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[2]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[3]['cant'] - $stock_reserva[3]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[3]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[4]['cant'] - $stock_reserva[4]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[4]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[5]['cant'] - $stock_reserva[5]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[5]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[6]['cant'] - $stock_reserva[6]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[6]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[7]['cant'] - $stock_reserva[7]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[7]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[8]['cant'] - $stock_reserva[8]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php $v=$stock_tasa[8]['cant']; if ($v!='' AND $v!=0) { echo $v;	}else{ echo '-';} ?></td>
				<td class="centrar-texto"><?php echo $t_m_t - $t_m_r + $ant_cant_modelo; ?></td>
				<td class="centrar-texto"><?php echo $t_m_t; ?></td>
		</tr>

		<?php } ?>
			<tr class="total-stock-modelo">
					<td ><?php echo 'TOTAL ACUMULADO X MES' ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[0]['cant'] - $stock_total_mes_r[0]['cant']+$ant_cant_modelo_m; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[0]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[1]['cant'] - $stock_total_mes_r[1]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[1]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[2]['cant'] - $stock_total_mes_r[2]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[2]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[3]['cant'] - $stock_total_mes_r[3]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[3]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[4]['cant'] - $stock_total_mes_r[4]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[4]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[5]['cant'] - $stock_total_mes_r[5]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[5]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[6]['cant'] - $stock_total_mes_r[6]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[6]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[7]['cant'] - $stock_total_mes_r[7]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[7]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[8]['cant'] - $stock_total_mes_r[8]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_total_mes[8]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $t_m_m-$t_m_t_r+$ant_cant_modelo_m; ?></td>
					<td class="centrar-texto"><?php echo $t_m_m; ?></td>
			</tr>
			<tr >
				<td colspan="21"><?php echo ''; ?></td>
			</tr>
		 <?php } //fin grupo
	 ?>

<tr class="total-stock-gral">
					<td ><?php echo 'TOTAL GRAL' ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[0]['cant'] - $stock_gral_mes_r[0]['cant']+$ant_cant_modelo_t; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[0]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[1]['cant'] - $stock_gral_mes_r[1]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[1]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[2]['cant'] - $stock_gral_mes_r[2]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[2]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[3]['cant'] - $stock_gral_mes_r[3]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[3]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[4]['cant'] - $stock_gral_mes_r[4]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[4]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[5]['cant'] - $stock_gral_mes_r[5]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[5]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[6]['cant'] - $stock_gral_mes_r[6]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[6]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[7]['cant'] - $stock_gral_mes_r[7]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[7]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[8]['cant'] - $stock_gral_mes_r[8]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $stock_gral_mes[8]['cant']; ?></td>
					<td class="centrar-texto"><?php echo $t_g_t-$t_g_r+$ant_cant_modelo_t; ?></td>
					<td class="centrar-texto"><?php echo $t_g_t; ?></td>
			</tr>





	</tbody>
</table>
