$(".item_link").click(function(event) {

	event.preventDefault();
	
	if ($(this).attr('data-id')=='control_entregas') {
		$(".mod").show();
		$(".input-buscar").show();
		abuscar='';
		$.ajax({
			url:"entregas_contenido_relleno.php",
			cache:false,
			type:"POST",
			data:{abuscar:abuscar},
			success:function(result){
				$(".mod").hide();
     			$(".contenido-principal").html(result);
	      	$(".mod").hide();
    		}
    	});
	}

	if ($(this).attr('data-id')=='agenda_entregas') {
		$(".mod").show();
		$(".input-buscar").show();
		abuscar='';
		$.ajax({
			url:"entregas_agenda_contenido_relleno.php",
			cache:false,
			type:"POST",
			data:{abuscar:abuscar},
			success:function(result){
				$(".mod").hide();
     			$(".contenido-principal").html(result);
	      	$(".mod").hide();
    		}
    	});
	}

	if ($(this).attr('data-id')=='ultimas_entregas') {
		$(".mod").show();
		$(".input-buscar").hide();
		nuevaUnidad='nuevaUnidad';
		$.ajax({
			url:"contenido_relleno_entregadas.php",
			cache:false,
			type:"POST",
			data:{nuevaUnidad:nuevaUnidad},
			success:function(result){
				$(".mod").hide();
     			$(".contenido-principal").html(result);
	      	$(".mod").hide();
    		}
    	});
	}

});

//--------Busqueda---------

$("#texto_buscar").focusin(function(event) {

	$(".zona-contenido").addClass('zona-contenido-total');
	$(".menu-lateral").addClass('menu_lateral_oculto');
	$(".menu-secundario").addClass('menu-secundario-total');
	
	$("#icono-menu").prop('checked', true);
});

// $("#texto_buscar").focusout(function(event) {

// 	if ($(this).val()=='') {
// 		$(".zona-contenido").removeClass('zona-contenido-total');
// 		$(".menu-lateral").removeClass('menu_lateral_oculto');
// 		$(".menu-secundario").removeClass('menu-secundario-total');
// 		$(".fila-modelo").addClass('fila-oculto');
// 		$(".fila-grupo").addClass('fila-oculto');
// 		$("#icono-menu").prop('checked', false);
// 	}
	

// });

$("#texto_buscar").keypress(function(e){
	var keycode = (event.keyCode ? event.keyCode : event.which);

	  if(keycode == '13' && $(this).val()!=''){
			$(".mod").show();
	    	abuscar = $("#texto_buscar").val();
    		url="entregas_busqueda_rapida_unidades.php";
			$.ajax({
				url:url,
				cache:false,
				type:"POST",
				data:{abuscar:abuscar},
				success:function(result){
		      	$(".contenido-principal").html(result);
		      	$(".mod").hide();
		    	}
	    });  
	  }

	  if ($(this).val()=='' && keycode == '13') {
	  		abuscar='';
			$(".mod").show();
    		url="entregas_contenido_relleno.php";
			$.ajax({
				url:url,
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

