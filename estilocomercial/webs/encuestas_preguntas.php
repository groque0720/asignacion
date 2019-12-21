<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	$encuesta=$_GET["id_encuesta"];
	$SQL="SELECT * FROM encuestas_preguntas WHERE id_encuesta =".$encuesta."   AND baja = 0 ORDER BY nro_pregunta";
	$res=mysqli_query($con, $SQL);

	$SQL="SELECT * FROM encuestas WHERE id_encuesta=".$encuesta;
	$res_=mysqli_query($con, $SQL);
	$res_encuesta=mysqli_fetch_array($res_);
	$nom_encuesta=$res_encuesta["encuesta"];

?>




<!DOCTYPE html>
<html lang="es">
<head>
	<title>Encuesta <?php echo $nom_encuesta; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/estilo_default.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

	<script>
		$(document).ready(function(){

			$(".editar, .nueva_app").click(function(e){
				e.preventDefault();
				id_encuesta=$("#id_encuesta").val();

				cad="";

				if ($(this).attr("data-mov")=='2') {
					cad="& id_app="+$(this).attr("data-id");
				}
				self.location="encuestas_pregunta.php?id_encuesta="+id_encuesta+cad+"& mov="+$(this).attr("data-mov");
			});

			$("input[name=proxima_pregunta]").change(function () {
					id=$(this).attr("data-id");
				    elegido=$(this).val();
				    op="act_prox_preg";
				    $.get("encuestas_preguntas_ajax.php",
				    	 {id: id, opcion: op, elegido: elegido },
				    	 function(data){

				         });
			});

			$(".si_respuesta").change(function(){
				nro_fila=$(this).attr("data-fila");
				id=$(".si_respuesta_"+nro_fila).attr("data-id");
			    elegido=$(".si_respuesta_"+nro_fila).val();
			    op="act_sirespuesta";
			    $.get("encuestas_preguntas_ajax.php",
			    	 {id: id, opcion: op, elegido: elegido },
			    	 function(data){

			         });
			});

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Preguntas Encuesta <?php echo $nom_encuesta; ?></h1>
		</div>
	</div>
	<div class="ed-container web-70 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="encuestas.php">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<a class="icon-enlace espacio nueva_app" data-mov="1" href="">Nueva Pregunta</a>
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">

			<input type="hidden" id="id_encuesta" value="<?php echo $encuesta; ?>">

			<div class="zona-tabla-75">

				<table class="tabla-default">
					<thead>
						<tr>
							<td width="1%">Nro</td>
							<td width="15%">Pregunta</td>
							<td width="2%">Si Resp.</td>
							<td width="2%">Prox. Preg.</td>
							<td width="2%">Opción</td>
						</tr>
					</thead>
					<tbody>
						<?php
							$nro_fila=0;
							while ($app=mysqli_fetch_array($res)) {
							$nro_fila=$nro_fila+1;	?>
							<tr <?php if ($app['activo']==0) { echo 'class="no-activo"';} ?> >
								<td><div class="centrar-texto"><?php echo $app["nro_pregunta"] ?></div> </td>
								<td><?php echo $app["pregunta"]?></td>
								<td>
									<div class="centrar-texto">
										<?php
											$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE activo = 1 AND baja =0 AND id_tipo_respuesta=".$app["id_tipo_respuesta"];
											$form=mysqli_query($con, $SQL);

										 ?>
										<select name="si_respuesta" id="si_respuesta" class="<?php echo "si_respuesta si_respuesta_".$nro_fila ?>" data-fila="<?php echo $nro_fila; ?>" data-id="<?php echo $app["id_pregunta"]; ?>">
											<option value="0">#S/O</option>
											<?php  while ($linea_respuestas=mysqli_fetch_array($form)) { ?>
												<option value="<?php echo $linea_respuestas["id_linea_tipo_respuesta"] ?>" <?php if ($app["si_respuesta"]==$linea_respuestas["id_linea_tipo_respuesta"]) { echo "selected";} ?>><?php echo $linea_respuestas["linea_tipo_respuesta"]; ?></option>
											<?php } ?>
										</select>
									</div>
								</td>

								<td><div class="centrar-texto"><input class="input-tabla"  class="proxima_pregunta" size="1" type="text" id="" name="proxima_pregunta" data-id="<?php echo $app["id_pregunta"]; ?>" value="<?php echo $app["proxima_pregunta"]; ?>"></div> </td>
								<td>
									<a title="Editar Pregunta" class="icon-menu espacio editar" id="id_app" data-mov="2" data-id="<?php echo $app["id_pregunta"]; ?>" href="">Editar</a>
								</td>
							</tr>
						<?php } ?>

					</tbody>
				</table>

			</div>

		</div>

	</div>

	<div class="barra">

	</div>



<?php mysqli_close($con); ?>
</body>
</html>