$(document).ready(function(){


	$('#texto_buscar').autocomplete({
		source: "noticias_autocomplete.php"
		});

	function cambiartipo(){


		$("#img_carga").show();

		reg_inicio=0; // cambiar aca tambien el numero de registros por pagina
		pagina=1;
		id=$("#idusuario").val();

		switch($('#pos_not').val())
		{
			case '1': ref_pag='noti_res_nueva.php';$("#leyenda").html("RESERVAS NUEVAS");break;
			case '2': ref_pag='noti_res_mod.php';$("#leyenda").html("RESERVAS MODIFICADAS");break;
			case '3': ref_pag='noti_res_anulada.php';$("#leyenda").html("RESERVAS ANULADAS");break;
			case '4': ref_pag='noti_res_facturacion.php';$("#leyenda").html("PEDIDOS DE FACTURACION");break;
			case '5': ref_pag='noti_res_creditos.php';$("#leyenda").html("NOVEDADES DE CREDITOS");break;
			case '6': ref_pag='noti_res_cancelacion.php';$("#leyenda").html("UNIDADES CANCELADAS");break;

		}

		$.ajax({url:ref_pag, cache:false, type:"GET", data:{id:id, inicio: reg_inicio, pagina: pagina},success:function(result){
	      	$("#tabla").html(result);
	      	$("#img_carga").hide();
	    	}});

	}



	if($("#id_usuario_dyv").val()== 47){
		$("#pos_not").val("1");
		cambiartipo();
	}


	$("#noti_res").click(function(event) {
		$("#pos_not").val("1");
		cambiartipo();
	});

	$("#noti_act").click(function(event) {
		$("#pos_not").val("2");
		cambiartipo();
	});

	$("#noti_anu").click(function(event) {
		$("#pos_not").val("3");
		cambiartipo();
	});

	$("#noti_fact").click(function(event) {
		$("#pos_not").val("4");
		cambiartipo();
	});

	$("#noti_cred").click(function(event) {
		$("#pos_not").val("5");
		cambiartipo();
	});

	$("#noti_canc").click(function(event) {
		$("#pos_not").val("6");
		cambiartipo();
	});


});