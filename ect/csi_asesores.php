
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// $SQL="SELECT * FROM usuarios WHERE perfil=3 AND activo=1";
	// $asesores = mysqli_query($con, $SQL);

 ?>

<div class="titulo-modelo">
	<?php echo "CSI ASESORES"; ?>
</div>


<div class="titulo-modelo margen-abajo-10 ancho-80 centrar-caja flexible justificar" style="margin-bottom: 10px;">
	<div class="periodo">
		<label for="">Mes</label>
		<?php 
			$SQL="SELECT * FROM meses";
			$meses = mysqli_query($con, $SQL);
		 ?>
		<select name="mes" id="mes" class="definir">
			<option value="0"></option>
			<?php 
				while ($mes=mysqli_fetch_array($meses)) { ?>
					<option value="<?php echo $mes['idmes'] ?>"><?php echo $mes['mes']; ?></option>
			<?php } ?>

		</select>

		<label for="">Año</label>
		<input type="text" value="<?php echo date('Y'); ?>" placeholder="Año" size="5" name="ano" id="ano" class="definir">

	</div>
	<div class="objetivo">

	</div>
</div>
<div id="zona_definicion_objetivos">
	<?php //include('definicion_objetivos_cuerpo.php'); ?>	
</div>

<script src="js/csi_asesores.js"></script>