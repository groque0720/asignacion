
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// $SQL="SELECT * FROM usuarios WHERE perfil=3 AND activo=1";
	// $asesores = mysqli_query($con, $SQL);

 ?>

<div class="titulo-modelo">
	<?php echo "ASESORES - OBJETIVOS ACTIVOS"; ?>
</div>

<?php include('asesores_cuerpo.php'); ?>
