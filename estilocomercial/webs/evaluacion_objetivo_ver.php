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
		.res_final{
			color: red;
			font-weight: bold;
		}
		.titulo {
			color: red;
		}
		table tr{
			height: 30px;
		}
	</style>

	<script>
		$(document).ready(function(){

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1> <?php echo $_GET['usu']; ?></h1>
			<hr>
		</div>
	</div>
	<div class="ed-container zona-nav">
		<div class="ed-item web-50">
			<!-- <a class="icon-izquierda espacio" href="javascript:history.back()">Página Anterior</a> -->
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<!-- <a class="icon-pin espacio texto-rojo" href="<?php echo "evaluacion_objetivo_evaluador.php?id=".$_GET["id"]."&per=".$_GET["per"]."&usu=".$_GET["usu"]; ?>">Realizar Como Evaluador</a> -->
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				//mysql_query("SET NAMES 'utf8'");
				$tot_auto=0;
				$tot_eva=0;
			?>

			<div class="ed-container web-80">
				<div class="ed-item web-20 centrar-texto">
					Evaluación: <span class="negrita-italica titulo">Objetivos</span>
				</div>
				<div class="ed-item web-20 centrar-texto">
					Sector: <span class="negrita-italica titulo">Ventas</span>
				</div>
				<div class="ed-item web-30 centrar-texto">
					Evaluado por: <span class="negrita-italica titulo">Gerente Comercial</span>
				</div>
				<div class="ed-item web-30  centrar-texto">
					Período: <span class="negrita-italica titulo"><?php echo $_GET['per']; ?></span>
				</div>
				<div class="ed-item total">
					<hr>
				</div>
			</div>
			<div class="zona-tabla-80" id="zona_ajax">

				<table class="tabla-default">
					<thead>
						<tr>
							<td width="5%">Nro</td>
							<td width="50%">Objetivo</td>
							<td width="5%">Ponderación</td>
							<td colspan="2" width="20%">Auto evaluación</td>
							<td colspan="2" width="20%">Evaluación Sup.</td>
						</tr>

					</thead>

				<tbody>

					<?php

						$SQL="SELECT * FROM evaluaciones_realizadas_objetivos WHERE id_evaluacion_realizada =".$_GET['id'];
						$evaluaciones = mysqli_query($con, $SQL);
						$cantidad=0;
					while ($eva=mysqli_fetch_array($evaluaciones)) { $cantidad=$cantidad+1;?>
						<tr>
							<td><div class="centrar-texto"><?php echo $eva['nro_objetivo']; ?></div></td>
							<td><div class="izquierda-texto"><?php echo $eva['objetivo']; ?></div></td>
							<td><div class="centrar-texto"><?php echo $eva['ponderacion']." %"; ?></div></td>

							<?php

								$SQL="SELECT * FROM evaluacion_ponderaciones_o WHERE puntos = '".$eva['autoevaluacion']."'";
								$respueta_p=mysqli_query($con, $SQL);
								$pon['resultado']='';
								if (mysql_num_rows($respueta_p)) {
									$pon=mysqli_fetch_array($respueta_p);
								}
								$tot_auto= $tot_auto + ($eva['autoevaluacion']/100*$eva['ponderacion']);
							 ?>
							<td><div width="50%" class="centrar-texto"><?php echo $pon['resultado']; ?></div></td>
							<td><div width="50%" class="centrar-texto"><?php echo $eva['autoevaluacion']; ?></div></td>
							<?php

								$SQL="SELECT * FROM evaluacion_ponderaciones_o WHERE puntos = '".$eva['evaluacion_sup']."'";
								$respueta_p=mysqli_query($con, $SQL);
								$pon['resultado']='';
								if (mysql_num_rows($respueta_p)) {
									$pon=mysqli_fetch_array($respueta_p);
								}
								$tot_eva=$tot_eva + ($eva['evaluacion_sup']/100*$eva['ponderacion']);
							 ?>

							<td><div width="50%" class="centrar-texto"><?php echo $pon['resultado']; ?></div></td>
							<td><div class="centrar-texto"><?php echo $eva['evaluacion_sup']; ?></div></td>
						</tr>
					<?php } //cierro el while?>
						<tr class="fila_resultado">
							<td colspan='3'><div class="centrar-texto">Total</div></td>
							<td colspan='2'><div class="centrar-texto"><?php echo number_format($tot_auto,2); ?></div></td>
							<td colspan='2'><div class="centrar-texto"><?php echo number_format($tot_eva,2); ?></div></td>
						</tr>
						<tr class="fila_resultado texto-rojo">
							<td colspan='3'><div class="centrar-texto">Resultado de Evaluación de Objetivos:</div></td>
							<td colspan='4'><div class="centrar-texto">
							<?php
								$SQL="SELECT * FROM evaluacion_ponderaciones_o_objetivos WHERE hasta >=".$tot_eva." AND desde <=".$tot_eva;
							 	$res_calificacion = mysqli_query($con, $SQL);
							 	$valor_calificacion = mysqli_fetch_array($res_calificacion);
							 	echo $valor_calificacion['cumplimiento'];
							 ?>


						</div></td>

						</tr>
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