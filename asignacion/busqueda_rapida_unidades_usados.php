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
	ORDER BY posgrupo, posmodelo";
	$unidades = mysqli_query($con, $SQL);
 ?>
<?php include('carga_unidades_usados_busqueda.php'); ?>