<?php 
@session_start();
$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
?>

<table class="listado_gestoria">
	<colgroup>
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="20%">
			<col width="20%">
			<col width="10%">
			<col width="15%">
			<col width="5%">
			
	</colgroup>
	<thead>
		<tr>
			<td>Nro Rva.</td>
			<td>Fecha</td>
			<td>Nro Un.</td>
			<td>Venta</td>
			<td>Modelo / Versión</td>
			<td>Cliente en Reserva</td>
			<td>Asesor</td>
			<td style="color: yellow;">Cliente Asignación</td>
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

	$SQL="SELECT * FROM view_reservas_nro_unidad_duplicadas";
	$duplicadas= mysqli_query($con, $SQL);

	while ($reserva=mysqli_fetch_array($duplicadas)) { $fila++; $libre = '';

			$SQL="SELECT  * FROM view_reserva_control_duplicadas_nrounidad WHERE nro_unidad = '".$reserva['nro_unidad']."'";
			$resf=mysqli_query($con, $SQL);

			$SQL="SELECT cliente FROM asignaciones WHERE nro_unidad = ".$reserva['nro_unidad'];
			$unidades = mysqli_query($con, $SQL);

			$unidad = mysqli_fetch_array($unidades);

			echo "<tr><td></td></tr>";

				while ($regf=mysqli_fetch_array($resf)) {?>

					<tr class="">
						<td class="centrar-texto "><?php echo $regf['nro_reserva']; ?></td>
						<td class="centrar-texto "><?php echo cambiarFormatoFecha($regf['fecha']); ?></td>
						<td class="centrar-texto "><?php echo $regf['nro_unidad']; ?></td>
						<td class="centrar-texto "><?php echo $regf['compra']; ?></td>
						<?php 
							if ($regf['compra']=='Nuevo') { ?>
								<td class="celda-espacio-left "><?php echo $regf['modelo']." ".$regf['version']; ?></td>
						<?php }else{ ?>
								<td class="celda-espacio-left "><?php echo $unidad['detalleu']. " Dominio: ".$unidad['dominiou']; ?></td>
						<?php } ?>

						<td class="celda-espacio-left "><?php echo $regf['cliente']; ?></td>
						<td class="centrar-texto  "><?php echo $regf['asesor']; ?></td>
						<td class="centrar-texto  "><?php echo $unidad['cliente']; ?></td>
						<td class="centrar-texto" ><a target="_blank" href="<?php echo "../ventas/web/reserva.php?IDrecord=".$regf['nro_reserva']; ?>"><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $regf['nro_reserva']; ?>" class="icon-search-plus"></span></a></td>
					</tr>

				<?php }

	} ?>

	</tbody>
</table>
<script src="js/app.js"></script>