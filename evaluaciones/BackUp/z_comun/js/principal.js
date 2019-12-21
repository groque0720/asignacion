$(document).ready(function(){

	$(".lienzo-calificacion").hide();


	$(".lienzo-menu").hide();

	$(".item-hijo").hide();

	function ocultar_menu_lateral(){
			if ($(".valor-menu").attr('data-valor')==1) {
			$(".menu-lateral").addClass('menu-lateral-oculto');
			$(".valor-menu").attr('data-valor',0);
			$(".lienzo-menu").fadeOut(500);

		}else{
			$(".menu-lateral").removeClass('menu-lateral-oculto');
			$(".valor-menu").attr('data-valor',1);
			$(".lienzo-menu").fadeIn(500);
		}	
	}


	$(".icono-menu, .lienzo-menu").click(function(event){
		event.preventDefault();
		ocultar_menu_lateral();
	
	});


	function color_label(nombre){

		$(".form-label").removeClass('activo');

		$(".form-label").each(function(){

			if (nombre==$(this).attr('for')) {
				// alert($(this).attr('for'));
				$(this).addClass('activo');
			}
		});

	}

	function quitar_enfoque(){
		$(".form-label").removeClass('activo');
	}


	$(".form-input, .form-select").click(function(){
		nombre=$(this).attr('name');
		color_label(nombre);
	});

	$(".form-input, .form-select").focusout(function(){
		quitar_enfoque();
	});


	$("#i-padre").click(function(event){

		event.preventDefault();
		if ($(this).attr('data-valor')==0) {
			// $(".item-hijo").removeClass('item-hijo-oculto');
			$(".item-hijo").fadeOut(500);
			$(this).attr('data-valor',1);
		}else{
			// $(".item-hijo").addClass('item-hijo-oculto');
			$(".item-hijo").fadeIn(500);
			$(this).attr('data-valor',0);
		}
	});

	$(".item-link").click(function(event){
		event.preventDefault();
	})



});