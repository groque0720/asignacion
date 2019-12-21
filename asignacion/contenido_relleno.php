
<?php

	if (isset($_POST['modelo_activo_busqueda_vacio'])) {
		include("funciones/func_mysql.php");
		conectar();
		mysqli_query($con,"SET NAMES 'utf8'");
		extract($_POST);
		$modelo_activo = $_POST['modelo_activo_busqueda_vacio'];
	}


	if (isset($_POST['modelo_activo'])) {
		$modelo_activo = $_POST['modelo_activo'];
	}

	$SQL="SELECT * FROM asignaciones WHERE borrar = 0 AND entregada = 0 AND id_modelo = $modelo_activo ORDER BY año, id_mes, nro_orden, nro_unidad";
	$unidades = mysqli_query($con, $SQL);

	$SQL="SELECT modelo FROM modelos WHERE idmodelo=".$modelo_activo;
	$modelos=mysqli_query($con, $SQL);
	$modelo = mysqli_fetch_array($modelos);

 ?>
<div class="titulo-modelo">
	<?php echo $modelo['modelo']; ?>
</div>
<?php include('contenido_relleno_cuerpo.php'); ?>