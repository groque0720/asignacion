
<div class="flexible justificar margen-arriba-10">

	<?php if ($mes_act==13 OR $mes_act == 7) {
		$ancho_tabla_01 = 'ancho-30';
		$ancho_tabla_02 = 'ancho-40';
		$estilo = "";

	}else{

		$ancho_tabla_01 = 'ancho-50';
		$ancho_tabla_02 = 'ancho-50';
		$estilo = "display:none;";

		} ?>

	<div class="<?php echo $ancho_tabla_01; ?> ">
		<div class="centrar-texto ancho-95 centrar-caja negrita flexible">
			<div class="ancho-95 centrar-texto">Plan Mensual Individual	</div>
			<div class="ancho-5"><a data-ano="<?php echo $ano; ?>"  data-idmes="<?php echo $mes_act; ?>" data-idasesor="<?php echo $id_asesor; ?>" id="cargar_plan_accion" href=""><span class="icon-mini-menu"></span></a></div>
		</div>

		<div id="tabla-plan-accion" >
			<?php include('pmi_lista_plan_de_accion.php'); ?>
		</div>

	</div>


	<div class="<?php echo $ancho_tabla_02 ?> ">
		<div class="centrar-texto ancho-95 centrar-caja negrita flexible">
			<div class="ancho-95 centrar-texto">Aspectos a Mejorar - ECT 5.0</div>
			<div class="ancho-5"><a data-ano="<?php echo $ano; ?>"  data-idmes="<?php echo $mes_act; ?>" data-idasesor="<?php echo $id_asesor; ?>" id="cargar_aspectos_mejorar" href=""><span class="icon-mini-menu"></span></a></div>
		</div>

		<div id="tabla-aspecto-mejorar" >
			<?php include('pmi_lista_aspectos_mejorar_ect.php'); ?>
		</div>
	</div>

	<div class="ancho-30" style="<?php echo $estilo; ?>">
		<div class="centrar-texto ancho-95 centrar-caja negrita flexible">
			<div class="ancho-95 centrar-texto">Evaluación de Desempeño</div>
			<!-- <div class="ancho-5"><a data-ano="<?php echo $ano; ?>"  data-idmes="<?php echo $mes_act; ?>" data-idasesor="<?php echo $id_asesor; ?>" id="cargar_evaluaciones" href=""><span class="icon-mini-menu"></span></a></div> -->
		</div>

		<div id="tabla-aspecto-mejorar" >
			<?php //include('pmi_lista_evaluaciones.php'); ?>



			<?php 
				$SQL="SELECT * FROM ect_view_devolucion_evaluaciones WHERE id_asesor_ect=".$id_asesor." AND id_mes =".$mes_act." AND ano = ".$ano;
				$res=mysqli_query($con, $SQL);
			 ?>

			<table class="margen-arriba-5">
				<colgroup>
					<col width="33.33%">
					<col width="33.33%">
					<col width="33.33%">
				</colgroup>
				<thead>
					<tr>
						<td>Evaluación</td>
						<td>Puntuación</td>
						<td>Máximo</td>
					</tr>
				</thead>
				<tbody>

					<?php while ($evaluacion = mysqli_fetch_array($res)) { ?>
							<tr style="border: none;"><td style=""></td></tr>
							<tr>
								<td class="celda-espacio-left">Objetivos</td>
								<td class="centrar-texto"><?php echo $evaluacion['objetivo']; ?></td>
								<td class="centrar-texto">100</td>
							</tr>
							<tr>
								<td class="celda-espacio-left">Factores</td>
								<td class="centrar-texto"><?php echo $evaluacion['factores']; ?></td>
								<td class="centrar-texto">140</td>
							</tr>
							<tr class="fondo-gris-1 negrita">
								<td class="celda-espacio-left">Puntaje Total</td>
								<td class="centrar-texto"><?php echo $evaluacion['objetivo']+$evaluacion['factores']; ?></td>
								<td class="centrar-texto">240</td>
							</tr>
							<tr style="border: none;"><td style="border: none;"></td></tr>
							<tr class="negrita">
								<td class="celda-espacio-left">Resultado Gral.</td>
								<td  class="centrar-texto" colspan="2"><?php echo $evaluacion['resultado']; ?> </td>
							</tr>
							<tr>
								<td class="celda-espacio-left" colspan="3"><?php echo $evaluacion['devolucion']; ?> </td>
							</tr>
					<?php } ?>		
				</tbody>

			</table>
		


		</div>

	</div>


</div>


<div class="margen-arriba-10">
	<div class="centrar-texto ancho-95 centrar-caja negrita">
		Aspectos a Mejorar CSI TASA - DYV 
	</div>

	<table class="ancho-100 margen-arriba-5">
		<colgroup>
			<col width="5%">
			<col width="5%">
			<col width="80%">
			<col width="5%">
			<col width="5%">
		</colgroup>
		<thead>
			<tr>
				<td>CSI</td>
				<td>Item</td>
				<td>Detalle</td>
				<td>Puntaje</td>
				<td>Ideal</td>
			</tr>
		</thead>
		<tbody>
		



			<?php 
				if ($mes_act==1) {
					$mes_ant = 12;
				}else{
					$mes_ant = $mes_act-1;
				}
			 ?>
			<tr style="border: none;"><td style="border: none;"></td></tr>

			<?php 
				$SQL="SELECT * FROM ect_view_csi_tasa_devolucion WHERE id_sucursal_ect=".$sucursal['id_sucursal']." AND id_mes =".$mes_ant." AND ano = ".$ano_ant;
				$csis_tasa= mysqli_query($con, $SQL);

				while ($csi_tasa = mysqli_fetch_array($csis_tasa)) { ?>
					<tr>
						<td class="centrar-texto"><?php echo 'TASA'; ?></td>
						<td class="centrar-texto"><?php echo $csi_tasa['item_tasa']; ?></td>
						<td >
							<div class="centrar-caja csi_devolucion" style="width: 98%; text-align: justify; font-size: 11.5px;">
								<?php echo $csi_tasa['devolucion']; ?>
							</div>
						</td>
						<td class="centrar-texto "><?php echo number_format($csi_tasa['puntaje'],2); ?></td>
						<td class="centrar-texto">96</td>
					</tr>
			<?php } ?>
			<tr style="border: none;"><td style="border: none;"></td></tr>
			<?php 
				$SQL="SELECT * FROM ect_view_csi_dyv_devolucion WHERE id_asesor_ect=".$id_asesor." AND id_mes =".$mes_ant." AND ano = ".$ano_ant;
				$csis_dyv= mysqli_query($con, $SQL);

				while ($csi_dyv = mysqli_fetch_array($csis_dyv)) { ?>
					<tr>
						<td class="centrar-texto"><?php echo 'DYV'; ?></td>
						<td class="centrar-texto"><?php echo $csi_dyv['item_dyv']; ?></td>
						<td >
							<div class="centrar-caja csi_devolucion" style="width: 98%; text-align: justify; font-size: 11.5px;">
								<?php echo $csi_dyv['devolucion']; ?>
							</div>
						</td>
						<td class="centrar-texto "><?php echo number_format($csi_dyv['puntaje'],2); ?></td>
						<td class="centrar-texto">96</td>
					</tr>
			<?php } ?>

		</tbody>

	</table>


</div>

<script src="js/plan_mensual_individual_cuerpo.js"></script>