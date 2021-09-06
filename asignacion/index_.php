<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Asignación TASA</title>
	<link rel="shortcut icon" type="image/x-icon" href="dyv.ico" />
	<link rel="stylesheet" href="css/estilo.css">
	<style>
		.cuerpo {
			width: 90%;
			margin: auto;
			margin-top: 40px;
		}

		.img {
			margin: auto;
			text-align: center;
			width: 50%;
		}
		.imagen{
			width: 70%;
		}
	</style>

</head>
<body>
<?php
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL = "SELECT estado FROM asignacion_estado WHERE id = 1";
	$result=mysqli_query($con, $SQL);
	$habilitado=mysqli_fetch_array($result);

	if ($habilitado['estado'] == 1) {
		echo '<script>	window.location.href = "../asignacion/";</script>';
	}

 ?>

<h1><center>Realizando Asignación <?php echo date('m')." - ".date('Y') ?> </center></h1>

	<div class="cuerpo">
		<div class="img">
			<img class="imagen" src="imagenes/webmant.jpg" alt="en mantenimeinto">
		</div>
	</div>

</body>
</html>
