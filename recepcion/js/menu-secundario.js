	$(".item_link").click(function(event) {
		event.preventDefault();
		
		nuevo='-';
		if ($(this).attr('data-id')==1) {
			$(".mod").show();
			$.ajax({
				url:'registro_formulario.php',
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


});

//--------Busqueda---------

$("#texto_buscar").keypress(function(e){
	var keycode = (event.keyCode ? event.keyCode : event.which);

	abuscar = $("#texto_buscar").val();
	
	if(keycode == '13' && $(this).val()!=''){
		$(".zona_ver_mas").hide();
		$(".mod").show();
			$.ajax({
				url:'recepcion_busqueda.php',
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
			url:'recepcion_cuerpo.php',
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
