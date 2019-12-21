
<?php
	@session_start();

	if (isset($abuscar) OR isset($_POST['abuscar'])) {
		include("funciones/func_mysql.php");
		conectar();
		mysqli_query($con,"SET NAMES 'utf8'");
		extract($_POST);
	}

 ?>
<!-- <div class="titulo-modelo">
	SECTOR ENTREGAS DE UNIDADES
</div> -->

<?php

	$SQL="SELECT * FROM view_asignaciones_entregas WHERE id_ubicacion = ".$_SESSION["idsuc"]." ORDER BY fec_pedido DESC, hora_pedido ASC";
	$unidades = mysqli_query($con, $SQL);

	// $usuario=mysqli_fetch_assoc($unidades);

	// echo $usuario['modelo'];

	// var_dump($usuario);

	// foreach ($unidades as $unidad) {
	// 	echo $unidad['id'].'<br>';
	// }

 include('entregas_contenido_relleno_cuerpo.php'); ?>