<!DOCTYPE html>
<html lang="es">
<head>
	<title>Evaluación de Desempeño</title>
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
		.select_evaluacion{
			width: 90%;
			font-size: 1em;
			margin: 5px;
			border-color: #ECE7E7;
			color: #525259;
			font-family: 'open sans';
		}
	</style>

	<script>
		$(document).ready(function(){

			$(".select_evaluacion").change(function(){
				// alert($(this).attr('data-id')+" "+$(this).val());
				// id=<?php echo $_GET['id']; ?>;
				id_evaluacion_objetivo = $(this).attr('data-id');
				autoevaluacion = $(this).val();
				operacion="carga_evaluador";

				$.ajax({
					url:"evaluacion_objetivo_asesores_procesar.php",
					cache:false,
					type:"POST",
					data:{id:id_evaluacion_objetivo, autoevaluacion:autoevaluacion, operacion:operacion},
					success:function(result){
						// // self.location="cuestionario.php?id="+result+"&cue=0 ";
						// alert(result);
					}
	    		});
			});

			$(".terminada").click(function(){

				if(confirm('Confirma que a termindo la Autoevaluación')){
					id=<?php echo $_GET['id']; ?>;
					operacion="terminado_evaluador";
					$.ajax({
						url:"evaluacion_objetivo_asesores_procesar.php",
						cache:false,
						type:"POST",
						data:{id:id, operacion:operacion},
						success:function(result){
							alert('Muchas Gracias por su tiempo');

						}
		    		});
				}

			})

		});
	</script>

</head>
<body class="desarroll">
	<?php //include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1> Evaluación de <?php echo $_GET['usu']; ?></h1>
			<hr>
		</div>
	</div>
	<div class="ed-container zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="javascript:history.back()">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">

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
<!-- 				<div class="ed-item web-30 centrar-texto">
					Evaluado por: <span class="negrita-italica titulo">Gerente Comercial</span>
				</div> -->
				<div class="ed-item web-30  centrar-texto">
					Período: <span class="negrita-italica titulo"><?php echo $_GET['per']; ?></span>
				</div>
				<div class="ed-item total">

				</div>
			</div>
			<div class="zona-tabla-80" id="zona_ajax">
				<hr>
				<table class="tabla-default">
					<thead>
						<tr>
							<td width="5%">Nro</td>
							<td width="50%">Objetivo</td>
							<td width="5%">Ponderación</td>
							<td width="20%">Evaluación</td>
							<!-- <td colspan="2" width="20%">Evaluación Sup.</td> -->
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

							<td>
								<div width="50%" class="centrar-texto">
									<select class="select_evaluacion" data-id="<?php echo $eva['id_evaluacion_objetivo'] ?>" name="auto_evaluacion" id="">
										<option value="0"></option>
										<?php
											$SQL="SELECT * FROM evaluacion_ponderaciones_o";
											$respueta_p=mysqli_query($con, $SQL);

											while($li_pon=mysqli_fetch_array($respueta_p)) {?>
											<option value="<?php echo $li_pon['puntos']; ?>" <?php if ($li_pon['puntos']==$eva['evaluacion_sup']) {
												echo "selected";
											} ?>><?php echo $li_pon['resultado']; ?></option>

											<?php } ?>

									</select>

								</div>
							</td>

						</tr>
					<?php } //cierro el while?>

					</tbody>
				</table>
			<hr>
			</div>

			<div class="ed-container web-80">
				<div class="ed-item derecha-texto">
					<span class="terminada texto-rojo negrita-italica cursor">Terminado</span>
				</div>

			</div>

		</div>

	</div>




<?php mysqli_close($con); ?>
</body>
</html>