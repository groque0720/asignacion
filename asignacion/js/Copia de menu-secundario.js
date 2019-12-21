	$(".item_link").click(function(event) {
		event.preventDefault();
		

		if ($(this).attr('data-id')==1) {
			$(".mod").show();
			grupo=$('#grupo_activo').val();
			if (grupo!=16) { link='unidad.php';}else{link='usado.php';};
			nuevaUnidad='nuevaUnidad';

			$.ajax({
				url:link,
				cache:false,
				type:"POST",
				data:{nuevaUnidad:nuevaUnidad},
				success:function(result){
					$(".mod").hide();
	     			$(".lienzo-unidad").html(result);
	      		$(".lienzo-unidad").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')==2) {
			$(".mod").show();
			nuevaUnidad='nuevaUnidad';
			$.ajax({
				url:"carga-masiva.php",
				cache:false,
				type:"POST",
				data:{nuevaUnidad:nuevaUnidad},
				success:function(result){
					$(".mod").hide();
	     			$(".lienzo-unidad").html(result);
	      		$(".lienzo-unidad").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')==3) {
			$(".mod").show();
			nuevaUnidad='nuevaUnidad';
			$.ajax({
				url:"cambio-unidad.php",
				cache:false,
				type:"POST",
				data:{nuevaUnidad:nuevaUnidad},
				success:function(result){
					$(".mod").hide();
	     			$(".lienzo-unidad").html(result);
	      		$(".lienzo-unidad").show();
	    		}
	    	});
		}

		if ($(this).attr('data-id')==4) {
			$(".mod").show();
			nuevaUnidad='nuevaUnidad';

			$.ajax({
				url:"filtro_avanzado.php",
				cache:false,
				type:"POST",
				data:{nuevaUnidad:nuevaUnidad},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-principal").html(result);
	     			//oculta menu lateral
						$(".zona-contenido").addClass('zona-contenido-total');
						$(".menu-lateral").addClass('menu_lateral_oculto');
						$(".menu-secundario").addClass('menu-secundario-total');
						$("#icono-menu").prop('checked', true);
					//fin ocultar menu lateral
		      	$(".mod").hide();
	    		}
	    	});
		}

		if ($(this).attr('data-id')=='ultimas_entregas') {
			$(".mod").show();
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
	    	if ($("#usado_activo").val()=='si') {
	    		url='carga_unidades_usados_busqueda.php';
	    	}else{
	    		url="busqueda_rapida_unidades.php";
	    	}
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
			$(".mod").show();
			modelo_activo=$("#modelo_activo").val();;
			$.ajax({
				url:"contenido_relleno.php",
				cache:false,
				type:"POST",
				data:{modelo_activo_busqueda_vacio:modelo_activo},
				success:function(result){
		      	$(".contenido-principal").html(result);
		      	$(".mod").hide();
		    	}
			 });
	  }
});

