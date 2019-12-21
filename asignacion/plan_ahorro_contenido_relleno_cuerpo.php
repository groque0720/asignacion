<?php 
@session_start();
$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
?>

<table class="listado_gestoria">
	<colgroup>
			<col width="2.5%">
			<col width="3.5%">
			<col width="2%">
			<col width="3.3%">
			<col width="3%">
			<col width="3.5%">
			<col width="3.5%">
			<col class="fila-grupo fila-oculto" width="4%">
			<col class="fila-modelo fila-oculto" width="10%">

			<col width="3%">
			<col width="9%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col width="7%">
			<col width="4%">
			<col width="3.5%">
			<?php if ($p==14) {?>
			<col width="1%">
			<?php } ?>
			
			
	</colgroup>
	<thead>
		<tr>
			<td>Nro Un.</td>
			<td>Mes</td>
			<td>Año</td>
			<td>Nro Orden</td>
			<td>Interno</td>
			<td>Fec. Desp.</td>
			<td>Fec. Arribo</td>
			<td class="fila-grupo fila-oculto">Modelo</td>
			<td class="fila-modelo fila-oculto">Versión</td>

			<td>Chasis</td>
			<td>Colores</td>
			<td>Asignado</td>
			<td>Dest./Ub.</td>
			<td>Canc.</td>
			<td>Pte.</td>
			<td>Cliente</td>
			<td>Asesor</td>
			<td>Fec. Rva.</td>
			<?php if ($p==14) {?>
				<td><span class="icon-delete"></span></td>
			<?php } ?>
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

	$fila=0;
	while ( $unidad=mysqli_fetch_array($unidades)) { $fila++; $libre = '';?>

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

		<tr class="<?php echo 'fila_'.$fila.' '.$entregada.' '.$libre. ' '.$nc.' '.$por_caer_fc.' '.$atp; ?>">
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $mes_a[$unidad['id_mes']]['mes']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['año']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_despacho']); ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_arribo']); ?></td>
			<td class="centrar-texto celda fila-grupo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_grupo']]['grupo']; ?></td>
			<td class="centrar-texto celda fila-modelo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['chasis']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $color_a[$unidad['color_uno']]['color']." - ".$color_a[$unidad['color_dos']]['color']." - ".$color_a[$unidad['color_tres']]['color']; ?></td>
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
			
			<td class="centrar-texto celda" style="font-weight: bold;font-size: 12px;" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['cancelada']==1) { echo 'Si';}else{echo '-';} ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['patentada']==1) { echo 'Si';}else{echo '-';} ?></td>
			<td class="celda-espacio-left celda celda_cliente" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $cliente; ?></td>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $usuario_a[$unidad['id_asesor']]['nombre']; ?></td>
			<td class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_reserva']); ?></td>
			<?php if ($p==14) {?>
				<td class="centrar-texto" ><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $unidad['id_unidad']; ?>" class="icon-delete borrar-unidad"></span></td>
			<?php } ?>
		</tr>

	<?php } ?>

	</tbody>
</table>
<script src="js/plan_ahorro_app.js"></script>