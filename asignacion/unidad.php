
<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();
$id_perfil=$_SESSION["idperfil"];
$id_sucursal=$_SESSION["idsuc"];
$id_usuario = $_SESSION["id"];
$nom_asesor=$_SESSION["usuario"];

$lectura='';
$deshabilitado='';
$asesor_class='';

if ($id_perfil<>14 AND $id_perfil<>5) {
	$lectura="readonly='readonly'";
	$deshabilitado="disabled";
	$asesor_class='input-asesor';
}

if (isset($nuevaUnidad)) { // alta de nueva unidad

	$SQL="SELECT MAX(nro_unidad) as nro_unidad FROM asignaciones";
	$unidades=mysqli_query($con, $SQL);
	$unidad=mysqli_fetch_array($unidades);
	$nro = (int)$unidad['nro_unidad'] + 1;

	$SQL="INSERT INTO asignaciones (nro_unidad, guardado) VALUES ($nro, 0)";
	mysqli_query($con, $SQL);

	$SQL="SELECT MAX(id_unidad) AS id FROM asignaciones";
	$res_query=mysqli_query($con, $SQL);
	$res_unidad = mysqli_fetch_array($res_query);

	$id_unidad= $res_unidad['id'];
}

$SQL="SELECT * FROM asignaciones WHERE id_unidad =".$id_unidad;
$unidades = mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($unidades);

$SQL="SELECT * FROM asignaciones_levantadas WHERE id_asesor = ".$id_usuario." AND nro_unidad =".$unidad['nro_unidad']." AND (fec_alta >= CURDATE()-1) AND (fec_alta <= CURDATE())";
$reservas = mysqli_query($con, $SQL);
$cant=0;
$cant = mysqli_num_rows($reservas);

if ($cant>=1) {
	echo '<script>
			swal("Reserva Reciente", "NO puede reservar la misma unidad en el mismo ciclo de caidas automáticas", "error");
		</script>';
}
?>

<div class="unidad">
	<form class="form-unidad" action="" method="POST">
		<input type="hidden" id="guardado" value="<?php echo $unidad['guardado']; ?>">
		<!-- id="text_busqueda" espara cuando guardo la unidad si tiene filtro me carrgue la pagina con el ultimo filtro realizado -->
		<input type="hidden" name="text_busqueda" id="text_busqueda" value="">
		<input type="hidden" name="es_planilla_tpa" id="es_planilla_tpa" value="">
		<input type="hidden" name="es_planilla_entregas" id="es_planilla_entregas" value="">
		<input type="hidden" name="reservada" id="reservada" value="<?php echo $unidad['reservada']; ?>">
		<input type="hidden" name="asesor_a_reservar" id="asesor_a_reservar" value="<?php echo $id_usuario; ?>">
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">
		<input type="hidden" name='suc_a_reservar' id="suc_a_reservar" value="<?php echo $id_sucursal; ?>">

		<!-- <div class="titulo centrar-texto">
			DETALLE DE UNIDAD
		</div> -->
		<div class="unidad-inputs">
			<div class="lado unidad-izquierdo">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo no-confirmada">
						UNIDAD ASIGANDA A
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100 centrar-texto ">
						<?php
							$SQL="SELECT * FROM negocios";
							$negocios=mysqli_query($con, $SQL);
						 ?>
						<select class="form-inputs ancho-50" name="id_negocio" id="id_negocio" <?php echo $lectura; ?>>
							<?php
								while ($negocio = mysqli_fetch_array($negocios)) { ?>
									<option value="<?php echo $negocio['id_negocio']; ?>" <?php if ($unidad['id_negocio']==$negocio['id_negocio']) { echo 'selected';	} ?>><?php echo $negocio['negocio']; ?></option>
							<?php }	 ?>
						</select>
					</div>
				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS DE UNIDAD
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-1-3">
						<label class="an" for="">Nro Un.</label>
						<input class="form-inputs" type="hidden" size="5" id="id_unidad" name="id_unidad" value="<?php echo $unidad['id_unidad']; ?> " <?php echo $lectura; ?>>
						<input class="form-inputs" type="text" size="5" id="nro_unidad" name="nro_unidad" value="<?php echo $unidad['nro_unidad'].' '; ?> " <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 ">
					<label class="ancho-1-3" for="">Mes</label>
					<select class="form-inputs ancho-2-3" name="id_mes" id="id_mes" <?php echo $lectura; ?>>
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM meses";
							$meses=mysqli_query($con, $SQL);
							while ($mes=mysqli_fetch_array($meses)) {?>
								<option value="<?php echo $mes['idmes'] ?>" <?php if ($mes['idmes']==$unidad['id_mes']) { echo 'selected';	} ?>><?php echo $mes['mes']; ?></option>
						 <?php } ?>
					</select>
					</div>
					<div class="ancho-1-3">
						<label class="an" for="">Año</label>
						<input class="form-inputs" type="text" size="5" name="año" id="año" value="<?php echo $unidad['año']; ?>" <?php echo $lectura; ?>>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-2-6">
						<label class="ancho-20" for="">Modelo</label>
						<select class="form-inputs ancho-2-3" name="id_grupo" id="grupo" <?php echo $lectura; ?>>
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND activo = 1 ORDER BY posicion";
							$grupos=mysqli_query($con, $SQL);
							while ($modelo=mysqli_fetch_array($grupos)) { ?>
								<option value="<?php echo $modelo['idgrupo']; ?>" <?php if ($modelo['idgrupo']==$unidad['id_grupo']) { echo 'selected';	} ?>><?php echo $modelo['grupo']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-4-6">
						<label class="ancho-20" for="">Versión</label>
						<select class="form-inputs ancho-80" name="id_modelo" id="id_modelo" <?php echo $lectura; ?>>
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM modelos WHERE idgrupo = ".$unidad['id_grupo']." ORDER BY posicion";
							$versiones=mysqli_query($con, $SQL);
							while ($version=mysqli_fetch_array($versiones)) { ?>
								<option value="<?php echo $version['idmodelo']; ?>" <?php if ($version['idmodelo']==$unidad['id_modelo']) { echo 'selected'; } ?>><?php echo $version['modelo']; ?></option>
							<?php }	?>
						</select>
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-30 centrar-texto">
						<label class="ancho-1-3" for="">Nro Orden</label>
						<input class="form-inputs" type="text" size="12" id="nro_orden" name="nro_orden" value="<?php echo $unidad['nro_orden']; ?> " <?php echo $lectura; ?>>
					</div>
					<div class="ancho-30 centrar-texto">
						<div class="ancho-100 centrar-texto"><label class="ancho-1-3" for="">Interno</label></div>
						<input class="form-inputs centrar-texto" type="text" size="10" id="interno" name="interno" value="<?php echo $unidad['interno']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-30 ">
						<div class="ancho-100 centrar-texto"><label class="ancho-1-3" for="">Color</label></div>
						<select class="form-inputs ancho-75" id="id_color" name="id_color" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM colores ORDER BY color";
								$colores=mysqli_query($con, $SQL);
								while ($color=mysqli_fetch_array($colores)) { ?>
									<option value="<?php echo $color['idcolor']; ?>" <?php if ($color['idcolor']==$unidad['id_color']) {echo 'selected';	} ?>><?php echo $color['color']; ?></option>
								<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-30">
						<label class="ancho-1-3" for="">Nro Chasis</label>
						<input class="form-inputs" type="text" size="10" id="chasis" name="chasis" value="<?php echo $unidad['chasis']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-30">
						<label class="ancho-1-3" for="">Sucursal Destino</label>
						<select class="form-inputs ancho-75 <?php echo $asesor_class; ?>" name="id_sucursal" id="id_sucursal">
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM sucursales";
								$sucursales=mysqli_query($con, $SQL);
								while ($sucursal=mysqli_fetch_array($sucursales)) { ?>
									<option value="<?php echo $sucursal['idsucursal']; ?>" <?php if ($sucursal['idsucursal']==$unidad['id_sucursal']) {	echo 'selected';} ?>><?php echo $sucursal['sucres']; ?></option>
								<?php }  ?>
						</select>
					</div>
					<div class="ancho-30">

						<label class="ancho-1-3" for="">Ubicación</label>
						<select class="form-inputs ancho-75" name="id_ubicacion" id="id_ubicacion" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM sucursales";
								$sucursales=mysqli_query($con, $SQL);
								while ($sucursal=mysqli_fetch_array($sucursales)) { ?>
									<option value="<?php echo $sucursal['idsucursal']; ?>" <?php if ($sucursal['idsucursal']==$unidad['id_ubicacion']) {	echo 'selected';} ?>><?php echo $sucursal['sucres']; ?></option>
								<?php }  ?>
						</select>
					</div>

				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						CONFIRMADA UNIDAD DE TASA
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-50 centrar-texto ">
						<select class="form-inputs ancho-100" name="estado_tasa" id="estado_tasa" <?php echo $lectura; ?>>
							<option value="0" <?php if ($unidad['estado_tasa']==0) { echo 'selected';	} ?>>No Confirmada</option>
							<option value="1" <?php if ($unidad['estado_tasa']==1) { echo 'selected';	} ?>>Confirmada</option>
						</select>
					</div>
					<div class="ancho-30 centrar-texto" style="display: <?php if ($deshabilitado == 'disabled') { echo 'none'; } ?>">
						<label class="ancho-40" for="">Disponible</label>
						<input class="form-inputs input-fecha" type="checkbox" size="5" name="no_disponible" <?php if ($unidad['no_disponible']!=1) { echo 'checked';} ?>>
					</div>
				</div>
				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						TASA
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-45" for="">Fec. Playa</label>
						<input class="form-inputs input-fecha" type="date" size="5" name="fec_playa" value="<?php echo $unidad['fec_playa']; ?>" <?php echo $lectura; ?>>
					</div>

					<div class="ancho-1-3">
						<?php if ($id_perfil==14) { ?>
						<label class="ancho-35" for="">Costo $</label>
						<input class="form-inputs" type="text" size="8" id="costo_z" name="costo_z" value="<?php echo $unidad['costo']; ?>" <?php echo $lectura; ?>>
						<?php } ?>
						<input class="form-inputs" type="hidden" size="8" id="costo" name="costo" value="<?php echo $unidad['costo']; ?>">
					</div>


					<div class="ancho-1-3">
						<label class="ancho-20" for="">Pagado</label>
						<input class="form-inputs input-fecha" type="checkbox" size="5" name="pagado" <?php if ($unidad['pagado']==1) { echo 'checked';} ?> <?php echo $deshabilitado; ?>>

					</div>
				</div>
				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						FECHAS
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Despacho</label>
						<input class="form-inputs input-fecha" type="date" size="5" name="fec_despacho" value="<?php echo $unidad['fec_despacho']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Arribo</label>
						<input class="form-inputs input-fecha" type="date" size="5" id="fec_arribo" name="fec_arribo" value="<?php echo $unidad['fec_arribo']; ?>" <?php echo $lectura; ?>>
					</div>

				</div>

			</div>

			<div class="lado unidad-derecho cuadro">

				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo no-confirmada">
						ESTADO DE LA RESERVA
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100 centrar-texto ">
						<select class="form-inputs ancho-50" name="estado_reserva" id="estado_reserva" <?php echo $lectura; ?>>
							<option value="0" <?php if ($unidad['estado_reserva']==0) { echo 'selected';	} ?>>No Confirmada</option>
							<option value="1" <?php if ($unidad['estado_reserva']==1) { echo 'selected';	} ?>>Confirmada</option>
						</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS DE RESERVA
					</div>
				</div>

				<div class="form-linea centrar-texto">
					<div class="ancho-1-3 ">
						<label class="ancho-1-3" for="">Fecha</label>
						<!-- compruebo si tiene fecha sino cargo la fecha actual -->
						<?php if ($id_perfil==3) {?>
								<?php if ($unidad['fec_reserva']!='') {?>
									<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="date" size="5" id="fec_reserva" name="fec_reserva" value="<?php echo $unidad['fec_reserva']; ?>" <?php echo $lectura; ?>>
								<?php }else{ ?>
									<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="date" size="5" id="fec_reserva" name="fec_reserva" value="<?php echo date("Y-m-d"); ?>" <?php echo $lectura; ?>>
								<?php } ?>
						<?php }else{?>
							<input class="form-inputs input-fecha<?php echo $asesor_class; ?>" type="date" size="5" id="fec_reserva" name="fec_reserva" value="<?php echo $unidad['fec_reserva']; ?>" <?php echo $lectura; ?>>
						<?php } ?>

					</div>
					<div class="ancho-1-3 ">
						<label class="ancho-1-3" for="">Hora</label>
						<!-- compruebo si tiene hora sino cargo la fecha actual -->
						<?php if ($id_perfil==3) {?>
							<?php if ($unidad['hora']!='' AND $unidad['reservada']==1) {?>
								<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo $unidad['hora']; ?>" readonly="readonly">
							<?php }else{?>
								<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo date("H:i"); ?>" readonly="readonly">
							<?php } ?>
						<?php }else{?>

							<?php if ($unidad['hora']!='' AND $unidad['reservada']==1) {?>
								<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo $unidad['hora']; ?>" >
							<?php }else{?>
									<?php if ($id_perfil==14) { ?>
										<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo date("H:i"); ?>">
									<?php } ?>

							<?php } ?>
						<?php } ?>

					</div>
					<div class="ancho-1-3">
						<div class="ancho-45">
							<?php if ($id_perfil==14  AND $unidad['reservada']==1) {?>
							<input style="cursor:pointer;" class="form-inputs centrar-texto" id="levantar_reserva" type="submit" size="5" value="Levantar">
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="form-linea centrar-texto">
					<div class="ancho-100 ">
						<label class="ancho-15" for="">Cliente</label>
						<input class="form-inputs ancho-85 <?php echo $asesor_class; ?>" type="text" size="5" id="cliente" name="cliente" value="<?php echo $unidad['cliente']; ?> ">
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-100 ">
						<label class="ancho-15" for="">Asesor</label>
							<select class="form-inputs ancho-85 <?php echo $asesor_class; ?>" name="id_asesor" id="id_asesor" <?php echo $lectura; ?>>
									<option value="1"></option>
									<?php
										$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND activo = 1 ORDER BY nombre";
										$usuarios = mysqli_query($con, $SQL);
										while ($usuario=mysqli_fetch_array($usuarios)) { ?>
											<option value="<?php echo $usuario['idusuario']; ?>" <?php if ($usuario['idusuario']==$unidad['id_asesor']) { echo 'selected';	} ?>><?php echo $usuario['nombre']; ?></option>
										<?php } ?>
							</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						COLORES PEDIDO POR EL CLIENTE
					</div>
				</div>

				<?php
					$SQL="SELECT * FROM colores";
					$colores=mysqli_query($con, $SQL);
					$i=1;
					while ($color=mysqli_fetch_array($colores)) {
						$color_a[$color['idcolor']]['color']= $color['color'];
						$i++;
					}
				?>

				<div class="form-linea centrar-texto">
					<?php

						if ($unidad['color_uno']==19 OR $unidad['color_uno']==20 OR $unidad['color_uno']==23) {
							$bloquear = "disabled";
						}else{
							$bloquear='';
						}

					 ?>
					<div class="ancho-30 ">
						<label class="ancho-1-3" for="">Color 1</label>
						<select class="form-inputs ancho-100 <?php echo $asesor_class; ?>" name="color_uno" id="color_uno" readonly >
							<!-- $id_usuario == 16 = Jergus Ariel -->
							<!-- $id_usuario == 106 = Acosta Lucas -->
						<?php if (($unidad['color_uno']!=15 AND $unidad['color_uno']!=16 AND $unidad['color_uno']!=23)
							 OR ($id_usuario == 16 OR $id_usuario == 106)  ) {	 ?>
							<option value="0"></option>
							<?php
								for ($j=1; $j < $i ; $j++) { ?>
									<option value="<?php echo $j; ?>"  <?php if ($j==$unidad['color_uno']) { echo 'selected';} ?>><?php echo $color_a[$j]['color'] ?></option>
							<?php } ?>
							<?php }else{ ?>
									<option value="<?php echo $unidad['color_uno']; ?>"  selected ?> <?php echo $color_a[$unidad['color_uno']]['color'] ?></option>
								<?php } ?>
						</select>
					</div>

					<?php

						if ($unidad['color_uno']==15 OR $unidad['color_uno']==16) {
							$mostrar = "style='display: none;'";
						}else{
							$mostrar='';
						}

						if ($unidad['color_dos']==19 OR $unidad['color_dos']==20) {
							$bloquear = "disabled";
						}else{
							$bloquear='';
						}

					 ?>
					<div class="ancho-30 " <?php echo $mostrar; ?>>
						<label class="ancho-1-3" for="">Color 2</label>
						<select class="form-inputs ancho-100 <?php echo $asesor_class; ?>" name="color_dos" id="color_dos">
							<option value="0"></option>
							<?php
								for ($j=1; $j <= $i ; $j++) { ?>
									<option value="<?php echo $j; ?>" <?php if ($j==$unidad['color_dos']) { echo 'selected';} ?>><?php echo $color_a[$j]['color'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-30 " <?php echo $mostrar; ?>>
						<label class="ancho-1-3" for="">Color 3</label>
						<select class="form-inputs ancho-100 <?php echo $asesor_class; ?>" name="color_tres" id="color_tres">
							<option value="0"></option>
							<?php
								for ($j=1; $j <= $i ; $j++) { ?>
									<option value="<?php echo $j; ?>"  <?php if ($j==$unidad['color_tres']) { echo 'selected';} ?>><?php echo $color_a[$j]['color'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						ESTADO DE CUENTA
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Fec. Lim. Canc.</label>
						<input class="form-inputs input-fecha" type="date" size="5" id="fec_limite" name="fec_limite" value="<?php echo $unidad['fec_limite']; ?>" <?php echo $lectura; ?>>
					</div>
					<?php

					$leyenda = "";
						if ($id_perfil==5) {
							$leyenda = "readonly='readonly'";
						 }

						//Sacar a Luis cuando venga Roxy de Vacaciones

						//if ($id_usuario==94) {
						//	$leyenda = "";
						//	$lectura = "";
						 //}

						// Habilitar a Roxy cuando venga de Vacaciones
						if ($id_usuario==87 OR $id_usuario==94 OR $id_usuario==120 OR $id_usuario==119 OR $id_usuario==31 OR $id_usuario==50) {
						//if ($id_usuario==87) {
							$leyenda = "";
							$lectura = "";
						 }
						   ?>
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Fec. Canc.</label>
						<input class="form-inputs input-fecha" type="date" size="5" id="fec_cancelacion" name="fec_cancelacion" value="<?php echo $unidad['fec_cancelacion']; ?>" <?php echo $lectura.' '.$leyenda; ?> >
					</div>
				</div>

				<?php
					$entrega_habilitada='';
					if ($unidad['fec_cancelacion']=="" OR $unidad['fec_cancelacion']==null) {
						$entrega_habilitada = 'disabled';
				 	}

				 ?>

				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						ENTREGA DE UNIDAD
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Entrega</label>
						<input class="form-inputs input-fecha" type="date" size="5" id="fec_entrega" name="fec_entrega" value="<?php echo $unidad['fec_entrega']; ?>" <?php echo $entrega_habilitada; ?>>
					</div>
					<div class="ancho-50">
						<label class="ancho-35" for="">Nro Remito</label>
						<input class="form-inputs" type="text" size="10" id="nro_remito" name="nro_remito" value="<?php echo $unidad['nro_remito']; ?>" <?php echo $entrega_habilitada; ?>>
					</div>

				</div>




				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						INSCRIPCIÓN REGISTRAL
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-50">
						<label class="ancho-45" for="">Fec. Insc</label>
						<input class="form-inputs input-fecha" type="date" size="5" name="fec_inscripcion" value="<?php echo $unidad['fec_inscripcion']; ?>" <?php echo ''.$lectura; ?>>
					</div>
					<div class="ancho-50">
						<label class="ancho-35" for="">Patente</label>
						<input class="form-inputs" type="text" size="8" name="patente" value="<?php echo $unidad['patente']; ?>" <?php echo $lectura; ?>>
					</div>

				</div>

			</div>

		</div>

		<?php
			$visualizar = '';

			 if ($id_perfil!=5 and $id_usuario != 106) {
				$visualizar = 'style=display:none';
			} ?>

		<div class="zona-botones zona-entregas" <?php echo $visualizar; ?>>
			<div class="form-linea centrar-texto">

				<?php
					$SQL="SELECT * FROM entregas_ubicaciones WHERE activo = 1 AND id_sucursal=".$unidad['id_ubicacion']." ORDER BY ubicacion_entrega";
					$ubicaciones = mysqli_query($con, $SQL);
				 ?>
					<div class="ancho-20">
						<label class="ancho-40" for="">Ubicación.:</label>
						<select class="ancho-60" name="id_ubicacion_entrega" id="id_ubicacion_entrega" >
							<option value="0"></option>
							<?php while ($ubic=mysqli_fetch_array($ubicaciones)) { ?>
								<option value="<?php echo $ubic['id_ubicacion_entrega']; ?>" <?php if ($ubic['id_ubicacion_entrega']==$unidad['id_ubicacion_entrega']) { echo 'selected';} ?>><?php echo $ubic["ubicacion_entrega"]; ?></option>
							<?php } ?>
						</select>
					</div>

				<?php
					$SQL="SELECT * FROM entregas_estados_unidad WHERE activo = 1 ORDER BY orden";
					$estados=mysqli_query($con, $SQL);

				 ?>
					<div class="ancho-30">
						<label class="ancho-25" for="">Estado:</label>
						<select class="ancho-65" name="id_estado_entrega" id="id_estado_entrega" >
							<option value="0"></option>
							<?php
								while ($estado=mysqli_fetch_array($estados)) { ?>
									<option value="<?php echo $estado['id_estado_entrega']; ?>" <?php if ($estado['id_estado_entrega']==$unidad['id_estado_entrega']) { echo 'selected';} ?>><?php echo $estado['estado_unidad']; ?></option>
							<?php } ?>

						</select>
					</div>
					<div class="ancho-20">
						<label class="ancho-35" for="">Pedido:</label>
						<input class="form-inputs" type="date" size="8" name="fec_pedido" value="<?php echo $unidad['fec_pedido']; ?>" >
					</div>
					<div class="ancho-20">
						<label class="ancho-35" for="">Hora:</label>
						<input class="form-inputs" type="time" size="8" name="hora_pedido" value="<?php echo $unidad['hora_pedido']; ?>" >
					</div>
					<div class="ancho-10">

						<?php
							$cadena= '?grupo='.$unidad['id_grupo'].'&modelo='.$unidad['id_modelo'].'&color='.$unidad['id_color'].'&chasis='.$unidad['chasis'].'&cliente='.$unidad['cliente'];
						 ?>
						<a href="<?php echo 'entregas_control_entrega_pdf.php'.$cadena; ?>" target="_blank" ><span class="boton-salida">Ctrl. Salida</span></a>
					</div>

				</div>
		</div>
		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-10">
					<label class="ancho-35" for="">Observación</label>
				</div>
				<div class="ancho-90 derecha-texto">
					<textarea class="unidad-obs" style="font-weight: bold; color: red; font-size: 1.2em;" name="observacion" id="" cols="30" rows="2"><?php echo $unidad['observacion']; ?></textarea>
				</div>
			</div>
		</div>
		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-20">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
						<?php if ($unidad['no_disponible'] != 1 or $id_usuario == 11 or $id_usuario == 56 OR $id_usuario == 16): ?>
							<div class="ancho-20 derecha-texto">
								<?php if ($id_perfil==14 OR $id_perfil==5 OR $id_usuario == 94) { ?>
									<input type="submit" class="botones btn-aceptar" value="Guardar">
								<?php }else { ?>
									<?php if ($id_perfil==3 AND $unidad['reservada']==0 AND $cant<=0) {?>
										<input type="submit" class="botones btn-aceptar" value="Reservar">
									<?php }
								 	}?>
							</div>
						<?php endif ?>
			</div>
		</div>
		<div id="mensajes_unidad"></div>

	</form>
</div>

<script src="js/unidad.js"></script>