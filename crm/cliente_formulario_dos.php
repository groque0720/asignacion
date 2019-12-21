
<?php

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();
$id_perfil=$_SESSION["idperfil"];
$id_sucursal=$_SESSION["idsuc"];
$id_usuario = $_SESSION["id"];
$nom_usu=$_SESSION["usuario"];


if (isset($nuevo) ) {

$SQL="INSERT INTO prospectos_clientes (fecha_alta) VALUES ('".date("Y-m-d")."') ";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(id) as id FROM prospectos_clientes";
$clientes=mysqli_query($con, $SQL);
$cliente=mysqli_fetch_array($clientes);
$id = (int)$cliente['id'];

 }

$SQL=" SELECT * FROM prospectos_clientes WHERE id = ".$id;
$clientes=mysqli_query($con, $SQL);
$cliente = mysqli_fetch_array($clientes);

?>

<div class="formulario">
	<form class="form-formulario" action="" method="POST">
		<input type="hidden" id="id" value="<?php echo $cliente['id']; ?>">
		<input type="hidden" id="id_form_cliente" value="<?php echo $cliente['id']; ?>">
		<input type="hidden" id="guardado" value="<?php echo $cliente['guardado']; ?>">
		<!-- id="text_busqueda" espara cuando guardo la unidad si tiene filtro me carrgue la pagina con el ultimo filtro realizado -->
		<input type="hidden" name="text_busqueda" id="text_busqueda" value="">
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">
		<input type="hidden" name='id_sucursal' id="id_sucursal" value="<?php echo $id_sucursal; ?>">
		<input type="hidden" name='es_dato' id="es_dato" value="0">
		<?php 
			//proceso si es llamado del formulario de prospecto
			if (isset($id_prospecto)) { ?>
				<input type="hidden" name='id_prospecto' id="id_prospecto" value="<?php echo $id_prospecto; ?>">
				<input type="hidden" name='alta_desde_prospecto' id='alta_desde_prospecto' value="1">
				<input type="hidden" name="id_prospecto_alta" id="id_prospecto_alta" value="<?php echo $id_prospecto; ?>">

		<?php	}else{ ?>
				<input type="hidden" name='id_prospecto' id="id_prospecto" value="">
				<input type="hidden" name='alta_desde_prospecto' id='alta_desde_prospecto' value="0">
				<input type="hidden" name="id_prospecto_alta" id="id_prospecto_alta" value="<?php echo $cliente['id_prospecto_alta']; ?>">
		<?php	}   ?>

<!-- 		<div class="titulo centrar-texto">
			 SECTOR RECEPCION
		</div> -->
		<div class="unidad-inputs">
			<div class="lado">
				 <div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo no-confirmada">
						REGISTRO DE CLIENTE
					</div>
				</div>

				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS BASE 
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-50 ">
						<label class="ancho-40" for="">Código Cliente</label>
						<input class="form-inputs ancho-40 derecha-texto" type="text" id="id_" name="id_" value="<?php echo $cliente['id'];?>" autocomplete="off" disabled>
					</div>
					<div class="ancho-50 derecha-texto">
						<label class="ancho-40" for="">Fecha Alta</label>
						<input class="input-fecha ancho-60" type="date" size="5" id="fecha_alta" name="fecha_alta" value="<?php echo $cliente['fecha_alta'];?>" disabled>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100">
						<hr>
					</div>
				</div>
				<div class="form-linea">
					<input class="form-inputs" type="hidden" id="id" name="id" value="<?php echo $cliente['id']; ?>">
					<div class="ancho-70">
						<label class="ancho-30" for="">Ap. y Nom.</label>
						<input class="form-inputs ancho-75" type="text" id="cliente" name="nombre" value="<?php echo $cliente['nombre'];?>" autocomplete="off">
					</div>
					<div class="ancho-30 derecha-texto">
						<label class="ancho-35" for="">Estado</label>
						<select class="form-inputs ancho-60" name="id_estado_cliente" id="id_estado_cliente">
						<?php 
							$SQL="SELECT * FROM prospectos_clientes_estados";
							$estados=mysqli_query($con, $SQL);
							while ($estado = mysqli_fetch_array($estados)) { ?>
								<option value="<?php echo $estado['id']; ?>" <?php if ($estado['id']==$cliente['id_estado_cliente']) { echo 'selected';}else{ echo 'disabled';} ?>><?php echo $estado['estado_cliente']; ?></option>
						<?php }  ?>
							
						</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-30 centrar-texto">
						<label class="ancho-30" for="">Fec. Nac.</label>
						<input class="input-fecha ancho-90" type="date" size="5" id="fec_nac" name="fec_nac" value="<?php echo $cliente['fec_nac'];?>">
					</div>
					<div class="ancho-20 centrar-texto">
						<label class="ancho-30" for="">Sexo</label>
						<select class="form-inputs ancho-90" name="id_sexo" id="id_sexo">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM sexos";
							$sexos=mysqli_query($con, $SQL);
							while ($sexo=mysqli_fetch_array($sexos)) { ?>
								<option value="<?php echo $sexo['id']; ?>" <?php if ($sexo['id']==$cliente['id_sexo']) { echo 'selected';}?>><?php echo $sexo['sexo']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-25 centrar-texto">
						<label class="ancho-30" for="">Nro Doc.</label>
						<input class="form-inputs ancho-90 centrar-texto" type="text" id="nro_doc" name="nro_doc" value="<?php echo $cliente['nro_doc'];?>" autocomplete="off">
					</div>
					<div class="ancho-25 derecha-texto">
						<div class="centrar-texto"><label class="ancho-30 centrar-texto" for="">Cuil/Cuit</label></div>
						<input class="form-inputs ancho-90 centrar-texto" type="text" id="cuil_cuit" name="cuil_cuit" value="<?php echo $cliente['cuil_cuit'];?>" autocomplete="off">
					</div>

				</div>

				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS DE CONTACTO
					</div>
				</div>

				<div class="form-linea">

					<div class="ancho-45 centrar-texto">
						<label class="ancho-40" for="">Teléfono</label>
						<input class="form-inputs ancho-75 " type="text" id="telefono" name="telefono" value="<?php echo $cliente['telefono'];?>" autocomplete="off">
					</div>
					<div class="ancho-45 derecha-texto">
						<label class="ancho-40" for="">Celular</label>
						<input class="form-inputs ancho-75 " type="text" id="celular" name="celular" value="<?php echo $cliente['celular'];?>" autocomplete="off">
					</div>

				</div>
				<div class="form-linea">

					<div class="ancho-65">
						<label class="ancho-30" for="">E-mail</label>
						<input class="form-inputs ancho-80" type="text" id="email" name="email" value="<?php echo $cliente['email'];?>" autocomplete="off">
					</div>
					<div class="ancho-40 derecha-texto">
						<label class="ancho-60" for="">Preferencia</label>
						<select class="form-inputs ancho-50" name="id_pref_contacto" id="id_pref_contacto">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM preferencias_contactos";
							$preferencias_contactos=mysqli_query($con, $SQL);
							while ($preferencia=mysqli_fetch_array($preferencias_contactos)) { ?>
								<option value="<?php echo $preferencia['id']; ?>" <?php if ($preferencia['id']==$cliente['id_pref_contacto']) { echo 'selected';}?>><?php echo $preferencia['preferencia']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DIRECCION
					</div>
				</div>


				<div class="form-linea">
					<div class="ancho-45 centrar-texto">
						<label class="ancho-40" for="">Provincia</label>
						<select class="form-inputs ancho-75" name="id_provincia" id="id_provincia">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM provincias";
							$provincias=mysqli_query($con, $SQL);
							while ($provincia=mysqli_fetch_array($provincias)) { ?>
								<option value="<?php echo $provincia['id_provincia']; ?>" <?php if ($provincia['id_provincia']==$cliente['id_provincia']) { echo 'selected';}?>><?php echo $provincia['provincia']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-45 centrar-texto">
						<label class="ancho-40" for="">Localidad</label>
						<select class="form-inputs ancho-75" name="id_localidad" id="id_localidad">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM localidades";
							$localidades=mysqli_query($con, $SQL);
							while ($localidad=mysqli_fetch_array($localidades)) { ?>
								<option value="<?php echo $localidad['id']; ?>" <?php if ($localidad['id']==$cliente['id_localidad']) { echo 'selected';}?>><?php echo $localidad['localidad']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-100 centrar-texto">
						<label class="ancho-30" for="">Dirección</label>
						<input class="form-inputs ancho-85" type="text" id="direccion" name="direccion" value="<?php echo $cliente['direccion'];?>" autocomplete="off">
					</div>
				</div>

				
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS ADICIONALES
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-35 centrar-texto">
						<label class="ancho-50" for="">Ocupación</label>
						<select class="form-inputs ancho-100" name="id_ocupacion" id="id_ocupacion">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM ocupaciones";
							$ocupaciones=mysqli_query($con, $SQL);
							while ($ocupacion=mysqli_fetch_array($ocupaciones)) { ?>
								<option value="<?php echo $ocupacion['id']; ?>" <?php if ($ocupacion['id']==$cliente['id_ocupacion']) { echo 'selected';}?>><?php echo $ocupacion['ocupacion']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-25 centrar-texto">
						<label class="ancho-50" for="">Estado Civil</label>
						<select class="form-inputs ancho-100" name="id_estado_civil" id="id_estado_civil">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM estados_civiles";
							$estados_civiles=mysqli_query($con, $SQL);
							while ($estado_civil=mysqli_fetch_array($estados_civiles)) { ?>
								<option value="<?php echo $estado_civil['id']; ?>" <?php if ($estado_civil['id']==$cliente['id_estado_civil']) { echo 'selected';}?>><?php echo $estado_civil['estado_civil']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-20 centrar-texto">
						<label class="ancho-50" for="">Grupo Familiar</label>
						<select class="form-inputs ancho-100" name="id_grupo_familiar" id="id_grupo_familiar">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM grupos_familiares";
							$grupos_familiares=mysqli_query($con, $SQL);
							while ($grupo_familiar=mysqli_fetch_array($grupos_familiares)) { ?>
								<option value="<?php echo $grupo_familiar['id']; ?>" <?php if ($grupo_familiar['id']==$cliente['id_grupo_familiar']) { echo 'selected';}?>><?php echo $grupo_familiar['grupo_familiar']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-10 centrar-texto">
						<label class="ancho-50" for="">Hijos</label>
						<select class="form-inputs ancho-100" name="cant_hijos" id="cant_hijos">
						<option value="0" <?php if ($cliente['cant_hijos']==0) { echo 'selected';} ?>>0</option>
						<option value="1" <?php if ($cliente['cant_hijos']==1) { echo 'selected';} ?>>1</option>
						<option value="2" <?php if ($cliente['cant_hijos']==2) { echo 'selected';} ?>>2</option>
						<option value="3" <?php if ($cliente['cant_hijos']==3) { echo 'selected';} ?>>+2</option>
						</select>
					</div>

				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						OBSERVACION
					</div>
				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100">
						<textarea class="unidad-obs" name="observacion" id="" cols="30" rows="4"><?php echo $cliente['observacion']; ?></textarea>
					</div>
				</div>

		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-40">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-40 derecha-texto">
					<input type="submit" class="botones btn-aceptar" value="Guardar">
				</div>
			</div>
		</div>
		<div id="mensajes_formulario"></div>
		
	</form>
</div>

<script src="js/cliente_formulario.js"></script>