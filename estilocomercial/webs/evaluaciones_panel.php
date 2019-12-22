<!DOCTYPE html>
<html lang="es">
<head>
	<title>Evaluaciones</title>
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
		.form_evaluaciones{
			border: 1px solid #C8C9CD;
			box-shadow: 2px 2px 3px #C8C9CD;
			padding: 10px;
			border-radius: 5px;
		}
		.periodo, .fecha{
			width: 100%;
			font-size: 1em;
		}
		.btn_cancelar{
			font-weight: bold;
			color: red;
			font-size: 1em;
		}
		.procesando{
			font-size: 1.2em;
			color: orange;
		}
	</style>

	<script>
		$(document).ready(function(){

			$("#proceso_carga").hide();

			$("#btn_cancelar").click(function(event){
				event.preventDefault();
				$("#dialog").dialog('close');
			})

			$("#dialog").dialog({
				autoOpen: false, // no abrir automáticamente
		     	resizable: false, //permite cambiar el tamaño
		     	width: 300,
		     	height:150, // altura
			    modal: true, //capa principal, fondo opaco
			    resizable: false,
                autoResize: false,
			    buttons: { //crear botón de cerrar
		          }
			});


			$(".nueva_app").click(function(event){
				event.preventDefault();
				$("#dialog").dialog('open');

			});

			$("#form_evaluaciones").submit(function(event){
				event.preventDefault();
				$("#formulario_carga").hide();
				$("#proceso_carga").show();
				$.ajax({
					url:"evaluaciones_creacion.php",
					cache:false,
					type:"POST",
					data:$(this).serialize(),
					success:function(result){
						$("#zona_ajax").html(result);
						$("#dialog").dialog('close');
					}
	    		});

			})



		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Lista de Evaluaciones de Desempeño</h1>
			<hr>
		</div>
	</div>
	<div class="ed-container web-50 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="panel.php">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<a class="icon-enlace espacio nueva_app" data-mov="1" href="">Crear Nueva Evaluación</a>
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				//mysql_query("SET NAMES 'utf8'");

			?>

			<div class="zona-tabla" id="zona_ajax">

				<?php include("evaluaciones_panel_cuerpo.php"); ?>

			</div>

		</div>

	</div>
<div id="dialog" class="dialog_evaluaciones">
	<form action="" id="form_evaluaciones" name="form_evaluaciones" class="form_evaluaciones">

		<div class="ed-container" id="formulario_carga">
			<div class="ed-item ">
				<label for="Periodo">Período</label>
			</div>
			<div class="ed-item ">
				<input type="text" id="periodo" name="periodo" class="periodo" required>
			</div>
			<div class="ed-item ">
				<label for="Periodo">Fecha</label>
			</div>
			<div class="ed-item ">
				<input type="date" id="fecha" name="fecha" class="fecha" required>
				<hr>
			</div>
			<div class="ed-container">
				<div class="ed-item web-50 izquierda-contenido">
					<a class="btn_cancelar" id="btn_cancelar" href="">Cancelar</a>
				</div>
				<div class="ed-item web-50 derecha-contenido">
					<input type="submit" id="crear" name="crear" class="crear" value="Crear">
				</div>
			</div>

		</div>

		<div class="ed-container" id="proceso_carga">
			<div class="ed-item centrar-texto">
				<span class="procesando">Procesando</span>
			</div>
			<div class="ed-item centrar-texto ">
				<img src="../imagenes/carga_circulo.gif" alt="Procesando">
			</div>
			<div class="ed-item centrar-texto">
				<span>Aguarde un momento por favor Generando las Mismas (Aprox 60 Seg.)</span>
			</div>
		</div>


	</form>

</div>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>

<?php mysqli_close($con); ?>
</body>
</html>