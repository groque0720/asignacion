<!DOCTYPE html>
<html lang="es">
<head>
	<title>Panel de Aplicaciones</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/panel.css">
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$(".app").click(function(){
				self.location = $(this).attr("data-url");
			})
		});

	</script>
</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>
	<div class="ed-container">
		<div class="ed-item centrar-texto">
			<h1>Aplicaciones Disponibles</h1>
		</div>
	</div>
	<div class="ed-container">

		<?php
			include("../funciones/func_mysql.php");
			conectar();
			mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT * FROM aplicaciones WHERE activo=1 AND baja=0 ORDER BY aplicacion";
			$res=mysqli_query($con, $SQL);

			while ($app=mysqli_fetch_array($res)) { ?>
				<div class="ed-item web-1-8 centrar-texto caja-app">
					<div class="app ed-item" data-url="<?php echo $app["url"]; ?>"><?php echo  $app["aplicacion"]; ?></div>
				</div>
			<?php }	?>
	</div>


	<header class="ed-item centrar-texto">

	</header>

	<section>
	</section>

	<footer>
	</footer>
</body>
</html>