<!DOCTYPE html>
<html lang="es">
<head>
	<title>Encuestas</title>
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

				cad="";

				if ($(this).attr("data-mov")=='2') {
					cad="id_app="+$(this).attr("data-id")+"&";
				}

				self.location="encuesta.php?"+cad+"mov="+$(this).attr("data-mov");

			});

			$(".preguntas").click(function(e){
				e.preventDefault();
				self.location="encuestas_preguntas.php?id_encuesta="+$(this).attr("data-id");
			});

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Lista de Encuestas</h1>
		</div>
	</div>
	<div class="ed-container web-50 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="panel.php">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<a class="icon-enlace espacio nueva_app" data-mov="1" href="">Nueva Encuesta</a>
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				mysql_query("SET NAMES 'utf8'");
				$SQL="SELECT * FROM encuestas WHERE baja = 0 ORDER BY encuesta";
				$res=mysqli_query($con, $SQL);
			?>

			<div class="zona-tabla">

				<table class="tabla-default">
					<thead>
						<tr>
							<td width="10%">Encuestas</td>
							<td width="7%">Opción</td>
						</tr>
					</thead>
					<tbody>
						<?php
							$nro_fila=0;
							while ($app=mysqli_fetch_array($res)) {
							$nro_fila=$nro_fila+1;	?>
							<tr <?php if ($app['activo']==0) { echo 'class="no-activo"';} ?> >
								<td><?php echo $app["encuesta"] ?></td>
								<td>
									<a title="Editar Encuesta" class="icon-menu espacio editar" id="id_app" data-mov="2" data-id="<?php echo $app["id_encuesta"]; ?>" href="">Editar</a>
									<a title="Ver Preguntas" class="icon-menu-secundario espacio preguntas" id="id_app" data-mov="2" data-id="<?php echo $app["id_encuesta"]; ?>" href="">?? Ver Preguntas</a>
								</td>
							</tr>
						<?php } ?>

					</tbody>
				</table>

			</div>

		</div>

	</div>



<?php mysqli_close($con); ?>
</body>
</html>