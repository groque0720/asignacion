<!DOCTYPE html>
<html lang="es">
<head>
	<title>Aplicaciones</title>
	<meta charset="UTF-8">
	 <meta http-equiv="refresh" content="500">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="css/styles.css">
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<style>
		.ui-dialog-titlebar{
			display: none;
		}
		#form_excel {
			border: 1px solid #000;
		}
		.item {
			border: 1px solid #D2CACA;
			border-radius: 5px;
			margin: 12px;
			padding: 10px;
			transition:all 0.3s ease;
			position: relative;
		}
		.cantidad {
			position: absolute;
			padding: 5px 10px;
			right: -5px;
			background: red;
			color: white;
			font-weight: bold;
			border-radius: 50%;
			top: -5px;
		}

		.item:hover .titulo{
			color:red;
/*			font-weight: bold;*/
		}

		.item:hover {
			box-shadow: 3px 3px 2px #D8D1D1;
		}


	</style>
	<script>
		$(document).ready(function(){

		})
	</script>
</head>
<body>
<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
@session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../login");
	//ademas salgo de este script
	exit();
}
?>
	<div class="ed-container">
		-
	</div>
	<div class="ed-container web-60">
		<div class="ed-item web-1-3 movil-1-3">
			<img class="web-40 movil-30" src="imagenes/logodyv.png" alt="logodyv">
		</div>
		<div class="ed-item web-1-3 movil-1-3 centrar-texto">
			<span>Panel de Aplicaciones</span>
		</div>
		<div class="ed-item web-1-3 movil-1-3 derecha-contenido ">
			<img class="web-40 movil-25" src="imagenes/logo_toyota.png" alt="logodyv">
		</div>
		<div class="ed_item total">
		<hr>
		</div>
	</div>
	<div class="ed-container web-60 tablet-90 movil-90 base-100">

		<?php

			$SQL="select aplicaciones.id_aplicacion AS id_aplicacion,aplicaciones.url AS url,aplicaciones.imagen AS imagen,usuarios_aplicaciones.id_usuario AS id_usuario,aplicaciones.aplicacion AS aplicacion,usuarios.idsucursal AS idsucursal from ((usuarios_aplicaciones join aplicaciones on((usuarios_aplicaciones.id_aplicaciones = aplicaciones.id_aplicacion))) join usuarios on((usuarios_aplicaciones.id_usuario = usuarios.idusuario))) where (aplicaciones.activo = 1 AND id_usuario =". $_SESSION['id'].") order by aplicaciones.aplicacion ";


			$SQL = "SELECT
					aplicaciones.id_aplicacion AS id_aplicacion,
					aplicaciones.url AS url,
					aplicaciones.imagen AS imagen,
					usuarios_aplicaciones.id_usuario AS id_usuario,
					aplicaciones.aplicacion AS aplicacion,
					usuarios.idsucursal AS idsucursal,
					aplicaciones.activo
					from ((usuarios_aplicaciones join aplicaciones on((usuarios_aplicaciones.id_aplicaciones = aplicaciones.id_aplicacion))) join usuarios on((usuarios_aplicaciones.id_usuario = usuarios.idusuario)))
					where (aplicaciones.activo = 1 AND id_usuario =". $_SESSION['id'].")
					order by aplicaciones.aplicacion ";

			///$SQL="SELECT * FROM view_aplicaciones_usuarios WHERE id_usuario = ".$_SESSION["id"];
			$aplicaciones=mysqli_query($con, $SQL);

			while ($aplicacion = mysqli_fetch_array($aplicaciones)) { ?>

				<div class="ed-item web-1-6 tablet-1-6 movil-1-6 base-1-6 item">


				<?php

					if ($aplicacion['id_aplicacion']==17) {

					$SQL="SELECT count(visto) as cantidad FROM recepcion WHERE visto = 0 AND id_asesor = ".$_SESSION["id"];
					$res_cantidad = mysqli_query($con, $SQL);
					$noti = mysqli_fetch_array($res_cantidad);

					if ($noti['cantidad']!=0) { ?>
						<div class="cantidad">
							<span><?php echo $noti['cantidad']; ?></span>
						</div>
				<?php } } ?>
					<div class="img">
						<a  target="_blank" href="<?php echo $aplicacion['url']; ?>">
							<div class="ed-container" style="height: 80px;">
								<div class="ed-item centrar-texto">
									<img src="<?php echo $aplicacion['imagen']; ?>" alt="<?php echo $aplicacion['aplicacion']; ?>">
								</div>
							</div>
							<div class="centrar-texto" style="font-size:14px;">
								<span class="titulo">
									<?php echo $aplicacion['aplicacion']; ?>
								</span>
							</div>
						</a>
					</div>
				</div>


			<?php } ?>

	</div>


</body>
</html>