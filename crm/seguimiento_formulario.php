
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

$SQL="INSERT INTO prospectos_seguimientos (guardado, id_prospecto) VALUES (0, $id_prospecto) ";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(id) as id FROM prospectos_seguimientos";
$seguimientos=mysqli_query($con, $SQL);
$seguimiento=mysqli_fetch_array($seguimientos);
$id = (int)$seguimiento['id'];

 }

$SQL=" SELECT * FROM prospectos_seguimientos WHERE id = ".$id;
$seguimientos=mysqli_query($con, $SQL);
$seguimiento = mysqli_fetch_array($seguimientos);

?>

<div class="formulario">
	<form class="form-formulario" id="form-seguimiento" action="" method="POST">
		<input type="hidden" id="id_seguimiento" value="<?php echo $seguimiento['id']; ?>">
		<input type="hidden" id="guardado_seguimiento" value="<?php echo $seguimiento['guardado']; ?>">
		<!-- id="text_busqueda" espara cuando guardo la unidad si tiene filtro me carrgue la pagina con el ultimo filtro realizado -->
		<input type="hidden" name='id_perfil' id="id_perfil" value="<?php echo $id_perfil; ?>">
		<input type="hidden" name='id_sucursal' id="id_sucursal" value="<?php echo $id_sucursal; ?>">
		<input type="hidden" name='id_prospecto' id="id_prospecto" value="<?php echo $seguimiento['id_prospecto'];  ?>">
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
				<input class="form-inputs" type="hidden" id="id" name="id" value="<?php echo $seguimiento['id']; ?>">
				<div class="form-linea ">
					<div class="centrar-texto ancho-100 subtitulo">
						PROXIMO CONTACTO
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-50 ">
						<label class="ancho-40" for="">Fecha</label>
						<input class="input-fecha ancho-60" type="date" size="5" id="fec_contacto" name="fec_contacto" value="<?php echo $seguimiento['fec_contacto']; ?>">
					</div>
					<div class="ancho-50 derecha-texto">
						<label class="ancho-40" for="">Hora</label>
						<input class="input-fecha ancho-60" type="time" size="5" id="hora" name="hora" value="<?php echo $seguimiento['hora'];   ?>">
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100">
						<hr>
					</div>
				</div>
				<div class="form-linea">
					
					<div class="ancho-50">
						<label class="ancho-35" for="">Tipo de Contacto</label>
						<select class="form-inputs ancho-60" name="id_tipo_contacto" id="id_tipo_contacto">
						<option value="0"></option>
						<?php 
							$SQL="SELECT * FROM prospectos_seguimientos_tipo_contacto";
							$tipos=mysqli_query($con, $SQL);
							while ($tipo = mysqli_fetch_array($tipos)) { ?>
								<option value="<?php echo $tipo['id']; ?>" <?php if ($tipo['id']==$seguimiento['id_tipo_contacto']) { echo 'selected';} ?>><?php echo $tipo['tipo_contacto']; ?></option>
						<?php }  ?>
							
						</select>
					</div>
					<div class="ancho-30 derecha-texto">
						<label class="ancho-35" for="">Realizado</label>
						<select class="form-inputs ancho-40" name="realizado" id="realizado">
							<option value="0" <?php if ($seguimiento['realizado']==0) { echo 'selected';} ?>>No</option>
							<option value="1" <?php if ($seguimiento['realizado']==1) { echo 'selected';} ?>>Si</option>
						</select>
						<span class="icon-check-square-o contacto_realizado"></span>
						<span class="icon-times contacto_no_realizado"></span>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-100">
						<hr>
					</div>
				</div>
				<div class="form-linea">
					<div class="ancho-50 izquierda-texto">
						<label class="ancho-50" for="">Fecha de Contacto</label>
						<input class="input-fecha ancho-50 derecha-texto" type="date" size="5" id="fec_realizado" name="fec_realizado" value="<?php echo $seguimiento['fec_realizado']; ?>">
					</div>
					<div class="ancho-50 derecha-texto">
						<label class="ancho-30" for="">Resultado</label>
						<select class="form-inputs ancho-70" name="id_resultado" id="id_resultado">
						<option value="0"></option>
						<?php
							$SQL="SELECT * FROM prospectos_seguimiento_resultados";
							$resultados=mysqli_query($con, $SQL);
							while ($resultado=mysqli_fetch_array($resultados)) { ?>
								<option value="<?php echo $resultado['id']; ?>" <?php if ($resultado['id']==$seguimiento['id_resultado']) { echo 'selected';}?>><?php echo $resultado['resultado']; ?></option>
							<?php } ?>
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
						<textarea class="unidad-obs" name="observacion" id="" cols="30" rows="4"><?php echo $seguimiento['observacion']; ?></textarea>
					</div>
				</div>

		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-40">
					<input type="submit" class="botones btn-cancelar btn-cancelar-seguimiento" value="Cancelar">
				</div>
				<div class="ancho-40 derecha-texto">
					<?php if ($seguimiento['realizado']==0) { ?>
						<input type="submit" class="botones btn-aceptar" value="Guardar">
					<?php } ?>
					
				</div>
			</div>
		</div>
		<div id="mensajes_formulario"></div>
		
	</form>
</div>

<script src="js/seguimiento_formulario.js"></script>
