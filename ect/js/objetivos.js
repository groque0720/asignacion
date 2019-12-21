$(".check_activo").click(function(){

	if ($(this).prop('checked')) {
		valor = 1;
	}else{
		valor = 0;
	}

	id=$(this).attr('data-id');

	$.ajax({
		url:"objetivos_actualizar_activacion.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});

$(".borrar_tipo_objetivo").click(function(){
	id=$(this).attr('data-id');
	fila = $(this).attr('data-fila');

	swal({
	  title: "Borrar Objetivo",
	  text: "Confirma borrar el objetivo de todos las aplicaciones de ETC desde ahora?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Borrar!",
	  closeOnConfirm: true },
	  function(){
		$.ajax({
			url:"objetivos_borrar.php",
			cache:false,
			type:"POST",
			data:{id},
			success:function(result){
				$("."+fila).addClass('fila-oculta');
			}
		});
	 });






















});