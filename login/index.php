﻿<!DOCTYPE html>
<html lang="es">
<head>
	<title>Derka y Vargas</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<link rel="stylesheet" href="css/estilo.css">
	<!-- <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script> -->
	<script src="js/jquery-2.1.3.min.js"></script>
	<link rel="stylesheet" href="css/en_proceso.css">
	<script src="alertas_query/sweetalert-dev.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<script>
		$(document).ready(function(){

			if (navigator.geolocation)
				{
					navigator.geolocation.getCurrentPosition(function(objPosition)
					{
						var lon = objPosition.coords.longitude;
						var lat = objPosition.coords.latitude;

						// $('#form_login').append();
						$('#form_login').append("<input type='hidden' name='latitud' value="+ lat +">");
						$('#form_login').append("<input type='hidden' name='longitud' value="+ lon +">");

					});
				};


			$(".mod").hide();

			$("#usuario").focus();

			$("#form_login").submit(function(event){
				event.preventDefault();
				$(".mod").show();
				$.ajax({
					url:"validarusuario.php",
					cache:false,
					type:"POST",
					data:$(this).serialize(),
					success:function(result){
						$(".mod").hide();
						$("#mensaje_ajax").html(result);

					}
				})

			})

		})
	</script>
</head>
<body>

	<section class="mod model-3">
	  <div class="spinner">
	  	<img class="imagen_gira" src="imagenes/logo_dyv.png" alt="">
	  </div>
	</section>

<div class="container">
	<div class="slider">
		<ul>
			<li><img src="imagenes/slider/img01_tn.jpg" alt=""></li>
			<li><img src="imagenes/slider/img02_n.jpg" alt=""></li>
			<li><img src="imagenes/slider/img03_n.jpg" alt=""></li>
		</ul>
	</div>
	<div class="lienzo">

	</div>
	<?php
		function getRealIP() {

	        if (!empty($_SERVER['HTTP_CLIENT_IP']))
	            return $_SERVER['HTTP_CLIENT_IP'];

	        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	            return $_SERVER['HTTP_X_FORWARDED_FOR'];

	        return $_SERVER['REMOTE_ADDR'];
		}
	 ?>
	<div class="formulario">
		<div class="cuadro_form">
			<div class="cuadro">
				<div class="logo">
					<img src="imagenes/logodyv.png" alt="">
				</div>
				<form action="" id="form_login" class="form_login">
				<input type="hidden" name="ip_user" value="<?php echo getRealIP() ?>">
					<div id="form_login">
						<h1>Iniciar sesión</h1>
						<div class="renglon">
							<div class="zona_input">
								<input type="text" name="usuario" id="usuario" value="" placeholder="Usuario" >
							</div>
							<div class="zona_logo">
								<img src="imagenes/user.png" alt="">
							</div>
						</div>
						<div class="renglon">
							<div class="zona_input">
								<input type="password" name="contraseña" id="contraseña" value="" placeholder="Contraseña">
							</div>
							<div class="zona_logo">
								<img src="imagenes/lock.png" alt="">
							</div>
						</div>
						<div class="renglon_dos">
							<div class="zona_boton_envio">
								<input class="btn_entrar" type="submit"  value="ENTRAR">
							</div>
						</div>
<!-- 						<div class="renglon_tres">
							<div class="zona_olvido">
								<span id="olvido_pass">¿Olvidaste tu clave?</span>
								<span class="texto-verde">No Eres Miembro? Regístrate</span>
							</div>
						</div> -->

					</div>
				</form>


			</div>

		</div>
		<div class="cuadro_form logo_world">
			<img src="imagenes/logo_toyota.png" alt="">
		</div>
		<div class="cuadro_form derechos">
			<span>2020 &copy; Derka y Vargas S. A. Todos los Derechos Reservados</span>
		</div>
		<div id="mensaje_ajax" class="mensaje_ajax"></div>
	</div>
</div>
</body>
</html>