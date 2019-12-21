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

if ($id_perfil<>14) {
	$lectura="readonly='readonly'";
	$deshabilitado="disabled";
	$asesor_class='input-asesor';
}

if (isset($nuevaUnidad)) { // alta de nueva unidad

	$SQL="SELECT MAX(nro_unidad) as nro_unidad FROM asignaciones_usados";
	$unidades=mysqli_query($con, $SQL);
	$unidad=mysqli_fetch_array($unidades);
	$nro = (int)$unidad['nro_unidad'] + 1;

	$SQL="INSERT INTO asignaciones_usados (nro_unidad, guardado) VALUES ($nro, 0)";
	mysqli_query($con, $SQL);

	$SQL="SELECT MAX(id_unidad) AS id FROM asignaciones_usados";
	$res_query=mysqli_query($con, $SQL);
	$res_unidad = mysqli_fetch_array($res_query);

	$id_unidad= $res_unidad['id'];
}

$SQL="SELECT * FROM asignaciones_usados WHERE id_unidad =".$id_unidad;
$unidades = mysqli_query($con, $SQL);
$unidad=mysqli_fetch_array($unidades);

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
				<div class="form-linea ancho-100">
					<hr class="ancho-100">
				</div>

				<div class="form-linea">
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-1-3" for="">Vehículo</label>
						<select class="form-inputs ancho-75" id="id_marca" name="id_marca" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php 
								$SQL="SELECT * FROM asignaciones_usados_marcas ORDER BY marca";
								$marcas=mysqli_query($con, $SQL);
								while ($marca=mysqli_fetch_array($marcas)) { ?>
									<option value="<?php echo $marca['id_marca']; ?>" <?php if ($marca['id_marca']==$unidad['id_marca']) {echo 'selected';	} ?>><?php echo $marca['marca']; ?></option>
								<?php } ?>
						</select><a href=""><span class="icon-carga-uno"></span></a>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-1-3" for="">Modelo</label>
						<select class="form-inputs ancho-75" id="id_modelo" name="id_modelo" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php 
								$SQL="SELECT * FROM asignaciones_usados_modelos ORDER BY modelo";
								$modelos=mysqli_query($con, $SQL);
								while ($modelo=mysqli_fetch_array($modelos)) { ?>
									<option value="<?php echo $modelo['id_modelo']; ?>" <?php if ($modelo['id_modelo']==$unidad['id_modelo']) {echo 'selected';	} ?>><?php echo $modelo['modelo']; ?></option>
								<?php } ?>
						</select><a href=""><span class="icon-carga-uno"></span></a>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<label class="ancho-1-3" for="">Versión</label>
						<select class="form-inputs ancho-75" id="id_version" name="id_version" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php 
								$SQL="SELECT * FROM asignaciones_usados_versiones ORDER BY version";
								$versiones=mysqli_query($con, $SQL);
								while ($version=mysqli_fetch_array($versiones)) { ?>
									<option value="<?php echo $version['id_version']; ?>" <?php if ($version['id_version']==$unidad['id_version']) {echo 'selected';	} ?>><?php echo $version['version']; ?></option>
								<?php } ?>
						</select><a href=""><span class="icon-carga-uno"></span></a>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-1-3">
						<div class="ancho-100 centrar-texto"><label class="ancho-100" for="">Año</label></div>
						<input class="form-inputs" type="text" size="10" id="año" name="año" value="<?php echo $unidad['año'].' '; ?> " <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<div class="ancho-100 centrar-texto"><label class="ancho-100" for="">Km</label></div>
						<input class="form-inputs centrar-texto" type="text" size="10" id="km" name="km" value="<?php echo $unidad['km']; ?>" <?php echo $lectura; ?>>
					</div>
					<div class="ancho-1-3 centrar-texto">
						<div class="ancho-100 centrar-texto"><label class="ancho-100" for="">Dominio</label></div>
						<input class="form-inputs centrar-texto" type="text" size="10" id="dominio" name="dominio" value="<?php echo $unidad['dominio']; ?>" <?php echo $lectura; ?>>
					</div>
				</div>
				<div class="form-linea ancho-100">
					<hr class="ancho-100">
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-30 ">
						<div class="ancho-100 centrar-texto"><label class="ancho-1-3" for="">Color</label></div>
						<select class="form-inputs ancho-75" id="id_color" name="id_color" <?php echo $lectura; ?>>
							<option value="0"></option>
							<?php 
								$SQL="SELECT * FROM asignaciones_usados_colores ORDER BY color";
								$colores=mysqli_query($con, $SQL);
								while ($color=mysqli_fetch_array($colores)) { ?>
									<option value="<?php echo $color['idcolor']; ?>" <?php if ($color['idcolor']==$unidad['id_color']) {echo 'selected';	} ?>><?php echo $color['color']; ?></option>
								<?php } ?>
						</select>
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
				</div>
				<div class="form-linea centrar-texto">
					<div class="ancho-60 ">
						<div class="ancho-100 centrar-texto"><label class="ancho-1-3" for="">Último Dueño</label></div>
						<input class="form-inputs centrar-texto" type="text" size="10" id="ultimo_dueño" name="ultimo_dueño" value="<?php echo $unidad['ultimo_dueño']; ?>" <?php echo $lectura; ?>>
						
					</div>
					<div class="ancho-30">
						<div class="ancho-100 centrar-texto"><label class="ancho-1-3" for="">Asesor de Toma</label></div>
						<select class="form-inputs ancho-85 <?php echo $asesor_class; ?>" name="id_asesor" id="id_asesor" <?php echo $lectura; ?>>
									<option value="1"></option>
									<?php 
										$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ORDER BY nombre";
										$usuarios = mysqli_query($con, $SQL);
										while ($usuario=mysqli_fetch_array($usuarios)) { ?>
											<option value="<?php echo $usuario['idusuario']; ?>" <?php if ($usuario['idusuario']==$unidad['id_asesor']) { echo 'selected';	} ?>><?php echo $usuario['nombre']; ?></option>
										<?php } ?>
							</select>
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
							<!-- <input class="form-inputs centrar-texto btn-reservar" type="submit" size="5" value="Reservar"> -->
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
										$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ORDER BY nombre";
										$usuarios = mysqli_query($con, $SQL);
										while ($usuario=mysqli_fetch_array($usuarios)) { ?>
											<option value="<?php echo $usuario['idusuario']; ?>" <?php if ($usuario['idusuario']==$unidad['id_asesor']) { echo 'selected';	} ?>><?php echo $usuario['nombre']; ?></option>
										<?php } ?>
							</select>
					</div>
				</div>


				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						ENTREGA DE UNIDAD
					</div>
				</div>
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
		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-20">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-20 derecha-texto">

					<?php if ($id_perfil==14) { ?>
						<input type="submit" class="botones btn-aceptar" value="Guardar">
					<?php }else { ?>
						<?php if ($id_perfil==3 AND $unidad['estado_reserva']!=1) {?>
							<input type="submit" class="botones btn-aceptar" value="Reservar">
						<?php }
					 	}?>
				
				</div>
			</div>
		</div>
		<div id="mensajes_unidad"></div>
		
	</form>
</div>

<script src="js/unidad.js"></script>