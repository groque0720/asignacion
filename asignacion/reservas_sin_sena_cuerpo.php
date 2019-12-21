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

		<tr class="">
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_reserva']; ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fecha_reserva']); ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
			<td class="centrar-texto " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['compra']; ?></td>
			<?php 
				if ($unidad['compra']=='Nuevo') { ?>
					<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_modelo']]['grupo']. " ".$modelo_a[$unidad['id_version']]['modelo']; ?></td>
			<?php }else{ ?>
					<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['detalleu']. " Dominio: ".$unidad['dominiou']; ?></td>
			<?php } ?>

			<td class="celda-espacio-left " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['cliente']; ?></td>
			<td class="centrar-texto  " data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $usuario_a[$unidad['id_asesor']]['nombre']; ?></td>
			<td class="centrar-texto" ><a target="_blank" href="<?php echo "../ventas/web/reserva.php?IDrecord=".$unidad['nro_reserva']; ?>"><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $unidad['nro_reserva']; ?>" class="icon-search-plus"></span></a></td>
		</tr>

	<?php } ?>

	</tbody>
</table>
<script src="js/app.js"></script>