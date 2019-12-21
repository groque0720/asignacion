$(".btn-cancelar").click(function(event) {


	event.preventDefault();
	$(".lienzo-formulario").hide();
	id_recepcion = $("#id_recepcion").val();

	if ($('#guardado').val()==0) {
		$.ajax({
			url:"recepcion_borrar.php",
			cache:false,
			type:"POST",
			data:{id_recepcion:id_recepcion},
			success:function(result){
			}
		});
	}

});

$(".form-formulario").submit(function(event) {
	event.preventDefault();
	band=0;
	$("#boton_ver_mas").attr("data-ini",0);

	// alert($("#fecha").val());


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


	if (band==0) {
		$(".mod").show();
		$.ajax({
			url:"recepcion_guardar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-formulario").hide();
	 			$(".contenido-principal").html(result);
			}
		});
	}
});

$('#add_localidad').click(function(event) {
	if ($('#id_provincia').val()!=0) {
	id_provincia=$('#id_provincia').val();
	// nva_loc = prompt('Ingrese Localidad de Registro  Automotor para la provincia seleccionada');
	
	swal({
	    title: "Agregar Localidad",
	    text: "Por favor ingrese el nombre de la Localidad",
	    type: "input",
	    showCancelButton: true,
	    closeOnConfirm: false,
	    animation: "slide-from-top",
	    inputPlaceholder: "Localidad" },

	    function(inputValue){
	        if (inputValue === false) return false;
	        if (inputValue === "") {
	        	swal.showInputError("Por favor ingrese el Nombre de la Localidad!");
	        	return false
	        	}
	        localidad = inputValue;

			$(".mod").show();
			$.ajax({
				url:"insertar_localidad.php",
				cache:true,
				type:"POST",
				data:{id_provincia:id_provincia, localidad:localidad},
				success:function(result){
					$('#id_localidad').html(result);
					swal("Se incorporó nueva localidad ",inputValue, "success");
					$(".mod").hide();
					}
			});
	 	}
	 );

}else{
	swal("PROVINCIA NO SELECCIONADA", "Por favor  seleccione primero la Provincia", "error");
}
});

$('#add_medio_contacto').click(function(event) {

	
	swal({
	    title: "Agregar Medio de Contacto",
	    text: "Por favor ingrese el nuevo Medio de Contacto",
	    type: "input",
	    showCancelButton: true,
	    closeOnConfirm: false,
	    animation: "slide-from-top",
	    inputPlaceholder: "Medio de Contacto" },

	    function(inputValue){
	        if (inputValue === false) return false;
	        if (inputValue === "") {
	        	swal.showInputError("Por favor ingrese el Nuevo de Contacto!");
	        	return false
	        	}
	        modo_acercamiento=inputValue;

			$(".mod").show();
			$.ajax({
				url:"insertar_modo_contacto.php",
				cache:true,
				type:"POST",
				data:{modo_acercamiento:modo_acercamiento},
				success:function(result){
					$('#id_acercamiento').html(result);
					swal("Se incorporó nuevo Medio de Contacto ",inputValue, "success");
					$(".mod").hide();
					}
			});
	 	}
	 );


});

$("#id_grupo").change(function(){
	id_grupo=$(this).val();
	$(".mod").show();
	$.ajax({
		url:"recepcion_actualizar_modelo.php",
		cache:true,
		type:"POST",
		data:{id_grupo:id_grupo},
		success:function(result){
			$("#id_modelo").html(result);
			$(".mod").hide();
			}
	});

});

$("#id_provincia").change(function(){
	id_provincia=$(this).val();
		$.ajax({
		url:"recepcion_actualizar_localidad.php",
		cache:true,
		type:"POST",
		data:{id_provincia:id_provincia},
		success:function(result){
			$("#id_localidad").html(result);
			$(".mod").hide();
			}
	});
});

