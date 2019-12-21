<?php

include('../z_comun/vista.php');

@session_start();

?>

<div class="zona-formulario ancho-30 margen-auto" style="border: 1px solid #E0DDDD; border-radius: 5px; padding: 15px; margin-bottom: 10px !important;">
	<?php
		$SQL="SELECT * FROM usuarios WHERE id_usuario = ".$_SESSION["id_usuario"];
		$usuarios = mysqli_query($con, $SQL);
		$usuario = mysqli_fetch_array($usuarios);
	 ?>
	 <div>
	 	<center><span style="font-size: 20px;"><?php echo $usuario['nombre']; ?></span></center>
	 </div>
</div>


<div class="zona-formulario ancho-30 margen-auto" style="border: 1px solid #E0DDDD; border-radius: 5px; padding: 15px;">
	<center><h1>CAMBIAR CONTRASEÑA</h1></center>
	<br>
	<hr>
	<br>
<form class="form-login" action="validar_usuario.php" method="POST">
	<div class="form-renglon ancho-100">
		<div class="form-columna ancho-100">
			<label class="form-label" for="">Anterior Contraseña</label>
			<input class="form-input ancho-100" type="password" id="password" name="password" value="">
		</div>
	</div>
	<br>
	<div class="form-renglon ancho-100">
		<div class="form-columna ancho-100">
			<label class="form-label" for="">Nueva Contraseña</label>
			<input class="form-input ancho-100"  type="password" id="newpassword" name="newpassword" value="">
		</div>
	</div>
	<br>
	<div class="form-renglon ancho-100">
		<div class="form-columna ancho-100">
			<label class="form-label" for="">Confirmación Contraseña</label>
			<input class="form-input ancho-100" type="password" id="confirmnewpassword" name="confirmnewpassword" value="">
		</div>
	</div>
	<br>
		<div class="conjunto-grupo">
			<div class="form-grupo ">
				<div class="form-renglon flex">
					<div class="form-columna ancho-50">
						<a class="form-btn cancelar" href="/periodos" >CANCELAR</a>
					</div>
					<div class="form-columna ancho-50 derecha-texto">
						<input class="form-btn aceptar" type="submit">
					</div>
				</div>
			</div>
		</div>
</div>

</form>
	<div class="mensaje-ajax"></div>
<script>

$(document).ready(function(){


	$(".form-login").submit(function(event) {
		event.preventDefault();

		band=0;

		if (band==0 && $("#password").val()=='') {
			$("#password").focus();
			swal('','Por favor ingrese password Anterior','warning');
			band=1;
		}

		if (band==0 && $("#newpassword").val()=='') {
			$("#newpassword").focus();
			swal('','Por favor ingrese nueva Contraseña','warning');
			band=1;
		}

		if (band==0 && $("#confirmnewpassword").val()=='') {
			$("#confirmnewpassword").focus();
			swal('','Por favor ingrese Confirmación de Contraseña','warning');
			band=1;
		}

		if (band==0) {

			$(".mod").show();

			$.ajax({
				url:"validar_usuario.php",
				cache:false,
				type:"POST",
				data:$(this).serialize(),
				success:function(result){
					$(".mod").hide();
		 			$(".mensaje-ajax").html(result);
				}
			});

		}

	});

});
</script>