<!DOCTYPE html>
<html lang="es">
<head>
	<title>Publicaciones</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/estilo_default.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="../css/jquery-ui.css"></script>

	<style>
		.form_publicaciones{
			border: 1px solid #C8C9CD;
			box-shadow: 2px 2px 3px #C8C9CD;
			padding: 10px;
			border-radius: 5px;
			background: white;
		}
		.periodo, .fecha{
			width: 100%;
			font-size: 1em;
		}
		.url{
			width: 100%;
		}
		.btn_cancelar{
			font-weight: bold;
			color: red;
			font-size: 1em;
		}
		select {
			width: 100%;
		}
		.procesando{
			font-size: 1.2em;
			color: orange;
		}
		.visto{
			color: green;
			font-weight: bold;

		}
		.novisto{
			color: red;
			font-weight: bold;
		}
	</style>

	<script>
		$(document).ready(function(){



		});
	</script>

</head>
<body class="desarroll">

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Lista Publicaciones Realizadas</h1>
			<hr>
		</div>
	</div>
	<div class="ed-container web-80 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="javascript:window.history.back();">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<!-- <a class="icon-enlace espacio nueva_app" data-mov="1" href="">Nueva Publicación</a> -->
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				//mysql_query("SET NAMES 'utf8'");

			?>

			<div class="zona-tabla-90" id="zona_ajax">

				<?php include("notificaciones_lista_cuerpo_asesores.php"); ?>

			</div>

		</div>

	</div>


<!-- 	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div> -->

<?php mysqli_close($con); ?>
</body>
</html>