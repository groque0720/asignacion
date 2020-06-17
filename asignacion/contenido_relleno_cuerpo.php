<?php
@session_start();
$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
$clase_don_vargas = '';

if ($_SESSION["id"]==47) {
	$clase_don_vargas = 'ocultar-don-vargas-tablet';
}

?>

<table class="listado_gestoria">
	<colgroup>
			<col width="2.5%">
			<col width="3.5%">
			<col class="<?php echo $clase_don_vargas; ?>" width="2%">
			<col width="3.3%">
			<col width="3%">
			<col width="3.5%">
			<col width="3.5%">
			<col class="fila-grupo fila-oculto" width="4%">
			<col class="fila-modelo fila-oculto" width="10%">
			<col class="<?php echo $clase_don_vargas; ?>" width="3%">
			<col class="<?php echo $clase_don_vargas; ?>" width="9%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col class="<?php echo $clase_don_vargas; ?>" width="2%">
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
			<td class="<?php echo $clase_don_vargas; ?>" >Año</td>
			<td>Nro Orden</td>
			<td>Interno</td>
			<td>Fec. Desp.</td>
			<td>Fec. Arribo</td>
			<td class="fila-grupo fila-oculto">Modelo</td>
			<td class="fila-modelo fila-oculto">Versión</td>
			<td class="<?php echo $clase_don_vargas; ?>">Chasis</td>
			<td class="<?php echo $clase_don_vargas; ?>">Colores</td>
			<td>Asignado</td>
			<td>Dest./Ub.</td>
			<td>Canc.</td>
			<td class="<?php echo $clase_don_vargas; ?>" >Ant.</td>
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
			if ($unidad['estado_reserva']==1 AND $unidad['cancelada']==0 AND $unidad['fec_limite']!='' AND $unidad['fec_limite']!= null) {
				$fecha=$unidad['fec_limite'];
				$dias=(strtotime($fecha) - strtotime('now'))/86400;
				// $dias = floor($dias);
				if ($dias<=0) {
					$por_caer_fc='unidad-falta-cancelar ';
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

			if ($unidad['id_estado_entrega']==6) {

				if ($unidad['reservada']==0) {

					$sinestrada = "background: yellow; color: #1C9504; font-weight: bold;";

				}else{

					if ($unidad['estado_reserva']==1) {
						$sinestrada = "background: yellow; color: #444444;";
					}else{
						$sinestrada = "background: yellow; color: red;";
					}

				}

			}else{
				$sinestrada = "";
			}


			if ($unidad['color_uno'] == 19 or $unidad['color_uno'] == 23) {

				$pago_tasa = 'background: orange;';

			}else{

				$pago_tasa = '';

			}

			if ($unidad['color_uno'] == 20) {
				$tasa_cero = 'background: #a8b29f;';
			}else{
				$tasa_cero = '';
			}

			if ($unidad['no_disponible'] == 1) {
				// $color_no_disponible = 'background: #F9A5FA;';
				$color_no_disponible = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #F3C5DD 10px,
			      #F3C5DD 15px
			    )";
			}else{
				$color_no_disponible = '';
			}

			if ($unidad['libre_condicionada'] == 1 AND $unidad['no_disponible'] != 1) {
				// $color_no_disponible = 'background: #F9A5FA;';
				$libre_condicionada = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #A4FABC 10px,
			      #A4FABC 15px
			    )";
			}else{
				$libre_condicionada = '';
			}


			if ( ($unidad['id_grupo'] == 17 or $unidad['id_grupo']== 7) AND $unidad['estado_reserva']== 0 AND substr($unidad['chasis'], 0, 1) == 'K') {

				$chasis_k = 'background: linear-gradient(to right, rgba(145,232,66,1) 0%, rgba(232,235,221,1) 100%)';

			}else{

				$chasis_k = '';

			}



			$dias = '';


			if ($unidad['fec_arribo']<>'') {
				$dias = ((strtotime($unidad['fec_arribo'])-strtotime(date("Y/m/d"))))/86400;
				$dias = abs($dias);
				$dias = floor($dias);
			}else{
				$dias = '-';
			}


			//$dias = $unidad['fec_arribo']->diff(date("Y/m/d"));



		 ?>

		<tr class="<?php echo 'fila_'.$fila.' '.$entregada.' '.$libre. ' '.$nc.' '.$por_caer_fc.' '.$atp; ?>" style="<?php echo $pago_tasa.' '.$tasa_cero.' '.$sinestrada.' '.$chasis_k.' '.$color_no_disponible.' '.$libre_condicionada; ?>">
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $mes_a[$unidad['id_mes']]['mes']; ?></td>

			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['año']; ?></td>


			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_despacho']); ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_arribo']); ?></td>
			<td class="centrar-texto celda fila-grupo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_grupo']]['grupo']; ?></td>
			<td class="centrar-texto celda fila-modelo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['chasis']; ?></td>
			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>">
				<?php if ($unidad['no_disponible'] == 1) {
					echo "<span style='color: red;font-weight: bold; font-size: 12px;'>// No Disponible //</span>";
					}
				 ?>
				 <?php if ($unidad['libre_condicionada'] == 1 AND $unidad['no_disponible'] != 1) {
					echo "<span style='color: green;font-weight: bold; font-size: 12px;'>// Precio Junio//</span>";
					}
				 ?>
				<?php echo $color_a[$unidad['color_uno']]['color']." - ".$color_a[$unidad['color_dos']]['color']." - ".$color_a[$unidad['color_tres']]['color']; ?></td>
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
			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $dias; ?></td>
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
<script src="js/app.js"></script>