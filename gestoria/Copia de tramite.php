
<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_GET);
	@session_start();
	$SQL="SELECT * FROM registros_gestoria WHERE id_reg_gestoria = ".$id;
	$res_reg = mysqli_query($con, $SQL);
	$reg = mysqli_fetch_array($res_reg);

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Gestoría</title>
	<link rel="stylesheet" href="css/estilo.css">
	<link href="https://file.myfontastic.com/PKG4Yur63nr52FU8DsbmDY/icons.css" rel="stylesheet">
	<link rel="stylesheet" href="css/estilo_proceso.css">
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="js/js_tramite_ab_clientes.js"></script>
	<script src="js/js_tramite.js"></script>
	<script src="alertas_query/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="shortcut icon" type="image/x-icon" href="dyv.ico" />

	
</head>
<body>

	<section class="mod model-3">
	  <div class="spinner">
	  	<img class="imagen_gira" src="imagenes/logo_dyv.png" alt="">
	  	
	  </div>
	</section>
	<div class="lienzo">
		<div class="form-obs">
			<div class="form-linea">
				Fecha de Observación:
				<input id="fecha_obs" class="" type="date" value="">
			</div>
			<div class="form-linea">
				<textarea name="observacion" id="observacion" cols="30" rows="10"></textarea>
			</div>
			<div class="form-linea">
			<input type="bottom" id="cerrar_form_obs" class="input-40 boton-obs centrar-texto" value="Cancelar">
				<input type="bottom"id="guardar_form_obs" class="input-40 boton-obs centrar-texto" value="Guardar">
			</div>
		</div>
	</div>
	<header>

		<div class="zona-header input-90">
			
			<div class="zona-logo-dyv">
				<img class="logo-dyv" src="imagenes/logodyv.png" alt="DyV">
			</div>
			<div class="zona-logo-tyt">
				<img class="logo-toyota" src="imagenes/logo_toyota.png" alt="Toyota">
			</div>

			
		</div>

	</header>
		<div class="input-80 mg-auto">
			<a class="input-15 icon-fecha-derecha link_volver" href="index.php">Volver al listado</a>
		</div>	
<form action="guardar_tramite.php" method="POST" id="form_tramite">
	<div class="contenedor">
		<div class="contenido">
			<div class="zona-form cuadro">
				

					<h2 class="form_titulo">REGISTRO GESTORIA</h2>
					<input type="hidden" name="id_reg_gestoria" id="id_reg_gestoria" value="<?php echo $reg['id_reg_gestoria']; ?>">
					<div class="contenedor-inputs">
						<div class="form-linea">
							<input type="text" name="nro_leg" id="nro_leg" class="nro_leg input-15 centrar-texto" value="<?php echo $reg['nro_leg']; ?>" placeholder="Nro Leg." disabled>
							<input type="hidden" name="leg" value="<?php echo $reg['leg']; ?>">
							<input type="hidden" id="guardado" name="guardado" value="<?php echo $reg['guardado']; ?>">
							<!-- <input type="number" class="input-15" id="nro_rva" name="nro_rva" value="<?php echo $reg['nro_rva']; ?>" placeholder="Nro Rva" required> -->
							<input type="date" id="fec_rec_tra" name="fec_rec_tra" value="<?php echo $reg['fec_rec_tra']; ?>" required>
							<?php 
								if ($reg['guardado']==0) {
									$SQL="SELECT * FROM sucursales WHERE activo = 1";
									$res_suc=mysqli_query($con, $SQL);
									
								}else{
									$SQL="SELECT * FROM sucursales";
									$res_suc=mysqli_query($con, $SQL);
								}
								
							 ?>
							<select name="sucursal" id="sucursal" required>
								<option value="0">Elija Sucursal</option>
								<?php 
									while ($suc=mysqli_fetch_array($res_suc)) { ?>

										<option value="<?php echo $suc['idsucursal']; ?>" <?php if ($reg['id_sucursal']==$suc['idsucursal']) { echo 'selected'; $suc_sel=$suc['sucres'];
										} ?>><?php echo $suc['sucursal']; ?></option>
								<?php }	 ?>
							</select>

							<input type="hidden" id="suc_res" value="<?php echo $suc_sel; ?>">
							<?php 
								if ($reg['guardado']==0) {
									$SQL="SELECT * FROM usuarios WHERE activo = 1 AND idperfil = 3 ORDER BY nombre";
									$res_usu=mysqli_query($con, $SQL);
								}else{
									$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ORDER BY nombre";
									$res_usu=mysqli_query($con, $SQL);
								}
							 ?>
							<select name="asesor" id="asesor" required>
								<option value="0">Elija Asesor</option>
								<?php 
									while ($usu=mysqli_fetch_array($res_usu)) { ?>
										<option value="<?php echo $usu['idusuario']; ?>" <?php if ($reg['id_asesor']==$usu['idusuario']) { echo "selected"; } ?>><?php echo $usu['nombre']; ?></option>
								<?php }	 ?>
							</select>
							
						</div>
						<hr>
						<div class="form-linea">
							
							<select name="compra" id="compra" class="input-15" required>
								<option value="1" <?php if ($reg['compra']==1) { echo 'selected';} ?>>Nuevo</option>
								<option value="0" <?php if ($reg['compra']==0) { echo 'selected';} ?>>Usado</option>
							</select>
							<input type="text" name="interno" id="interno" class="input-10" value="<?php echo $reg['interno']; ?>" placeholder="Interno">
							
							<?php 
							if ($reg['guardado']==0) {
								$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND activo = 1 ORDER BY posicion";
								$res_grupo = mysqli_query($con, $SQL);
								}else {
									$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 ORDER BY posicion";
									$res_grupo = mysqli_query($con, $SQL);
								}
							 ?>



							<select name="modelo" id="modelo" class="input-15" required>
								<option value="">Modelo</option>
								<?php 
									while ($mod=mysqli_fetch_array($res_grupo)) {?>
										<option value="<?php echo $mod['idgrupo']; ?>" <?php if ($mod['idgrupo']==$reg['id_modelo']){ echo 'selected';} ?>>
											<?php echo $mod['grupo']; ?>
										</option>
									<?php } ?>
							</select>
	
							<?php 
							if ($reg['guardado']==0) {
								$SQL="SELECT * FROM modelos WHERE  activo = 1 AND idgrupo=".$reg['id_modelo'];
								$res_modelos = mysqli_query($con, $SQL);
								}else {
									$SQL="SELECT * FROM modelos WHERE idgrupo=".$reg['id_modelo'];
									$res_modelos = mysqli_query($con, $SQL);
								}
							 ?>
							
							<select name="version" id="version" class="input-45" required>
								<option value="">Seleccionar Versión</option>
								<?php 
									while ($mod=mysqli_fetch_array($res_modelos)) {?>
										<option value="<?php echo $mod['idmodelo']; ?>" <?php if ($mod['idmodelo']==$reg['id_version']){ echo 'selected';} ?>>
											<?php echo $mod['modelo']; ?>
										</option>
									<?php } ?>
							</select>

							<input type="text" name="usado" id="usado" class="input-60" value="<?php echo $reg['usado']; ?>" placeholder="usado">

						</div>
						<div class="form-linea">
							<select name="credito" id="credito" class="input-15">
								<option value="1" <?php if ($reg['prenda']==1){ echo 'selected';} ?>>Prenda Si</option>
								<option value="0" <?php if ($reg['prenda']==0){ echo 'selected';} ?>>Prenda No</option>
							</select>
							<input type="text" id="financiera" name="financiera" class="input-80" value="<?php echo $reg['financiera']; ?>" placeholder="Entidad Finaciera">
						</div>
						<hr>
						<div class="form-linea">
							<select name="tipo_persona" id="tipo_persona" class="input-20">
								<option value="1" <?php if ($reg['tipo_persona']==1){ echo 'selected';} ?>>Persona Jurídica</option>
								<option value="0" <?php if ($reg['tipo_persona']==0){ echo 'selected';} ?>>Persona Física</option>
							</select>
							<div class="input-40">
								<span class="input-40">Estado Gral. Doc. UIF:</span>
								<select name="estado_reg" id="estado_reg" class="input-40">
									<option value="1" <?php if ($reg['estado_reg']==1){ echo 'selected';} ?>>Completo</option>
									<option value="0" <?php if ($reg['estado_reg']==0){ echo 'selected';} ?>>Incompleto</option>
								</select>
							</div>
							<div class="input-10">
								<input type="text" name="cant_miembros" size="1" id="cant_miembros" class="centrar-texto cant_miembros" value="<?php echo $reg['cant_miembro']; ?>">
								<span id="add_member" class="icon-plus"></span>	
							</div>
							

						</div>
						<div class="zona-personas">

						<?php 

							$SQL="DELETE FROM registros_gestoria_clientes WHERE id_reg_gestoria = ". $reg['id_reg_gestoria']." AND guardado = 0";
							$res_cli=mysqli_query($con, $SQL);


							$SQL="SELECT * FROM registros_gestoria_clientes WHERE id_reg_gestoria = ". $reg['id_reg_gestoria'];
							$res_cli=mysqli_query($con, $SQL);
							$cant=0;

							while ($cli=mysqli_fetch_array($res_cli)) {  $cant++; if ($cant==1) { $id_primer_cliente= $cli['id_cliente_gestoria']; $nom_cli=$cli['nombre'];
							$estado_primer_cliente = $cli['estado']; $class_cli='cliente-seleccionado cliente input-80';} else{$class_cli='cliente input-80';}?>
								<input type="hidden" id="id_primer_cliente" value="<?php echo $cli['id_cliente_gestoria'] ?>" >
								<input type="hidden" id="nombre_primer_cliente" value="<?php echo $cli['nombre']; ?>" >
								<div class="form-linea">
									<label class="" for=""><?php echo $cant; ?></label>
									<input type="text" id="" data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="<?php echo $class_cli; ?>" value="<?php echo $cli["nombre"]; ?>" placeholder="Cliente">
									<?php if ($cli['estado']==0) { $class_est_cli="estado-doc icon-asignacion input-5 incompleto";}else{$class_est_cli="estado-doc icon-asignacion input-5 completo";} ?>
									<span data-estado="<?php echo $cli['estado']; ?>" data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="<?php echo $class_est_cli; ?>"></span>
									<span data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="icon-borrar input-5 remove_member"></span>
								</div>
							<?php  } ?>

						</div>	
											
						<hr>
						<div class="form-linea">
							<input type="date" id="fec_rec_gestoria" name="fec_rec_gestoria" value="<?php echo $reg['fec_rec_gestoria'];?>" class="input-20" placeholder="Fecha Registro">

							<?php 
								$SQL="SELECT * FROM provincias";
								$res_prov = mysqli_query($con, $SQL);

							 ?>
							
							<select name="provincia" name="provincia" id="provincia" class="input-20">
								<option value="0">Provincia</option>

								<?php 
									while ($prov=mysqli_fetch_array($res_prov)) { ?>
										<option value="<?php echo $prov['id_provincia'] ?>" <?php if ($prov['id_provincia']==$reg['id_provincia']) { echo 'selected';} ?>><?php echo $prov['provincia']; ?></option>
								<?php } ?>
								
							</select>
							<div class="input-30">
								<?php 
								$SQL="SELECT * FROM registros_gestoria_localidades WHERE id_provincia = ".$reg['id_provincia']." ORDER BY localidad ASC";
								$res_loc=mysqli_query($con, $SQL);
								 ?>
								<select name="loc_registro" id="loc_registro" class="input-90">
									<option value="1">Localidad</option> 
									<!-- value = "1" porque es la localidad vacia en mi tabla -->
									<?php 
									while ($loc=mysqli_fetch_array($res_loc)) { ?>
										<option value="<?php echo $loc['id_localidad'];?>" <?php if ($loc['id_localidad']==$reg['id_localidad']) {
											echo 'selected';} ?>><?php echo $loc['localidad']; ?></option>
									<?php } ?>

								</select>
								<span id="add_localidad" class="icon-plus input-10"></span>
							</div>
								
						</div>
						<div class="form-linea">
							<input type="text" id="patente" name="patente" class="input-20" value="<?php echo $reg['patente']; ?>" placeholder="Patente">


							<?php 
								if ($reg['guardado']==0) {
									$SQL="SELECT * FROM usuarios WHERE activo = 1 AND idperfil = 7";// and idsucursal = ".$reg['id_sucursal'];
									$res_ges=mysqli_query($con, $SQL);
								}else{
									$SQL="SELECT * FROM usuarios WHERE idperfil = 7"; // and idsucursal = ".$reg['id_sucursal'];
									$res_ges=mysqli_query($con, $SQL);
								}
							 ?>
							<select name="gestor" id="gestor" class="input-20">
								<option value="0">Gestor</option>
								<?php 
								while ($ges=mysqli_fetch_array($res_ges)) { ?>
									<option value="<?php echo $ges['idusuario']; ?>" <?php if ($ges['idusuario']==$reg['id_gestor']) {
										echo 'selected';
									} ?>><?php echo $ges['nombre']; ?></option>
								<?php } ?>
							</select>
							<input type="date"  id="fec_ins" name="fec_ins" class="input-20" value="<?php echo $reg['fec_ins']; ?>" placeholder="Fecha Registro">
						</div>
						<hr>
						<div class="form-linea">
						<label for="">Notas:</label>
							<textarea name="notas" id="notas" cols="30" rows="5"><?php echo $reg['notas']; ?></textarea>
						</div>
						<hr>
						<div class="form-linea">
							<?php 
							$SQL = "SELECT * FROM registros_gestoria_obs WHERE id_reg_gestoria = ".$reg['id_reg_gestoria']." ORDER BY fecha DESC";
							$res_obs=mysqli_query($con, $SQL);
							 ?>
							 <div>
							 	<input type="bottom" id="js_cargar_observacion" class="boton-obs centrar-texto" value="Nueva Observación">
							 </div>
							
							<div id="zona-tabla-obs" class="input-100">
								<table>
									<thead>
										<tr>
											<td width="15%">Fecha</td>
											<td>Observación</td>
										</tr>
									</thead>
									<tbody>
										<?php 
										while ($obs=mysqli_fetch_array($res_obs)) {?>
											<tr>
												<td class="centrar-texto"><?php echo cambiarFormatoFecha($obs['fecha']); ?></td>
												<td><?php echo $obs['obs']; ?></td>
											</tr>
										
										 <?php }?>
									</tbody>
								</table>
							</div>
						</div>

					</div>
					

			</div>
			<div class="zona-ckeck-list cuadro">


				<?php 
					$SQL="SELECT * FROM registros_gestoria_clientes_doc WHERE id_cliente_gestoria = $id_primer_cliente";
					$res_doc = mysqli_query($con, $SQL);
				?>


				<h2 class="form_titulo">CHECK LIST U.I.F.</h2>
				<h2 class="form_titulo"><?php echo $nom_cli; ?></h2>
				<div class="zona-checks">
					<div class="input-100">
						<?php 
							$check=0;
							while ($doc=mysqli_fetch_array($res_doc)) { $check++;?>
							
								<div class="form-linea linea-doc">
									

									<?php
									 $SQL="SELECT * FROM registros_gestoria_uif_doc WHERE id_doc_uif = ".$doc['id_doc_uif'];
									 $res_uif = mysqli_query($con, $SQL);
									 $uif=mysqli_fetch_array($res_uif);
									 ?>

									<label class="input-85" for="<?php echo 'check_'.$check; ?>"><?php echo $uif['documentacion'];?></label>
									<input class="item_chech input-10" data-id="<?php echo $doc['id_doc_cli']; ?>" type="checkbox" id="<?php echo 'check_'.$check; ?>" name="<?php echo 'check_'.$check; ?>" <?php if ($doc['estado']==1) {
										echo 'checked';
									} ?>>

								</div>
						<?php }	 ?>
					</div>
				</div>
	
				
			</div>
		</div>

	


		<div class="zona-cabecera">
			<div class="zona-menu-tramite">
				<ul class="menu">
					<li class="item-menu"><a class="icon-cancelar item__link" href="">Cancelar</a></li>
				</ul>
			</div>
			<div class="zona-menu-tramite">
				<ul class="menu">
					<li class="item-menu"><a class="icon-guardar item__link" href=""><input type="submit" class="btn_submit" value="Guardar"></a></li>
				</ul>
			</div>
		</div>
	</div>

</form>


</body>
</html>
