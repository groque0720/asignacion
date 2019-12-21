	$(".item_link").click(function(event) {
		event.preventDefault();
		$(".zona_ver_mas").show();
		$("#texto_buscar").val('');
		$(".boton_nuevo").show();
		$("#boton_ver_mas").attr('data-ini','0');

		if ($(this).attr('data-id')!='nuevo') {
			$('.item_link').removeClass('item-activo');
			$(this).addClass('item-activo');	
		}
	
		nuevo='-';

		if ($(this).attr('data-id')=='nuevo') {

			url=$("#listado_activo").val();

			$(".mod").show();
			$.ajax({
				url: url,
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
				$(".mod").hide();
	 			$(".lienzo-formulario").html(result);
	  			$(".lienzo-formulario").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')=='dato') {
			$("#listado_activo").val('dato_formulario.php');
			$('#nuevo').html('<span class="icon-plus"> </span> Nuevo Dato');
			$(".mod").show();
			$.ajax({
				url:'datos.php',
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-principal").html(result);
	      		// $(".lienzo-formulario").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')=='cliente') {
			$(".boton_nuevo").hide();
			$("#listado_activo").val('cliente_formulario.php');
			$('#nuevo').html('<span class="icon-plus"> </span> Nuevo Cliente');
			$(".mod").show();
			$.ajax({
				url:'clientes.php',
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-principal").html(result);
	      		// $(".lienzo-formulario").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')=='prospectos') {
			$("#listado_activo").val('prospecto_formulario.php');
			$('#nuevo').html('<span class="icon-plus"> </span> Nuevo Prospecto');
			$(".mod").show();
			$.ajax({
				url:'prospectos.php',
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-principal").html(result);
	      		// $(".lienzo-formulario").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')=='agenda') {
			$(".boton_nuevo").hide();
			$("#listado_activo").val('agenda_contacto_formulario.php');
			$('#nuevo').html('<span class="icon-plus"> </span> Nuevo contacto');
			$(".mod").show();
			$.ajax({
				url:'agenda_contacto.php',
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-principal").html(result);
	      		// $(".lienzo-formulario").show();
	    		}
	    	});
		}
});

//--------Busqueda---------

$("#texto_buscar").keypress(function(e){

		switch($("#listado_activo").val()) {
		    case 'dato_formulario.php':
		        url_link = 'datos_busqueda.php';
		        break;
		    case 'cliente_formulario.php':
		        url_link = 'clientes_busqueda.php';
		        break;
		    case 'prospecto_formulario.php':
		        url_link = 'prospectos_busqueda.php';
		        break;
		    case 'agenda_contacto_formulario.php':
		        url_link = 'agenda_contacto_busqueda.php';
		        break;
		}

	var keycode = (event.keyCode ? event.keyCode : event.which);
	abuscar = $("#texto_buscar").val();
	
	if(keycode == '13' && $(this).val()!=''){
		$(".zona_ver_mas").hide();
		$(".mod").show();
			$.ajax({
				url:url_link,
				cache:false,
				type:"POST",
				data:{abuscar:abuscar},
				success:function(result){
			      	$(".contenido-principal").html(result);
			      	$(".mod").hide();
		    	}
	    });  
	}

	if(keycode == '13' && $(this).val()==''){
		$(".zona_ver_mas").show();
		$("#boton_ver_mas").attr('data-ini',0);
		$(".mod").show();
		$.ajax({
			url:url_link,
			cache:false,
			type:"POST",
			data:{abuscar:abuscar},
			success:function(result){
		      	$(".contenido-principal").html(result);
		      	$(".mod").hide();
	    	}
	    });

	}

});

$("#select_suc").change(function(){
	$(".mod").show();
	id_suc=$(this).val();
	$.ajax({
		url:'recepcion_cuerpo_suc.php',
		cache:false,
		type:"POST",
		data:{id_suc:id_suc},
		success:function(result){
	      	$(".contenido-principal").html(result);
	      	$(".mod").hide();
    	}
    });
})
