
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

if ($_SESSION["es_gerente"]==1) {
	$mostar="";
}else{
	$mostar="disabled";
};


if (isset($nuevo) ) {

$SQL="INSERT INTO prospectos (fecha_carga, id_usuario) VALUES ('".date("Y-m-d")."',{$id_usuario}) ";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(id) as id FROM prospectos";
$prospectos=mysqli_query($con, $SQL);
$prospecto=mysqli_fetch_array($prospectos);
$id = (int)$prospecto['id'];

 }

$SQL=" SELECT * FROM prospectos WHERE id = ".$id;
$prospectos=mysqli_query($con, $SQL);
$prospecto = mysqli_fetch_array($prospectos);


$SQL="DELETE FROM prospectos_seguimientos WHERE id_guardado = 0 AND id_prospecto = ".$id;
mysqli_query($con, $SQL);

?>

<div class="formulario-prospecto">
	<form class="form-formulario" action="" method="POST">
		<input type="hidden" id="id" value="<?php echo $prospecto['id']; ?>">
		<input type="hidden" id="guardado" value="<?php echo $prospecto['guardado']; ?>">
		<!-- id="text_busqueda" espara cuando guardo la unidad si tiene filtro me carrgue la pagina con el ultimo filtro realizado -->
		<input type="hidden" name="text_busqueda" id="text_busqueda" value="">
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">
		<input type="hidden" name='id_sucursal' id="id_sucursal" value="<?php echo $id_sucursal; ?>">

<!-- 		<div class="titulo centrar-texto">
			 SECTOR RECEPCION
		</div> -->
		<div class="unidad-inputs">
			<div class="lado lado-60">
				<div class="form-linea ">
					<input class="form-inputs" type="hidden" id="id_" name="id_" value="<?php echo $prospecto['id']; ?>">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS BASE DEL PROSPECTO 
					</div>
				</div>
				<div class="form-linea">

					<div class="ancho-25">
						<label class="ancho-35" for="">N° Prosp.</label>
						<input class="form-inputs ancho-50 derecha-texto" type="text" id="nombre" name="nombre" value="<?php echo $prospecto['id'];?>" autocomplete="off" readonly>
					</div>
					<div class="ancho-35 centrar-texto">
						<label class="ancho-35" for="">Fec. Carga</label>
						<input class="input-fecha ancho-65" type="date" size="5" id="fecha_carga" name="fecha_carga" value="<?php echo $prospecto['fecha_carga'];?>" readonly>
					</div>

					<div class="ancho-35 centrar-texto">
						<label class="ancho-30" for="">Fec. Alta</label>
						<?php 
							if ($prospecto['guardado']==0) {?>
								<input class="input-fecha ancho-70" type="date" size="5" id="fecha_alta" name="fecha_alta" value="<?php echo date('Y-m-d');?>">
						<?php }else{ ?>
							<input class="input-fecha ancho-70" type="date" size="5" id="fecha_alta" name="fecha_alta" value="<?php echo $prospecto['fecha_alta'];?>">
						<?php } ?>
						
					</div>
				</div>

				<div class="form-linea ">
					<input class="form-inputs" type="hidden" id="id" name="id" value="<?php echo $prospecto['id']; ?>">
					<div class="centrar-texto ancho-100 subtitulo">
						CONSOLIDACION  
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-65 derecha-texto">
						<label class="ancho-30" for="">Cliente</label>
						<input class="form-inputs ancho-15 derecha-texto" type="text" size="5" id="id_cliente" name="id_cliente" value="<?php echo $prospecto['id_cliente'];?>" autocomplete="off" readonly>
							<?php 

							$SQL="SELECT * FROM prospectos_clientes WHERE id = {$prospecto['id_cliente']}";
							$clientes = mysqli_query($con, $SQL);
							if (!empty($clientes)) { $cliente=mysqli_fetch_array($clientes);	}else{$cliente['nombre']='';$cliente['telefono']='';$cliente['celular']='';}

							 ?>
						<input class="form-inputs ancho-70" type="text" id="nombre_cliente" size="5" value="<?php echo $cliente['nombre']; ?>" autocomplete="off" readonly>
					</div>
					<?php if ($prospecto['guardado']==1) { ?>
						<div class="ancho-5 centrar-texto">
							<a href="#" id="detalle_cliente"><span class="icon-search"></span></a>
						</div>
					<?php } ?>
					<?php if ($prospecto['guardado']==0) { ?>
						<div class="ancho-5 centrar-texto">
							<a href="#" id="agregar_cliente"><span class="icon-plus"></span></a>
						</div>
					<?php } ?>

						<div class="ancho-5 centrar-texto">
							<a href="#" style="display:none;" id="detalle_cliente"><span class="icon-search"></span></a>
						</div>

					

					<div class="ancho-35 derecha-texto">
						<label class="ancho-30" for="">Asesor *</label>
						<select class="form-inputs ancho-70" name="id_usuario" id="id_usuario">
						<?php 
							$SQL="SELECT * FROM usuarios WHERE activo = 1 AND idperfil = 3 AND id_negocio = 2 ORDER BY nombre";
							$asesores=mysqli_query($con, $SQL);
						 ?>
						 <option value="0"></option>
						<?php while ($asesor=mysqli_fetch_array($asesores)) { ?>
							<option value="<?php echo $asesor['idusuario'];?>" <?php if ($asesor['idusuario']==$prospecto['id_usuario']) {
								echo 'selected'; }else{ echo $mostar; }?>><?php echo $asesor['nombre']; ?></option>
						<?php	} ?>
						</select>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-65 izquierda-texto">
						<label class="ancho-40" for="">Teléfono</label>
						<input class="form-inputs ancho-75 izquierda-texto" id="telefono_cliente" type="text" size="5" value="<?php echo $cliente['telefono']." - ".$cliente['celular'];?>" autocomplete="off" readonly>
						
					</div>
					<div class="ancho-35 derecha-texto">
						<label class="ancho-30" for="">Sucursal</label>
						<?php
						 $SQL="SELECT * FROM sucursales";
						 $sucursales = mysqli_query($con, $SQL);
						 $sucursal= mysqli_fetch_array($sucursales); 
						  ?>
						<input class="form-inputs ancho-70" type="text" size="5" value="<?php echo $sucursal['sucursal']; ?>" readonly>
						
					</div>
				</div>
				<div class="form-linea ">
					<input class="form-inputs" type="hidden" id="id" name="id" value="<?php echo $prospecto['id']; ?>">
					<div class="centrar-texto ancho-100 subtitulo">
						PLAN DE AHORRO DE INTERES 
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100 derecha-texto">
						<label class="ancho-30" for="">Modelo Plan de Ahorro *</label>
						<select class="form-inputs ancho-70" name="id_modelo_tpa" id="id_modelo_tpa">
						<?php 
							$SQL="SELECT * FROM modelos_tpa WHERE activo = 1 ORDER BY orden";
							$modelos_tpa=mysqli_query($con, $SQL);
						 ?>
						 <option value="0"></option>
						<?php while ($modelo_tpa=mysqli_fetch_array($modelos_tpa)) { ?>
							<option value="<?php echo $modelo_tpa['id'];?>" <?php if ($modelo_tpa['id']==$prospecto['id_modelo_tpa']) {
								echo 'selected'; }?>><?php echo $modelo_tpa['modelo']; ?></option>
						<?php	} ?>
						</select>
					</div>
				</div>	
				<div class="form-linea">
					<div class="ancho-100">
						<hr>
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-100 centrar-texto">
						<span class="negrita subrayado">Modelo de Interés</span>
					</div>
				</div>

				<div class="form-linea">

					<div class="ancho-40">
						<label class="ancho-25" for="">Modelo</label>
						<select class="form-inputs ancho-75" name="id_modelo" id="id_modelo" >
						<option value="0"></option>
						<?php 
							if ($prospecto['guardado']==0) {
								$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND activo = 1 ORDER BY posicion";
							}else{
								$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 ORDER BY posicion";
							}
							$grupos=mysqli_query($con, $SQL);
							while ($modelo=mysqli_fetch_array($grupos)) { ?>
								<option value="<?php echo $modelo['idgrupo']; ?>" <?php if ($modelo['idgrupo']==$prospecto['id_modelo']) { echo 'selected';	} ?>><?php echo $modelo['grupo']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-60 derecha-texto">
						<label class="ancho-20" for="">Versión</label>
						<select class="form-inputs ancho-85" name="id_version" id="id_version" >
						<option value="0"></option>
						<?php 
							if ($prospecto['guardado']==0) {
								$SQL="SELECT * FROM modelos WHERE idgrupo = ".$prospecto['id_modelo']." AND activo = 1 ORDER BY posicion";
							}else{
								$SQL="SELECT * FROM modelos WHERE idgrupo = ".$prospecto['id_modelo']." ORDER BY posicion";
							}
							$versiones=mysqli_query($con, $SQL);
							while ($version=mysqli_fetch_array($versiones)) { ?>
								<option value="<?php echo $version['idmodelo']; ?>" <?php if ($version['idmodelo']==$prospecto['id_version']) { echo 'selected'; } ?>><?php echo $version['modelo']; ?></option>
							<?php }	?>
						</select>
					</div>
				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						ACERCAMIENTO DEL CLIENTE
					</div>
				</div>
				<div class="form-linea">

					<div class="ancho-30">
						<label class="ancho-60" for="">Modo*</label>
						<select class="form-inputs ancho-60" name="id_modo_acercamiento" id="id_modo_acercamiento" >
						<option value="0"></option>
						<?php 
							if ($prospecto['guardado']==0) {
								$SQL="SELECT * FROM prospectos_modos_acercamientos WHERE activo = 1 ";
							}else{
								$SQL="SELECT * FROM prospectos_modos_acercamientos";
							}
							$modos=mysqli_query($con, $SQL);
							while ($modo=mysqli_fetch_array($modos)) { ?>
								<option value="<?php echo $modo['id']; ?>" <?php if ($modo['id']==$prospecto['id_modo_acercamiento']) { echo 'selected'; } ?>><?php echo $modo['modo']; ?></option>
							<?php }	?>
						</select>
					</div>
					<div class="ancho-40 centrar-texto">
						<label class="ancho-10" for="">Canal*</label>
						<select class="form-inputs ancho-75" name="id_canal_acercamiento" id="id_canal_acercamiento" >
						<option value="0"></option>
						<?php 
							if ($prospecto['guardado']==0) {
								$SQL="SELECT * FROM prospectos_canales_acercamientos WHERE activo = 1";
							}else{
								$SQL="SELECT * FROM prospectos_canales_acercamientos";
							}
							$canales=mysqli_query($con, $SQL);
							while ($canal=mysqli_fetch_array($canales)) { ?>
								<option value="<?php echo $canal['id']; ?>" <?php if ($canal['id']==$prospecto['id_canal_acercamiento']) { echo 'selected'; } ?>><?php echo $canal['canal']; ?></option>
							<?php }	?>
						</select>
					</div>

					<div class="ancho-35 derecha-texto">
						<label class="ancho-40" for="">Ponderar*</label>
						<select class="form-inputs ancho-60" name="id_ponderacion" id="id_ponderacion" >
						<option value="0"></option>
						<?php 
							if ($prospecto['guardado']==0) {
								$SQL="SELECT * FROM prospectos_ponderacion WHERE activo = 1";
							}else{
								$SQL="SELECT * FROM prospectos_ponderacion";
							}
							$ponderaciones=mysqli_query($con, $SQL);
							while ($ponderacion=mysqli_fetch_array($ponderaciones)) { ?>
								<option value="<?php echo $ponderacion['id']; ?>" <?php if ($ponderacion['id']==$prospecto['id_ponderacion']) { echo 'selected'; } ?>><?php echo $ponderacion['ponderacion']; ?></option>
							<?php }	?>
						</select>
					</div>
				</div>

			</div>

			<div class="lado lado-40">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						SEGUIMIENTO DE PROSPECTO
					</div>
				</div>

				<div class="form-linea derecha-texto">
					<div class="ancho-100 derecha-texto">
						<a href="" id="nuevo_contacto" data-id="<?php echo $prospecto['id'] ?>" class="negrita derecha-texto"><span class="icon-plus"> </span>Nuevo</a>
					</div>
				</div>

				<div class="form-linea" id="zona_tabla_seguimiento">
					<?php include('prospecto_formulario_seguimiento.php'); ?>
				</div>
			</div> 
			<!-- Cierre lado Derecho -->
		</div>


		<div class="unidad-inputs">
			<div class="lado lado-60">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						OBSERVACION
					</div>
				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100">
						<textarea class="unidad-obs" name="observacion" id="" cols="30" rows="4"><?php echo $prospecto['observacion']; ?></textarea>
					</div>
				</div>

			</div>
			<div class="lado lado-40">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						CIERRE DEL PROSPECTO
					</div>
				</div>

				<div class="form-linea">

					<div class="ancho-45 centrar-texto">
						<label class="ancho-30" for="">Fecha</label>
						<input class="input-fecha ancho-75" type="date" size="5" id="fecha_cierre" name="fecha_cierre" value="<?php echo $prospecto['fecha_cierre'];?>">
					</div>


					<div class="ancho-55 derecha-texto">
						<label class="ancho-25" for="">Motivo</label>
						<select class="form-inputs ancho-70" name="id_motivo_cierre" id="id_motivo_cierre">
						<?php 
							$SQL="SELECT * FROM prospectos_motivos_cierre";
							$motivos=mysqli_query($con, $SQL);
						 ?>
						 <option value="0"></option>
						<?php while ($motivo=mysqli_fetch_array($motivos)) { ?>
							<option value="<?php echo $motivo['id'];?>" <?php if ($motivo['id']==$prospecto['id_motivo_cierre']) {
								echo 'selected'; }?>><?php echo $motivo['motivo']; ?></option>
						<?php	} ?>
						</select>
					</div>
				</div>

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

<script src="js/prospecto_formulario.js?<?php  echo rand();?>"></script>
