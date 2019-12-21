$(document).ready(function(){
	$(".mod").hide();
	$(".lienzo-unidad").hide();
	$(".carga-masiva").hide();

	$(".celda").click(function(event) {
		id_unidad=$(this).attr('data-id');
		$(".mod").show();
		$.ajax({
			url:"unidad.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
				$(".mod").hide();
     			$(".lienzo-unidad").html(result);
      			$(".lienzo-unidad").show();
    		}
    	});
	});

	$(".borrar-unidad").click(function(event) {
		id_unidad = $(this).attr('data-id');
		fila = $(this).attr('data-fila');

	swal({
	  title: "Eliminar Unidad",
	  text: "Confirma Eliminar?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Eliminar!",
	  closeOnConfirm: true },
	  function(){
			$(".mod").show();
				$.ajax({
					url:"unidad_borrar.php",
					cache:false,
					type:"POST",
					data:{id_unidad:id_unidad},
					success:function(result){
						$("."+fila).addClass('fila-oculta');
						$(".mod").hide();
						$(".sweet-overlay").hide();
						$(".sweet-alert").hide();
						
						// swal("Borrado", "La unidad se ha eliminado", "success");
					}
				});
	 });
		
	});

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

})