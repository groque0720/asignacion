$(".check_activo").click(function(){

	if ($(this).prop('checked')) {
		valor = 1;
	}else{
		valor = 0;
	}

	id=$(this).attr('data-id');

	$.ajax({
		url:"asesores_actualizar_activacion.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});

$(".borrar-activacion").click(function(){
	id=$(this).attr('data-id');
	fila = $(this).attr('data-fila');

	swal({
	  title: "Borrar Asesor",
	  text: "Confirma borrar al asesor de todos las aplicaciones de ETC desde ahora?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Borrar!",
	  closeOnConfirm: true },
	  function(){
		$.ajax({
			url:"asesores_borrar_activacion.php",
			cache:false,
			type:"POST",
			data:{id},
			success:function(result){
				$("."+fila).addClass('fila-oculta');
			}
		});
	 });






















});