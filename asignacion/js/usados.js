

$(".celda-usado").click(function(event) {
	id_unidad=$(this).attr('data-id');
	$(".mod").show();
	$.ajax({
		url:"usado.php",
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
	  title: "Eliminar Usado",
	  text: "Confirma Eliminar?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Eliminar!",
	  closeOnConfirm: true },
	  function(){
			$(".mod").show();
				$.ajax({
					url:"unidad_borrar_usado.php",
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