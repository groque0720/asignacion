
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM view_reservas_levantadas";
	$unidades = mysqli_query($con, $SQL);

 ?>
<div class="titulo-modelo">
	<?php echo 'LISTA DE RESERVAS LEVANTADAS POR FALTA DE CONFIRMACIÓN - (ÚLTIMAS 50)' ?>
</div>
<?php include('reservas_unidades_levantadas_cuerpo.php'); ?>

