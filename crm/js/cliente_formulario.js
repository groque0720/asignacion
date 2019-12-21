$("#cliente").focus();

function cerrar_formulario() {
	if (parseInt($("#lienzo_activos").val())>0) {
		nro=parseInt($("#lienzo_activos").val())-1;
		$(".lienzo-formulario-"+$("#lienzo_activos").val()).hide();
		$("#lienzo_activos").val(nro);
		
	}else{
		$(".lienzo-formulario").hide();
	}
}


$(".btn-cancelar").click(function(event) {

	event.preventDefault();
	cerrar_formulario();

	id_cliente = $("#id").val();

	if ($('#guardado').val()==0) {
		
		$.ajax({
			url:"cliente_borrar.php",
			cache:false,
			type:"POST",
			data:{id_cliente:id_cliente},
			success:function(result){

			}
		});
	}
});

$(".form-formulario").submit(function(event) {
	event.preventDefault();
	band=0;

	// alert($("#fecha").val());
	
	 if ($("#cliente").val()=="" && band==0) {
		$("#cliente").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Apellido y Nombre' para guardar");
		band=1;
	}

	if ($("#id_localidad").val()==0 && band==0) {
		$("#id_localidad").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Localidad' para guardar");
		band=1;
	}

	if ($("#id_asesor").val()==0 && band==0) {
		$("#id_asesor").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Asesor' para guardar");
		band=1;
	}

	id_prospecto = $("#id_prospecto").val();

	if (band==0) {

		//actualiza los datos en el formulario de prospecto
		$("#id_cliente").val($("#id_form_cliente").val());
		$("#nombre_cliente").val($("#cliente").val());
		$("#telefono_cliente").val($("#telefono").val() +' - '+ $("#celular").val());
		//Fin de actualizacion de formulario de prospecto
		
		$(".mod").show();
		$.ajax({
			url:"cliente_guardar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			cerrar_formulario()
	 			if (id_prospecto=='') {
	 				$(".contenido-principal").html(result);
	 			}
	 		}
		});
	}
});


$("#id_provincia").change(function(){
	$(".mod").show();
	id_provincia=$(this).val();
		$.ajax({
		url:"ajax_actualizar_localidad.php",
		cache:true,
		type:"POST",
		data:{id_provincia:id_provincia},
		success:function(result){
			$("#id_localidad").html(result);
			$(".mod").hide();
			}
	});
});

