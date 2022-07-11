<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();

// script para cuando esta bloqueada la planilla
include('aa_cerrar_sesiones.php');

$id_perfil=$_SESSION["idperfil"];
$id_sucursal=$_SESSION["idsuc"];
$id_usuario = $_SESSION["id"];
$nom_asesor=$_SESSION["usuario"];
$es_gerente=$_SESSION["es_gerente"];

// if ($id_usuario == 94) {
// 	$id_perfil = 14;
// }

$lectura='';
$deshabilitado='';
$asesor_class='';

if ($id_perfil<>14 or $id_usuario == 94) {
	$lectura="readonly='readonly'";
	$deshabilitado="disabled";
	$asesor_class='input-asesor';
}

if (isset($nuevaUnidad)) { // alta de nueva unidad

	$SQL="SELECT MAX(nro_unidad) as nro_unidad FROM asignaciones_usados";
	$unidades=mysqli_query($con, $SQL);
	$unidad=mysqli_fetch_array($unidades);
	$nro = (int)$unidad['nro_unidad'] + 1;

	$SQL="SELECT MAX(interno) AS interno FROM asignaciones_usados";
	$res_query=mysqli_query($con, $SQL);
	$res_unidad = mysqli_fetch_array($res_query);

	$interno= (int)$res_unidad['interno'] + 1;

	$SQL="INSERT INTO asignaciones_usados (nro_unidad, interno, guardado) VALUES ($nro, $interno, 0)";
	mysqli_query($con, $SQL);

	$SQL="SELECT MAX(id_unidad) AS id FROM asignaciones_usados";
	$res_query=mysqli_query($con, $SQL);
	$res_unidad = mysqli_fetch_array($res_query);

	$id_unidad= $res_unidad['id'];

}

$SQL="SELECT * FROM asignaciones_usados WHERE id_unidad =".$id_unidad;
$unidades = mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($unidades);

$SQL="SELECT * FROM asignaciones_usados_levantadas WHERE id_asesor = ".$id_usuario." AND nro_unidad =".$unidad['nro_unidad']." AND (fec_alta >= CURDATE()-1) AND (fec_alta <= CURDATE())";
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
		<input type="hidden" name="reservada" id="reservada" value="<?php echo $unidad['reservada']; ?>">
		<input type="hidden" name="asesor_a_reservar" id="asesor_a_reservar" value="<?php echo $id_usuario; ?>">
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">

		<div class="titulo centrar-texto">
			DETALLE DE UNIDAD USADA
		</div>
		<div class="unidad-inputs">
			<div class="lado unidad-izquierdo">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo no-confirmada">
						SITUACION DE USADO
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-30 centrar-texto ">
						<label class="an" for="">Estado Físico</label>
						<select class="form-inputs" id="id_estado" name="id_estado" <?php echo $lectura; ?> required>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM asignaciones_usados_estados WHERE activo = 1 ORDER BY estado_usado";
								$estados=mysqli_query($con, $SQL);
								while ($estado=mysqli_fetch_array($estados)) { ?>
									<option value="<?php echo $estado['id_estado_usado']; ?>" <?php if ($estado['id_estado_usado']==$unidad['id_estado']) {echo 'selected';	} ?>><?php echo $estado['estado_usado']; ?></option>
								<?php } ?>
						</select>
					</div>
					<div class="ancho-40 centrar-texto ">
						<label class="an" for="">Estado Certificación</label>
						<select class="form-inputs" id="id_estado_certificado" name="id_estado_certificado" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM asignaciones_usados_estados_certificados";
								$estados=mysqli_query($con, $SQL);
								while ($estado=mysqli_fetch_array($estados)) { ?>
									<option value="<?php echo $estado['id']; ?>" <?php if ($estado['id']==$unidad['id_estado_certificado']) {echo 'selected';	} ?>><?php echo $estado['estado_certificado']; ?></option>
								<?php } ?>
						</select>
					</div>
					<div class="ancho-30 centrar-texto ">
						<label class="an" for="">Fec. Recepción</label>
						<input class="form-inputs input-fecha" type="date" name="fec_recepcion" id="fec_recepcion" value="<?php echo $unidad['fec_recepcion']; ?>" >
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
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-1-3" for="">Interno</label>
						<input class="form-inputs centrar-texto" type="text" size="5" id="interno" name="interno" value="<?php echo $unidad['interno']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-10" for="">Por</label>
						<select class="form-inputs" id="por" name="por" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM grupos ORDER BY grupo";
								$grupos=mysqli_query($con, $SQL);
								while ($grupo=mysqli_fetch_array($grupos)) { ?>
									<option value="<?php echo $grupo['idgrupo']; ?>" <?php if ($grupo['idgrupo']==$unidad['por']) {echo 'selected';	} ?>><?php echo $grupo['grupo']; ?></option>
								<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100 centrar-texto">
						<label class="ancho-30" for="">Vehículo</label>
						<input class="form-inputs ancho-85" type="text" autocomplete="off" size="40" id="vehiculo" name="vehiculo" value="<?php echo $unidad['vehiculo']; ?>" <?php echo $lectura; ?>>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-1-3">
						<label class="ancho-20" for="">Año</label>
						<input class="form-inputs ancho-80" type="text" size="10" id="año" name="año" value="<?php echo $unidad['año'].' '; ?> " <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-20" for="">Km</label>
						<input class="form-inputs ancho-80 centrar-texto" type="text" size="10" id="km_z" name="km_z" value="<?php echo number_format($unidad['km'], 0, ',','.'); ?>" <?php echo $lectura; ?>>
						<input class="form-inputs ancho-80 centrar-texto" type="hidden" size="10" id="km" name="km" value="<?php echo $unidad['km'] ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-20" for="">Dominio</label>
						<input class="form-inputs centrar-texto ancho-60" type="text" size="10" id="dominio" name="dominio" value="<?php echo $unidad['dominio']; ?>" <?php echo $lectura; ?>>
					</div>
				</div>

				<div class="form-linea centrar-texto">
					<div class="ancho-1-3 ">
						<label class="" for="">Color</label>
						<select class="form-inputs " id="id_color" name="id_color" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM asignaciones_usados_colores ORDER BY color";
								$colores=mysqli_query($con, $SQL);
								while ($color=mysqli_fetch_array($colores)) { ?>
									<option value="<?php echo $color['id_color']; ?>" <?php if ($color['id_color']==$unidad['color']) {echo 'selected';	} ?>><?php echo $color['color']; ?></option>
								<?php } ?>
						</select>
					</div>
					<div class="ancho-1-3">
						<label class="" for="">Ub.</label>
						<select class="form-inputs  <?php echo $asesor_class; ?>" name="id_sucursal" id="id_sucursal">
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM sucursales";
								$sucursales=mysqli_query($con, $SQL);
								while ($sucursal=mysqli_fetch_array($sucursales)) { ?>
									<option value="<?php echo $sucursal['idsucursal']; ?>" <?php if ($sucursal['idsucursal']==$unidad['id_sucursal']) {	echo 'selected';} ?>><?php echo $sucursal['sucres']; ?></option>
								<?php }  ?>
						</select>
					</div>
					<div class="ancho-1-3">
						<label class="" for="">Asesor Toma</label>
						<select class="form-inputs" name="asesortoma" id="asesortoma" <?php echo $lectura; ?>>
									<option value="1"></option>
									<?php
										$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ORDER BY nombre";
										$usuarios = mysqli_query($con, $SQL);
										while ($usuario=mysqli_fetch_array($usuarios)) { ?>
											<option value="<?php echo $usuario['idusuario']; ?>" <?php if ($usuario['idusuario']==$unidad['asesortoma']) { echo 'selected';	} ?>><?php echo $usuario['nombre']; ?></option>
										<?php } ?>
							</select>
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-100 ">
						<label class="ancho-30" for="">Último Dueño</label>
						<input class="form-inputs ancho-80" type="text" size="10" id="ultimo_dueño" name="ultimo_dueño" value="<?php echo $unidad['ultimo_dueño']; ?>" <?php echo $lectura; ?> autocomplete="off">
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-25 ">
					<?php if ($es_gerente==1) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Toma + Imp. $</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="toma_mas_impuesto_z" name="toma_mas_impuesto_z" value="<?php echo number_format($unidad['toma_mas_impuesto'], 2, ',','.'); ?>" <?php echo $lectura; ?>>
					<?php } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="toma_mas_impuesto" name="toma_mas_impuesto" value="<?php echo $unidad['toma_mas_impuesto']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-25 ">
					<?php if ($es_gerente==1) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Costo Contable $</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="costo_contable_z" name="costo_contable_z" value="<?php echo number_format( $unidad['costo_contable'], 2, ',','.'); ?>" <?php echo $lectura; ?>>
						<?php } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="costo_contable" name="costo_contable" value="<?php echo $unidad['costo_contable']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-25 ">
						<?php // if ($id_perfil!=3 OR $es_gerente==1) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Precio Venta $</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="precio_venta_z" name="precio_venta_z" value="<?php echo number_format($unidad['precio_venta'], 2, ',','.'); ?>" <?php echo $lectura; ?>>
						<?php // } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="precio_venta" name="precio_venta" value="<?php echo $unidad['precio_venta']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-25 ">

						<div class="centrar-texto"><label class="ancho-30" for="">Precio Info $</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="precio_info_z" name="precio_info_z" value="<?php echo number_format($unidad['precio_info'], 2, ',','.'); ?>" <?php echo $lectura; ?>>

						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="precio_info" name="precio_info" value="<?php echo $unidad['precio_info'];?>" <?php echo $lectura; ?>>
					</div>
				</div>


				<div class="form-linea centrar-texto">
					<div class="ancho-25 ">
					<?php if ($id_perfil!=3 OR $es_gerente==1 ) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Costo Reparación</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="costo_reparacion_z" name="costo_reparacion_z" value="<?php echo number_format($unidad['costo_reparacion'], 2, ',','.'); ?>" <?php echo $lectura; ?>>
					<?php } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="costo_reparacion" name="costo_reparacion" value="<?php echo $unidad['costo_reparacion']; ?>" <?php echo $lectura; ?>>
					</div>

					<div class="ancho-25 ">
					<?php if ($id_perfil!=3 OR $es_gerente==1) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Costo Transferencia</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="transferencia_z" name="transferencia_z" value="<?php echo number_format($unidad['transferencia'], 2, ',','.'); ?>" <?php echo $lectura; ?>>
					<?php } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="transferencia" name="transferencia" value="<?php echo $unidad['transferencia']; ?>" <?php echo $lectura; ?>>
					</div>

					<div class="ancho-25 ">
					<?php if ($id_perfil!=3 OR $es_gerente==1) { ?>
						<div class="centrar-texto"><label class="ancho-30" for="">Precio 0km</label></div>
						<input class="form-inputs centrar-texto ancho-90" autocomplete="off" type="text" size="10" id="precio_0km_z" name="precio_0km_z" value="<?php echo number_format($unidad['precio_0km'], 2, ',','.'); ?>" <?php echo $lectura; ?> >

					<?php } ?>
						<input class="form-inputs centrar-texto ancho-90" type="hidden" size="10" id="precio_0km" name="precio_0km" value="<?php echo $unidad['precio_0km']; ?>"autocomplete="off" <?php echo $lectura; ?>>

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
							<?php if ($unidad['hora']!='') {?>
								<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo $unidad['hora']; ?>" readonly="readonly">
							<?php }else{?>
								<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo date("H:i:s"); ?>" readonly="readonly">
							<?php } ?>
						<?php }else{?>
							<input class="form-inputs input-fecha <?php echo $asesor_class; ?>" type="time" id="hora" name="hora" placeholder="HH:mm:ss" value="<?php echo $unidad['hora']; ?>" >
						<?php } ?>
					</div>
					<div class="ancho-1-3">
						<div class="ancho-45">
							<?php if ($id_perfil==14  AND $unidad['reservada']==1 AND $id_usuario != 94) {?>
							<input style="cursor:pointer;background: #D0D0D0" class="form-inputs centrar-texto" id="levantar_reserva" type="texto" size="5" value="Levantar">
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-100 ">
						<label class="ancho-15" for="">Cliente</label>
						<input class="form-inputs ancho-85 <?php echo $asesor_class; ?>" type="text" size="5" id="cliente" name="cliente" value="<?php echo $unidad['cliente']; ?> " autocomplete="off">
					</div>
				</div>

				<?php
					// if ($id_usuario == 94) {
					// 	$lectura="readonly='readonly'";
					// 	$deshabilitado="disabled";
					// 	$asesor_class='input-asesor';
					// }
				 ?>
				<div class="form-linea centrar-texto">
					<div class="ancho-100 ">
						<label class="ancho-15" for="">Asesor</label>
							<select class="form-inputs ancho-85 <?php echo $asesor_class; ?>" name="id_asesor" id="id_asesor" <?php echo $lectura; ?>>
									<option value="1"></option>
									<?php
										$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ORDER BY nombre";
										$usuarios = mysqli_query($con, $SQL);
										while ($usuario=mysqli_fetch_array($usuarios)) { ?>
												<option value="<?php echo $usuario['idusuario']; ?>"
												<?php if ($usuario['idusuario']==$unidad['id_asesor']) { echo 'selected';} ?>>
													<?php echo $usuario['nombre']; ?>
											</option>
										<?php } ?>
							</select>
					</div>
				</div>
				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						CANCELACION
					</div>
				</div>

				<?php
						if ($id_usuario==136 OR $id_usuario==87 or $id_usuario==119 or $id_usuario==120 OR $id_usuario==31 OR $id_usuario==50 OR $id_usuario==14 OR $id_usuario==96) {
							$lectura = "";
						 }
						   ?>

				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Fecha</label>
						<input class="form-inputs input-fecha" type="date" size="5" name="fecha_cancelacion" value="<?php echo $unidad['fecha_cancelacion']; ?>" <?php echo $lectura; ?>>
					</div>


				</div>




				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						ENTREGA DE UNIDAD
					</div>
				</div>
					<?php
						if ($id_perfil==5) {
							$lectura = "";
						 }
						   ?>
				<div class="form-linea centrar-texto">
					<div class="ancho-1-3">
						<label class="ancho-1-3" for="">Entrega</label>
						<input class="form-inputs input-fecha" type="date" size="5" name="fec_entrega" value="<?php echo $unidad['fec_entrega']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-50">
						<label class="ancho-35" for="">Nro Remito</label>
						<input class="form-inputs" type="text" size="10" name="nro_remito" value="<?php echo $unidad['nro_remito']; ?>" <?php echo $lectura; ?>>
					</div>

				</div>


			</div>

		</div>
		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-10">
					<label class="ancho-35" for="">Observación</label>
				</div>
				<div class="ancho-90 derecha-texto">
					<textarea class="unidad-obs" name="observacion" id="" cols="30" rows="2"><?php echo $unidad['observacion']; ?></textarea>
				</div>
			</div>
		</div>


<?php
	 if ($id_usuario == 94) {
		$id_perfil = 14;
		}
 ?>

		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-20">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-20 derecha-texto">
					<?php if ($unidad['id_estado'] == 1 OR $id_perfil!=3): ?>
						<?php if ($id_usuario==136 OR $id_perfil==14 OR $id_usuario==87 or $id_perfil==5 OR $id_usuario == 31 OR $id_usuario == 50) { ?>
							<input type="submit" class="botones btn-aceptar" value="Guardar">
						<?php }else { ?>
							<?php if ($id_perfil==3 AND $unidad['reservada']==0 AND $cant<=0) {?>
								<input type="submit" class="botones btn-aceptar" value="Reservar">
							<?php }
						 	}?>
					<?php endif ?>
				</div>
			</div>
		</div>
		<div id="mensajes_unidad"></div>
	</form>
</div>

<script src="js/form_usados.js"></script>