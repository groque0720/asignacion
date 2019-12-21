
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// $SQL="SELECT * FROM usuarios WHERE perfil=3 AND activo=1";
	// $asesores = mysqli_query($con, $SQL);

 ?>

<div class="titulo-modelo flexible justificar menu-pmi">
	<div>PLAN DE MENSUAL INDIVIDUAL</div>
	<div><a id="imprimir_comparativo" href="">Imprimir</a></div>
</div>


<div class="titulo-modelo margen-abajo-10 ancho-100 flexible justificar" style=" margin-bottom: 10px;">
	<div class="periodo">
		<span style="color: red;" id="desde">Desde</span>
		<label for="">Mes</label>
		<?php 
			$SQL="SELECT * FROM meses";
			$meses = mysqli_query($con, $SQL);
		 ?>
		<select name="mes_desde" id="mes_desde" class="definir">
			<option value="0"></option>
			<?php 
				while ($mes=mysqli_fetch_array($meses)) { ?>
					<option value="<?php echo $mes['idmes'] ?>"><?php echo $mes['mes']; ?></option>
			<?php } ?>

		</select>

		<label for="">Año</label>
		<input type="text" value="<?php echo date('Y'); ?>" placeholder="Año" size="5" name="ano_desde" id="ano_desde" class="definir">

		<span style="color: red;">Hasta</span>

		<label for="">Mes</label>
		<?php 
			$SQL="SELECT * FROM meses";
			$meses = mysqli_query($con, $SQL);
		 ?>
		<select name="mes_hasta" id="mes_hasta" class="definir">
			<option value="0"></option>
			<?php 
				while ($mes=mysqli_fetch_array($meses)) { ?>
					<option value="<?php echo $mes['idmes'] ?>"><?php echo $mes['mes']; ?></option>
			<?php } ?>

		</select>

		<label for="">Año</label>
		<input type="text" value="<?php echo date('Y'); ?>" placeholder="Año" size="5" name="ano_hasta" id="ano_hasta" class="definir">

	</div>

	<div class="sucursal-asesor flexible">

		
		<label for="">Sucursal</label>

		<?php
			$SQL="SELECT * FROM sucursales";
			$sucursales=mysqli_query($con, $SQL);
		 ?>
		 <select name="sucursal" id="sucursal" class="definir">
		 	<option value="5">Todas</option>
		 	<?php while ($sucursal = mysqli_fetch_array($sucursales)) { ?>
		 		<option value="<?php echo $sucursal['idsucursal'] ?> "><?php echo $sucursal['sucursal']; ?></option>
		 	<?php } ?>
		 </select>



		<div id="zona_asesor">
			<label for="" class="">Asesor</label>

			 <select name="asesor" id="asesor" class="definir">
			 	<option value="0">Todos</option>
			 </select>
		</div>

	</div>

</div>


<div id="zona_comparativo">
	<?php //include('csi_comparativo_cuerpo.php'); ?>	
</div>

<script src="js/csi_comparativo.js"></script>

