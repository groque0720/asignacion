<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	 $lectura="";
 ?>
<div class="titulo-modelo centrar-texto">
	<span class="icon-cogs">  <span>  </span>Herramienta Para Filtro Avanzado</span>
</div>
<form id="form_filtro" action="" method="POST">

	<div class="contenedor_item_filtro cuadro" style="display:flex;">

			<div class="form-linea item_filtro ancho-15  item_modelo">
				<label class="ancho-30 centrar-texto" for="cb_modelo">Asesor</label>
				<select class="form-inputs ancho-70" name="id_asesor" id="id_asesor" <?php echo $lectura; ?>>
				<option value="0"></option>
				<?php 
				$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND activo = 1 AND idusuario <> 1 ORDER BY nombre";
				$usuarios = mysqli_query($con, $SQL);
				while ($usuario=mysqli_fetch_array($usuarios)) { ?>
					<option value="<?php echo $usuario['idusuario']; ?>"><?php echo $usuario['nombre']; ?></option>
				<?php } ?>
				</select>
			</div>

			<div class="form-linea item_filtro ancho-15  item_modelo">
				<label class="ancho-30 centrar-texto" for="cb_modelo">Modelo</label>
				<select class="form-inputs ancho-70" name="id_grupo" id="id_grupo" <?php echo $lectura; ?>>
				<option value="0"></option>
				<?php 
					$SQL="SELECT * FROM grupos WHERE posicion > 0 AND cerokilometro = 1 AND activo = 1 ORDER BY posicion";
					$grupos=mysqli_query($con, $SQL);
					while ($modelo=mysqli_fetch_array($grupos)) { ?>
						<option value="<?php echo $modelo['idgrupo']; ?>"><?php echo $modelo['grupo']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-linea item_filtro ancho-15  item_version">
				<label class="ancho-30 centrar-texto" for="cb_modelo">Versión</label>
				<select class="form-inputs ancho-70" name="id_modelo" id="id_modelo" <?php echo $lectura; ?>>
				<option value="0"></option>
				</select>
			</div>

			<div class="form-linea item_filtro ancho-15  item_version">
				<label class="ancho-30 centrar-texto" for="cb_modelo">Filtro Mes</label>
				<select class="form-inputs ancho-60" name="filtro_mes" id="filtro_mes" <?php echo $lectura; ?>>
				<option value="1" >Igual</option>
				<option value="2" >Desde</option>
				<option value="3" >Hasta</option>
				<option value="4" >Entre Meses</option>
				</select>
			</div>

			<div class="form-linea item_filtro ancho-20 item_modelo">
				<label class="ancho-25 centrar-texto" for="cb_modelo">Mes</label>
				<select class="form-inputs ancho-60" name="mes_desde" id="mes_desde" <?php echo $lectura; ?>>
				<option value="0" selected></option>
				<?php 
					$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);
					while ($mes=mysqli_fetch_array($meses)) { ?>
						<option value="<?php echo $mes['idmes']; ?>"><?php echo $mes['mes']; ?></option>
					<?php } ?>
				</select>
				<input class="centrar-texto" type="text" size="4" id="año_desde" name="año_desde" placeholder="Año" value="<?php echo date('Y'); ?>" style="margin-left:5px;">
			</div>

			<div class="form-linea item_filtro ancho-20 item_modelo" id="hasta">
				<label class="ancho-25 centrar-texto" for="cb_modelo">Hasta</label>
				<select class="form-inputs ancho-60" name="mes_hasta" id="mes_hasta" <?php echo $lectura; ?>>
				<option value="0" selected></option>
				<?php 
					$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);
					while ($mes=mysqli_fetch_array($meses)) { ?>
						<option value="<?php echo $mes['idmes']; ?>"><?php echo $mes['mes']; ?></option>
					<?php } ?>
				</select>
				<?php 

				 ?>
				<input class="centrar-texto" type="text" size="4" id="año_hasta" name="año_hasta" value="<?php echo date('Y'); ?>" placeholder="Año" style="margin-left:5px;">
			</div>

			<div class="form-linea item_filtro ancho-10 item_modelo" id="hasta">
				<label class="ancho-60 centrar-texto" for="cb_modelo">Cancelada</label>
				<select class="form-inputs ancho-40" name="cancelada" id="cancelada" <?php echo $lectura; ?>>
					<option value="0" selected></option>
					<option value="1" >Si</option>
					<option value="0" >No</option>
				</select>
			</div>

			<div class="form-linea item_filtro ancho-5 item_modelo">
				<input type="submit" id="generar" class="btn-aceptar ancho-100" value="Generar" style="color:white; cursor:pointer;">
			</div>

	</div>
</form>
<div id="resultado_filtro" class="contenedor-respuesta centrar-texto">

</div>

<script src="js/filtro_avanzado.js"></script>



