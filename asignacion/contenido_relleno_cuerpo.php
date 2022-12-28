<?php
@session_start();

// script para cuando esta bloqueada la planilla
include('aa_cerrar_sesiones.php');


$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
$clase_don_vargas = '';

if ($_SESSION["id"]==47) {
	$clase_don_vargas = 'ocultar-don-vargas-tablet';
}

?>

<table class="listado_gestoria">
	<colgroup>
			<col width="2.5%" class="<?php echo $clase_don_vargas; ?>">
			<col width="3.5%">
			<col  width="2%" class="<?php echo $clase_don_vargas; ?>">
			<col width="3.3%">
			<col width="3%">
			<col width="3.5%">
			<col width="3.5%">
			<col class="fila-grupo fila-oculto" width="4%">
			<col class="fila-modelo fila-oculto" width="10%">
			<col width="3%">
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
			<td class="<?php echo $clase_don_vargas; ?>">Nro Un.</td>
			<td>Mes</td>
			<td class="<?php echo $clase_don_vargas; ?>" >Año</td>
			<td>Nro Orden</td>
			<td>Interno</td>
			<td>Fec. Desp.</td>
			<td>Fec. Arribo</td>
			<td class="fila-grupo fila-oculto">Modelo</td>
			<td class="fila-modelo fila-oculto">Versión</td>
			<td>Chasis</td>
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

		<?php if ($unidad['reservada']==1 AND ( $unidad['estado_reserva']==0 OR  $unidad['reserva']==0 )) {
			$nc = 'unidad-reservada-nc';
		}else{
			$nc = '';
		} ?>

		<?php
		$dias='';
		$por_caer_fc='';
			if ($unidad['estado_reserva']==1 AND $unidad['cancelada']==0 AND $unidad['fec_limite']!='' AND $unidad['fec_limite']!= null) {
				$fecha=$unidad['fec_arribo'];
				// $dias=(strtotime($fecha) - strtotime('now'))/86400;
				$dias=(strtotime('now') - strtotime($fecha))/86400;
				$dias = floor($dias);
				if ($dias >= 5) {
					$por_caer_fc='unidad-falta-cancelar ';
				}
			}
		?>


		<?php
			$atp = '';
			$atp_60_dias = '';
			if ($unidad['id_negocio']==2) {
				$atp='unidad-atp';

				if ($unidad['fec_arribo']<>'') {
					$fecha_a=$unidad['fec_arribo'];
					$dias_de_llegada=(strtotime('now')-strtotime($fecha_a))/86400;
					if ($dias_de_llegada >= 60) {
						$atp_60_dias='background: red; color: white';
					}
				}
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

			$libre_condicionada = '';
			$color_no_disponible = '';
			$rosada_precio_junio ='';

			if ($unidad['no_disponible'] == 1 and $unidad['libre_condicionada'] != 1) {
				// $color_no_disponible = 'background: #F9A5FA;';
				$color_no_disponible = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #F3C5DD 10px,
			      #F3C5DD 15px
			    )";
			}else {
				if ($unidad['libre_condicionada'] == 1) {
					// $color_no_disponible = 'background: #F9A5FA;';
					$libre_condicionada = "background: repeating-linear-gradient(
				      45deg,
				      rgba(0, 0, 0, 0) 5px,
				      rgba(0, 0, 0, 0) 10px,
				      #A4FABC 10px,
				      #A4FABC 15px
				    )";
				}
			}
			if ($unidad['rosada_precio_junio'] == 1 AND $unidad['no_disponible'] == 0) {
				$color_no_disponible = '';
				$libre_condicionada = '';
				$rosada_precio_junio = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #F3C5DD 10px,
			      #F3C5DD 15px
			    )";
			}
			$amarillo_junio = '';
			if ($unidad['amarillo'] == 1 AND $unidad['precio_julio'] == 1) {
				$amarillo_junio = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #EEF788 10px,
			      #EEF788 15px
			    )";
			}

			$naranja_agosto = '';
			if ($unidad['naranja'] == 1 AND $unidad['precio_agosto'] == 1) {
				$naranja_agosto = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #F9D0A1 10px,
			      #F9D0A1 15px
			    )";
			}
			$corolla_dic2020 = '';
			if ($unidad['corolla_dic2020']==1) {
				$corolla_dic2020 = "background: #72C6FA;";
			}

			// $preventa_hilux_oct = '';
			// if ($unidad['preventa_hilux_oct']==1) {
			// 	$preventa_hilux_oct = "background: repeating-linear-gradient(
			//       45deg,
			//       rgba(0, 0, 0, 0) 5px,
			//       rgba(0, 0, 0, 0) 10px,
			//       rgba(169	,102, 62, 0.7) 10px,
			//       rgba(169	,102, 62, 0.7) 15px
			//     )";
			// }

			$preventa_oct20 = '';
			if ($unidad['preventa_oct20']==1) {
				$preventa_oct20 = "background: repeating-linear-gradient(
			      45deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #C5C5C5 10px,
			      #C5C5C5 15px
			    )";
			}

			// $preventa_hilux20 = '';
			// if ($unidad['preventa_hilux20']==1) {
			// 	$preventa_hilux20 = "background: #36c;
			// 		background:
			// 		linear-gradient(115deg, transparent 50%, rgba(255,255,255,.75) 50%) 0 0,
			// 		linear-gradient(245deg, transparent 50%, rgba(255,255,255,.75) 50%) 0 0,
			// 		linear-gradient(115deg, transparent 50%, rgba(255,255,255,.75) 50%) 7px -15px,
			// 		linear-gradient(245deg, transparent 50%, rgba(255,255,255,.75) 50%) 7px -15px,
			// 		#36c;
			// 		background-size: 15px 30px;";
			// }

			$preventa_hilux20 = '';
			if ($unidad['preventa_hilux20']==1) {
				$preventa_hilux20 = "background:
					linear-gradient(135deg, #ECEDDC 25%, transparent 25%) -50px 0,
					linear-gradient(225deg, #ECEDDC 25%, transparent 25%) -50px 0,
					linear-gradient(315deg, #ECEDDC 25%, transparent 25%),
					linear-gradient(45deg, #ECEDDC 25%, transparent 25%);
					background-size: 10px 10px;
					background-color: rgba(231,149,148,0.5)";

			}


			$preventa_hilux_oct = '';
			if ($unidad['preventa_hilux_oct']==1) {
				$preventa_hilux_oct = "background:
					linear-gradient(135deg, #8DBCFB 25%, transparent 25%) -50px 0,
					linear-gradient(225deg, #8DBCFB 25%, transparent 25%) -50px 0,
					linear-gradient(315deg, #8DBCFB 25%, transparent 25%),
					linear-gradient(45deg, #8DBCFB 25%, transparent 25%);
					background-size: 10px 10px;
					background-color: rgba(231,149,148,0.5)";
			}


			$prioridad_entrega = '';
			if ($unidad['cliente'] == "CRUZ MIGUEL ANGEL" or $unidad['cliente'] == "ALVAREZ CARLOS TOMAS" or $unidad['cliente'] == "VARGAS FAUSTINO RAUL") {
				$prioridad_entrega = "background: repeating-linear-gradient(
			      135deg,
			      rgba(0, 0, 0, 0) 5px,
			      rgba(0, 0, 0, 0) 10px,
			      #ABF7C0 10px,
			      #ABF7C0 15px
			    )";
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

			$chasis_2023 = ['P8192264', 'P8192264', 'P0051888', 'P8200673', 'P0059326', 'P0068289', 'P0068229', 'P0068166', 'P0068647', 'P0068666', 'P0067908', 'P8203082', 'P0067290', 'P0066372', 'P0068064', 'P0065401', 'P0234694', 'P2280945', 'P8214328', 'P2128569', 'P8214221', 'P0234529', 'P8215215', 'P2128854', 'P8215801', 'P0234231', 'P2281193'];
			if(in_array($unidad['chasis'], $chasis_2023)) {
				$modelo_2023 = 'color: orange !important; text-shadow: .5px .5px 2px orange;';
				$modelo_text_2023 = "text-decoration: line-through solid rgb(68, 68, 68);"
			}else{
				$modelo_2023 = '';
				$modelo_text_2023 = '';
			}
			//$dias = $unidad['fec_arribo']->diff(date("Y/m/d"));
		 ?>



		<tr class="<?php echo 'fila_'.$fila.' '.$entregada.' '.$libre. ' '.$nc.' '.$por_caer_fc.' '.$atp; ?>" style="<?php echo $pago_tasa.' '.$tasa_cero.' '.$sinestrada.' '.$chasis_k.' '.$color_no_disponible.' '.$libre_condicionada.' '.$rosada_precio_junio.' '.$amarillo_junio.' '.$naranja_agosto.' '.$corolla_dic2020.' '.$preventa_hilux_oct.' '.$preventa_oct20.' '.$preventa_hilux20.' '.$prioridad_entrega.' '.$por_caer_fc.' '.$modelo_2023 ?>">
			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_unidad']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $mes_a[$unidad['id_mes']]['mes']; ?></td>

			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" style="<?php echo $modelo_text_2023 ?>"  data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['año']; ?></td>


			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['nro_orden']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['interno']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_despacho']); ?></td>
			<td class="centrar-texto celda" style="<?php echo $atp_60_dias ?>" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo cambiarFormatoFecha($unidad['fec_arribo']); ?></td>
			<td class="centrar-texto celda fila-grupo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $grupo_a[$unidad['id_grupo']]['grupo']; ?></td>
			<td class="centrar-texto celda fila-modelo fila-oculto" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $modelo_a[$unidad['id_modelo']]['modelo']; ?></td>
			<td class="centrar-texto celda" data-id="<?php echo $unidad['id_unidad']; ?>"><?php echo $unidad['chasis']; ?></td>
			<td class="centrar-texto celda <?php echo $clase_don_vargas; ?>" data-id="<?php echo $unidad['id_unidad']; ?>">
				<?php
					if ($unidad['no_disponible'] == 1) {
						echo "<span style='color: red;font-weight: bold; font-size: 12px;'>// No Disponible//</span>";
					}
				 ?>
				 <?php if ($unidad['precio_junio'] == 1 ) {
					echo "<span style='font-weight: bold; font-size: 12px;'>// Precio Junio//</span>";
					}
				 ?>
				 <?php if ($unidad['reserva'] == 0 ) {
					echo "<span style='font-weight: bold; font-size: 12px;'>// SIN RESERVA //</span>";
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
			<td class="celda-espacio-left celda celda_cliente" data-id="<?php echo $unidad['id_unidad']; ?>">
					<?php echo $cliente.' '; ?>
					<span style="<?php echo ($unidad['reventa'] == 1) ? 'color:red; text-decoration: underline;' :''; ?>"><?php echo ($unidad['reventa'] == 1) ? '(revta)' : ''; ?></span>
				</td>
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