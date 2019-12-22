<?php include("../_seguridad/_seguridad.php") ?>


<?php

	if ($_GET["mov"]=="2") {
		$id_pregunta=$_GET["id_app"];
	}
	$mov=$_GET["mov"];
	$id_encuesta=$_GET["id_encuesta"];

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");


	if ($mov=="2") {
	$SQL="SELECT * FROM encuestas_preguntas WHERE id_pregunta =".$id_pregunta;
	$res=mysqli_query($con, $SQL);
	$app=mysqli_fetch_array($res);
	}

	$SQL="SELECT * FROM encuestas WHERE id_encuesta=".$id_encuesta;
	$res=mysqli_query($con, $SQL);
	$encuesta=mysqli_fetch_array($res);
	$nom_encuesta=$encuesta["encuesta"];

	 ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Encuesta <?php echo $nom_encuesta; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/estilo_default.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

	<script>

		$(document).ready(function(){


			$(".form_app").submit(function(){
				event.preventDefault();
				$.ajax({
					url:"encuestas_preguntas_abm.php",
					cache:false,
					type:"POST",
					data:$(this).serialize(),
					success:function(result){
						self.location = "encuestas_preguntas.php?id_encuesta="+$("#id_encuesta").val();
					}
		    	});
			});

			//----------------------------------------------------------------------------------------
			$("#id_formato_respuesta").change(function () {
				$("#id_formato_respuesta option:selected").each(function () {
				   	//sentencias para resetear SELECT #modelo
				   	$("#id_tipo_respuesta").html("");
				   	$("#si_respuesta").html("");
				    elegido=$(this).val();
				    op="cambio_formato";
				    $.get("encuestas_pregunta_ajax.php",
				    	 {opcion:op, elegido: elegido },
				    	 function(data){
				    		$("#id_tipo_respuesta").html(data);
				         });
				 });

			});
			//-----------------------------------------------------------------------------------------
			$("#id_tipo_respuesta").change(function () {
				$("#id_tipo_respuesta option:selected").each(function () {
				   	//sentencias para resetear SELECT #modelo
				   	$("#si_respuesta").html("");
				    elegido=$(this).val();
				    op="cambio_tipo_respuesta";
				    $.get("encuestas_pregunta_ajax.php",
				    	 {opcion:op, elegido: elegido },
				    	 function(data){
				    		$("#si_respuesta").html(data);
				         });
				 });

			});
			//--------------------------------------------------------------------------------------------

		});
	</script>
</head>
<body>
	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<?php if ($mov==2) { ?>
				<h1>Modificar Pregunta - Encuesta <?php echo $nom_encuesta; ?></h1>
			<?php } else { ?>
			<h1>Nueva Pregunta - Encuesta <?php echo $nom_encuesta; ?></h1>
			<?php } ?>
		</div>
	</div>
	<div class="ed-container web-50 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="encuestas_preguntas.php?id_encuesta=<?php echo $id_encuesta; ?>">Página Anterior</a>
		</div>
	</div>

<!-- $mov=2 quiere decir que van a realizar una modificación en el nombre de la aplicación -->

<?php if ($mov==2) { ?>

	<div class="ed-container">
		<div class="ed-item">
			<div class="zona-form">
				<form id="from_app" class="form_app" action="app_abm.php" method="GET">
					<input type="hidden" id="id_encuesta" name="id_encuesta" value="<?php echo $id_encuesta; ?>">
					<input type="hidden" id="id_pregunta" name="id_pregunta" value="<?php echo $id_pregunta; ?>">
					<input type="hidden" id="mov" name="mov" value="2">
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Nro Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="nro_pregunta" id="nro_pregunta" value="<?php echo $app["nro_pregunta"]; ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="pregunta" id="pregunta" value="<?php echo $app["pregunta"]; ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Formato de Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_formatos_respuestas WHERE activo = 1 AND baja =0";
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="id_formato_respuesta" id="id_formato_respuesta" required>
								<option value=""></option>
								<?php  while ($formato=mysqli_fetch_array($form)) { ?>
									<option value="<?php echo $formato["id_formato_respuesta"] ?>" <?php if ($app["id_formato_respuesta"]==$formato["id_formato_respuesta"]) { echo "selected";} ?>><?php echo $formato["formato_respuesta"]; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Tipo de Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_tipos_respuestas WHERE activo=1 AND baja=0 AND id_formato_respuesta=".$app["id_formato_respuesta"];
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="id_tipo_respuesta" id="id_tipo_respuesta" required>
								<option value="0">#S/O</option>
								<?php  while ($tipo_respuesta=mysqli_fetch_array($form)) { ?>
									<option value="<?php echo $tipo_respuesta["id_tipo_respuesta"] ?>" <?php if ($app["id_tipo_respuesta"]==$tipo_respuesta["id_tipo_respuesta"]) { echo "selected";} ?>><?php echo $tipo_respuesta["tipo_respuesta"]; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Si Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE activo = 1 AND baja =0 AND id_tipo_respuesta=".$app["id_tipo_respuesta"];
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="si_respuesta" id="si_respuesta" required>
								<option value="0">#S/O</option>
								<?php  while ($linea_respuestas=mysqli_fetch_array($form)) { ?>
									<option value="<?php echo $linea_respuestas["id_linea_tipo_respuesta"] ?>" <?php if ($app["si_respuesta"]==$linea_respuestas["id_linea_tipo_respuesta"]) { echo "selected";} ?>><?php echo $linea_respuestas["linea_tipo_respuesta"]; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Próxima Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="proxima_pregunta" id="proxima_pregunta" value="<?php echo $app["proxima_pregunta"]; ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Activo:</label>
						</div>
						<div class="cdr_input">
							<select name="activo" id="activo" required>
								<option value=""></option>
								<option value="1" <?php if ($app["activo"]==1) { echo "selected";} ?>>Si</option>
								<option value="0" <?php if ($app["activo"]==0) { echo "selected";} ?>>No</option>
							</select>

						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Baja:</label>
						</div>
						<div class="cdr_input">
							<select name="baja" id="baja" required>
								<option value=""></option>
								<option value="1" <?php if ($app["baja"]==1) { echo "selected";} ?>>Si</option>
								<option value="0" <?php if ($app["baja"]==0) { echo "selected";} ?>>No</option>
							</select>

						</div>
					</div>
					<hr>
					<div class="linea">
						<div class="ed-item derecha-contenido">
							<a class="icon-aceptarR espacio nueva_app" data-mov="2" href="">
								<input type="submit" id="btn-enviar" class="btn-enviar" value="Guardar">
							</a>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>

<?php } ?>

<!-- $mov=2 quiere decir que van a realizar una modificación en el nombre de la aplicación -->

<?php if ($mov==1) { ?>

	<div class="ed-container">
		<div class="ed-item">
			<div class="zona-form">
				<form id="from_app" class="form_app" action="app_abm.php" method="GET">
					<input type="hidden" id="id_encuesta" name="id_encuesta" value="<?php echo $id_encuesta; ?>">
					<input type="hidden" id="mov" name="mov" value="1">
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Nro Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="nro_pregunta" id="nro_pregunta" value="" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="pregunta" id="pregunta" value="" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Formato de Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_formatos_respuestas WHERE activo = 1 AND baja =0";
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="id_formato_respuesta" id="id_formato_respuesta" required>
								<option value=""></option>
								<?php  while ($formato=mysqli_fetch_array($form)) { ?>
									<option value="<?php echo $formato["id_formato_respuesta"] ?>"><?php echo $formato["formato_respuesta"]; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Tipo de Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_tipos_respuestas WHERE activo = 1 AND baja =0";
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="id_tipo_respuesta" id="id_tipo_respuesta" required>
								<option value=""></option>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Si Respuesta:</label>
						</div>
						<div class="cdr_input">
							<?php
								$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE activo = 1 AND baja =0";
								$form=mysqli_query($con, $SQL);

							 ?>
							<select name="si_respuesta" id="si_respuesta" required>
								<option value=""></option>
							</select>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Próxima Pregunta:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="proxima_pregunta" id="proxima_pregunta" value="" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Activo:</label>
						</div>
						<div class="cdr_input">
							<select name="activo" id="activo" required>
								<option value=""></option>
								<option value="1">Si</option>
								<option value="0">No</option>
							</select>

						</div>
					</div>
					<hr>
					<div class="linea">
						<div class="ed-item derecha-contenido">
							<a class="icon-aceptar espacio nueva_app" data-mov="1" href="">
								<input type="submit" id="btn-enviar" class="btn-enviar" value="Guardar">
							</a>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>

<?php } ?>

<?php mysqli_close($con); ?>

</body>
</html>