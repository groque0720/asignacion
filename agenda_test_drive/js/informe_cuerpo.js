$(".checkbox_ok").click(function(){

	if( $(this).prop('checked') ) {
    	valor = 1;
    	$('.fila_'+$(this).attr('data-fila')).addClass('fila-ok');
    	$('.fila_'+$(this).attr('data-fila')).removeClass('fila-no-ok');
	}else{
		valor = 0;
		$('.fila_'+$(this).attr('data-fila')).addClass('fila-no-ok');
    	$('.fila_'+$(this).attr('data-fila')).removeClass('fila-ok');
	}

	id_linea = $(this).attr('data-id');


	$.ajax({
		url:"guardar_control.php",
		cache:false,
		type:"POST",
		data:{valor, id_linea},
		success:function(result){
		}
	});

})