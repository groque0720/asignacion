$(document).ready(function(){


	$(".agendar").click(function(event) {
	band=0;

		if ($("#modelo-seleccionado").val()=='') {
			swal("Falta Datos", "Por Favor Seleccione Modelo", "error");
			band=1;
		}

		if ($("#sucursal-seleccionado").val()=='') {
			swal("Falta Datos", "Por Favor Seleccione Sucursal", "error");
			band=1;
		}

		if ($("#fecha").val()=='') {
			$("#fecha").focus();
			swal("Falta Datos", "Por Favor Seleccione Fecha", "error");
			band=1;
		}

		if (band==0) {
			$(".mod").show();
			id_linea = $(this).attr('data-id');
			$.ajax({
			url:"formulario_agendar.php",
			cache:false,
			type:"POST",
			data:{modelo:modelo, sucursal:sucursal, fecha:fecha, id_linea:id_linea},
			success:function(result){
				$(".mod").hide();
					$(".lienzo-form-agendar").html(result);
				$(".lienzo-form-agendar").show();
			}
		});
		}

	})

});