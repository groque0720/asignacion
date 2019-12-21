<?php include("../_seguridad/_seguridad.php") ?>


<?php

	if ($_GET["mov"]=="2") {
		$id=$_GET["id_app"];
	}
	$mov=$_GET["mov"];

	if ($mov==2) {
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	$SQL="SELECT * FROM aplicaciones WHERE id_app =".$id;
	$res=mysqli_query($con, $SQL);
	$app=mysqli_fetch_array($res);
	}

	 ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="../css/estilo_default.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

	<script>

		$(document).ready(function(){

			$(".form_app").submit(function(){
				event.preventDefault();
				$.ajax({
					url:"app_abm.php",
					cache:false,
					type:"POST",
					data:$(this).serialize(),
					success:function(result){
						self.location = "apps.php";
					}
		    	});
			});
		});
	</script>
</head>
<body>
	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<?php if ($mov==2) { ?>
				<h1>Modificar Nombre de Aplicación</h1>
			<?php } else { ?>
			<h1>Nueva Aplicación</h1>
			<?php } ?>
		</div>
	</div>
	<div class="ed-container web-50 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="apps.php">Página Anterior</a>
		</div>
	</div>

<!-- $mov=2 quiere decir que van a realizar una modificación en el nombre de la aplicación -->

<?php if ($mov==2) { ?>

	<div class="ed-container">
		<div class="ed-item">
			<div class="zona-form">
				<form id="from_app" class="form_app" action="app_abm.php" method="GET">
					<input type="hidden" id="id_app" name="id_app" value="<?php echo $app["id_app"]; ?>">
					<input type="hidden" id="mov" name="mov" value="2">

					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Nombre de la Aplicación:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="aplicacion" id="aplicacion" value="<?php echo $app["aplicacion"] ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">URL:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="url" id="url" value="<?php echo $app["url"] ?>" required>
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
							<a class="icon-aceptar espacio nueva_app" data-mov="2" href="">
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
					<input type="hidden" id="id_app" name="id_app" value="<?php echo $app["id_app"]; ?>">
					<input type="hidden" id="mov" name="mov" value="1">
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Nombre de la Aplicación:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="aplicacion" id="aplicacion" value="" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">URL:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="url" id="url" value="" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Activo:</label>
						</div>
						<div class="cdr_input">
							<select name="activo" id="activo" required>
								<option value=""></option>
								<option value="1" selected>Si</option>
								<option value="0">No</option>
							</select>

						</div>
					</div>
					<hr>
					<div class="linea">
						<div class="ed-item derecha-contenido">
							<a class="icon-aceptar nueva_app" data-mov="2" href="">
								<input type="submit" id="btn-enviar" class="btn-enviar" value="Guardar">
							</a>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>

<?php } ?>













</body>
</html>