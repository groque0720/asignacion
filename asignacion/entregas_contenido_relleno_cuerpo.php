<input type="hidden" id="es_entrega" value="es_entrega" >
<div class="titulo-modelo" style="display:flex;">

	<div class="ancho-50">
		SECTOR ENTREGAS DE UNIDADES
	</div>

	<div class="ancho-50" style="text-align: right;">

		<form class="form_orden" action="entregas_planilla.php" method="POST" target="_blank">
			<input type="hidden" name="sql" id="sql" value="<?php echo $SQL ?>">
			<span class="icon-print">
			  <input style="background:white; color:#b63b4d; border: none; font-weight:bold; cursor:pointer;" type="submit" value="Imprimir Planilla">
			 </span>
		</form>



	</div>

</div>
<table class="listado_gestoria">
	<colgroup>
			<col width="1%">
			<col width="1.5%">
			<col width="2%">
			<col width="2%">
			<col width="10.5%">
			<col width="3%">
			<col width="3%">
			<col width="2%">
			<col width="4%">
			<col width="1.5%">
			<col width="6%">
			<col width="3%">
			<col width="3%">
			<col width="2%">
			<col width="5%">
			<col width="10%">


	</colgroup>
	<?php
		$addclass = 'ordenar-entregas icon-chevron-down';
		$addclassselect = $addclass.' orden-seleccionado';
	 ?>
	<thead>
		<tr>
			<td>Nro</td>
			<td class="<?php if ($orden=='interno') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="interno"><span class="">Interno</span></td>
			<td >Nro Orden</td>
			<td >Chasis</td>
			<td class="<?php if ($orden=='grupo, modelo') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="grupo, modelo" >Modelo Versión</td>
			<td>Llegó</td>
			<td>Color</td>
			<td>Suc.</td>
			<td class="<?php if ($orden=='id_ubicacion_entrega DESC') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="id_ubicacion_entrega DESC" >Ubic.</td>
			<td >Canc.</td>
			<td>Cliente</td>
			<td class="<?php if ($orden=='asesor') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="asesor">Asesor</td>
			<td class="<?php if ($orden=='fec_pedido DESC') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="fec_pedido DESC">Pedido</td>
			<td>Hora</td>
			<td class="<?php if ($orden=='id_estado_entrega DESC') {echo $addclassselect;}else{ echo $addclass;} ?>" data-id="id_estado_entrega DESC">Estado</td>
			<td>Observación</td>
		</tr>
	</thead>
	<tbody class="lista-unidades">



	<?php
	//cargo en un arreglo todos los meses que ocuparia en la tabla.
		$SQL="SELECT * FROM meses";
		$meses=mysqli_query($con, $SQL);
		$i=1;
		while ($mes=mysqli_fetch_array($meses)) {
			$mes_a[$i]['mes']= $mes['mes'];
			$i++;
		}
	//fin de carga de meses.
	//
	//cargo en arreglo los colores de la tabla
	$SQL="SELECT * FROM colores ORDER BY color";
	$colores=mysqli_query($con, $SQL);
	$color_a[0]['color']= '-';
	$i=1;
	while ($color=mysqli_fetch_array($colores)) {
		$color_a[$color['idcolor']]['color']= $color['color'];
		$i++;
	}
	//fin de carga de colores
	//
	//	cargo los destinos de unidad
	$SQL="SELECT * FROM sucursales";
	$sucursales=mysqli_query($con, $SQL);
	$sucursal_a[0]['sucres']= '-';
	$i=1;
	while ($sucursal=mysqli_fetch_array($sucursales)) {
		$sucursal_a[$i]['sucres']= $sucursal['sucres'];
		$i++;
	}
	//fin de carga de sucursales
	//
	//
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

	$SQL="SELECT * FROM entregas_ubicaciones WHERE activo = 1";
	$ubicaciones=mysqli_query($con, $SQL);
	$ubicaciones_a[0]['ubicacion_entrega']= '-';
	$i=1;
	while ($ubicacion=mysqli_fetch_array($ubicaciones)) {
		$ubicaciones_a[$ubicacion['id_ubicacion_entrega']]['ubicacion_entrega']= $ubicacion['ubicacion_entrega'];
		$i++;
	}

	$SQL="SELECT * FROM entregas_estados_unidad WHERE activo = 1";
	$estados=mysqli_query($con, $SQL);
	$estados_a[0]['estado_unidad']= '-';
	$i=1;
	while ($estado=mysqli_fetch_array($estados)) {
		$estados_a[$estado['id_estado_entrega']]['estado_unidad']= $estado['estado_unidad'];
		$estados_a[$estado['id_estado_entrega']]['color']= $estado['color'];
		$i++;
	}

	$fila=0;
	while ($unidad=mysqli_fetch_array($unidades)) { $fila++; $libre = '';?>

		<?php if ($unidad['reservada']==0) {
			$libre = 'unidad-libre';
		}else{
			$libre = '';
		} ?>

		<?php if ($unidad['reservada']==1 AND $unidad['estado_reserva']==0) {
			$nc = 'unidad-reservada-nc';
		}else{
			$nc = '';
		} ?>

		<?php
		$dias='';
		$por_caer_fc='';
			if ($unidad['cancelada']==0 AND $unidad['fec_limite']!='' AND $unidad['fec_limite']!= null) {
				$fecha=$unidad['fec_limite'];
				$segundos=strtotime($fecha) - strtotime('now');
				$dias=intval($segundos/60/60/24);
				if ($dias<=3) {
					$por_caer_fc='unidad-falta-cancelar';
				}
			}
		?>


		<?php
			$atp = '';
			if ($unidad['id_negocio']==2) {
				$atp='unidad-atp';
			}

			$entregada='';
			if ($unidad['entregada']==1) {
				$entregada='entregada';
			}


			$largo_cliente=strlen ($unidad['cliente']);
			if ($largo_cliente>=28) {
				$cliente = substr($unidad['cliente'], 0, 17)."..";
			}else{
				$cliente =$unidad['cliente'];
			}
		 ?>

		<tr class="<?php echo 'fila_'.$fila.' '.$atp; ?>">
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $fila; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['chasis']; ?></td>
			<td class="celda" style="padding-left: 3px;" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo "  ".$grupo_a[$unidad['id_grupo']]['grupo']." ".$modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_arribo']); ?></td>

			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['id_color']!='') {echo $color_a[$unidad['id_color']]['color'];}else{ echo '-';}  ?></td>
			<?php

				$destino=$unidad['id_sucursal'];
				$class_destino='';


				if ($unidad['fec_arribo']!='' AND $unidad['fec_arribo']!=null) {

					if ($unidad['id_sucursal']!='' AND $unidad['id_sucursal']!=0 AND $unidad['id_sucursal']!=$unidad['id_ubicacion']) {
						$class_destino='nodestino';
					} ?>

					<td class="<?php echo 'centrar-texto celda '.$class_destino;  ?> " data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['id_ubicacion']!='') { echo $sucursal_a[$unidad['id_ubicacion']]['sucres'];}else{ echo "-";} ?></td>

				<?php }else{?>

					<td class="<?php echo 'centrar-texto celda '.$class_destino;  ?> " data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['id_sucursal']!='') { echo $sucursal_a[$unidad['id_sucursal']]['sucres'];}else{ echo "-";} ?></td>

				<?php } ?>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $ubicaciones_a[$unidad['id_ubicacion_entrega']]['ubicacion_entrega']; ?></td>
			<td class="centrar-texto celda" style="font-weight: bold;font-size: 12px;" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['cancelada']==1) { echo 'Si';}else{echo '-';} ?></td>
			<td class="celda-espacio-left celda celda_cliente" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $cliente; ?></td>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $usuario_a[$unidad['id_asesor']]['nombre']; ?></td>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_pedido']); ?></td>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>">
				<?php echo cambiarFormatohora($unidad['hora_pedido']); ?></td>
			<?php  $color_estado ="";// $estados_a[$unidad['id_estado_entrega']]['color']; ?>
			<td style="<?php echo 'background: '.$color_estado; ?>" class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $estados_a[$unidad['id_estado_entrega']]['estado_unidad']; ?></td>
			<td class="celda" style="padding-left: 3px;" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['observacion']; ?></td>
		</tr>

	<?php } ?>

	</tbody>
</table>

<script src="js/entregas.js"></script>