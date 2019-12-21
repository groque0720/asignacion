<div class="titulo-modelo centrar-texto">
	<?php echo 'UNIDADES EN VIAJE' ?>
</div>
<?php

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

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

$SQL="SELECT * FROM grupos WHERE activo = 1 AND cerokilometro = 1 AND posicion>0 ORDER BY posicion";
$grupos=mysqli_query($con, $SQL); ?>

<table class="listado_gestoria listado_asignacion">
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
				<td>Destino</td>
				<td>Canc.</td>
				<td>Pte.</td>
				<td>Cliente</td>
				<td>Asesor</td>
				<td>Fec. Rva.</td>
			</tr>
		</thead>
<?php 
while ($grupo=mysqli_fetch_array($grupos)) {
	
	$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo=".$grupo['idgrupo']." ORDER BY posicion" ;
	$modelos=mysqli_query($con, $SQL);


	while ($modelo=mysqli_fetch_array($modelos)) {?>
		
		<?php 

		$SQL="SELECT * FROM view_asignaciones_enviaje WHERE entregada = 0 AND id_modelo = ". $modelo['idmodelo'] ." ORDER BY año, id_mes, nro_orden, nro_unidad";
		$unidades = mysqli_query($con, $SQL);
		
		while ($unidad=mysqli_fetch_array($unidades)) { ?>
		<tbody  style="page-break-inside: always;" class="lista-unidades listado_asignacion">
			<tr style="page-break-inside: always;" class="<?php echo 'fila_'.$fila.' '.$libre. ' '.$nc.' '.$por_caer_fc.' '.$atp; ?>">
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $mes_a[$unidad['id_mes']]['mes']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['año']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_despacho']); ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_arribo']); ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda fila-grupo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_grupo']]['grupo']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda fila-modelo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['chasis']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $color_a[$unidad['color_uno']]['color']." - ".$color_a[$unidad['color_dos']]['color']." - ".$color_a[$unidad['color_tres']]['color']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $color_a[$unidad['id_color']]['color']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $sucursal_a[$unidad['id_sucursal']]['sucres']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['cancelada']==1) { echo 'Si';}else{echo '-';} ?></td>
				<td style="page-break-inside: always;" class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php if ($unidad['patentada']==1) { echo 'Si';}else{echo '-';} ?></td>
				<td style="page-break-inside: always;" class="celda-espacio-left celda celda_cliente" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['cliente']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $usuario_a[$unidad['id_asesor']]['nombre']; ?></td>
				<td style="page-break-inside: always;" class="centrar-texto  celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_reserva']); ?></td>
			</tr>
	
	<?php } ?>
	</tbody>
<?php } ?>

<?php } ?>
	</table>
<style>
	
	@page {
		size: A4 landscape;
	}
	table {page: rotada; page-break-before: right;}

</style>