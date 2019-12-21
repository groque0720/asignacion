$(".check_activo").click(function(){

	if ($(this).prop('checked')) {
		valor = 1;
	}else{
		valor = 0;
	}

	id=$(this).attr('data-id');

	$.ajax({
		url:"asesores_objetivos_actualizar_activacion.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});










