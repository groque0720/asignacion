<?php
	include("funciones/func_mysql.php");
	conectar();
	//mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
	$lectura = "";
 ?>
<form class="form-busq-entregadas" action="">
	<div class="contenedor_item_filtro cuadro" style="display:flex;">
		<div class="form-linea item_filtro ancho-30 item_modelo">
			<label class="ancho-100 centrar-texto" for="cb_modelo">BUSQUE POR INTERVALOS DE MESES</label>
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
		<div class="form-linea item_filtro ancho-10 item_modelo">
					<input type="submit" id="generar" class="btn-aceptar ancho-100" value="Generar" style="color:white; cursor:pointer;">
		</div>
	</div>
</form>

<?php
$SQL="SELECT * FROM view_asignaciones_entregadas LIMIT 100";
$unidades = mysqli_query($con, $SQL);
 include('contenido_relleno_entregadas_cuerpo.php'); ?>
</div>

<script src="js/entregadas.js"></script>
