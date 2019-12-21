
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

$SQL="INSERT INTO recepcion (id_sucursal, fecha, hora) VALUES ($id_sucursal, '".date("Y-m-d")."','".date("H:i")."') ";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(id_recepcion) as id FROM recepcion";
$registros=mysqli_query($con, $SQL);
$registro=mysqli_fetch_array($registros);
$id_recepcion = (int)$registro['id'];

 }


if ($id_perfil=='3' && $id_sucursal!=3) {
	$sololectura="readonly='readonly'";
	$deshabilitar='disabled ';
	$deshabilitar_comentario_asesor='';

	//Si el vendedor ingresa la opcion de "visto para al valor 1 (visto)"

	$SQL=" UPDATE recepcion SET ";
	$SQL .=" visto = 1 ";
	$SQL .=" WHERE id_recepcion = ".$id_recepcion;
	$registros=mysqli_query($con, $SQL);

}else{
	$sololectura='';
	$deshabilitar='';
	$deshabilitar_comentario_asesor = "readonly='readonly'";
}

$SQL=" SELECT * FROM recepcion WHERE id_recepcion = ".$id_recepcion;
$registros=mysqli_query($con, $SQL);
$registro = mysqli_fetch_array($registros);

?>

<div class="formulario">
	<form class="form-formulario" action="" method="POST">
		<input type="hidden" id="id_recepcion" value="<?php echo $registro['id_recepcion']; ?>">
		<input type="hidden" id="guardado" value="<?php echo $registro['guardado']; ?>">
		<!-- id="text_busqueda" espara cuando guardo la unidad si tiene filtro me carrgue la pagina con el ultimo filtro realizado -->
		<input type="hidden" name="text_busqueda" id="text_busqueda" value="">
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">
		<input type="hidden" name='id_sucursal' id="id_sucursal" value="<?php echo $id_sucursal; ?>">

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

<!-- 		<div class="titulo centrar-texto">
			 SECTOR RECEPCION
		</div> -->
		<div class="unidad-inputs">
			<div class="lado">
				<!-- <div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo no-confirmada">
						REGISTRO DE RECEPION
					</div>
				</div>-->

				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS DE REGISTRO
					</div>
				</div>


				<div class="form-linea">
				<input class="form-inputs" type="hidden" size="5" id="id_recepcion" name="id_recepcion" value="<?php echo $registro['id_recepcion'];?>">
					<div class="ancho-35">
						<label class="ancho-30" for="">Fecha</label>
						<input class="form-inputs ancho-75" type="date" size="5" id="fecha" name="fecha" value="<?php echo $registro['fecha'];?>" <?php echo $sololectura; ?>>
					</div>
					<div class="ancho-20 centrar-texto">
						<input type="time" class="form-inputs ancho-100" id="hora" name="hora" value="<?php echo $registro['hora']; ?>" <?php echo $sololectura; ?>>
					</div>
<!-- 				</div>
				<div class="form-linea"> -->
					<div class="ancho-45 derecha-texto">
						<label class="ancho-5" for="">Medio</label>
						<select class="form-inputs ancho-70" name="id_acercamiento" id="id_acercamiento">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM recepcion_modo_acercamiento WHERE activo=1";
							$medios=mysqli_query($con, $SQL);
							while ($medio=mysqli_fetch_array($medios)) { ?>
								<option value="<?php echo $medio['id_modo_acercamiento']; ?>" <?php if ($medio['id_modo_acercamiento']==$registro['id_acercamiento']) { echo 'selected';}else{echo $deshabilitar;} ?>><?php echo $medio['modo_acercamiento']; ?></option>
							<?php } ?>
						</select>
						<span class="icon-plus-circled cursor-pointer" id="add_medio_contacto"></span>
					</div>
				</div>
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						DATOS DEL CLIENTE
					</div>
				</div>
				<div class="form-linea">
						<div class="ancho-100">
							<label class="ancho-20" for="">Nombre</label>
							<input class="form-inputs ancho-90" type="text" size="5" id="cliente" name="cliente" value="<?php echo $registro['cliente'].' ';?> " autocomplete="false" <?php echo $sololectura; ?>>
						</div>
				</div>
				<div class="form-linea">
					<div class="ancho-35 centrar-texto">
						<label class="ancho-30" for="">Teléfono</label>
						<input class="form-inputs ancho-70" type="text" size="5" id="fecha" name="telefono" value="<?php echo $registro['telefono'].' ';?> " autocomplete="false" <?php echo $sololectura; ?>>
					</div>
					<div class="ancho-65 derecha-texto">
						<label class="ancho-25" for="">E-mail</label>
						<input style="font-size: 15px;" type="text" class="form-inputs ancho-80" id="mail" name="mail" value="<?php echo $registro['mail']; ?>" autocomplete="false" <?php echo $sololectura; ?>>
					</div>
				</div>

				<div class="form-linea">
					<div class="ancho-40 centrar-texto">
						<label class="ancho-30" for="">Provincia</label>
						<select  class="form-inputs ancho-70" name="id_provincia" id="id_provincia" <?php echo $sololectura; ?>>
						<option value="0"></option>
							<?php
								$SQL="SELECT * FROM provincias";
								$provincias = mysqli_query($con, $SQL);
								while ($provincia=mysqli_fetch_array($provincias)) { ?>
									<option value="<?php echo $provincia['id_provincia']; ?>" <?php if ($provincia['id_provincia']==$registro['id_provincia']) { echo 'selected';	}else{echo $deshabilitar;} ?>><?php echo $provincia['provincia'] ?></option>
								<?php } ?>
						</select>
					</div>
					<div class="ancho-60 derecha-texto">
						<label class="ancho-30" for="">Localidad</label>
						<select class="form-inputs ancho-70"  name="id_localidad" id="id_localidad">
							<option value="0"></option>
							<?php
								$SQL="SELECT * FROM recepcion_localidades WHERE id_provincia = ".$registro['id_provincia'];
								$localidades=mysqli_query($con, $SQL);

								while ($localidad=mysqli_fetch_array($localidades)) { ?>
									<option value="<?php echo $localidad['id_localidad']?>" <?php if ($localidad['id_localidad']==$registro['id_localidad']) { echo 'selected';}else{echo $deshabilitar;} ?>><?php echo $localidad['localidad'] ?></option>
								<?php } ?>

						</select>
						<span class="icon-plus-circled cursor-pointer" id="add_localidad" ></span>
					</div>
				</div>
				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						VEHICULO DE INTERES
					</div>
				</div>
				<div class="form-linea">

					<div class="ancho-40">
						<label class="ancho-20" for="">Modelo</label>
						<select class="form-inputs ancho-2-3" name="id_grupo" id="id_grupo" >
						<option value="0"></option>
						<?php
							if ($registro['guardado']==0) {
								$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND activo = 1 ORDER BY posicion";
							}else{
								$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 ORDER BY posicion";
							}
							$grupos=mysqli_query($con, $SQL);
							while ($modelo=mysqli_fetch_array($grupos)) { ?>
								<option value="<?php echo $modelo['idgrupo']; ?>" <?php if ($modelo['idgrupo']==$registro['id_grupo']) { echo 'selected';	}else{echo $deshabilitar;} ?>><?php echo $modelo['grupo']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="ancho-60">
						<label class="ancho-20" for="">Versión</label>
						<select class="form-inputs ancho-85" name="id_modelo" id="id_modelo" >
						<option value="0"></option>
						<?php
							if ($registro['guardado']==0) {
								$SQL="SELECT * FROM modelos WHERE idgrupo = ".$registro['id_grupo']." AND activo = 1 ORDER BY posicion";
							}else{
								$SQL="SELECT * FROM modelos WHERE idgrupo = ".$registro['id_grupo']." ORDER BY posicion";
							}
							$versiones=mysqli_query($con, $SQL);
							while ($version=mysqli_fetch_array($versiones)) { ?>
								<option value="<?php echo $version['idmodelo']; ?>" <?php if ($version['idmodelo']==$registro['id_modelo']) { echo 'selected'; }else{echo $deshabilitar;} ?>><?php echo $version['modelo']; ?></option>
							<?php }	?>
						</select>
					</div>
				</div>

				<div class="form-linea">
					<div class="centrar-texto ancho-100 subtitulo">
						DERIVACIÓN Y SEGUIMIEMTO
					</div>
				</div>

				<div class="form-linea">

					<div class="ancho-50">
						<label class="ancho-20" for="">Asesor</label>
						<select class="form-inputs ancho-80" name="id_asesor" id="id_asesor">
						<?php
							$SQL="SELECT * FROM usuarios WHERE activo = 1 AND idperfil = 3 AND idsucursal = ".$id_sucursal;
							$asesores=mysqli_query($con, $SQL);
						 ?>
						 <option value="0"></option>
						<?php while ($asesor=mysqli_fetch_array($asesores)) { ?>
							<option value="<?php echo $asesor['idusuario'];?>" <?php if ($asesor['idusuario']==$registro['id_asesor']) {
								echo 'selected'; }else{echo $deshabilitar;}?>><?php echo $asesor['nombre']; ?></option>
						<?php	} ?>
						</select>
					 </div>

					<div class="ancho-30 centrar-texto">
						<label class="ancho-20" for="">Derivado</label>
						<select class="form-inputs" name="derivado" id="derivado">
							<option value="1" <?php if ($registro['derivado']==1) { echo 'selected';	}else{echo $deshabilitar;} ?>>Si</option>
							<option value="0"  <?php if ($registro['derivado']==0) { echo 'selected';	}else{echo $deshabilitar;} ?>>No</option>
						</select>
					</div>

					<div class="ancho-20 derecha-texto">
						<label class="ancho-20" for="">Visto</label>
						<select class="form-inputs" name="visto" id="visto">
							<option value="1" <?php if ($registro['visto']==1) { echo 'selected';	}else{echo $deshabilitar;} ?>>Si</option>
							<option value="0"  <?php if ($registro['visto']==0) { echo 'selected';	}else{echo $deshabilitar;} ?>>No</option>
						</select>
					</div>

				</div>

				<div class="form-linea">
					<div class="ancho-100">
						<label class="ancho-20 izquierda-texto" for="" style="color: red; font-weight: bold; ">Motivo No Compra</label>
						<select class="form-inputs ancho-75" name="motivo_no_compra" id="motivo_no_compra">
							<option value="0"></option>
							<option value="1" <?php if ($registro['motivo_no_compra'] == 1) {
								echo 'selected';
							} ?>>Precio</option>
						</select>
					</div>

				</div>





				<div class="form-linea">
					<hr class="ancho-100">
				</div>

				<div class="form-linea">

					<div class="ancho-30">
						<label class="ancho-20" for="">Carga en CRM</label>
						<select class="form-inputs" name="carga_registro" id="carga_registro">
							<option value="1"  <?php if ($registro['carga_registro']==1) { echo 'selected';	}else{echo $deshabilitar;} ?>>Si</option>
							<option value="0"  <?php if ($registro['carga_registro']==0) { echo 'selected';	}else{echo $deshabilitar;} ?>>No</option>
						</select>
					</div>

					<div class="ancho-40 centrar-texto">
						<label class="ancho-20" for="">Seguimiento</label>
						<select class="form-inputs" name="seguimiento" id="seguimiento">
							<option value="1" <?php if ($registro['seguimiento']==1) { echo 'selected';	}else{echo $deshabilitar;} ?>>Si</option>
							<option value="0" <?php if ($registro['seguimiento']==0) { echo 'selected';	}else{echo $deshabilitar;} ?>>No</option>
						</select>
					</div>

					<div class="ancho-30 derecha-texto">
						<label class="ancho-20" for="">Terminado?</label>
						<select class="form-inputs" name="terminado" id="terminado">
							<option value="1" <?php if ($registro['terminado']==1) { echo 'selected';	}else{echo $deshabilitar;} ?>>Si</option>
							<option value="0" <?php if ($registro['terminado']==0) { echo 'selected';	}else{echo $deshabilitar;} ?>>No</option>
						</select>
					</div>

				</div>

			</div>

		</div>


		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-100">
					<label class="ancho-35" for="">Observación Recepción</label>
				</div>
			</div>
			<div class="form-linea">
				<div class="ancho-100 derecha-texto">
					<textarea class="unidad-obs" name="observacion" id="" cols="30" rows="2" <?php echo $deshabilitar; ?>><?php echo $registro['observacion']; ?></textarea>
				</div>
			</div>
			<div class="form-linea">
				<div class="ancho-100">
					<label class="ancho-35" for="">Observación Asesor</label>
				</div>
			</div>
			<div class="form-linea">
				<div class="ancho-100 derecha-texto">
					<textarea class="unidad-obs" name="observacion_asesor" id="" cols="30" rows="2" <?php echo $deshabilitar_comentario_asesor; ?>><?php echo $registro['observacion_asesor']; ?></textarea>
				</div>
			</div>
		</div>
<!-- 		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-40">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-40 derecha-texto">
					<input type="submit" class="botones btn-aceptar" value="Guardar">
				</div>
			</div>
		</div> -->
		<div id="mensajes_formulario"></div>

	</form>
</div>

<script src="js/registro_formulario.js"></script>