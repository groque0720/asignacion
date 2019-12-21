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
		.factor{
			background: #EEFFFF;
			font-weight: bold;
		}
		.factor_linea {
			margin-left: 50px;
		}
		.select_evaluacion{
			width: 95%;
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
				tot_eva=0;
				$("select").each(function(){
					tot_eva = tot_eva + parseInt($(this).val());
				})
				$("#resultado_evaluacion").html(tot_eva);

				id_evaluacion_objetivo = $(this).attr('data-id');
				evaluacion_sup = $(this).val();
				operacion="carga_evaluador_factor";

				$.ajax({
					url:"evaluacion_objetivo_asesores_procesar.php",
					cache:false,
					type:"POST",
					data:{id:id_evaluacion_objetivo, evaluacion_sup:evaluacion_sup, operacion:operacion},
					success:function(result){
						// // self.location="cuestionario.php?id="+result+"&cue=0 ";
						// alert(result);
					}
	    		});


			})

			$(".terminada").click(function(){

				if(confirm('Confirma que a termindo la Autoevaluación')){
					id=<?php echo $_GET['id']; ?>;
					operacion="terminado_evaluador_factor";
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
			<h1> <?php echo $_GET['usu']; ?></h1>
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
				mysql_query("SET NAMES 'utf8'");
				$tot_eva=0;
			?>

			<div class="ed-container web-80">
				<div class="ed-item web-20 centrar-texto">
					Evaluación: <span class="negrita-italica titulo">Factores</span>
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
							<td width="50%">Factor</td>
							<td width="10%">Evaluación de Superior</td>
						</tr>

					</thead>

				<tbody>
					<?php
						$SQL="SELECT * FROM  evaluacion_f_preguntas";
						$res_factor=mysqli_query($con, $SQL);
						$linea=0;
						while ( $preg=mysqli_fetch_array($res_factor)) { $linea=$linea+1;?>

							<tr class="factor">
								<td width="5%"><div class="izquierda-texto"><?php echo $linea. " - ".$preg["factor"]; ?></div>	</td>
								<td width="3%"><div class="centrar-texto"><?php echo "Ponderación: ".$preg["ponderacion"]; ?></div>	</td>

							</tr>

							<?php
							$SQL="SELECT * FROM evaluaciones_realizadas_factores WHERE id_evaluacion_f=".$preg["id_evaluacion_f"]." AND id_evaluacion_realizada =".$_GET['id'];
							$evaluaciones = mysqli_query($con, $SQL);

							while ($eva=mysqli_fetch_array($evaluaciones)) { ?>

							<tr >
								<td width="5%"><div class="factor_linea"><?php echo $eva["factor"]; ?></div>	</td>


								<td>
									<div width="50%" class="centrar-texto">
									<select class="<?php echo 'select_evaluacion select_'.$linea; ?>" data-id="<?php echo $eva['id_evaluacion_objetivo'] ?>" name="auto_evaluacion" id="">
										<option value="0"></option>
										<?php
											$SQL="SELECT * FROM evaluacion_ponderaciones_f";
											$respueta_p=mysqli_query($con, $SQL);

											while($li_pon=mysqli_fetch_array($respueta_p)) {?>
											<option value="<?php echo $li_pon['puntos']; ?>" <?php if ($li_pon['puntos']==$eva['evaluacion_sup']) {
												echo "selected";
											} ?>><?php echo $li_pon['detalle']; ?></option>

											<?php } ?>

									</select>

								</div>


								</td>


								<?php
									$calificacion = '-';
									$tot_eva = $tot_eva + $eva['evaluacion_sup'];

									$SQL="SELECT * FROM evaluacion_ponderaciones_f WHERE puntos = ".$eva['evaluacion_sup'];
									$res_pon=mysqli_query($con, $SQL);


									if (!empty($res_pon)) {
										$cali=mysqli_fetch_array($res_pon);
										$calificacion = $cali['detalle'];
									}
								 ?>
<!-- 								<td width="5%"><div class="centrar-texto"><?php echo $calificacion; ?></div>	</td> -->

							</tr>

							<?php }
						}
					 ?>

					 	<tr class="fila_resultado">
							<td width="5%"><div class="centrar-texto">Resultado</div></td>
							<td width="3%"><div class="centrar-texto" id="resultado_evaluacion"><div class="resultado"></div> <?php echo $tot_eva;?></div>	</td>

						</tr>

					</tbody>
				</table>
			<hr>
			<div class="ed-container ">
				<div class="ed-item derecha-texto">
					<span class="terminada texto-rojo negrita-italica cursor">Terminado</span>
				</div>

			</div>


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