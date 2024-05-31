<?php
  set_time_limit(300);
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
	// $sucursal_a[0]['sucres']= '-';
	// $i=0;
	while ($sucursal=mysqli_fetch_array($sucursales)) {
		$sucursal_a[$sucursal['idsucursal']]= $sucursal['sucres'];
		// $i++;
	}
	//fin de carga de sucursales
	//
	//
	// $SQL="SELECT * FROM usuarios WHERE idperfil = 3";
	// $usuarios=mysqli_query($con, $SQL);
	// $usuario_a[1]['nombre']= '-';
	// $i=1;
	// while ($usuario=mysqli_fetch_array($usuarios)) {
	// 	$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
	// 	$i++;
	// }

	$SQL="SELECT * FROM grupos";
	// $SQL="SELECT * FROM grupos WHERE activo = 1";
	$grupos=mysqli_query($con, $SQL);
	$grupo_a[]['grupo']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
		$i++;
	}

	$SQL="SELECT * FROM modelos";
	// $SQL="SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL);
	$modelos_a[]['modelo']= '-';
	$i=1;
	while ($modelo=mysqli_fetch_array($modelos)) {
		$modelo_a[$modelo['idmodelo']]['modelo']= $modelo['modelo'];
		$i++;
	}

?>
<div class="resultado_busqueda">
<table class="listado_gestoria listado_asignacion">
			<colgroup>
					<col width="2.5%">
					<col width="3.5%">
					<col width="2%">
					<col width="3%">
					<col width="3%">
					<col width="3.5%">
					<col width="3.5%">
					<col class="fila-grupo " width="12%">
					<col width="3%">
					<col width="2%">
					<col width="2%">
					<col width="10%">
					<col width="6%">
					<col width="3.5%">
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
				<td class="fila-grupo ">Modelo</td>
				<td>Chasis</td>
				<td>Asignado</td>
				<td>Destino</td>
				<td>Cliente</td>
				<td>Asesor</td>
				<td>Fec. Ent.</td>
			</tr>
		</thead>
<?php




		while ($unidad=mysqli_fetch_array($unidades)) { 
			// Formatear la fecha de despacho
				$fec_despacho = new DateTime($unidad['fec_despacho']);
				$formatted_fec_despacho = $fec_despacho->format('d-m-y');

				// Formatear la fecha de arribo (si existe)
				$fec_arribo = !empty($unidad['fec_arribo']) ? new DateTime($unidad['fec_arribo']) : null;
				$formatted_fec_arribo = $fec_arribo ? $fec_arribo->format('d-m-y') : '';

				// Formatear la fecha de entrega (si existe)
				$fec_entrega = !empty($unidad['fec_entrega']) ? new DateTime($unidad['fec_entrega']) : null;
				$formatted_fec_entrega = $fec_entrega ? $fec_entrega->format('d-m-y') : '';			
			?>


		<tbody  style="page-break-inside: always;" class="lista-unidades listado_asignacion">
			<tr style="page-break-inside: always;" class="<?php echo 'fila_'.$fila.' '.$libre. ' '.$nc.' '.$por_caer_fc.' '.$atp; ?>">
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $mes_a[$unidad['id_mes']]['mes']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['año']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $formatted_fec_despacho; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $formatted_fec_arribo; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda fila-grupo " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_grupo']]['grupo'].' '.$modelo_a[$unidad['id_modelo']]['modelo']; ?></td>

				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $sucursal_a[$unidad['id_sucursal']]; ?></td>
				<td style="page-break-inside: always;" class="celda-espacio-left celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['cliente']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['asesor']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $formatted_fec_entrega; ?></td>
			</tr>

	<?php } ?>
	</tbody>



	</table>

	<script src="js/entregas.js"></script>
	<script src="js/entregadas.js"></script>