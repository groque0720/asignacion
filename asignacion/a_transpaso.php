<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL="SELECT * FROM modelos WHERE activo = 1 ";
	$modelos=mysqli_query($con, $SQL);

	while ($modelo=mysqli_fetch_array($modelos)) {
		$modelo_t[$modelo['id_modelo_transpaso']]['id_modelo']=$modelo['idmodelo'];
		$modelo_t[$modelo['id_modelo_transpaso']]['id_grupo']=$modelo['idgrupo'];
 	}


 	$SQL="SELECT * FROM colores_t";
 	$colores=mysqli_query($con, $SQL);

 	while ($color=mysqli_fetch_array($colores)) {
 		$color_t[$color['id_color']]['color']=$color['color_servidor'];
 	}

 	$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND activo = 1";
 	$usuarios= mysqli_query($con, $SQL);

 	while ($usuario = mysqli_fetch_array($usuarios)) {
 		$usuario_t[$usuario['id_usuario_transpaso']]['id_usuario']=$usuario['idusuario'];
 	}


	$SQL="SELECT * FROM unidades";
	$unidades=mysqli_query($con, $SQL);

	$i=0;


	while ($unidad=mysqli_fetch_array($unidades)) {

			// if ($i<91) {

				$nro_unidad=$unidad['IdUnidad'];
				$id_negocio=1;
				$id_mes=$unidad['AsigMes'];
				$año=$unidad['AsigAño'];
				$id_grupo=$modelo_t[$unidad['Modelo']]['id_grupo'];
				$id_modelo=$modelo_t[$unidad['Modelo']]['id_modelo'];
				$nro_orden=$unidad['NroOrden'];
				$interno=$unidad['Interno'];
				$chasis=$unidad['Chasis'];
				if ($unidad['Destino']!=null OR $unidad['Destino']!='') {$id_sucursal = $unidad['Destino'];}else{$id_sucursal = "null";}

				$estado_tasa=$unidad['Asignacion'];
				if ($unidad['PagoT']!=null OR $unidad['PagoT']!='') {$fec_despacho = "'".$unidad['PagoT']."'";}else{$fec_despacho = "null";}
				if ($unidad['Llego']!=null OR $unidad['Llego']!='') {$fec_arribo = "'".$unidad['Llego']."'";}else{$fec_arribo = "null";}
				if ($unidad['FechaPlaya']!=null OR $unidad['FechaPlaya']!='') {$fec_playa = "'".$unidad['FechaPlaya']."'";}else{$fec_playa = "null";}

				if ($unidad['SePago']!=null OR $unidad['SePago']!='') {$pagado = $unidad['SePago'];}else{$pagado = "null";}
				if ($unidad['Reservada']!=null OR $unidad['Reservada']!='') {$reservada = $unidad['Reservada'];}else{$reservada = "null";}
				if ($unidad['Confirmada']!=null OR $unidad['Confirmada']!='') {$estado_reserva = $unidad['Confirmada'];}else{$estado_reserva = "null";}

				if ($unidad['FechaReserva']!=null OR $unidad['FechaReserva']!='') {$fec_reserva = "'".$unidad['FechaReserva']."'";}else{$fec_reserva = "null";}
				if ($unidad['Hora']!=null OR $unidad['Hora']!='') {$hora = "'".$unidad['Hora']."'";}else{$hora = "null";}
				if ($unidad['Cliente']!=null OR $unidad['Cliente']!='') {$cliente = "'".$unidad['Cliente']."'";}else{$cliente = "null";}

				if ($unidad['IdAsesor']!=null OR $unidad['IdAsesor']!='') {$id_asesor = $usuario_t[$unidad['IdAsesor']]['id_usuario'];}else{$id_asesor = "null";}

				if ($unidad['ColorAsignado']!=null OR $unidad['ColorAsignado']!='') {$id_color = $color_t[$unidad['ColorAsignado']]['color'];}else{$id_color = "null";}
				
				if ($unidad['fechalimite']!=null OR $unidad['fechalimite']!='') {$fec_limite = "'".$unidad['fechalimite']."'";}else{$fec_limite = "null";}
				if ($unidad['FechaCanc']!=null OR $unidad['FechaCanc']!='') {$fec_cancelacion = "'".$unidad['FechaCanc']."'";}else{$fec_cancelacion = "null";}
				if ($unidad['Cancelada']!=null OR $unidad['Cancelada']!='') {$cancelada = $unidad['Cancelada'];}else{$cancelada = "null";}

				if ($unidad['patentada']!=null OR $unidad['patentada']!='') {$patentada = $unidad['patentada'];}else{$patentada = "null";}
				if ($unidad['Observacion']!=null OR $unidad['Observacion']!='') {$observacion = "'".$unidad['Observacion']."'";}else{$observacion = "null";}

				if ($unidad['FechaEntrega']!=null OR $unidad['FechaEntrega']!='') {$fec_entrega = "'".$unidad['FechaEntrega']."'";}else{$fec_entrega = "null";}
				if ($unidad['Entregada']!=null OR $unidad['Entregada']!='') {$entregada = $unidad['Entregada'];}else{$entregada = "null";}

				$SQL=" INSERT INTO asignaciones (";
				$SQL .= "nro_unidad, id_negocio, id_mes, año, id_grupo, id_modelo, nro_orden, interno, chasis, id_sucursal,";
				$SQL .= " estado_tasa, fec_despacho, fec_arribo, fec_playa, pagado, reservada, estado_reserva, fec_reserva, hora, cliente, id_asesor, id_color, ";
				$SQL .= " fec_limite, fec_cancelacion, cancelada, patentada, observacion, guardado, fec_entrega, entregada ";
				$SQL .=" ) VALUES ( ";
				$SQL .= " $nro_unidad, $id_negocio, $id_mes, $año, $id_grupo, $id_modelo, '$nro_orden', '$interno', '$chasis', $id_sucursal,";
				$SQL .= " $estado_tasa, $fec_despacho, $fec_arribo, $fec_playa, $pagado, $reservada, $estado_reserva, $fec_reserva, $hora, $cliente, $id_asesor, $id_color, ";
				$SQL .= " $fec_limite, $fec_cancelacion, $cancelada, $patentada, $observacion, 1, $fec_entrega, $entregada ";
				$SQL .= " ) ";
				$query=mysqli_query($con, $SQL);

				if ($query) {
					echo "ok ".$i."<br>";
					} else {
					echo "no OK ". $i."<br>";
					}

				$i++;	

	}
 ?>
