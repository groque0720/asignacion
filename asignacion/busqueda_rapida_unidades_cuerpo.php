<?php 	
	$SQL="SELECT * FROM view_asignaciones WHERE (
	cliente LIKE '%" . $abuscar . "%' OR
	nro_unidad LIKE '%" . $abuscar . "%' OR 
	modelo LIKE '%" . $abuscar . "%' OR
	grupo LIKE '%" . $abuscar . "%' OR 
	nro_orden LIKE '%" . $abuscar . "%' OR
	interno LIKE '%" . $abuscar . "%' OR
	chasis LIKE '%" . $abuscar . "%' OR
	asesor LIKE '%" . $abuscar . "%' )
	ORDER BY posgrupo, posmodelo, año, id_mes";
	$unidades = mysqli_query($con, $SQL);
 ?>

 <div class="titulo-modelo derecha-texto">
	<a href="<?php echo 'busqueda_rapida_unidades_pdf.php?abuscar='.$abuscar; ?>" target="_blank"><span class="icon-print">Imprimir Resultado de Búsqueda</span></a>
</div>

<?php include('contenido_relleno_cuerpo.php'); ?>
<script>
	$(".zona-contenido").addClass('zona-contenido-total');
	$(".menu-lateral").addClass('menu_lateral_oculto');
	$(".menu-secundario").addClass('menu-secundario-total');
	$(".fila-modelo").removeClass('fila-oculto');
	$(".fila-grupo").removeClass('fila-oculto');
	$("#icono-menu").prop('checked', false);
</script>