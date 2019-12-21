$(document).ready(function(){

	$(".limpiar").click(function(event) {
		event.preventDefault();
		swal({
		  title: "Desea Borrar Registro de Test Drive?",
		  text: "Se perderá la información ingresada..",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Si, Borrar!",
		  closeOnConfirm: true
		},
		function(){
	  		//swal("Deleted!", "Your imaginary file has been deleted.", "success");
			//$(".lienzo-form-agendar").hide();
			borrar='si';
			id_linea = $("#id_linea").val();
			sucursal = $("#sucursal").val();
			modelo = $("#modelo").val();
			fecha = $("#fecha").val();
			$(".mod").show();
				$.ajax({
					url:"guardar_agenda.php",
					cache:false,
					type:"POST",
					data:{borrar, id_linea, sucursal, modelo, fecha},
					success:function(result){
						$(".mod").hide();
		     			$(".agenda").html(result);
		      			$(".lienzo-form-agendar").hide();
		    		}
		    	});
		});
	});

	$(".cancelar").click(function(event) {
		event.preventDefault();
		$(".lienzo-form-agendar").hide();
	});

	$(".form-agendar").submit(function(event) {
		event.preventDefault();
		band=0;

		if ($("#cliente").val()=='') {
			$("#cliente").focus();
			swal("Falta Datos", "Por Favor ingrese nombre de cliente", "error");
			band=1;
		}

		if ($("#telefono").val()=='' && band==0) {
			$("#telefono").focus();
			swal("Falta Datos", "Por Favor ingrese el teléfono del cliente", "error");
			band=1;
		}

		if ($("#id_asesor").val()==1 && band==0) {
			$("#id_asesor").focus();
			swal("Falta Datos", "Por Favor Seleccione el Asesor", "error");
			band=1;
		}

		if (band==0) {

			$(".mod").show();
			$.ajax({
				url:"guardar_agenda.php",
				cache:false,
				type:"POST",
				data:$(this).serialize(),
				success:function(result){
					$(".mod").hide();
	     			$(".agenda").html(result);
	      			$(".lienzo-form-agendar").hide();
	    		}
	    	});

		}



	});

});