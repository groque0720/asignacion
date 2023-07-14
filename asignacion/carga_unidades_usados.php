<?php

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();
$p=$_SESSION["idperfil"];
$es_gerente = $_SESSION["es_gerente"];
$id_usuario = $_SESSION["id"];
//cargo en arreglo los colores de la tabla
	$SQL="SELECT * FROM asignaciones_usados_colores ORDER BY color";
	$colores=mysqli_query($con, $SQL);
	$color_a[0]['color']= '-';
	$i=1;
	while ($color=mysqli_fetch_array($colores)) {
		$color_a[$color['id_color']]['color']= $color['color'];
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
	$por_a[]['grupo_res']= '-';
	$i=1;
	while ($grupo=mysqli_fetch_array($grupos)) {
		$por_a[$grupo['idgrupo']]['grupo_res']= $grupo['grupo_res'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_marcas";
	$usados_marcas=mysqli_query($con, $SQL);
	$marca_a[]['grupo']= '-';
	$i=1;
	while ($marca=mysqli_fetch_array($usados_marcas)) {
		$marca_a[$marca['id_marca']]['marca']= $marca['marca'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_modelos";
	$usados_modelos=mysqli_query($con, $SQL);
	$modelo_a[]['modelo']= '-';
	$i=1;
	while ($modelo=mysqli_fetch_array($usados_modelos)) {
		$modelo_a[$modelo['id_modelo']]['modelo']= $modelo['modelo'];
		$i++;
	}

	$SQL="SELECT * FROM asignaciones_usados_versiones";
	$usados_versiones=mysqli_query($con, $SQL);
	$version_a[]['grupo']= '-';
	$i=1;
	while ($version=mysqli_fetch_array($usados_versiones)) {
		$version_a[$version['id_version']]['version']= $version['version'];
		$i++;
	}

	if ($es_gerente !=1 ) {
		$ocultar='centrar-texto celda-usado fila-oculto';
	}else{
		$ocultar='centrar-texto celda-usado';
	}

?>

<div class="titulo-modelo derecha-texto">
	<input type="hidden" id="usado_activo" value='si'>
	<a href="usados_pdf.php" target="_blank"><span class="icon-print"> Imprimir Planilla Usados</span></a>
</div>

<table class="listado_gestoria">
	<colgroup>
			<col width="1.5%">
			<col width="1.5%">
			<col width="9%">
			<col width="1%">
			<col width="1.5%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col width="6.5%">
			<col width="3%">
			<col width="3%">
			<?php if ($es_gerente==1) {?>
				<col width="2%">
				<col width="2%">
				<col width="2%">
				<col width="2%">

			<?php } ?>
			<col width="2%">
			<col width="2%">
			<col width="2%">

			<?php if ($_SESSION['id']==47 || $_SESSION['id']==89): ?>
				<col width="2%">
			<?php endif ?>


			<col width="1.5%">
			<col width="1%">
			<col width="5%">
			<col width="3%">
			<?php if ($p==14) {?>
			<col width="1%">
			<?php } ?>

	</colgroup>
	<tbody class="lista-unidades">
		<?php

			$SQL="SELECT * FROM asignaciones_usados_estados ORDER BY posicion";
			$estado_usado = mysqli_query($con, $SQL);

			while ($estado=mysqli_fetch_array($estado_usado)) {
				// usuarios permitidos a ver otros estados de los usados
			  $user_permitidos = [1, 2, 11, 16, 27, 36, 41, 45, 46, 47, 49, 56, 72, 89, 94, 103, 106, 124, 135];
				// condicional para mostrar otros estados segun usuarios permitidos.
			  if ( $estado['id_estado_usado'] == 1 or $estado['id_estado_usado'] == 4  or in_array($id_usuario,$user_permitidos) ) {

				$SQL="SELECT *, DATEDIFF(DATE(NOW()),fec_recepcion)as ant FROM asignaciones_usados WHERE entregado = 0 AND id_estado =".$estado['id_estado_usado']." ORDER BY vehiculo";
				$usados=mysqli_query($con, $SQL);
				$cant=mysqli_num_rows($usados);
				$sumatoria_precio_venta = 0;
				if ($cant>0) {?>

					<thead>
						<tr class="titulo-estado-usado"><td colspan="21"> <?php echo $estado['estado_usado']; ?></td></tr>
						<tr>
							<td>Nro</td>
							<td>Int.</td>
							<td>Marca - Modelo - Versión</td>
							<td>Por</td>
							<td>Año</td>
							<td>KM</td>
							<td>Dominio</td>
							<td>Color</td>
							<td>Ult. Dueño</td>
							<td>Asesor T.</td>
							<td>Recep / Ant.</td>
							<?php if ($es_gerente == 1) {?>
								<td>Toma + Imp</td>
								<td>Costo Cont.</td>
								<td>Costo Rep.</td>
								<td>$ Info</td>
							<?php } ?>
							<td>$ Transf.</td>
							<td>$ Venta</td>
							<td>$ Contado</td>

							<?php if ($_SESSION['id']==47 || $_SESSION['id']==89): ?>
								<td>$ 0km</td>
							<?php endif ?>


							<td>Suc.</td>
							<td>Canc.</td>
							<td>Cliente</td>
							<td>Asesor</td>
							<?php if ($p==14) {?>
								<td><span class="icon-delete"></span></td>
							<?php } ?>

						</tr>
					</thead>
				<?php }
				$fila=0;
				while ($usado=mysqli_fetch_array($usados)) {$fila++; $libre = '';?>

				<?php $sumatoria_precio_venta = $sumatoria_precio_venta + $usado['precio_venta']; ?>

				<?php if ($usado['reservada']==0) {
					$libre = 'unidad-libre';
				}else{
					$libre = '';
				} ?>

				<?php if ($usado['reservada']==1 AND $usado['estado_reserva']==0) {
					$nc = 'unidad-reservada-nc';
				}else{
					$nc = '';
				} ?>

				<?php
					$sin_cancelar = '';
					$dias = 0; ?>

				<?php if ($usado['reservada']==1 AND $usado['estado_reserva']==1 and $usado['fecha_cancelacion'] == null) {
					$fecha_res=$usado['fec_reserva'];
					$dias=(strtotime($fecha_res)-strtotime('now'))/86400;
					$dias = abs(floor($dias));
					if ($dias>=10) {
						$sin_cancelar='background:#8E8EF7; ';
					}
				}?>
				<?php

					if ($usado['ant']>=50) {
						$antiguedad = number_format(((int)$usado['ant']), 0, ',','.');
						$antiguedad_d = ' ('.$antiguedad.')';
						$antiguedad_color = 'background:#C8CBC2;';
					}else{
						$antiguedad = '';
						$antiguedad_d = $antiguedad;
						$antiguedad_color = '';
					}

				 ?>

					<tr class="<?php echo 'fila_'.$estado['id_estado_usado'].'_'.$fila.' '.$libre. ' '.$nc ?>" style="<?php echo $antiguedad_color.' '.$sin_cancelar;  ?>">
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $fila; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['interno']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['vehiculo']; ?> <span style="color: #f0f0f0;background: #efb810;"><?php if($usado['id_estado_certificado'] == 2) { echo '(**UCT**)'; } ?></span> </td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $por_a[$usado['por']]['grupo_res']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['año']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo number_format($usado['km'], 0, ',','.'); ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['dominio']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $color_a[$usado['color']]['color']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['ultimo_dueño']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usuario_a[$usado['asesortoma']]['nombre']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo cambiarFormatoFecha($usado['fec_recepcion'])."<b style='font-size: 10px; color:red;'>$antiguedad_d</b>"; ?></td>
						<td class="<?php echo $ocultar; ?>" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['toma_mas_impuesto'], 0, ',','.');?></td>
						<td class="<?php echo $ocultar; ?>" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['costo_contable'], 0, ',','.'); ?></td>
						<td class="<?php echo $ocultar; ?>" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['costo_reparacion'], 0, ',','.'); ?></td>
						<td class="centrar-texto celda-usado <?php echo $ocultar; ?>" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['precio_info'], 0, ',','.'); ?></td>

						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['transferencia'], 0, ',','.'); ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>" style="font-weight: bold; color: black;"><?php echo '$'.number_format($usado['precio_venta'], 0, ',','.'); ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo '$'.number_format($usado['precio_contado'], 0, ',','.'); ?></td>

							<?php if ($_SESSION['id']==47 || $_SESSION['id']==89): ?>
								<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>" style="font-weight: bold; color: black;"><?php echo '$'.number_format($usado['precio_0km'], 0, ',','.'); ?></td>
							<?php endif ?>

						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $sucursal_a[$usado['id_sucursal']]['sucres']; ?></td>

						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php
							if ($usado['reservada']==1) {
								if ($usado['fecha_cancelacion']==null) { echo 'No';}else{ echo 'Si';}
							}else{
								echo '-';
							} ?></td>

						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usado['cliente']; ?></td>
						<td class="centrar-texto celda-usado" data-id="<?php echo $usado['id_unidad']; ?>"><?php echo $usuario_a[$usado['id_asesor']]['nombre'];; ?></td>
						<?php if ($p==14) {?>
							<td class="centrar-texto" ><span data-fila="<?php echo 'fila_'.$estado['id_estado_usado'].'_'.$fila; ?>" data-id="<?php echo $usado['id_unidad']; ?>" class="icon-delete borrar-unidad"></span></td>
						<?php } ?>
					</tr>
				<?php } ?>
				<tr class="titulo-estado-usado">
					<td colspan="5"><?php echo $estado['estado_usado'].' : '.$cant.' Un.'; ?></td>
					<td colspan="5"><?php if($cant>0) { echo 'Total Precio de Venta:   $ '.number_format($sumatoria_precio_venta, 2, ',','.'); } ?></td>
					<td colspan="5"><?php if($cant>0) { echo 'Promedio de Precio de Venta:   $ '. number_format($sumatoria_precio_venta/$cant, 2, ',','.');} ?></td>
				</tr>
				<tr class="titulo-estado-usado"><td colspan="17"></td></tr>
			<?php
				}
			} ?>

	</tbody>
</table>

<script src="js/usados.js"></script>



