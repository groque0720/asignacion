$(".check_activo").click(function(){

	if ($(this).prop('checked')) {
		valor = 1;
	}else{
		valor = 0;
	}

	id=$(this).attr('data-id');

	$.ajax({
		url:"modelos_actualizar_activacion.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});

$(".borrar-modelo").click(function(){
	id=$(this).attr('data-id');
	fila = $(this).attr('data-fila');

	swal({
	  title: "Borrar Modelo",
	  text: "Confirma borrar el modelo de todos las aplicaciones de ETC desde ahora?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Borrar!",
	  closeOnConfirm: true },
	  function(){
		$.ajax({
			url:"modelos_borrar.php",
			cache:false,
			type:"POST",
			data:{id},
			success:function(result){
				$("."+fila).addClass('fila-oculta');
			}
		});
	});
});

$(".nuevo_modelo").click(function(event){
	event.preventDefault();
	id='';
	$(".lienzo-unidad").show();
	$.ajax({
		url:"modelos_nueva_linea.php",
		cache:false,
		type:"POST",
		data:{id},
		success:function(result){
			// $(".tabla_cuerpo_modelos").after(result);
			$(".lienzo-unidad").html(result);
		}
	});
});