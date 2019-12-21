<?php 
 function dameFecha($fecha,$dia)
{   list($day,$mon,$year) = explode('/',$fecha);
    return date('d',mktime(0,0,0,$mon,$day+$dia,$year));        
}
 function dameFecha_dos($fecha,$dia)
{   list($day,$mon,$year) = explode('/',$fecha);
    return date('Y-m-d',mktime(0,0,0,$mon,$day+$dia,$year));        
}

		include_once("funciones/func_mysql.php");
		conectar();
		mysqli_query($con,"SET NAMES 'utf8'");
		extract($_POST);

 ?>

<table class="listado_gestoria ancho-90 tabla-agenda-entrega">
	<colgroup>
			<col width="10%">
			<col width="12%">
			<col class="columna" width="12%">
			<col width="12%">
			<col width="12%">
			<col width="12%">
			<col width="12%">
			<col width="12%">

	</colgroup>
	<?php 
		$dias = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
		$dia=date("w");
		$nro_dia = date('d');
		$nro_mes = date('m');
		$nro_año= date('Y');
		$fecha=$nro_dia.'/'.$nro_mes.'/'.$nro_año;
		$i = 0;
	 ?>
	 
	<thead>
		<tr>
			<td> Horario</td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
			<td> <?php echo $dias[$dia].' '.dameFecha($fecha,$i++); $dia++; if (($dia)>6) { $dia=0;}; ?></td>
		</tr>
	</thead>
	<tbody class="lista-unidades">

	<?php 

	$SQL="SELECT * FROM entregas_estados_unidad WHERE activo = 1";
	$estados=mysqli_query($con, $SQL);
	$estados_a[0]['estado_unidad']= '**/ Sin Estado /**';
	$estados_a[0]['color']= '#E6E6E6';
	$i=1;
	while ($estado=mysqli_fetch_array($estados)) {
		$estados_a[$estado['id_estado_entrega']]['estado_unidad']= $estado['estado_unidad'];
		$estados_a[$estado['id_estado_entrega']]['color']= $estado['color'];
		$i++;
	}

	$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
	$usuarios=mysqli_query($con, $SQL);
	$usuario_a[1]['nombre']= '-';
	$i=1;
	while ($usuario=mysqli_fetch_array($usuarios)) {
		$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
		$i++;
	}

	$SQL="SELECT * FROM grupos WHERE activo = 1";
	$grupos=mysqli_query($con, $SQL);
	$grupo_a[]['grupo']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
		$i++;
	}

	$SQL="SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL);
	$modelos_a[]['modelo']= '-';
	$i=1;
	while ($modelo=mysqli_fetch_array($modelos)) {
		$modelo_a[$modelo['idmodelo']]['modelo']= $modelo['modelo'];
		$i++;
	}

	$SQL="SELECT * FROM agenda_entregas_horarios WHERE activo = 1";
	$horarios= mysqli_query($con, $SQL);

	$fila=0;
	
	while ( $horario=mysqli_fetch_array($horarios)) { $fila++; $libre = '';?>

		<tr class="<?php echo 'fila_'.$fila; ?>" style="height: 90px;">

			<td class="centrar-texto celda" data-id="<?php ; ?>" style="font-size: 14px; font-weight: bold;"><?php echo $horario['horario']; ?></td>

			<?php

				$dia=date("w");
				for ($i=0; $i <= 6; $i++) { ?>

				<?php 

					//$fecha = date("Y-d-m", strtotime("$fecha"));
			
					$SQL="SELECT * FROM agenda_entregas_turnos WHERE id_sucursal = ".$id_sucursal." AND fecha = '".dameFecha_dos($fecha, $i)."' AND id_horario = ".$horario['id'];
					//$SQL="SELECT * FROM agenda_entregas_turnos WHERE fecha = '".$fecha."' AND id_horario = ".$horario['id'];
					$turnos=mysqli_query($con, $SQL);
					$turno = mysqli_fetch_array($turnos);
					$cant_turnos = mysqli_num_rows($turnos);
					if ($cant_turnos>0) {
						$color_fondo = 'background: #FAD7D7';
					}else{
						$color_fondo = '';
					}
				 ?>

				 <?php if ($cant_turnos>0): ?>

						<?php 
							$SQL="SELECT * FROM asignaciones WHERE nro_unidad = ". $turno['nro_unidad'];
							$unidades = mysqli_query($con, $SQL);
							$unidad = mysqli_fetch_array($unidades);

							if ($unidad['cancelada']==1) {
								$color_fondo = 'background: #58FA58';
							}
						 ?>
				<?php endif ?>

				<?php if ($dia==0 OR ($dia==6 AND $horario['dias_fuera_horario']==1)) {
					$celda_turno = "";
					$color_fondo = 'background: #C8C4C4';
				}else{
					$celda_turno = 'celda_turno';
					}

				$dia++;
				if (($dia)>6) { $dia=0;};

				?>
				
				<td style="<?php echo $color_fondo; ?>" class="<?php echo $celda_turno." ".$dia; ?>" data-idturno="<?php echo $turno['id']; ?>" data-nrounidad="<?php echo $turno['nro_unidad']; ?>" data-horario="<?php echo $horario['horario']; ?>" data-turno="<?php echo $horario['id']?>" data-fecha="<?php echo dameFecha_dos($fecha, $i); ?>">
					<?php if ($cant_turnos>0): ?>

						<!-- <?php 
							$SQL="SELECT * FROM asignaciones WHERE nro_unidad = ". $turno['nro_unidad'];
							$unidades = mysqli_query($con, $SQL);
							$unidad = mysqli_fetch_array($unidades);

						 ?> -->

						<table class="ancho-90" style='border: inset 0pt' >
							<tr style='border: inset 0pt'>
<!-- 							 	<td>Interno.:</td> -->
								<td  style='border: inset 0pt'><?php echo 'Interno: '.$unidad['interno']; ?></td>
							</tr> 
							<tr style='border: inset 0pt'><!-- <td>Vehículo:</td> -->
								<td style='border: inset 0pt'><?php echo $grupo_a[$unidad['id_grupo']]['grupo'].' '.$modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
							</tr>
							<tr style='border: inset 0pt'>
																<!-- <td>Cliente:</td> -->
								<td style='border: inset 0pt'><?php echo $unidad['cliente']; ?></td>
							</tr>
							<tr style='border: inset 0pt'>
							<!-- 	<td>Asesor:</td> -->
								<td style='border: inset 0pt'><?php echo $usuario_a[$unidad['id_asesor']]['nombre']; ?></td>
							</tr>
							<tr style='border: inset 0pt'>
							<!-- 	<td>Asesor:</td> -->
							<?php  $color_estado = $estados_a[$unidad['id_estado_entrega']]['color']; ?>
								<td style="<?php echo 'background: '.$color_estado.'; text-align:center; font-weight: bold;'; ?>"><?php echo $estados_a[$unidad['id_estado_entrega']]['estado_unidad']; ?></td>
							</tr>
						</table>
					<?php endif ?>
				</td>
				
			<?php  } ?>

		</tr>

	<?php } ?>

	</tbody>
</table>

<script src="js/entregas_turnos.js"></script>