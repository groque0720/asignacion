
<?php

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// $SQL="SELECT * FROM usuarios WHERE perfil=3 AND activo=1";
	// $asesores = mysqli_query($con, $SQL);

 ?>

<div class="titulo-modelo">
	<?php echo "MODELOS - OBJETIVOS ACTIVOS"; ?>
</div>

<?php include('modelos_cuerpo.php'); ?>
