
$(".form-busq-entregadas").submit(function(event) {
	event.preventDefault();
	band=0;

	if ($("#año_hasta").val()=='') {
			$("#año_hasta").focus();
			swal("Referencias Incompletas", "Defina los parametros de Meses a Buscar ", "error");
			band=1;
	}
	if ($("#mes_hasta").val()==0 ) {
			$("#mes_hasta").focus();
			swal("Referencias Incompletas", "Defina los parametros de Meses a Buscar ", "error");
			band=1;
	}
	if ( $("#año_desde").val()=='' ) {
			$("#año_desde").focus();
			swal("Referencias Incompletas", "Defina los parametros de Meses a Buscar ", "error");
			band=1;
	}
	if ($("#mes_desde").val()==0) {
			$("#mes_desde").focus();
			swal("Referencias Incompletas", "Defina los parametros de Meses a Buscar ", "error");
			band=1;
	}
	

	if (band==0) {
		$(".mod").show();
		$.ajax({
			url:"contenido_relleno_entregadas_busqueda.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
	 			$(".resultado_busqueda").html(result);
	 			$(".lienzo-unidad").hide();
	 			$(".mod").hide();
			}
		});
	}

});

