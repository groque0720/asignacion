<!DOCTYPE html>
<html lang="es">
<head>
	<title>Evaluación Asesores</title>
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
		.res_final{
			color: red;
			font-weight: bold;
		}
		.titulo {
			color: red;
		}
	</style>

	<script>
		$(document).ready(function(){

		});
	</script>

</head>
<body class="desarroll">
	<!-- 	<?php //include("../_seguridad/_seguridad.php") ?> -->

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Detalle de Evaluaciones de Desempeño</h1>
			<hr>
		</div>
	</div>
	<div class="ed-container web-80 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="javascript:history.back()">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">

		</div>

	</div>

	<div class="ed-container web-80">

		<div class="ed-item centrar-texto">
			Período: <span class="negrita-italica titulo"><?php echo $_GET['per']; ?></span>
			<?php $periodo= $_GET['per']; ?>
		</div>

		<div class="ed-item">
			<hr>
		</div>

	</div>

	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				mysql_query("SET NAMES 'utf8'");
			?>

			<div class="zona-tabla-90" id="zona_ajax">

				<table class="tabla-default">
					<thead>
						<tr>
							<td width="10%">Asesor</td>
							<td width="3%">Autoevaluacion de Objetivos</td>
							<td width="3%">Evaluado x Objetivos</td>
							<td width="3%">Evaluado x  Factores</td>
							<td class="texto_bold" width="3%">Resultado Final</td>
							<td width="3%">Calificación</td>
							<td width="5%">Ver Evaluación</td>
						</tr>
					</thead>

				<tbody>

					<?php

						$SQL="SELECT * FROM usuarios WHERE  gerente = 0 AND activo = 1 AND id_perfil = 2 AND id_usuario <> 22 AND id_sucursal = 3 ORDER BY id_sucursal ASC";
						$asesores = mysqli_query($con, $SQL);

					while ($usu=mysqli_fetch_array($asesores)) {

						$SQL="SELECT * FROM evaluaciones_realizadas WHERE id_evaluacion =".$_GET['id']." AND id_usuario = ".$usu['id_usuario'];
						$res=mysqli_query($con, $SQL);
						$ev=mysqli_fetch_array($res);
					 ?>
						<tr>
							<td><div class="centrar-texto"><?php echo $usu['nombre']; ?></div></td>
							<td><div class="centrar-texto"><?php echo $ev['puntaje_autoevaluado']; ?></div></td>
							<td><div class="centrar-texto"><?php echo $ev['puntaje_objetivos']; ?></div></td>
							<td><div class="centrar-texto"><?php echo $ev['puntaje_factores']; ?></div></td>
							<td class="res_final"><div class="centrar-texto"><?php echo (int)$ev['puntaje_factores']+(int)$ev['puntaje_objetivos']; ?></div></td>
							<td> <div class="centrar-texto">
								<?php
								$calificacion = (int)$ev['puntaje_factores']+(int)$ev['puntaje_objetivos'];

								if ($calificacion==0) {
								 	echo "-";
								 } else{
								 	$SQL="SELECT * FROM evaluacion_calificacion WHERE hasta >=".$calificacion." AND desde <=".$calificacion;
								 	$res_calificacion = mysqli_query($con, $SQL);
								 	$valor_calificacion = mysqli_fetch_array($res_calificacion);
								 	echo $valor_calificacion['calificacion'];

								 }

								 ?>
								 </div>
							</td>
							<td>
								<a class="icon-buscar espacio editar" href="<?php echo "evaluacion_objetivo_view.php?id=".$ev['id_evaluacion_realizada']."&per=".$periodo."&usu=".$usu['nombre']; ?>">Objetivo</a>
								<a class="icon-menu espacio editar" href="<?php echo "evaluacion_factor_view.php?id=".$ev['id_evaluacion_realizada']."&per=".$periodo."&usu=".$usu['nombre']; ?>">Factores</a>
							</td>
						</tr>
					<?php } //cierro el while?>

					</tbody>
				</table>

			</div>

		</div>

	</div>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>


<?php mysqli_close($con); ?>
</body>
</html>