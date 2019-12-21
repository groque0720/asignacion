
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	//$SQL="SELECT * FROM view_reservas_sin_sena";
	//$unidades = mysqli_query($con, $SQL);

 ?>
<div class="titulo-modelo">
	<?php echo 'LISTA DE RESERVAS CON MISMO NRO DE UNIDAD' ?>
</div>
<?php include('reservas_duplicadas_nrounidad_cuerpo.php'); ?>