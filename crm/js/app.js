$(document).ready(function(){
	$(".mod").hide();
	$(".lienzo-formulario").hide();
	$(".lienzo-formulario-1").hide();
	$(".lienzo-formulario-2").hide();
	$(".lienzo-formulario-3").hide();
	$("#imagen_carga").hide();

	$("#boton_ver_mas").click(function(){

		$("#imagen_carga").show();
		$(this).hide();
		final=parseInt($(this).attr("data-cantidad"));
		inicio=parseInt($(this).attr("data-ini"))+final;
		$(this).attr("data-ini",inicio);
		//$('#tabla-recepcion tr:last').after('<tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr>');
		switch($("#listado_activo").val()) {
		    case 'dato_formulario.php':
		        url_link='datos_contenido_paginacion.php';
		        break;
		    case 'cliente_formulario.php':
		        url_link='clientes_contenido_paginacion.php';
		        break;
		    case 'prospecto_formulario.php':
		        url_link='prospectos_contenido_paginacion.php';
		        break;
		    case 'agenda_contacto_formulario.php':
		        url_link='agenda_contacto_contenido_paginacion.php';
		        break;
		}


		$.ajax({
			url:url_link,
			cache:false,
			type:"POST",
			data:{inicio, final},
			success:function(result){
	 			$('#tabla-datos tr:last').after(result);
	 			$("#imagen_carga").hide();
				$("#boton_ver_mas").show();
			}
		})
	});



})