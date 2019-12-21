
<input type="hidden" id="es_entrega" value="es_entrega" >
<div class="titulo-modelo" style="display:flex;">

	<div class="ancho-50">
		AGENDA DE ENTREGAS
	</div>

	<div class="ancho-50" style="text-align: right;">

		<?php 

			$SQL="SELECT * FROM sucursales";
			$sucursales = mysqli_query($con, $SQL);
		 ?>

 		SUCURSAL: <!--<?php echo  strtoupper($sucursal['sucursal']); ?> -->

		<select name="sucursal_agenda" id="sucursal_agenda">

		<?php 
			while ($sucursal = mysqli_fetch_array($sucursales)) {?>
				<option value="<?php echo $sucursal['idsucursal']; ?>" <?php if ($sucursal['idsucursal']==$id_sucursal) {
				echo 'selected';} ?>><?php echo $sucursal['sucursal']; ?> </option>
		<?php } ?>
		<option value=""></option>
		</select>
<!-- 		<form class="form_orden" action="entregas_planilla.php" method="POST" target="_blank">
			<input type="hidden" name="sql" id="sql" value="<?php echo $SQL ?>">
			<span class="icon-print">
			  <input style="background:white; color:#b63b4d; border: none; font-weight:bold; cursor:pointer;" type="submit" value="Imprimir Planilla">
			 </span>
		</form> -->
	</div>
	
</div>
<div class="agenda-tabla">
	<?php include('entregas_agenda_contenido_relleno_cuerpo_tabla.php'); ?>

</div>

<link rel="stylesheet" href="css/entregas_agenda.css">