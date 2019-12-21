
<?php
	@session_start();

	if (isset($abuscar) OR isset($_POST['abuscar'])) {
		include_once("funciones/func_mysql.php");
		conectar();
		mysqli_query($con,"SET NAMES 'utf8'");
		extract($_POST);
	}

if (!isset($id_sucursal)) {
	$id_sucursal = $_SESSION["idsuc"];
}


 ?>
<!-- <div class="titulo-modelo">
	SECTOR ENTREGAS DE UNIDADES
</div> -->

<?php

 include('entregas_agenda_contenido_relleno_cuerpo.php'); ?> 