<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
 ?>



<!DOCTYPE html>
<html lang="es">
<head>
	<title></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="../css/estilo_default.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="js_informe_panel.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js_highcharts/highcharts.js"></script>
	<script src="../js_highcharts/modules/exporting.js"></script>

</head>
<body class="desarroll">
	<header class="ed-container web-90 ">
		<div class="ed-item centrar-texto">
			<h1>Reporte de Encuestas</h1>
		<hr>
		</div>

		<div class="ed-container derecha-contenido">
			<div class="ed-item web-60">
				<label for="mes">Mes</label>
				<select name="mes" id="mes">
					<option class="top" value="0">Todos</option>
					<?php
					$SQL="SELECT * FROM meses";
					$meses=mysqli_query($con, $SQL);
					while ($mes=mysqli_fetch_array($meses)) { ?>
					<option value="<?php echo $mes["id_mes"]; ?>"><?php echo $mes["mmm"]; ?></option>
					 <?php } ?>
				</select>

				<label for="año">Año</label>
				<select name="año" id="año">
					<option value="2016">2016</option>
					<option value="2017" selected>2017</option>
					<option value="2018">2018</option>
				</select>


				<label for="encuesta">Encuesta</label>
				<select name="encuesta" id="encuesta">
					<option class="top" value="0">Todas</option>
					<?php
						$SQL="SELECT * FROM encuestas WHERE activo=1";
						$encuestas=mysqli_query($con, $SQL);
						while ($enc=mysqli_fetch_array($encuestas)) { ?>
							<option value="<?php echo $enc["id_encuesta"];?> "> <?php echo $enc["encuesta"]; ?></option>
						<?php } ?>
				</select>


				<label for="sucursales">Sucursales</label>
				<select name="sucursales" id="sucursales">
					<option class="top" value="0">Derka y Vargas</option>
					<?php
						$SQL="SELECT * FROM sucursales WHERE activo = 1";
						$sucursales=mysqli_query($con, $SQL);

						while ($suc=mysqli_fetch_array($sucursales)) { ?>
						<option value="<?php echo $suc["id_sucursal"]; ?>"> <?php echo $suc["sucursal"]; ?></option>
						<?php } ?>
				</select>
			</div>
			<div class="ed-item web-25 izquierda-contenido">
				<div id="zona_asesor">
					<input type="hidden" id="asesor" value="0">
				</div>

			</div>
			<div class="ed-item web-15 izquierda-contenido">
					<a href="" class="icon-buscar espacio" id="generar_reporte">Generar Reporte</a>
			</div>
			<div class="ed-item">
				<hr>
			</div>

		</div>
	</header>

	<main class="ed-container">

		<div class="ed-item" id="zona_informe_ajax">

		</div>

	</main>



</body>