<?php 
@session_start();
$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
?>

<table class="listado_gestoria">
	<colgroup>
			<col width="8%">
			<col width="8%">
			<col width="8%">
			<col width="8%">
			<col width="25%">
			<col width="25%">
			<col width="10%">
			<col width="5%">
			
	</colgroup>
	<thead>
		<tr>
			<td>Nro Rva.</td>
			<td>Fecha</td>
			<td>Nro Un.</td>
			<td>Venta</td>
			<td>Modelo / Versión</td>
			<td>Cliente</td>
			<td>Asesor</td>
			<td>Ir</td>
		</tr>
	</thead>
	<tbody class="lista-unidades">
	
	<?php 

	$fila=0;
	while ( $unidad=mysqli_fetch_array($unidades)) { $fila++; $libre = '';?>

		<tr class="">
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_reserva']; ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fecha']); ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo 'Sin Nro'; ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['compra']; ?></td>
			<?php 
				if ($unidad['compra']=='Nuevo') { ?>
					<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['modelo']. " ".$unidad['version']; ?></td>
			<?php }else{ ?>
					<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['detalleu']. " Dominio: ".$unidad['dominiou']; ?></td>
			<?php } ?>

			<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['cliente']; ?></td>
			<td class="centrar-texto  " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['asesor']; ?></td>
			<td class="centrar-texto" ><a target="_blank" href="<?php echo "../ventas/web/reserva.php?IDrecord=".$unidad['nro_reserva']; ?>"><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $unidad['nro_reserva']; ?>" class="icon-search-plus"></span></a></td>
		</tr>

	<?php } ?>

	</tbody>
</table>
<script src="js/app.js"></script>