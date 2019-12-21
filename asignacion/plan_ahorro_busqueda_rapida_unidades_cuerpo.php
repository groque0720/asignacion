<?php 	
	$SQL="SELECT * FROM view_asignaciones WHERE id_negocio = 2 AND (
	cliente LIKE '%" . $abuscar . "%' OR
	nro_unidad LIKE '%" . $abuscar . "%' OR 
	modelo LIKE '%" . $abuscar . "%' OR
	grupo LIKE '%" . $abuscar . "%' OR 
	nro_orden LIKE '%" . $abuscar . "%' OR
	interno LIKE '%" . $abuscar . "%' OR
	chasis LIKE '%" . $abuscar . "%' OR
	asesor LIKE '%" . $abuscar . "%' )
	ORDER BY posgrupo, posmodelo";
	$unidades = mysqli_query($con, $SQL);
 ?>
<?php include('plan_ahorro_contenido_relleno_cuerpo.php'); ?>
<script>
	$(".zona-contenido").addClass('zona-contenido-total');
	$(".menu-lateral").addClass('menu_lateral_oculto');
	$(".menu-secundario").addClass('menu-secundario-total');
	$(".fila-modelo").removeClass('fila-oculto');
	$(".fila-grupo").removeClass('fila-oculto');
	$("#icono-menu").prop('checked', false);
</script>