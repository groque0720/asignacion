$(document).ready(function(){
	
	var id;
	var pagina;
	var ref_pag;

	function cerrarcarga(){
		$("#img_carga").hide();			
	}

	function abrircarga(){
		$("#img_carga").show();
	}

	function cambiarvista(){

		$('#tabla_res tr').eq(id).find('td:eq(8)').each(function () {
				$(this).find('#selvisto').addClass('visto');
				$(this).find('#selvisto').removeClass('novisto');
			 })

		$('#tabla_res tr').eq(id).removeClass('negrita');

			idnot=$('#tabla_res tr').eq(id).find('td:eq(9)').html();
			id_usuario=$('#idusuario').val();
			pos_not=$('#pos_not').val();

			$.ajax({url:"noti_res_marca_visto.php",
						cache:false,
						type:"POST",
						data:{id:idnot},
						success:function(result){}
					});


			$("#numero_anterior").val(parseInt($("#numero_anterior").val())-1);
			$("#numero_act").val(parseInt($("#numero_act").val())-1);

			switch(pos_not)
          	{
               case '1': cant=$("#cant_res").html();$("#cant_res").html(cant-1);break;
               case '2': cant=$("#cant_act").html();$("#cant_act").html(cant-1);break;
               case '3': cant=$("#cant_anu").html();$("#cant_anu").html(cant-1);break;
               case '4': cant=$("#cant_fact").html();$("#cant_fact").html(cant-1);break;
               case '5': cant=$("#cant_cred").html();$("#cant_cred").html(cant-1);break;
               case '6': cant=$("#cant_canc").html();$("#cant_canc").html(cant-1);break;               
          	}
	}

//-------------------------------------------------------
function cambiarpagina(){

		abrircarga();

		reg_inicio=((pagina-1)*50); // cambiar aca tambien el numero de registros por pagina

		switch($('#pos_not').val())
		{
			case '1': ref_pag='noti_res_nueva.php';break;
			case '2': ref_pag='noti_res_mod.php';break;
			case '3': ref_pag='noti_res_anulada.php';break;
			case '4': ref_pag='noti_res_facturacion.php';break;
			case '5': ref_pag='noti_res_creditos.php';break;
			case '6': ref_pag='noti_res_cancelacion.php';break;

		}

		// $("#tabla").load(ref_pag+$("#idusuario").val()+'&inicio='+reg_inicio+'&pagina='+pagina,function(){});

		id=$("#idusuario").val();
		// inicio=

		$.ajax({url:ref_pag, cache:false, type:"GET", data:{id:id, inicio: reg_inicio, pagina: pagina},success:function(result){
	      	$("#tabla").html(result);
	      	cerrarcarga();

	    	}});

}


//------------------------------------------------
	$(".visto").click(function(event) {
		event.preventDefault();
		alert("Ya visto");
		});
//------------------------------------------------

	$(".novisto").click(function(event) {
		event.preventDefault();
		id = $(this).attr('data-id');
		var clase = $('#tabla_res tr').eq(id).find('td:eq(8)').find('#selvisto').attr('class');
		if (clase!='visto') {
			if (confirm("Confirma la visualizaci\u00f3n??")) {
				
				// Mensaje de contenido de una celda especifica (Fila y Columna)
				//alert($('#tabla_res tr').eq(id).find('td:eq(8)').html())
				cambiarvista();
			};
		}else{
			alert("Ya Visto");
		};	
	});
//--------------------------------------------------
	$(".ir_reserva").click(function(event) {
		event.preventDefault();
		id = $(this).attr('data-id');
		//guardo la class en la variable clase
		var clase = $('#tabla_res tr').eq(id).find('td:eq(8)').find('#selvisto').attr('class');
		if (clase!='visto') {
			cambiarvista();
		};

		url = $(this).attr("href");
      	window.open(url, '_blank');
      	return false;
   	});
 //---------------------------------------------
	 $(".borrar").click(function(event){
		event.preventDefault();
		id = $(this).attr('data-id');
		//alert($('#tabla_res tr').eq(id).find('td:eq(9)').html());
		if (confirm("Confirma Eliminar Aviso??")) {
			
			$('#tabla_res tr').eq(id).hide();
			
			var clase = $('#tabla_res tr').eq(id).find('td:eq(8)').find('#selvisto').attr('class');
			if (clase!='visto') {
				cambiarvista();
			};

			idnot=$('#tabla_res tr').eq(id).find('td:eq(9)').html();
			$.ajax({url:"noti_res_marca_borrar.php",
						cache:false,
						type:"POST",
						data:{id:idnot},
						success:function(result){}
					});			

		};

	 });
//----------------------------------------------------------
	$(".indice").click(function(event){
		
		event.preventDefault();
		pagina = $(this).attr('data-id');
		cambiarpagina();
		
		
	});

//-------------------------------------------------------------		

	$(".fder").click(function(event){
		event.preventDefault();
		
		pagina = parseInt($("#pagina").val())+1;
		max_pag = $("#total_paginas").val();
		if ((pagina-1)<max_pag) {
		cambiarpagina();
		};
		
	}); 

//-------------------------------------------------------------		

	$(".fizq").click(function(event){
		event.preventDefault();
		pagina = parseInt($("#pagina").val())-1;
		if ((pagina+1)>1) {
		cambiarpagina();
		};
		
	}); 

//--------------------------------------------------

	$(".seguimiento").click(function(event){
		event.preventDefault();
		id = $(this).attr('data-id');
		idusu=$(this).attr('href');
		$("#leyenda").html("HISTORIAL DE MODIFICACIONES")
		$("#tabla").load("noti_res_seguimiento.php?id="+id+"&idusu="+idusu);

	});		  	


});