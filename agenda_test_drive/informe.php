<?php
	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM asignaciones WHERE entregada = 0 ORDER BY año, id_mes, nro_orden, nro_unidad";
	$unidades = mysqli_query($con, $SQL);
 ?>


<div class="zona_titulo_informe cuadro flex">

	<div class="form-linea item_filtro ancho-20  item_modelo">
		<label class="ancho-30 centrar-texto" for="cb_modelo">Sucursal</label>
		<select class="form-inputs ancho-60" name="id_sucursal" id="id_sucursal" >
		<option value="0">Todas</option>
		<?php 
			$SQL="SELECT * FROM sucursales";
			$sucursales=mysqli_query($con, $SQL);
			while ($sucursal=mysqli_fetch_array($sucursales)) { ?>
				<option value="<?php echo $sucursal['idsucursal']; ?>"><?php echo $sucursal['sucursal']; ?></option>
			<?php } ?>
		</select>
	</div>

	<div class="form-linea item_filtro ancho-20  item_modelo">
		<label class="ancho-30 centrar-texto" for="cb_modelo">Asesor</label>
		<select class="form-inputs ancho-70" name="id_asesor" id="id_asesor" >
			<option value="0">Todos</option>
		</select>
	</div>

	<div class="form-linea item_filtro ancho-20  item_modelo">
		<label class="ancho-30 centrar-texto" for="cb_modelo">Modelo</label>
		<select class="form-inputs ancho-60" name="id_modelo" id="id_modelo" >
		<option value="0">Todos</option>
		<?php 
			$SQL="SELECT * FROM modelos_test_drive WHERE activo = 1";
			$modelos=mysqli_query($con, $SQL);
			while ($modelo=mysqli_fetch_array($modelos)) { ?>
				<option value="<?php echo $modelo['id_modelo']; ?>"><?php echo $modelo['modelo']; ?></option>
			<?php } ?>
		</select>
	</div>

	<div class="form-linea item_filtro ancho-20 item_modelo">
		<label class="ancho-25 centrar-texto" for="cb_modelo">Mes</label>
		<select class="form-inputs ancho-40" name="id_mes" id="id_mes" >
		<option value="0" selected>Todos</option>
		<?php 
			$SQL="SELECT * FROM meses";
			$meses=mysqli_query($con, $SQL);
			while ($mes=mysqli_fetch_array($meses)) { ?>
				<option value="<?php echo $mes['idmes']; ?>"><?php echo $mes['mes']; ?></option>
			<?php } ?>
		</select>
		<input class="centrar-texto" type="text" size="4" id="anio" name="anio" placeholder="Año" value="<?php echo date('Y'); ?>" style="margin-left:5px;">
	</div>


	<div class="form-linea item_filtro ancho-10 item_modelo">
		<input type="submit" id="generar" class="btn-aceptar ancho-100" value="Generar" style="cursor:pointer;">
	</div>


</div>
<script src="js/informe.js"></script>
<div class="zona-tabla-infome">
	
</div>
<?php //include('informe_cuerpo.php'); ?>