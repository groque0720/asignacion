<!DOCTYPE html>
<html lang="es">
<head>

	<meta charset="UTF-8">
	<title>Login Ofirtual</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" href="login/css/estilo_login.css">
	<link rel="icon" href="z_comun/imagenes/logo_dyv.png">
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.css">
	<script src="login/js/index.js"></script>


</head>

<?php
	$nro = rand(1, 10);
 ?>

<body>


	<img class="img-fondo" src="<?php echo 'login/imagenes/slider/img0'.$nro.'.jpg'; ?>" alt="imagen">

	<div class="pagina">

		<div class="zona-login">


			<div class="zona-ingreso">

				<div class="zona-logo">
					<img src="z_comun/imagenes/logodyv.png" alt="logo-empresa">
				</div>
				<div>
					<hr class="linea-division">
				</div>

				<form action="" class="form-login">

					<div class="linea">
						<div class="zona-label">
							<label class="form-label" for="usuario">DNI (sin puntos)</label>
						</div>
						<div class="zona-input">
							<input class="form-input" id="usuario" name="usuario" type="text" value="">
						</div>
					</div>
					<div class="linea">
						<div class="zona-label">
							<label class="form-label" for="password">Contraseña</label>
						</div>
						<div class="zona-input">
							<input class="form-input" id="password" name="password" type="password" value="">
						</div>
					</div>
					<div class="linea zona-boton-enviar">
						<input class="form-submit" type="submit" value="ENTRAR">
					</div>

				</form>
			</div>

			<div class="zona-logo zona-logo-terminal">
				<img src="z_comun/imagenes/logo_toyota.png" alt="logo-empresa">
			</div>
			<div class="zona-copyright">
				<span><?php echo date('Y'); ?> © Derka y Vargas S. A. Todos los Derechos Reservados</span>
			</div>


		</div>

	</div>

	<div class="mensaje-ajax"></div>


</body>
</html>