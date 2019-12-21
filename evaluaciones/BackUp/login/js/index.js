$(document).ready(function(){


	$(".form-login").submit(function(event) {
		event.preventDefault();	

		band=0;

		if (band==0 && $("#usuario").val()=='') {
			$("#usuario").focus();
			swal('','Por favor ingrese Usuario','warning');
			band=1;
		}

		if (band==0) {

			$(".mod").show();

			$.ajax({
				url:"login/validar_usuario.php",
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