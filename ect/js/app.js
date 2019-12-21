$(document).ready(function(){
	$(".mod").hide();
	$(".lienzo-unidad").hide();
	$(".carga-masiva").hide();

	$('.icon-menu').click(function(event) {
		if (!$("#icono-menu").prop('checked')) {
			$(".zona-contenido").addClass('zona-contenido-total');
			$(".menu-lateral").addClass('menu_lateral_oculto');
			$(".menu-secundario").addClass('menu-secundario-total');
			$(".fila-modelo").removeClass('fila-oculto');
			$(".fila-grupo").removeClass('fila-oculto');
			// $("#icono-menu").prop('checked', false);
		}else{
			$(".zona-contenido").removeClass('zona-contenido-total');
			$(".menu-lateral").removeClass('menu_lateral_oculto');
			$(".menu-secundario").removeClass('menu-secundario-total');
			$(".fila-modelo").addClass('fila-oculto');
			$(".fila-grupo").addClass('fila-oculto');
			// $("#icono-menu").prop('checked', true);
		}
	});

});